<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$user = EliteUsers::getInstance()->getCurrentUser();
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
}
else if ('settings' == $_GET['action'])
{
    if (0 == $user['role'])
    {
        $tabs = array(
            array('class' => 'settings', 'name' => EliteHelper::getLangString('COMMON_MENU_SETTINGS')),
            array('class' => 'service', 'name' => EliteHelper::getLangString('COMMON_MENU_SERVICE')),
        );
    }
    else
    {
        $tabs = array(array('class' => 'settings', 'name' => EliteHelper::getLangString('COMMON_MENU_SETTINGS')));
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
