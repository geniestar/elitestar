<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
EliteHelper::updateBrowsingCounter();
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

if (isset($_GET['state']) && '' !== $_GET['state'])
{
    $state = intval($_GET['state']);
}

if (isset($_GET['city']) && '' !== $_GET['city'])
{
    $city = intval($_GET['city']);
}

if (isset($_GET['page']) && '' !== $_GET['page'] && intval($_GET['page']) - 1 > 0)
{
    $page = intval($_GET['page']) - 1;
    $count = 20;
    $start = $page*20;
}

if (isset($_GET['sort']) && '' !== $_GET['sort'])
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

if (isset($_GET['bs']) && '' !== $_GET['bs'])
{
    $bedsSingle = intval($_GET['bs']);
}

if (isset($_GET['bd']) && '' !== $_GET['bd'])
{
    $bedsDouble = intval($_GET['bd']);
}

if (isset($_GET['arrival']) && '' !== $_GET['arrival'])
{
    $arrivalTime = EliteHelper::getTime($_GET['arrival']);
}

if (isset($_GET['ds']) && '' !== $_GET['ds'])
{
    $durationStart = EliteHelper::getTime($_GET['ds']);
}

if (isset($_GET['de']) && '' !== $_GET['de'])
{
    $durationEnd = EliteHelper::getTime($_GET['de']);
}

if (isset($_GET['keyword']) && '' !== $_GET['keyword'])
{
    $name = $_GET['keyword']; 
}

if (isset($_GET['rent']) && '' !== $_GET['rent'])
{
    $rentLow = EliteHelper::getLowRent($_GET['rent']); 
    $rentHigh = EliteHelper::getHighRent($_GET['rent']); 

}
/* query */
$results = BackPackers::getInstance()->findBackPackers($state, $city, $start, $count, $sort, $arrivalTime, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, $name);
$total = BackPackers::getInstance()->findBackPackers($state, $city, $start, $count, $sort, $arrivalTime, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble, $name, null, null, true);
$backpackers = array();
$ids = array();
/* get user data*/
foreach ($results as $result)
{
    //$ids[] = "'" . $result['user_id'] . "'";
    $userInfo = EliteUsers::getInstance()->queryUser($result['user_id'], null, null, true);
    $result['user'] = $userInfo[0];
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
$favorites = array();
$favoritesInfo = array();
if ($user)
{
    $houseowner = LandLords::getInstance()->queryLandLord($user['id']);
    $favorites = json_decode(json_decode($houseowner[0]['favorites']), true);
    if (count($favorites) > 0)
    {
        foreach ($favorites as $key => $favorite)
        {
            $infos = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, $key);
            $favoritesInfo[] = array(
                'id' => $key,
                'name' => $infos[0]['name'],
                'role' => 1
            );
        }
    }
}

/* ad */
$adConfig = ConfigReader::getInstance()->readConfig('ad', 'ad');
$adConfig['sources'] = $adConfig['sources_backpacker'];
shuffle($adConfig['random_positions']);
shuffle($adConfig['sources']);
$adCount = 0;
$currentPosition = 0;

$adPositions = array_slice($adConfig['random_positions'], 0, $adConfig['number_of_ads']);
$adSources = array_slice($adConfig['sources'], 0, $adConfig['number_of_ads']);

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
        array('url' => '/css/search_common.css'),
        array('url' => '/css/register.css'),
    )
);
foreach ($states as $state)
{
    $headData['css'][] = array('url' => '/css/theme_' . $state['class'] . '.css');
}
$tailData = array(
    'js' => array(
        array('url' => '/js/search_common.js'),
        array('url' => '/js/find_backpacker.js'),
    )
);
EliteHelper::setStringToJs('COMMON_CHOOSE_A_SUBURB');
?>

<!DOCTYPE html>
<html>
    <head>
        <?php EliteHelper::initJsObject();?>
        <?php echo ContentGenerator::getContent('head', $headData);?>
    </head>
    <body>
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('COMMON_B_TITLE')));?>
        <?php echo ContentGenerator::getContent('common_menu', array('user' => $user, 'isBackpacker' => true));?>
        <?php echo ContentGenerator::getContent('common_login_panel', array('showSignUp' => true));?>
        <div class="main-container">
            <div class="col-left col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_searchmenu', array('action' => './find_backpacker.php'));?>
                </div>
                <div class="row">
                    <?php if($user && 0 === $user['role']) {echo ContentGenerator::getContent('common_favorite', array('favoritesInfo' => $favoritesInfo));}?>
                </div>
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_counter', array());?>
                </div>
                <?php foreach ($adConfig['sources_left'] as $ad):?>
                <div class="row">
                    <?php echo $ad;?>
                </div>
                <?php endforeach;?>
            </div>
            <div class="col-right col">
                <div class="row" id="sortbar">
                    <?php echo ContentGenerator::getContent('common_sortbar', array('sort' => $sort, 'url' => $_SERVER['REQUEST_URI']));?>
                </div>
                <div class="row" id="result-area">
                    <div id="favorite-container"></div>
                    <?php if($backpackers && count($backpackers) > 0):?>
                        <?php foreach ($backpackers as $backpacker):?>              
                            <?php 
                                if (in_array($currentPosition, $adPositions)) {
                                    echo $adSources[$adCount];
                                    $adCount++;
                                }
                                $currentPosition++;
                            ?>
                            <?php echo ContentGenerator::getContent('searchresult_backpacker', array('backpacker' => $backpacker, 'user' => $user));?>
                        <?php endforeach?>
                    <?php else:?>
                        <center style="margin: 120px 0;"><?php echo EliteHelper::getLangString('SEARCH_RESULT_NO_RESULT');?></center>
                    <?php endif?>
                    <?php echo ContentGenerator::getContent('searchresult_pagenation', array('total' => $total[0]['total'], 'url' => $_SERVER['REQUEST_URI']));?>
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
