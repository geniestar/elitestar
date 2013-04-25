<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$user = EliteUsers::getInstance()->getCurrentUser();
if ('basic' == $_GET['type'])
{

}
else if ('settings' == $_GET['type'])
{

}
else if ('messages' == $_GET['type'])
{

}
else if ('profit' == $_GET['type'])
{

}
else if ('suggestion' == $_GET['type'])
{

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
    <body>
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('COMMON_B_TITLE')));?>
        <?php echo ContentGenerator::getContent('common_menu', array('user' => $user));?>
        <div class="main-container">
            <div class="col-left-left-big col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_adminmenu', array());?>
                </div>
            </div>
            <div class="col-right-left-big col">
                <div class="row">
                </div>
                <div class="row">
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
