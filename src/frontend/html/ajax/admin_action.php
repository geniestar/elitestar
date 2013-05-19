<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/Messages.php');
include('/usr/share/pear/elitestar/lib/LiveMessages.php');
/* no login needed action*/
if (isset($_POST['action']) && 'check-user' === $_POST['action'])
{
    if (is_array(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password'], false, true)))
    {
        EliteHelper::ajaxReturnFailure('USER_ID_INVALID');
        exit;
    }
    else
    {
        EliteHelper::ajaxReturnSuccess(array('message' => ''));
        exit;
    }
}

$user = EliteUsers::getInstance()->getCurrentUser();
if (!$user)
{
    echo 'user wrong';
}

if (isset($_POST['action']) && 'delete-photo' === $_POST['action'])
{
      
    $oldhouseobject = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id'], $_POST['objectid']);
    $ophotos = json_decode(json_decode($oldhouseobject[0]['photos']), true);
    $photos = array();
    foreach($ophotos as $photo)
    {
        if ($photo !== $_POST['photoid'])
        {
            $photos[] = $photo;
        }
    }
    HouseObjects::getInstance()->updateHouseObjectInfo($user['id'], $_POST['objectid'], null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $photos[0], json_encode($photos), null);
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('REG_PHOTO_DELETE_SUCCESSFULLY')));
}
else if (isset($_POST['action']) && 'get-form' === $_POST['action'])
{
    if (isset($_POST['objectid']))
    {
        $houseobjects = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id'], $_POST['objectid']);
        $formHtml ='';
        $formHtml .= '<div id="houseowner-form" class="form">';
        $formHtml .= '<input type="hidden" name="objectid" value="' . $houseobjects[0]['id'] . '">';
        $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_houseobject', array('houseobject' => $houseobjects[0])) . '</div>';
        
        EliteHelper::ajaxReturnSuccess(array('html' => $formHtml));
    }
    else
    {
        $formHtml ='';
        $formHtml .= '<div id="houseowner-form" class="form">';
        $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_houseobject', array()) . '</div>';
        
        EliteHelper::ajaxReturnSuccess(array('html' => $formHtml));
    }
}
else if (isset($_POST['action']) && 'suggestions' === $_POST['action'])
{
    if (!isset($_POST['message']) || '' === $_POST['message'])
    {
        EliteHelper::ajaxReturnFailure('MESSAGE_IS_EMPTY');
        exit;
    }
    Messages::getInstance()->createMessage($user['id'], 'superuser', $_POST['message']); 
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
}
else if (isset($_POST['action']) && 'delete-object' === $_POST['action'])
{
    HouseObjects::getInstance()->deleteHouseObject($user['id'], $_POST['objectid']); 
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('ADMIN_DELETED_OBJECT')));
} 
else if (isset($_POST['action']) && 'recommend' === $_POST['action'])
{
    $finalResults = array();
    $user = EliteUsers::getInstance()->getCurrentUser();
    if (0 === $user['role'])
    {
        $houseobjects = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id'], $_POST['objectid']);
        foreach($houseobjects as $houseobject)
        {
            if (isset($_POST['objectid']) && '' !== $_POST['objectid'])
            {
                $results = BackPackers::getInstance()->findBackPackers($houseobject['state'], $houseobject['city'], 0, 99, BackPackers::SORT_BY_TIME, null, $houseobject['duration_start'], $houseobject['duration_end'], null, null, null, null, null);
            }
            else
            {
                $results = BackPackers::getInstance()->findBackPackers($houseobject['state'], $houseobject['city'], 0, 99, BackPackers::SORT_BY_TIME, null, null, null, null, null, null, null, null);
            }

            foreach ($results as $result)
            {
                $userInfo = EliteUsers::getInstance()->queryUser($result['user_id'], null, null, true);
                $result['user'] = $userInfo[0];
                $finalResults[$result['id']] = $result;
            }
        }
    }
    else
    {
        $backpackers = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_TIME, null, null, null, null, null, null, null, null, $user['id']);
        $backpacker = $backpackers[0];
        $results = HouseObjects::getInstance()->findHouseObjects($backpacker['state'], $backpacker['city'], 0, 99, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null);
        foreach ($results as $result)
        {
            if ($user['role'] == 1)
            {
                $userInfo = EliteUsers::getInstance()->queryUser($result['owner_id'], null, null, true);
            }
            else
            {
                $userInfo = EliteUsers::getInstance()->queryUser($result['user_id'], null, null, true);
            }
            $result['user'] = $userInfo[0];
            $finalResults[$result['id']] = $result;
        }
    }
    shuffle($finalResults);
    $finalResultsNoIndex = array();
    $count = 0;
    foreach ($finalResults as $result)
    {
        if ($count > 3)
        {
            $result['hidden'] = true;
        }
        $finalResultsNoIndex[] = $result;
        $count++;
    }
    $html = ContentGenerator::getContent('admin_recommend', array('results' => $finalResultsNoIndex, 'role' => $user['role'], 'type' => $_POST['type']));
    EliteHelper::ajaxReturnSuccess(array('html' => $html));
}
?>
