<?php
include ('MySqlDb.php');

/**
 * elite users
 */
class EliteUsers
{
    private static $_instance;
    const ROLE_LANDLORD = 0;
    const ROLE_BACKPACKER = 1;

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
    public function createUser($id, $password, $name, $mail, $phone, $role)
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
     * @param boolean $isMd5    is md5
     * @param boolean $isAdmin  isAdmin
     *
     */
    public function queryUser($id, $password, $isMd5 = false, $isAdmin = false)
    {
        if (!$isMd5)
        {
            $password = md5($password); 
        }
        $sql = 'SELECT * FROM users WHERE id=?';
        $inputParams = array($id);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        if ($isAdmin)
        {
            return $r;
        }
        else
        {
            if ($r && isset($r[0]) && $r[0]['password'] === $password)
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
    public function updateUserInfo($id, $password = null, $name = null, $mail = null, $phone = null, $role = null)
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

    public function login($id, $password, $isRemember)
    {
        $user = $this->queryUser($id, $password);
        /* set id and password in cookie*/
        if ($user && isset($user[0]))
        {
            $user = $user[0];
            if ($isRemember)
            {
                setcookie('u', $id, time()+60*60*24*365);
                setcookie('p', md5($password), time()+60*60*24*365);
            }
            else
            {
                /* expire when iser close broswer*/
                setcookie('u', $id, false);
                setcookie('p', md5($password), false);
            }
            return $user;
        }
        else
        {
            return false;
        }
    }

    public function getCurrentUser()
    {
        if (isset($_COOKIE['u']) && isset($_COOKIE['p']))
        {
            $user = $this->queryUser($_COOKIE['u'], $_COOKIE['p'], true);
        }
        if ($user && $user[0])
        {
            return $user[0];
        }
        else
        {
            return false;
        }
    }
}

//EliteUsers::getInstance()->createUser('testaccount2', 'test123', 'test user', 'test@yahoo.com', '0922', 'role');
//var_dump(EliteUsers::updateUserInfo('testaccount1', null, 'testuser3', 'no mail'));
//var_dump(EliteUsers::getInstance()->queryUser('testaccount2', 'test123'));
?>
