<?php
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
EliteHelper::updateBrowsingCounter();
$user = EliteUsers::getInstance()->getCurrentUser();
/*if ($user)
{
    if ($user['role'] === EliteUsers::ROLE_LANDLORD)
    {
        header('Location: find_backpacker.php');
        exit;
    }
    else
    {
        header('Location: find_house.php');
        exit;
    }
}*/
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_TITLE'),
    'css' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/common.css'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/login.css')
    )
);

$tailData = array(
    'js' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/js/index.js')
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
        <?php echo ContentGenerator::getContent('login', array());?>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
