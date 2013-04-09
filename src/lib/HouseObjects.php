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
    public function createHouseObject($ownerId, $state, $city, $address, $houseName, $duration, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, $facilities, $rentLow, $rentHigh, $mainPhoto, $photos, $description)
    {
        $sql = 'INSERT INTO houseobjects (owner_id, state, city, address, house_name, duration, rooms, beds_single, beds_double, toilets, parking_space, facilities, rent_low, rent_high, main_photo, photos, description, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($ownerId, $state, $city, $address, $houseName, $duration, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, json_encode($facilities), $rentLow, $rentHigh, $mainPhoto, json_encode($photos), $description, time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function findHouseObjects($state, $city, $start = 0, $count = 20, $sortBy = self::SORT_BY_PRICE_DESC, $address = null, $houseName = null, $duration = null, $rentLow = null, $rentHigh = null)
    {
        $conditions = array();
        $conditions['state'] = array('op' => '=', 'value' => $state);
        $conditions['city'] = array('op' => '=', 'value' => $city);
        if ($address)
        {
            $conditions['address'] = array('op' => ' like ', 'value' => '%' . $address . '%');
        }
        if ($houseName)
        {
            $conditions['houseName'] = array('op' => ' like ', 'value' => '%' . $houseName . '%');
        }
        if ($duration)
        {
            $conditions['duration'] = array('op' => '>=', 'value' => $duration);
        }
        if ($rentLow)
        {
            $conditions['rent_low'] = array('op' => '>=', 'value' => $rentLow);
        }
        if ($rentHigh)
        {
            $conditions['rent_high'] = array('op' => '<=', 'value' => $rentHigh);
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
        $sql = 'SELECT * FROM houseobjects WHERE ' . implode($conditionColumns, ' AND ') . ' ORDER BY ' . $sortBy . ' LIMIT ?,?';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }
    
    public function updateHouseObjectInfo($ownerId, $state = null, $city = null, $address = null, $houseName = null, $duration = null, $rooms = null, $bedsSingle = null, $bedsDouble = null, $toilets = null, $parkingSpace = null, $facilities = null, $rentLow = null, $rentHigh = null, $mainPhoto = null, $photos = null, $description = null)
    
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
        if ($address)
        {
            $updateArray['address'] = $address;
        }
        if ($houseName)
        {
            $updateArray['house_name'] = $houseName;
        }
        if ($duration)
        {
            $updateArray['duration'] = $duration; 
        }
        if ($rooms)
        {
            $updateArray['rooms'] = $rooms;
        }
        if ($bedsSingle)
        {
            $updateArray['beds_single'] = $bedsSingle;
        }
        if ($bedsDouble)
        {
            $updateArray['beds_double'] = $bedsDouble;
        }
        if ($toilets)
        {
            $updateArray['toilets'] = $toilets;
        }
        if ($parkingSpace)
        {
            $updateArray['parking_space'] = $parkingSpace;
        }
        if ($facilities)
        {
            $updateArray['facilities'] = json_encode($facilities);
        }
        if ($rentLow)
        {
            $updateArray['rent_low'] = $rentLow;
        }
        if ($rentHigh)
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
            $sql = 'UPDATE houseobjects set ' . implode($updateColumns, ', ') . ' WHERE owner_id=?';
        }

        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

}

//var_dump(HouseObjects::getInstance()->createHouseObject('testaccount1', 1, 3, 'address', 'house name','2013-05-03 12:00:00', 1, 2, 2, 1, 2, array('bbb'=>'aaa'), 180, 200, 'no photo', array(), 'des'));
//var_dump(HouseObjects::getInstance()->updateHouseObjectInfo('account1', 2, 5, 'address', 'house name','2013-05-03 12:00:00', 4, 2, 2, 1, 1,array('take'=>true), 150, 200, 'no photo', array(), 'des'));
var_dump(HouseObjects::getInstance()->findHouseObjects($state = 1, $city = 3, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, $address = 'addr', $houseName = null, $duration = null, $rentLow = 20, $rentHigh = 250));
?>
