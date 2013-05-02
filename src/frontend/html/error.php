<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');

$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
        array('url' => '/css/search_common.css'),
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
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('ERROR')));?>
        <div class="main-container">
            <div class="error-area">
                <?php echo EliteHelper::getErrorString($_GET['error']);?>
                <div class="clean"></div></br></br>
                <a style="cursor:pointer;" onclick="history.back()"><?php echo EliteHelper::getLangString('ERROR_PREV_PAGE');?></a>
            </div>
        </div>
    </body>
</html>
