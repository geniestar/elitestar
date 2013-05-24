<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');

$user = EliteUsers::getInstance()->getCurrentUser();

if (1 == $_POST['role'])
{
    $backpacker = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, $_POST['id']);
    if ($backpacker)
    {
        $backpacker = $backpacker[0];
        $userInfo = EliteUsers::getInstance()->queryUser($backpacker['user_id'], null, null, true);
        $backpacker['user'] = $userInfo[0];
        $html = ContentGenerator::getContent('searchresult_backpacker', array('backpacker' => $backpacker, 'user' => $user));
        EliteHelper::ajaxReturnSuccess(array('html' => $html));
    }
    else
    {
        EliteHelper::ajaxReturnFailure('FAVORIITE_FALUT_TO_GET_DATA');
        exit;
    }
    
}
else
{
    $houseObject = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, null, $_POST['id']);
    if ($houseObject)
    {
        $houseObject = $houseObject[0];
        $userInfo = EliteUsers::getInstance()->queryUser($houseObject['owner_id'], null, null, true);
        $ownerInfo = LandLords::getInstance()->queryLandLord($houseObject['owner_id']);
        $houseObject['user'] = $userInfo[0];
        $houseObject['owner'] = $ownerInfo;
        $html = ContentGenerator::getContent('searchresult_houseobject', array('houseObject' => $houseObject, 'user' => $user));
        EliteHelper::ajaxReturnSuccess(array('html' => $html)); 
    }
    else
    {
        EliteHelper::ajaxReturnFailure('FAVORIITE_FALUT_TO_GET_DATA');
        exit;
    }
}

?>
