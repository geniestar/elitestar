<?php

/**
 * back packers
 */
class BackPackers
{
    private static $_instance;
    const SORT_BY_PRICE = 'rent_low';
    const SORT_BY_PRICE_DESC = 'rent_low DESC';
    const SORT_BY_TIME = 'created_time';
    const SORT_BY_TIME_DESC = 'created_time DESC';

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
    public function createBackPacker($userId, $state, $city, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, $facilities, $additionalHelp, $favorates)
    {
        $sql = 'INSERT INTO backpackers (user_id, state, city, duration_start, duration_end, rent_low, rent_high, beds_single, beds_double, facilities, additional_help, favorates, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($userId, $state, $city, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, json_encode($facilities), json_encode($additionalHelp), json_encode($favorates), time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function findBackPackers($state = null, $city = null, $start = 0, $count = 20, $sortBy = self::SORT_BY_TIME_DESC, $durationStart = null, $durationEnd = null, $rentLow = null, $rentHigh = null, $userId = null)
    {
        $conditions = array();
        if ($state)
        {
            $conditions['state'] = array('op' => '=', 'value' => $state);
        }
        if ($ciry)
        {
            $conditions['city'] = array('op' => '=', 'value' => $city);
        }
    
        if ($durationStart)
        {
            $conditions['duration_start'] = array('op' => '<=', 'value' => $durationStart);
        }
        if ($durationEnd)
        {
            $conditions['duration_end'] = array('op' => '>=', 'value' => $durationEnd);
        }
        if ($rentLow)
        {
            $conditions['rent_low'] = array('op' => '<=', 'value' => $rentLow);
        }
        if ($rentHigh)
        {
            $conditions['rent_high'] = array('op' => '>=', 'value' => $rentHigh);
        }
        if ($userId)
        {
            $conditions['user_id'] = array('op' => '=', 'value' => $userId);
        }
        $conditionColumns = array();
        $inputParams = array();
        foreach ($conditions as $key => $value)
        {
            $conditionColumns[] = $key . $value['op']  . '?';
            $inputParams[] = $value['value'];
        }
        $inputParams[] = $start;
        $inputParams[] = $count;
        if (empty($conditionColumns))
        {
            $conditionColumns[] = true;
        }
        $sql = 'SELECT * FROM backpackers WHERE ' . implode($conditionColumns, ' AND ') . ' ORDER BY ' . $sortBy . ' LIMIT ?,?';
        echo $sql;
        var_dump($inputParams);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function updateBackPackerInfo($userId, $city = null, $duration = null, $bedsSingle = null, $bedsDouble = null, $facilities = null, $additionalHelp = null, $favorates = null)
    {
        $updateArray = array();

        if ($state)        
        {
            $updateArray['state'] = $state;
        }
        if ($city)
        {
            $updateArray['city'] = $city;
        }
        if ($durationStart)
        {
            $updateArray['duration_start'] = $durationStart; 
        }
        if ($durationEnd)
        {
            $updateArray['duration_end'] = $durationEnd; 
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
            $sql = 'UPDATE backpackers set ' . implode($updateColumns, ', ') . ' WHERE user_id=?';
        }

        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
}

//BackPackers::getInstance()->createBackPacker('testaccount5', 1, 2, '2013-05-03 12:00:00', '2013-10-03 12:00:00', 180, 200, 1, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'), array(123,124));
//BackPackers::getInstance()->updateBackPackerInfo('testaccount4', 5, '2013-05-03 13:00:00', 2, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'), array(1234,123));

//var_dump(BackPackers::getInstance()->findBackPackers($state = 1, $city = 2, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, 170, 200));
?>
