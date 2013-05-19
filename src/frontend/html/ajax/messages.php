<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/LiveMessages.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');

$user = EliteUsers::getInstance()->getCurrentUser();
if ($user)
{
    if ('add' === $_POST['action'])
    {
        if (!isset($_POST['message']) || '' === $_POST['message'])
        {
            EliteHelper::ajaxReturnFailure('MESSAGE_IS_EMPTY');
            exit;
        }
            LiveMessages::getInstance()->createMessage($user['id'], $_POST['id'], $_POST['message']); 
            $html = '';
            $html .= '<div class="message-single">';
            $html .= '<span class="title">' . EliteHelper::getLangString('ADMIN_MESSAGES_I') . '</span>' . ' ' . EliteHelper::getLangString('ADMIN_MESSAGES_SAID') . ' (' . date('Y/M/d h:i:s', time()) . '):';
            $html .= '<div class="message-body">' . str_replace("\n", '<br>', $_POST['message']) . '</div>';
            $html .= '</div>';
            EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_MESSAGE_SEND_MESSAGE_SUCCESSFULLY'), 'html' => $html));
    }
    else if ('checkCount' === $_POST['action'])
    {
        EliteHelper::ajaxReturnSuccess(array('count' => LiveMessages::getInstance()->checkUnreadMessages($user['id'])));
    }
    else if ('checkMessages' === $_POST['action'])
    {
        EliteHelper::ajaxReturnSuccess(array('count' => LiveMessages::getInstance()->getUnreadMessages($user['id'])));
    }
    else if (isset($_POST['action']) && 'get-messages' === $_POST['action'])
    {
        $messages = LiveMessages::getInstance()->getTalkerMessages($user['id'], $_POST['talker'], $_POST['start'], 5);
        $html .= ContentGenerator::getContent('admin_messages', array('messagesSets' => array(array('messages'=>$messages)), 'singleMessage' => true));
        EliteHelper::ajaxReturnSuccess(array('html' => $html, 'count' => count($messages)));
    }
    else if (isset($_POST['action']) && 'delete-messages' === $_POST['action'])
    {
        LiveMessages::getInstance()->deleteMessage($user['id'], $_POST['talker']);
        EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('ADMIN_DELETED_MESSAGE')));
    }
    else if (isset($_POST['action']) && 'check-unread-messages' === $_POST['action'])
    {
        $result = LiveMessages::getInstance()->checkUnreadMessages($user['id']);
        EliteHelper::ajaxReturnSuccess(array('unreadCount' => $result));
    }
    else if (isset($_POST['action']) && 'get-unread-messages' === $_POST['action'])
    {
        $result = LiveMessages::getInstance()->getUnreadMessages($user['id']);
        $unreadHtml = array();
        foreach ($result as $talker => $talking)
        {
            $html = ContentGenerator::getContent('admin_messages', array('messagesSets' => array(array('messages'=>$talking['messages'])), 'singleMessage' => true));
            $unreadHtml[$talker] = $html;
        }
        EliteHelper::ajaxReturnSuccess(array('unreads' => $unreadHtml));
    }
}
?>
