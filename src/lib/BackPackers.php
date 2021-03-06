<?php

/**
 * back packers
 */
class BackPackers
{
    private static $_instance;
    const SORT_BY_PRICE = 'rent_low';
    const SORT_BY_PRICE_DESC = 'rent_low DESC';
    const SORT_BY_TIME = 'updated_time';
    const SORT_BY_TIME_DESC = 'updated_time DESC';
    const SORT_BY_DURATION = '(duration_end - duration_start)';
    const SORT_BY_DURATION_DESC = '(duration_end - duration_start) DESC';

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
    public function createBackPacker($userId, $state, $city, $arrivalTime, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, $facilities, $additionalHelp, $name, $favorites)
    {
        $sql = 'INSERT INTO backpackers (user_id, state, city, arrival_time, duration_start, duration_end, rent_low, rent_high, beds_single, beds_double, facilities, additional_help, name, favorites, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($userId, $state, $city, $arrivalTime, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, json_encode($facilities), json_encode($additionalHelp), $name, json_encode($favorites), time(), time());
        //var_dump($inputParams);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        //var_dump($r);
        return $r;
    }

    public function findBackPackers($state = null, $city = null, $start = 0, $count = 20, $sortBy = self::SORT_BY_TIME_DESC, $arrivalTime, $durationStart = null, $durationEnd = null, $rentLow = null, $rentHigh = null, $bedsSingle = null, $bedsDouble = null, $name = null, $userId = null, $id = null, $getTotal = false)
    {
        $conditions = array();
        if (null !== $state)
        {
            $conditions['state'] = array('op' => '=', 'value' => $state);
        }
        if (null !== $city)
        {
            $conditions['city'] = array('op' => '=', 'value' => $city);
        }
    
        if (null !== $arrivalTime)
        {
            $conditions['arrival_time'] = array('op' => '=', 'value' => $arrivalTime);
        }
        if (null !== $durationStart)
        {
            $conditions['duration_start'] = array('op' => '>=', 'value' => $durationStart);
        }
        if (null !== $durationEnd)
        {
            $conditions['duration_end'] = array('op' => '<=', 'value' => $durationEnd);
        }
        if (null !== $rentLow)
        {
            $conditions['rent_low'] = array('op' => '>=', 'value' => $rentLow);
        }
        if (null !== $rentHigh)
        {
            $conditions['rent_high'] = array('op' => '<=', 'value' => $rentHigh);
        }
        if (null !== $bedsSingle)
        {
            $conditions['beds_single'] = array('op' => '=', 'value' => $bedsSingle);
        }
        if (null !== $bedsDouble)
        {
            $conditions['beds_double'] = array('op' => '=', 'value' => $bedsDouble);
        }
        if ($userId)
        {
            $conditions['user_id'] = array('op' => '=', 'value' => $userId);
        }
        if ($name)
        {
            $conditions['name'] = array('op' => ' like ', 'value' => '%' . $name . '%');
        }
        if ($id)
        {
            $conditions['id'] = array('op' => '=', 'value' => $id);
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
        if (!$getTotal)
        {
            $sql = 'SELECT * FROM backpackers WHERE ' . implode($conditionColumns, ' AND ') . ' ORDER BY ' . $sortBy . ' LIMIT ?,?';
        }
        else
        {
            $sql = 'SELECT count(*) as total FROM backpackers WHERE ' . implode($conditionColumns, ' AND ');
        }
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function updateBackPackerInfo($userId, $state = null, $city = null, $rent = null, $arrivalTime = null, $durationStart = null, $durationEnd = null, $bedsSingle = null, $bedsDouble = null, $facilities = null, $additionalHelp = null, $name = null, $favorites = null)
    {
        $updateArray = array();

        if (null !== $state)
        {
            $updateArray['state'] = $state;
        }
        if (null !== $city)
        {
            $updateArray['city'] = $city;
        }
        if (null !== $rent)
        {
            $updateArray['rent_low'] = $rent;
            $updateArray['rent_high'] = $rent;
        }
        if ($arrivalTime)
        {
            $updateArray['arrival_time'] = $arrivalTime;
        }
        if ($durationStart)
        {
            $updateArray['duration_start'] = $durationStart; 
        }
        if ($durationEnd)
        {
            $updateArray['duration_end'] = $durationEnd; 
        }
        if (null !== $bedsSingle)
        {
            $updateArray['beds_single'] = $bedsSingle;
        }
        if (null !== $bedsDouble)
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
        if ($favorites)
        {
            $updateArray['favorites'] = json_encode($favorites);
        }
        if ($name)
        {
            $updateArray['name'] = $name;
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
