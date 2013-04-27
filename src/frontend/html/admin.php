<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$user = EliteUsers::getInstance()->getCurrentUser();
$formHtml = '';
if (0 == $user['role'])
{
    $role = 'houseowner';
}
else
{
    $role = 'backpacker';
}
if ('basic' == $_GET['action'])
{
    $tabs = array(array('class' => 'basic-info', 'name' => EliteHelper::getLangString('COMMON_MENU_BASIC_INFO')));
    $formHtml .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
    $formHtml .= '<input type="hidden" name="edit" value="1">';
    $formHtml .= '<input type="hidden" name="basic-info" value="1">';
    $formHtml .= ContentGenerator::getContent('register_user', array('user' => $user));
    $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_contact', array('user' => $user)) . '</div>';
    $formHtml .= ContentGenerator::getContent('register_publish_btn', array('updateBtn' => true));
    $formHtml .= '</form>';
}
else if ('settings' == $_GET['action'])
{
    if (0 == $user['role'])
    {
        $tabs = array(
            array('class' => 'settings', 'name' => EliteHelper::getLangString('COMMON_MENU_SETTINGS')),
            array('class' => 'service', 'name' => EliteHelper::getLangString('COMMON_MENU_SERVICE'), 'unselected' => true),
        );
        $houseowner = LandLords::getInstance()->queryLandLord($user['id']);
        $houseowner = $houseowner[0];
        $houseobjects = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);

        $formHtml .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
        $formHtml .= '<input type="hidden" name="edit" value="1">';
        $formHtml .= '<input type="hidden" name="role" value="0">';
        $formHtml .= '<div id="form-houseobject" class="form">';
        $formHtml .= '<input type="hidden" name="objectid" value="' . $houseobjects[0]['id'] . '">';
        $formHtml .= '<div id="houseobject-selector">' . ContentGenerator::getContent('register_houseobject_selector', array('houseobjects' => $houseobjects)) . '<div class="clean"></div></div>';
        $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_houseobject', array('houseobject' => $houseobjects[0])) . '</div>';
        $formHtml .= '<div id="houseobject"></div></div>';
        $formHtml .= '<div id="form-service" class="form">' . ContentGenerator::getContent('register_houseowner', array('houseowner' => $houseowner)) . '</div>';
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
        $formHtml .= '<div class="form">' . ContentGenerator::getContent('register_backpacker', array('backpacker' => $backpacker)) . '</div>';
        $formHtml .= ContentGenerator::getContent('register_publish_btn', array('updateBtn' => true));
        $formHtml .= '</form>';
    }
}
else if ('messages' == $_GET['action'])
{
    $tabs = array(array('class' => 'messages', 'name' => EliteHelper::getLangString('COMMON_MENU_MESSAGES')));
}
else if ('profit' == $_GET['action'])
{
    $tabs = array(array('class' => 'profits', 'name' => EliteHelper::getLangString('COMMON_MENU_PROFITS')));
}
else if ('suggestion' == $_GET['action'])
{
    $tabs = array(array('class' => 'suggestion', 'name' => EliteHelper::getLangString('COMMON_MENU_SUGGESTION')));
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
    )
);
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
                    <?php echo ContentGenerator::getContent('common_adminmenu', array());?>
                </div>
            </div>
            <div class="col-left-left-big col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_admintab', array('tabs' => $tabs));?>
                </div>
                <div class="row">
                    <?php echo $formHtml;?>
                </div>
            </div>
            <div class="col-right-left-big col">
                <div class="row">
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
