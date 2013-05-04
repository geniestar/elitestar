<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');

$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
    )
);

$headData['css'][] = '/css/common.css';

?>
<!DOCTYPE html>
<html>
    <head>
        <?php EliteHelper::initJsObject();?>
        <?php echo ContentGenerator::getContent('head', $headData);?>
    </head>
    <body>
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('COMMON_B_TITLE')));?>
        <div class="main-container">
            <div class="error-area">
                <?php echo EliteHelper::getLangString('FORGOT_PASSWORD');?>
                </br>
                <form action="account_action.php" method="POST">
                    <input type="hidden" name="action" value="forget_pw">
                    <input type="text" name="id">
                    <input type="submit" value="<?php echo EliteHelper::getLangString('FORGOT_PASSWORD_SUBMIT');?>"></input>
                </form>
            </div>
        </div>
    </body>
</html>
