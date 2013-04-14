<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$user = EliteUsers::getInstance()->getCurrentUser();
//var_dump($user);

$state = null;
$city = null;
$start = 0;
$count = 20;
$sort = BackPackers::SORT_BY_TIME_DESC;
$bedsSingle = null;
$bedsDouble = null;
$arrivalTime = null;
$durationStart = null;
$durationEnd = null;
$rentLow = null;
$rentHigh = null;
$name = null;

if (isset($_GET['state']))
{
    $state = intval($_GET['state']);
}

if (isset($_GET['city']))
{
    $city = intval($_GET['city']);
}

if (isset($_GET['start']))
{
    $start = intval($_GET['start']);
}

if (isset($_GET['sort']))
{
    switch ($_GET['sort'])
    {
        case 't':
            $sort = BackPackers::SORT_BY_TIME;
            break;
        case 'td':
            $sort = BackPackers::SORT_BY_TIME_DESC;
            break;
        case 'p':
            $sort = BackPackers::SORT_BY_PRICE;
            break;
        case 'pd':
            $sort = BackPackers::SORT_BY_PRICE_DESC;
            break;
        case 'd':
            $sort = BackPackers::SORT_BY_DURATION;
            break;
        case 'dd':
            $sort = BackPackers::SORT_BY_DURATION_DESC;
            break;
    }
}

if (isset($_GET['bs']))
{
    $bedsSingle = intval($_GET['bs']);
}

if (isset($_GET['bd']))
{
    $bedsDouble = intval($_GET['bd']);
}

if (isset($_GET['arrival']))
{
    $arrivalTime = EliteHelper::getTime($_GET['arrival']);
}

if (isset($_GET['ds']))
{
    $durationStart = EliteHelper::getTime($_GET['ds']);
}

if (isset($_GET['de']))
{
    $durationEnd = EliteHelper::getTime($_GET['de']);
}

if (isset($_GET['keyword']))
{
    $name = $_GET['keyword']; 
}

if (isset($_GET['rent']))
{
    $rentLow = EliteHelper::getLowRent($_GET['rent']); 
    $rentHigh = EliteHelper::getHighRent($_GET['rent']); 

}


/* query */
$results = BackPackers::getInstance()->findBackPackers($state, $city, $start, $count, $sort, $arrivalTime, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, $name);

$backpackers = array();
$ids = array();
/* get user data*/
foreach ($results as $result)
{
    //$ids[] = "'" . $result['user_id'] . "'";
    $userInfo = EliteUsers::getInstance()->queryUser($result['user_id'], null, null, true);
    $result['user'] = $userInfo;
    $backpackers[] = $result;
}

$users = EliteUsers::getInstance()->queryUser($ids, null, null, true);

/*
foreach ($results as $result)
{
    $userInfo = $users[$result['user_id']];
    $result['user'] = $userInfo;
    $backpackers[] = $result;
}*/

//var_dump($backpackers);

$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/common.css'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/find_backpacker.css'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/css/theme' . $state . '.css')
    )
);
$tailData = array(
    'js' => array(
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/js/common.js'),
        array('url' => 'http://' . $_SERVER['SERVER_NAME'] . '/js/find_backpacker.js')
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
            <div class="col-left col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_searchmenu', array());?>
                </div>
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_favorite', array());?>
                </div>
            </div>
            <div class="col-right col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_sortbar', array());?>
                </div>
                <div class="row">
                    <?php echo ContentGenerator::getContent('searchresult_backpacker', array());?>
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
