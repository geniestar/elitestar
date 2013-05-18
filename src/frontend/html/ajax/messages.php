<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/LiveMessages.php');

$user = EliteUsers::getInstance()->getCurrentUser();
if ($user)
{
    if (!isset($_POST['message']) || '' === $_POST['message'])
    {
        EliteHelper::ajaxReturnFailure('MESSAGE_IS_EMPTY');
        exit;
    }
    if ('add' === $_POST['action'])
    {
        if ('message' === $_POST['type'])
        {
            LiveMessages::getInstance()->createMessage($user['id'], $_POST['id'], $_POST['message']); 
            EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
        }
        else
        {
            LiveMessages::getInstance()->createReply($user['id'], $_POST['id'], $_POST['message']); 
            EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
        }
    }
    else if ('checkCount' === $_POST['action'])
    {
        EliteHelper::ajaxReturnSuccess(array('count' => LiveMessages::getInstance()->checkUnreadMessages($user['id'])));
    }
    else if ('checkMessages' === $_POST['action'])
    {
        EliteHelper::ajaxReturnSuccess(array('count' => LiveMessages::getInstance()->getUnreadMessages($user['id'])));
    }
}
?>
