<?php
include ('./MySqlDb.php');
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
    public static function createLandLord($userId, $additionalHelp)
    {
        $sql = 'INSERT INTO landlords (user_id, additional_help, created_time, updated_time) VALUES(?, ?, ?, ?)';
        $inputParams = array($userId, json_encode($additionalHelp), time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public static function updateLandLordInfo($userId, $additionalHelp = null)
    {
        $updateArray = array();

        if ($additionalHelp)
        {
            $updateArray['additional_help'] = json_encode($additionalHelp);
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

//var_dump(LandLords::getInstance()->createLandLord('testaccount1', array('ccc'=>'asb')));
//var_dump(LandLords::getInstance()->updateLandLordInfo('testaccount1', array('cc'=>'asb')));

?>
