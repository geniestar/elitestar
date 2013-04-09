<?php

/**
 * land lords
 */
class LandLords
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
            self::$_instance = new LandLords();
        }
        return self::$_instance;
    }
    
    /**
     * create landlord
     *
     */
    public function createLandLord($userId, $additionalHelp, $favorates)
    {
        $sql = 'INSERT INTO landlords (user_id, additional_help, favorates, created_time, updated_time) VALUES(?, ?, ?, ?, ?)';
        $inputParams = array($userId, json_encode($additionalHelp), json_encode($favorates), time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function queryLandLord($userId)
    {
        $sql = 'SELECT * FROM landlords WHERE user_id=?';
        $inputParams = array($userId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function updateLandLordInfo($userId, $additionalHelp = null, $favorates)
    {
        $updateArray = array();

        if ($additionalHelp)
        {
            $updateArray['additional_help'] = json_encode($additionalHelp);
        }
        if ($favorates)
        {
            $updateArray['favorates'] = json_encode($favorates);
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

            $inputParams[] = $userId;
            $sql = 'UPDATE landlords set ' . implode($updateColumns, ', ') . ' WHERE user_id=?';
        }

        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

}

//var_dump(LandLords::getInstance()->createLandLord('testaccount2', array('ccc'=>'asb'), array(1,2,3)));
//var_dump(LandLords::getInstance()->updateLandLordInfo('testaccount1', array('cc'=>'asb'), array(1,2,3,4)));
//var_dump(LandLords::getInstance()->queryLandLord('testaccount2'));

?>
