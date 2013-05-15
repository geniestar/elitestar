<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/Messages.php');
/* no login needed action*/
if (isset($_POST['action']) && 'check-user' === $_POST['action'])
{
    if (is_array(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password'], false, true)))
    {
        EliteHelper::ajaxReturnFailure(array('message' => EliteHelper::getErrorString('USER_ID_INVALID')));
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
else if (isset($_POST['action']) && 'get-messages' === $_POST['action'])
{
    $messages = Messages::getInstance()->queryMessagesOfReceiver($user['id'], $_POST['start'], 5, $user['id']);
    $html .= ContentGenerator::getContent('admin_messages', array('messages' => $messages));
    EliteHelper::ajaxReturnSuccess(array('html' => $html, 'count' => count($messages)));
}
else if (isset($_POST['action']) && 'reply-messages' === $_POST['action'])
{
    Messages::createReply($_POST['messageId'], $user['id'], null, $_POST['message']);
    $html = '';
    $html .= '<div class="message-single reply">';
    $html .= '<span class="title">' . EliteHelper::getLangString('ADMIN_MESSAGES_I') . '</span>' . ' ' . EliteHelper::getLangString('ADMIN_MESSAGES_SAID') . ' (' . date('Y/M/d h:i:s', time()) . '):';
    $html .= '<div class="message-body">' . str_replace("\n", '<br>', $_POST['message']) . '</div>';
    $html .= '</div>';
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_REPLY_SUCCESSFULLY'), 'html' => $html));
}
else if (isset($_POST['action']) && 'delete-messages' === $_POST['action'])
{
    Messages::deleteMessage($_POST['messageId'], $user['id']);
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('ADMIN_DELETED_MESSAGE')));
}
else if (isset($_POST['action']) && 'suggestions' === $_POST['action'])
{
    Messages::getInstance()->createMessage($user['id'], 'superuser', $_POST['message']); 
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
}
else if (isset($_POST['action']) && 'delete-object' === $_POST['action'])
{
    HouseObjects::getInstance()->deleteHouseObject($user['id'], $_POST['objectid']); 
    EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('ADMIN_DELETED_OBJECT')));
}
?>
