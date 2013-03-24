<?php
include ('./MySqlDb.php');
/**
 * elite users
 */
class EliteUsers
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
            self::$_instance = new EliteUsers();
        }
        return self::$_instance;
    }

    /**
     * addUser
     *
     * @param string  $id       id for user
     * @param string  $password password
     * @param string  $name     user name
     * @param string  $mail     mail
     * @param string  $phone    phone
     * @param integer $role     role for user 0:landlord 1:packager
     *
     */
    public static function createUser($id, $password, $name, $mail, $phone, $role)
    {
        $sql = 'INSERT INTO users VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($id, md5($password), $name, $mail, $phone, $role, time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    /**
     * queryUser
     *
     * @param string  $id       id for user
     * @param string  $password password
     * @param boolean $isAdmin  isAdmin
     *
     */
    public static function queryUser($id, $password, $isAdmin = false)
    {
        $sql = 'SELECT * FROM users WHERE id=?';
        $inputParams = array($id);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        if ($isAdmin)
        {
            return $r;
        }
        else
        {
            if ($r && isset($r[0]) && $r[0]['password'] === md5($password))
            {
                return $r;
            }
            else
            {
                return null;
            }
        }
    }

    /**
     * updateUserInfo
     *
     * @param string  $id       id for user
     * @param string  $password password
     * @param string  $name     user name
     * @param string  $mail     mail
     * @param string  $phone    phone
     * @param integer $role     role for user 0:landlord 1:packager
     *
     */
    public static function updateUserInfo($id, $password = null, $name = null, $mail = null, $phone = null, $role = null)
    {
        $updateArray = array();

        if ($password)
        {
            $updateArray['password'] = $md5($password);
        }
        if ($name)
        {
            $updateArray['name'] = $name; 
        }
        if ($mail)
        {
            $updateArray['mail'] = $mail;
        }
        if ($phone)
        {
            $updateArray['phone'] = $phone;
        }
        if ($role)
        {
            $updateArray['role'] = $role;
        }
        
        if (!$updateArray)
        {
            return;
        }
        else
        {
            $updateColumns = array();
            $inputParams = array();
            foreach ($updateArray as $key => $value)
            {
                $updateColumns[] = $key . '=?';
                $inputParams[] = $value;
            }
            $updateColumns[] = 'updated_time=?';
            $inputParams[] = time();

            $inputParams[] = $id;
            $sql = 'UPDATE users set ' . implode($updateColumns, ', ') . ' WHERE id=?';
        }

        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
}

EliteUsers::getInstance()->createUser('testaccount2', 'test123', 'test user', 'test@yahoo.com', '0922', 'role');
//var_dump(EliteUsers::updateUserInfo('testaccount1', null, 'testuser3', 'no mail'));
var_dump(EliteUsers::getInstance()->queryUser('testaccount2', 'test123'));
?>
