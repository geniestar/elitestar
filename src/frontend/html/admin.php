<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/Messages.php');
include('/usr/share/pear/elitestar/lib/LiveMessages.php');
EliteHelper::updateBrowsingCounter();
$user = EliteUsers::getInstance()->getCurrentUser();
if (!$user)
{
    header('Location: error.php?error=NO_LOGIN');
    exit;
}
$formHtml = '';
if (0 == $user['role'])
{
    $role = 'houseowner';
}
else
{
    $role = 'backpacker';
}
EliteHelper::setParamsToJs('role', $role);
if ('basic' == $_GET['action'] || !isset($_GET['action']))
{
    EliteHelper::setParamsToJs('type', 'basic');
    $tabs = array(array('class' => 'basic-info', 'name' => EliteHelper::getLangString('COMMON_MENU_BASIC_INFO')));
    $formHtml .= '<form id="user-form" action="account_action.php" method="POST" enctype="multipart/form-data">';
    $formHtml .= '<input type="hidden" name="edit" value="1">';
    $formHtml .= '<input type="hidden" name="basic-info" value="1">';
    $formHtml .= ContentGenerator::getContent('register_user', array('user' => $user));
    $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_contact', array('user' => $user)) . '</div>';
    $formHtml .= ContentGenerator::getContent('register_publish_btn', array('updateBtn' => true, 'showBtn' => true));
    $formHtml .= '</form>';
}
else if ('settings' == $_GET['action'])
{
    EliteHelper::setParamsToJs('type', 'settings');
    if (0 == $user['role'])
    {
        $roleFormTabUnselected = (!isset($_GET['tab']) || $_GET['tab'] === 'settings')?false:true;
        $serviceFormTabUnselected = ($_GET['tab'] === 'service')?false:true;
        $roleFormClass = (!isset($_GET['tab']) || $_GET['tab'] === 'settings')?'':'hidden';
        $serviceFormClass = ($_GET['tab'] === 'service')?'':'hidden';
        $tabs = array(
            array('class' => 'settings', 'name' => EliteHelper::getLangString('COMMON_MENU_SETTINGS'), 'unselected' =>$roleFormTabUnselected, 'containId' => 'ajax-role-form'),
            array('class' => 'service', 'name' => EliteHelper::getLangString('COMMON_MENU_SERVICE'), 'unselected' => $serviceFormTabUnselected, 'containId' => 'form-service'),
        );
        $houseowner = LandLords::getInstance()->queryLandLord($user['id']);
        $houseowner = $houseowner[0];
        $houseobjects = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 100, HouseObjects::SORT_BY_TIME_DESC, null, null, null, null, null, null, null, null, $user['id']);
        $formHtml .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
        $formHtml .= '<input type="hidden" name="edit" value="1">';
        $formHtml .= '<input type="hidden" name="role" value="0">';
        $formHtml .= '<input type="hidden" name="tab" value="settings">';
        $formHtml .= '<div id="ajax-role-form" class="' . $roleFormClass . '"><div id="houseobject-selector">' . ContentGenerator::getContent('register_houseobject_selector', array('houseobjects' => $houseobjects)) . '<div class="clean"></div></div>';
        $formHtml .= '<div id="houseobject"></div></div>';
        $formHtml .= '<div id="form-service" class="' . $serviceFormClass . ' form">' . ContentGenerator::getContent('register_houseowner', array('houseowner' => $houseowner)) . '</div>';
        $formHtml .= ContentGenerator::getContent('register_publish_btn', array('updateBtn' => true));
        $formHtml .= '</form>';
    }
    else
    {
        $tabs = array(array('class' => 'settings', 'name' => EliteHelper::getLangString('COMMON_MENU_SETTINGS')));
        $backpacker = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);
        $backpacker = $backpacker[0];
        $formHtml .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
        $formHtml .= '<input type="hidden" name="edit" value="1">';
        $formHtml .= '<input type="hidden" name="role" value="1">';
        $formHtml .= '<div id="backpacker-form" class="form">' . ContentGenerator::getContent('register_backpacker', array('backpacker' => $backpacker)) . '</div>';
        $formHtml .= ContentGenerator::getContent('register_publish_btn', array('updateBtn' => true, 'showBtn' => true));
        $formHtml .= '</form>';
    }
}
else if ('messages' == $_GET['action'])
{
    EliteHelper::setParamsToJs('type', 'messages');
    $tabs = array(array('class' => 'messages', 'name' => EliteHelper::getLangString('COMMON_MENU_MESSAGES')));
    $total = LiveMessages::getInstance()->checkUnreadMessages($user['id']);
    $messages = LiveMessages::getInstance()->getOverallMessages($user['id'], 0, 5);
    $formHtml .= '<div class="big-title">' . sprintf(EliteHelper::getLangString('ADMIN_MESSAGES_TITLE'), $user['name'], $total) . '</div>';
    $formHtml .= '<div id="messages">';
    $formHtml .= ContentGenerator::getContent('admin_messages', array('messagesSets' => $messages));
    $formHtml .= '</div>';
}
else if ('profits' == $_GET['action'])
{
    EliteHelper::setParamsToJs('type', 'profits');
    $tabs = array(array('class' => 'profits', 'name' => EliteHelper::getLangString('COMMON_MENU_PROFITS')));
    $houseobjects = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 100, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);
    $formHtml = ContentGenerator::getContent('admin_profits', array('houseobjects' => $houseobjects));
}
else if ('suggestion' == $_GET['action'])
{
    EliteHelper::setParamsToJs('type', 'suggestion');
    $tabs = array(array('class' => 'suggestion', 'name' => EliteHelper::getLangString('COMMON_MENU_SUGGESTION')));
    $formHtml = ContentGenerator::getContent('admin_suggestion', array());
}
else if ('logout' == $_GET['action'])
{
    setcookie('u', '');
    setcookie('p', '');
    setcookie('su', '');
    setcookie('sp', '');
    header('Location: /');
    exit;
}
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
        array('url' => '/css/search_common.css'),
        array('url' => '/css/admin_common.css'),
        array('url' => '/css/register.css'),
    )
);
$tailData = array(
    'js' => array(
        array('url' => '/js/admin_common.js'),
        array('url' => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBOI345UTosGvgRwlz2xGXS3yc-HtSwCq4&sensor=false'),
    )
);
EliteHelper::setStringToJs('ADMIN_SAVED_IMFORMATION');
EliteHelper::setStringToJs('REG_FILED_EMPTY');
EliteHelper::setStringToJs('REG_PW_NOT_MATCH');
EliteHelper::setStringToJs('REG_READ_TOS');
EliteHelper::setStringToJs('REG_PW_LENGTH');
EliteHelper::setStringToJs('REG_ID_EMAIL');
EliteHelper::setStringToJs('REG_EMAIL_FORMAT');
EliteHelper::setStringToJs('REG_ADDRESS_WRONG');
EliteHelper::setStringToJs('REG_USER_ID_INVALID');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php EliteHelper::initJsObject();?>
        <?php echo ContentGenerator::getContent('head', $headData);?>
    </head>
    <body class="<?php echo $role;?>">
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('COMMON_B_TITLE')));?>
        <?php echo ContentGenerator::getContent('common_menu', array('user' => $user));?>
        <div class="main-container">
            <div class="col-combined col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_adminmenu', array('user' => $user, 'action' => $_GET['action']));?>
                </div>
            </div>
            <div class="col-left-left-big col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_admintab', array('tabs' => $tabs));?>
                </div>
                <div class="row form-area">
                    <?php echo $formHtml;?>
                </div>
            </div>
            <div class="col-right-left-big col">
                <div class="row" id="recommend-area">
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
