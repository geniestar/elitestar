<?php
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_TITLE'),
    'css' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/common.css'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/register.css')
    )
);

$tailData = array(
    'js' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/js/register.js')
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
        <?php echo ContentGenerator::getContent('register_header', array());?>
        <div id="register-form">
            <div id="user-form">
                <?php echo ContentGenerator::getContent('register_user', array());?>
            </div>
            <div id="role-form">
                <?php echo ContentGenerator::getContent('register_backpacker', array());?>
                <?php echo ContentGenerator::getContent('register_landlord', array());?>
            </div>
        </div>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
        <?php EliteHelper::passStringsToJs();?>
    </body>
</html>

