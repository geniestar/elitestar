<?php
/**
 * messages
 */
class Messages
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
            self::$_instance = new Messages();
        }
        return self::$_instance;
    }

    /**
     * create message
     *
     */
    public function createMessage($sender, $receiver, $message)
    {
        $sql = 'INSERT INTO messages (sender, receiver, message, is_reply, parent, created_time) VALUES(?, ?, ?, ?, ?, ?)';
        $inputParams = array($sender, $receiver, $message, 0, 0, time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function createSuggestion($sender, $message)
    {
        $receiver = 'superuser';
        $r = Messages::getInstance()->createMessage($sender, $receiver, $message);
        return $r;
    }

    /**
     * create reply
     *
     */
    public function createReply($messageId, $sender, $receiver, $message)
    {
        $sql = 'INSERT INTO messages (sender, receiver, message, is_reply, parent, created_time) VALUES(?, ?, ?, ?, ?, ?)';
        $inputParams = array($sender, $receiver, $message, 1, $messageId, time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
  
    public function querySuggestionsTotal()
    {
        $sql = 'SELECT count(*) as count FROM messages WHERE receiver=\'superuser\'';
        $inputParams = array($userId, $userId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r[0]['count'];
    }

    public function querySuggestions()
    {
        $inputParams = array('superuser');
        $sql = 'SELECT * FROM messages WHERE receiver=? AND is_reply = 0 ORDER BY created_time DESC';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        foreach ($r as $message)
        {
            $info = EliteUsers::getInstance()->queryUser($message['sender'], null, null, true);
            $message['senderInfo'] = $info[0];
            $finalResult[] = $message;
        } 
        return $finalResult;
    }

    public function queryMessagesTotal($userId)
    {
        $sql = 'SELECT count(*) as count FROM messages WHERE (receiver=? OR sender=?) AND is_reply=0 AND receiver != \'superuser\'';
        $inputParams = array($userId, $userId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $finalResult = array();
        return $r[0]['count'];
    }

    public function queryMessagesOfReceiver($userId, $start = 0, $count = 20, $withReplies = true)
    {
        $sql = 'SELECT * FROM messages WHERE (receiver=? OR sender=?) AND is_reply = 0 AND receiver != \'superuser\' ORDER BY created_time DESC LIMIT ?, ?';
        $inputParams = array($userId, $userId, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $finalResult = array();
        foreach ($r as $message)
        {
            $info = EliteUsers::getInstance()->queryUser($message['sender'], null, null, true);
            $message['senderInfo'] = $info[0];
            if ($message['sender'] === $userId)
            {
                $message['senderInfo']['name'] = EliteHelper::getLangString('ADMIN_MESSAGES_I');
            }
            $info = EliteUsers::getInstance()->queryUser($message['receiver'], null, null, true);
            $message['receiverInfo'] = $info[0];
            if ($message['receiver'] === $userId)
            {
                $message['receiverInfo']['name'] = EliteHelper::getLangString('ADMIN_MESSAGES_ME');
                $message['canDelete'] = true;
            }
            $replies = Messages::getInstance()->queryReplies($message['id']);
            if ($userId)
            {
                foreach($replies as $key => $reply)
                {
                    $info = EliteUsers::getInstance()->queryUser($reply['sender'], null, null, true);
                    $replies[$key]['senderInfo'] = $info[0];
                    // change sender to I if sender is current user.
                    if ($reply['sender'] === $userId)
                    {
                        $replies[$key]['senderInfo']['name'] = EliteHelper::getLangString('ADMIN_MESSAGES_I');
                    }
                }
            }
            $message['replies'] = $replies;
            $finalResult[] = $message;
        }
        return $finalResult;
    }

    public function queryMessagesOfSender($userId, $start = 0, $count = 20)
    {
        $sql = 'SELECT * FROM messages WHERE sender=? ORDER BY created_time DESC LIMIT ?, ?';
        $inputParams = array($userId, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function queryReplies($messageId)
    {
        $sql = 'SELECT * FROM messages WHERE is_reply=1 AND parent=? ORDER BY created_time ASC';
        $inputParams = array($messageId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
    
    public function deleteMessage($id, $userId)
    {
        $sql = 'DELETE FROM messages WHERE id=? and (receiver=?) AND is_reply=0';
        $inputParams = array($id, $userId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $sql = 'DELETE FROM messages WHERE parent=? and (receiver=? or sender=?) AND is_reply=1';
        $inputParams = array($id, $userId, $userId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
}
//var_dump(Messages::getInstance()->queryReplies(1));
//    Messages::getInstance()->createMessage('user1', 'user2', 'hi');
//    Messages::getInstance()->createReply(1, 'user2', 'user1', 'hello');
//var_dump(Messages::getInstance()->queryMessagesOfReceiver('user2', 0, 5));
//var_dump(Messages::getInstance()->queryMessagesTotal('user2', 0, 5));
//var_dump(Messages::getInstance()->queryMessagesOfSender('user1', false, 0, 2));

?>
