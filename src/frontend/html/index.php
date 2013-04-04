<?php
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
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
