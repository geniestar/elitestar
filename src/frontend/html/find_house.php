<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
$user = EliteUsers::getInstance()->getCurrentUser();
var_dump(HouseObjects::getInstance()->findHouseObjects($state = 1, $city = 3, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, $address = 'addr', $houseName = null, $durationStart = null, $durationEnd = null, $rentLow = 20, $rentHigh = 250));
var_dump(HouseObjects::getInstance()->findHouseObjects());
?>
