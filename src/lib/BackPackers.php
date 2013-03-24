<?php
include ('./MySqlDb.php');
/**
 * back packers
 */
class BackPackers
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
            self::$_instance = new BackPackers();
        }
        return self::$_instance;
    }
    
    /**
     * create backpacker
     *
     */
    public static function createBackPacker($userId, $city, $duration, $bedsSingle, $bedsDouble, $facilities, $additionalHelp)
    {
        $sql = 'INSERT INTO backpackers (user_id, city, duration, beds_single, beds_double, facilities, additional_help, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($userId, $city, $duration, $bedsSingle, $bedsDouble, json_encode($facilities), json_encode($additionalHelp), time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public static function updateBackPackerInfo($userId, $city = null, $duration = null, $bedsSingle = null, $bedsDouble = null, $facilities = null, $additionalHelp = null)
    {
        $updateArray = array();

        if ($city)
        {
            $updateArray['city'] = $city;
        }
        if ($duration)
        {
            $updateArray['duration'] = $duration; 
        }
        if ($bedsSingle)
        {
            $updateArray['beds_single'] = $bedsSingle;
        }
        if ($bedsDouble)
        {
            $updateArray['beds_double'] = $bedsDouble;
        }
        if ($facilities)
        {
            $updateArray['facilities'] = json_encode($facilities);
        }
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
            $sql = 'UPDATE backpackers set ' . implode($updateColumns, ', ') . ' WHERE user_id=?';
        }

        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

}

//BackPackers::getInstance()->createBackPacker('testaccount4', 2, '2013-05-03 12:00:00', 2, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'));
//BackPackers::getInstance()->updateBackPackerInfo('testaccount2', 5, '2013-05-03 13:00:00', 2, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'));

?>
