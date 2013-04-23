<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
$user = EliteUsers::getInstance()->getCurrentUser();
//var_dump($user);

$state = null;
$city = null;
$start = 0;
$count = 20;
$sort = HouseObjects::SORT_BY_TIME_DESC;
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

if (isset($_GET['start']) && '' !== $_GET['start'])
{
    $start = intval($_GET['start']);
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
    $keyword = $_GET['keyword']; 
}

if (isset($_GET['rent']) && '' !== $_GET['rent'])
{
    $rentLow = EliteHelper::getLowRent($_GET['rent']); 
    $rentHigh = EliteHelper::getHighRent($_GET['rent']); 

}
$user = EliteUsers::getInstance()->getCurrentUser();

$results = HouseObjects::getInstance()->findHouseObjects($state, $city, $start, $count, $sort, $keyword, $keyword, $durationStart, $durationEnd, $rentLow, $rentHigh, $bedsSingle, $bedsDouble);

$backpackers = array();
$ids = array();
/* get user data*/
foreach ($results as $result)
{
    //$ids[] = "'" . $result['user_id'] . "'";
    $userInfo = EliteUsers::getInstance()->queryUser($result['owner_id'], null, null, true);
    $ownerInfo = LandLords::getInstance()->queryLandLord($result['owner_id']);
    $result['user'] = $userInfo;
    $result['owner'] = $ownerInfo;
    $houseObjects[] = $result;
}

$favorites = array();
//var_dump($houseObjects);
if ($user)
{
    $backpacker = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);
    $favorites = json_decode(json_decode($backpacker[0]['favorites']), true);
    
    if (count($favorites) > 0)
    {
        $favoritesInfo = array();
        foreach ($favorites as $key => $favorite)
        {
            $infos = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, $key);
            $favoritesInfo[] = array(
                'id' => $key,
                'name' => $infos[0]['house_name'],
                'role' => 0
            );
        }
    }
}
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
    )
);

foreach ($states as $state)
{
    $headData['css'][] = array('url' => '/css/theme_' . $state['class'] . '.css');
}

$headData['css'][] = '/css/common.css';

$tailData = array(
    'js' => array(
        array('url' => '/js/search_common.js'),
        array('url' => '/js/find_house.js'),
        array('url' => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCPlYEN0gsTKKNZSV2XWQqJVrqer0ho_fk&sensor=false'),
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
                    <?php echo ContentGenerator::getContent('common_searchmenu', array('action' => './find_house.php'));?>
                </div>
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_favorite', array('favoritesInfo' => $favoritesInfo));?>
                </div>
            </div>
            <div class="col-right col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('common_sortbar', array('sort' => $sort, 'url' => $_SERVER['REQUEST_URI']));?>
                </div>
                <div class="row">
                    <?php foreach ($houseObjects as $houseObject):?>              
                        <?php echo ContentGenerator::getContent('searchresult_houseobject', array('houseObject' => $houseObject, 'user' => $user));?>
                    <?php endforeach?>
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
