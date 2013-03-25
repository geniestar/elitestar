<?php
include ('./MySqlDb.php');
/**
 * back packers
 */
class HouseObjects
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
            self::$_instance = new HouseObjects();
        }
        return self::$_instance;
    }
    
    /**
     * create houseobject
     *
     */
    public function createHouseObject($ownerId, $state, $city, $houseName, $duration, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, $facilities, $rent, $mainPhoto, $photos, $description)
    {
        $sql = 'INSERT INTO houseobjects (owner_id, state, city, house_name, duration, rooms, beds_single, beds_double, toilets, parking_space, facilities, rent, main_photo, photos, description, created_time, updated_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $inputParams = array($ownerId, $state, $city, $houseName, $duration, $rooms, $bedsSingle, $bedsDouble, $toilets, $parkingSpace, json_encode($facilities), $rent, $mainPhoto, json_encode($photos), $description, time(), time());
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        return $r;
    }

    public function updateHouseObjectInfo($ownerId, $state = null, $city = null, $houseName = null, $duration = null, $rooms = null, $bedsSingle = null, $bedsDouble = null, $toilets = null, $parkingSpace = null, $facilities = null, $rent = null, $mainPhoto = null, $photos = null, $description = null)
    
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
        if ($rent)
        {
            $updateArray['rent'] = $rent;
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

//HouseObjects::getInstance()->createHouseObject('testaccount1', 1, 2, 'house name','2013-05-03 12:00:00', 4, 2, 2, 1,1,array('bbb'=>'aaa'), 150, 'no photo', array(), 'des');
var_dump(HouseObjects::getInstance()->updateHouseObjectInfo('testaccount1', 2, 5, '1house name','2013-05-03 12:00:00', 4, 2, 2, 1,1,array('bbb'=>'aaa'), 150, 'no photo', array(), 'des'));

?>
