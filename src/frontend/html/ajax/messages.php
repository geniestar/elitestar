<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/Messages.php');

$user = EliteUsers::getInstance()->getCurrentUser();
if ($user)
{
    if ('add' === $_POST['action'])
    {
        if ('message' === $_POST['type'])
        {
            Messages::getInstance()->createMessage($user['id'], $_POST['id'], $_POST['message']); 
            EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
        }
        else
        {
            Messages::getInstance()->createReply($user['id'], $_POST['id'], $_POST['message']); 
            EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY')));
        }
    }
}
?>
