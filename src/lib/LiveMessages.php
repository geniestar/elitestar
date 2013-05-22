<?php
/**
 * LiveMessages
 */
class LiveMessages
{
    private static $_instance;

    /**
     * __construct 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
    }  

    /**
     * Destructor 
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
    }

    /**
     * getInstance
     *
     * @access
     * @return instance
     */
    public static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new LiveMessages();
        }
        return self::$_instance;
    }

    /**
     * create message
     *
     */
    public function createMessage($sender, $receiver, $message)
    {
        /*there are two records for sender and receiver*/
        $sql = 'INSERT INTO livemessages (viewer, sender, receiver, message, created_time) VALUES(?, ?, ?, ?, ?)';
        $inputParams = array($sender, $sender, $receiver, $message, time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $inputParams = array($receiver, $sender, $receiver, $message, time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        
        $sql = 'SELECT * from messagelog WHERE viewer=? AND talker=?';
        $inputParams = array($sender, $receiver);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);

        if (!$r)
        {
             $sql = 'INSERT INTO messagelog (viewer, talker, updated_time) VALUES(?, ?, ?)';
             $inputParams = array($sender, $receiver, time());
             $r = MySqlDb::getInstance()->query($sql, $inputParams);
        }
        else
        {
            $sql = 'UPDATE messagelog SET updated_time=? WHERE viewer=? AND talker=?';
            $inputParams = array(time(), $sender, $receiver);
            $r = MySqlDb::getInstance()->query($sql, $inputParams);
        }

        $sql = 'SELECT * from messagelog WHERE viewer=? AND talker=?';
        $inputParams = array($receiver, $sender);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);

        if (!$r)
        {
             $sql = 'INSERT INTO messagelog (viewer, talker, updated_time) VALUES(?, ?, ?)';
             $inputParams = array($receiver, $sender, time());
             $r = MySqlDb::getInstance()->query($sql, $inputParams);
        }
        else
        {
            $sql = 'UPDATE messagelog SET updated_time=? WHERE viewer=? AND talker=?';
            $inputParams = array(time(), $receiver, $sender);
            $r = MySqlDb::getInstance()->query($sql, $inputParams);
        }
        $unreadStatus = apc_fetch($this->getUserCacheKey($receiver));
        if (!$unreadStatus)
        {
            $unreadStatus = array();
        }
        if (!isset($unreadStatus[$sender]))
        {
            $unreadStatus[$sender] = 1;
        }
        else
        {
            $unreadStatus[$sender]++;
        }
        apc_store($this->getUserCacheKey($receiver), $unreadStatus);

        return $r;
    }

    private function getUserCacheKey($id)
    {
        return sprintf('MESSAGES_%s', $id);
    }

    public function getOverallMessages($id, $start, $count)
    {
        $sql = 'SELECT * from messagelog WHERE viewer=? ORDER BY updated_time DESC LIMIT?,?';
        $inputParams = array($id, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $unreadStatus = apc_fetch($this->getUserCacheKey($id));
        $messages = array();
        foreach ($r as $talking)
        {
            $count = 5; //default 5, but if there is more than 5 unread message, just show all
            if (isset($unreadStatus[$talking['talker']]) && $unreadStatus[$talking['talker']] > 5)
            {
                $count = $unreadStatus[$talking['talker']];
            }
            $singleTalkermessages = array();
            $singleTalkermessages['messages'] = LiveMessages::getInstance()->getTalkerMessages($id, $talking['talker'], 0, $count);
            $singleTalkermessages['talker'] = $talking['talker'];
            $sql = 'SELECT count(*) as count FROM livemessages WHERE viewer=? AND ((sender=? AND receiver=?) OR (sender=? AND receiver=?))';
            $inputParams = array($id, $id, $talking['talker'], $talking['talker'], $id);
            $r = MySqlDb::getInstance()->query($sql, $inputParams);
            $singleTalkermessages['count'] = $r[0]['count'];
            $messages[] = $singleTalkermessages;
        }
        $this->markAsRead($id);
        return $messages;
    }

    public function getTalkerMessages($id, $talker, $start, $count)
    {
        $queryCache = array();
        $sql = 'SELECT * FROM livemessages WHERE viewer=? AND ((sender=? AND receiver=?) OR (sender=? AND receiver=?)) ORDER BY created_time DESC LIMIT ?,?';
        $inputParams = array($id, $id, $talker, $talker, $id, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        foreach ($r as $key => $message)
        {
            if (!isset($queryCache[$message['sender']]))
            {
                $info = EliteUsers::getInstance()->queryUser($message['sender'], null, null, true);
                $queryCache[$message['sender']] = $info[0];
            }
            if (!isset($queryCache[$message['receiver']]))
            {
                $info = EliteUsers::getInstance()->queryUser($message['receiver'], null, null, true);
                $queryCache[$message['receiver']] = $info[0];
            }
            $info = $queryCache[$message['sender']];
            $r[$key]['senderInfo'] = $info;
            if ($message['sender'] === $id)
            {
                $r[$key]['senderInfo']['name'] = EliteHelper::getLangString('ADMIN_MESSAGES_I');
            }
            $info = $queryCache[$message['receiver']];
            $r[$key]['receiverInfo'] = $info;
            if ($message['receiver'] === $id)
            {
                $r[$key]['receiverInfo']['name'] = EliteHelper::getLangString('ADMIN_MESSAGES_ME');
            }
        }
        //return array_reverse($r); //latest at the bottom
        return $r;
    }
    
    public function deleteMessage($id, $talker)
    {
        $sql = 'DELETE FROM livemessages WHERE viewer=? AND ((sender=? AND receiver=?) OR (sender=? AND receiver=?))';
        $inputParams = array($id, $id, $talker, $talker, $id);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $sql = 'DELETE FROM messagelog WHERE viewer=? AND talker=?';
        $inputParams = array($id, $talker);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return;
    }
    
    public function checkUnreadMessages($id)
    {
        $unreadCount = 0;
        $unreadStatus = apc_fetch($this->getUserCacheKey($id));
        if ($unreadStatus)
        {
            foreach($unreadStatus as $unreadTalker => $count)
            {
                $unreadCount += $count;
            }
        }
        return $unreadCount;
    }

    public function getUnreadMessages($id)
    {
        $unreadMessages = array();
        $unreadStatus = apc_fetch($this->getUserCacheKey($id));
        if ($unreadStatus)
        {
            foreach($unreadStatus as $unreadTalker => $count)
            {
                $unreadMessages[$unreadTalker] = array();
                $unreadMessages[$unreadTalker]['messages'] = LiveMessages::getInstance()->getTalkerMessages($id, $unreadTalker, 0, $count);
            }
        }
        $this->markAsRead($id);
        return $unreadMessages;
    }

    public function markAsRead($id)
    {
        apc_store($this->getUserCacheKey($id), false);    
    }
}
//var_dump(Messages::getInstance()->queryReplies(1));
    //LiveMessages::getInstance()->createMessage('user1', 'user4', 'hi?');
  //  var_dump(LiveMessages::getInstance()->getOverallMessages('user4'));
  // var_dump( LiveMessages::getInstance()->getUnreadMessages('user4'));
  //  var_dump(LiveMessages::getInstance()->checkUnreadMessages('user4'));
//    Messages::getInstance()->createReply(1, 'user2', 'user1', 'hello');
//var_dump(Messages::getInstance()->queryMessagesOfReceiver('user2', 0, 5));
//var_dump(Messages::getInstance()->queryMessagesTotal('user2', 0, 5));
//var_dump(Messages::getInstance()->queryMessagesOfSender('user1', false, 0, 2));

?>
