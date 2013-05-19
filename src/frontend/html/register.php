<?php
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
EliteHelper::updateBrowsingCounter();
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_TITLE'),
    'css' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/common.css'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/register.css')
    )
);

$tailData = array(
    'js' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/js/register.js'),
        array('url' => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBOI345UTosGvgRwlz2xGXS3yc-HtSwCq4&sensor=false'),
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
        <?php echo ContentGenerator::getContent('register_header', array());?>
        <?php echo ContentGenerator::getContent('common_login_panel', array());?>
        <div id="register-form" class="main-container">
            <form action="account_action.php" method="POST" enctype="multipart/form-data">
                <div id="user-form">
                    <?php echo ContentGenerator::getContent('register_user', array());?>
                </div>
                <?php echo ContentGenerator::getContent('register_role_switcher', array());?>
                <div id="role-form">
                    <div id="backpacker-form-all" class="hidden">
                        <div id="backpacker-form" class="form">
                            <input type="hidden" name="role" value="1">
                            <?php echo ContentGenerator::getContent('register_backpacker', array());?>
                            <?php echo ContentGenerator::getContent('register_contact', array());?>
                        </div>
                        <?php echo ContentGenerator::getContent('register_publish_btn', array('role' => 'b'));?>
                    </div>
                    <div id="houseowner-form-all" class="hidden"> 
                        <div id="houseowner-form" class="form">
                            <input type="hidden" name="role" value="0">
                            <?php echo ContentGenerator::getContent('register_houseobject', array());?>
                            <?php echo ContentGenerator::getContent('register_contact', array());?>
                            <?php echo ContentGenerator::getContent('register_houseowner', array());?>
                        </div>
                        <?php echo ContentGenerator::getContent('register_publish_btn', array('role' => 'h'));?>
                    </div>
                </div>
            </form>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>

