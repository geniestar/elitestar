<?php

/**
 * back packers
 */
class HouseObjects
{
    private static $_instance;
    const SORT_BY_PRICE = 'rent_low';
    const SORT_BY_PRICE_DESC = 'rent_low DESC';
    const SORT_BY_TIME = 'created_time';
    const SORT_BY_TIME_DESC = 'created_time DESC';
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
            self::$_instance = new HouseObjects();
        }
        return self::$_instance;
    }
    
    /**
     * create houseobject
     *
     */
    public function createHouseObject($ownerId, $state, $city, $address, $houseName, $durationStart, $durationEnd, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, $weCharge, $facilities, $rentLow, $rentHigh, $mainPhoto, $photos, $description)
    {
        $sql = 'INSERT INTO houseobjects (owner_id, state, city, address, house_name, duration_start, duration_end, rooms, beds_single, beds_double, toilets, parking_space, wecharge, facilities, rent_low, rent_high, main_photo, photos, description, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($ownerId, $state, $city, $address, $houseName, $durationStart,  $durationEnd, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, $weCharge, json_encode($facilities), $rentLow, $rentHigh, $mainPhoto, json_encode($photos), $description, time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function findHouseObjects($state, $city, $start = 0, $count = 20, $sortBy = self::SORT_BY_PRICE_DESC, $address = null, $houseName = null, $durationStart, $durationEnd = null, $rentLow = null, $rentHigh = null, $bedsSingle = null, $bedsDouble = null, $userId = null, $id = null, $getTotal = false)
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
        if ($address && $houseName)
        {
            $conditions['keyword_search'] = array(
                'address' => array('op' => ' like ', 'value' => '%' . $address . '%'),
                'house_name' => array('op' => ' like ', 'value' => '%' . $houseName . '%'),
            );
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
            $conditions['owner_id'] = array('op' => '=', 'value' => $userId);
        }
        if ($id)
        {
            $conditions['id'] = array('op' => '=', 'value' => $id);
        }
        $conditionColumns = array();
        $inputParams = array();
        foreach ($conditions as $key => $value)
        {
            if ('keyword_search' !== $key)
            {
                $conditionColumns[] = $key . $value['op']  . '?';
                $inputParams[] = $value['value'];
            }
            else
            {
                $tmp = array();
                foreach ($value as $key => $orCondition)
                {
                    $tmp[] = $key . $orCondition['op'] . '?';
                    $inputParams[] = $orCondition['value'];
                }
                $conditionColumns[] = '(' . implode($tmp, ' OR ') .')';
            }
        }
        $inputParams[] = $start;
        $inputParams[] = $count;
        if (empty($conditionColumns))
        {
            $conditionColumns[] = true;
        }
        if (!$getTotal)
        {
            $sql = 'SELECT * FROM houseobjects WHERE ' . implode($conditionColumns, ' AND ') . ' ORDER BY ' . $sortBy . ' LIMIT ?,?';
        }
        else
        {
            $sql = 'SELECT count(*) as total FROM houseobjects WHERE ' . implode($conditionColumns, ' AND ');
        }
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
    
    public function updateHouseObjectInfo($ownerId, $id, $state = null, $city = null, $address = null, $houseName = null, $durationStart = null, $durationEnd = null, $rooms = null, $bedsSingle = null, $bedsDouble = null, $toilets = null, $parkingSpace = null, $weCharge = null, $facilities = null, $rentLow = null, $rentHigh = null, $mainPhoto = null, $photos = null, $description = null)
    {
        $updateArray = array();

        if (null!== $state)
        {
            $updateArray['state'] = $state;
        }
        if (null !== $city)
        {
            $updateArray['city'] = $city;
        }
        if ($address)
        {
            $updateArray['address'] = $address;
        }
        if ($houseName)
        {
            $updateArray['house_name'] = $houseName;
        }
        if ($durationStart)
        {
            $updateArray['duration_start'] = $durationStart; 
        }
        if ($durationEnd)
        {
            $updateArray['duration_end'] = $durationEnd; 
        }
        if (null!== $rooms)
        {
            $updateArray['rooms'] = $rooms;
        }
        if (null !== $bedsSingle)
        {
            $updateArray['beds_single'] = $bedsSingle;
        }
        if (null !== $bedsDouble)
        {
            $updateArray['beds_double'] = $bedsDouble;
        }
        if (null !== $toilets)
        {
            $updateArray['toilets'] = $toilets;
        }
        if (null !== $parkingSpace)
        {
            $updateArray['parking_space'] = $parkingSpace;
        }
        if ($weCharge)
        {
            $updateArray['wecharge'] = $weCharge;
        }
        if ($facilities)
        {
            $updateArray['facilities'] = json_encode($facilities);
        }
        if (null !== $rentLow)
        {
            $updateArray['rent_low'] = $rentLow;
        }
        if (null !== $rentHigh)
        {
            $updateArray['rent_high'] = $rentHigh;
        }
        if ($mainPhoto)
        {
            $updateArray['main_photo'] = $mainPhoto;
        }
        if ($photos)
        {
            $updateArray['photos'] = json_encode($photos);
        }
        if ($description)
        {
            $updateArray['description'] = $description;
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

            $inputParams[] = $ownerId;
            $inputParams[] = $id;

            $sql = 'UPDATE houseobjects set ' . implode($updateColumns, ', ') . ' WHERE owner_id=? AND id=?';
        }
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
    public function deleteHouseObject($userId, $objectId)
    {
        $sql = 'DELETE FROM houseobjects WHERE owner_id=? AND id=?';
        $inputParams = array();
        $inputParams = array($userId, $objectId);
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
}

//var_dump(HouseObjects::getInstance()->createHouseObject('testaccount1', 1, 3, 'address', 'house name','2013-05-03 12:00:00', 1, 2, 2, 1, 2, array('bbb'=>'aaa'), 180, 200, 'no photo', array(), 'des'));
//var_dump(HouseObjects::getInstance()->updateHouseObjectInfo('account1', 2, 5, 'address', 'house name','2013-05-03 12:00:00', '2013-05-07 12:00:00', 4, 2, 2, 1, 1,array('take'=>true), 150, 200, 'no photo', array(), 'des'));
//var_dump(HouseObjects::getInstance()->findHouseObjects($state = 1, $city = 3, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, $address = 'addr', $houseName = null, $durationStart = null, $durationEnd = null, $rentLow = 20, $rentHigh = 250));
?>
