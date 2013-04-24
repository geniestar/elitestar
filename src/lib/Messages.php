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

    public function queryMessagesOfReceiver($userId, $isReply = 0, $start = 0, $count = 20)
    {
        $sql = 'SELECT * FROM messages WHERE receiver=? ORDER BY created_time DESC LIMIT ?, ?';
        $inputParams = array($userId, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function queryMessagesOfSender($userId, $isReply = 0, $start = 0, $count = 20)
    {
        $sql = 'SELECT * FROM messages WHERE sender=? ORDER BY created_time DESC LIMIT ?, ?';
        $inputParams = array($userId, $start, $count);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
    
    public function deleteMessage($id)
    {
        $sql = 'DELETE FROM messages WHERE id=?';
        $inputParams = array($id);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
}
//    Messages::getInstance()->createMessage('user1', 'user2', 'hi');
//    Messages::getInstance()->createReply(1, 'user2', 'user1', 'hello');
// var_dump(Messages::getInstance()->queryMessagesOfReceiver('user1', false, 0, 2));
//var_dump(Messages::getInstance()->queryMessagesOfSender('user1', false, 0, 2));

?>
