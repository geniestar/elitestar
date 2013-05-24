<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
$user = EliteUsers::getInstance()->getCurrentUser();
if ($user)
{
    if (0 == $_POST['role'] && 1 == $user['role'])
    {
        if (isset($_POST['id']))
        {
            $backpacker = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);
            $favorites = json_decode(json_decode($backpacker[0]['favorites']), true);
            if (!$favorites)
            {
                $favorites = array();
            }
            if ('add' === $_POST['action'])
            {
                if (isset($favorites[$_POST['id']]))
                {
                    EliteHelper::ajaxReturnFailure('FAVORITE_LIST_EXIST');
                    exit;
                }
                $favorites[$_POST['id']] = true;
                if (count($favorites) >= 6)
                {
                    EliteHelper::ajaxReturnFailure('FAVORITE_LIST_FULL');
                    exit;
                }
            } 
            else if ('delete' === $_POST['action']) 
            {
                $tmp = array();
                foreach ($favorites as $key => $favorite)
                {
                    if ($key != $_POST['id'])
                    {
                        $tmp[$key] = $favorite;
                    }
                }
                $favorites = $tmp;
            }
            BackPackers::getInstance()->updateBackPackerInfo($user['id'], null, null,  null, null, null, null, null, null, null, null, null, json_encode($favorites));
            if ('add' === $_POST['action']) 
            {
                $infos = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, null, $_POST['id']);
                $data = array(
                    'id' => $_POST['id'],
                    'name' => $infos[0]['house_name'],
                    'role' => 0
                );
                $html = ContentGenerator::getContent('common_favorite_single', $data);
                EliteHelper::ajaxReturnSuccess(array('html' => $html, 'message' => EliteHelper::getLangString('SEARCH_RESULT_ADD_FAVORITE_SUCCESS')));
            }
            else {
                EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_DELETE_FAVORITE_SUCCESS')));
            }
        }
    }
    else if (1 == $_POST['role'] && 0 == $user['role'])
    {
        if (isset($_POST['id']))
        {
            $houseowner = LandLords::getInstance()->queryLandLord($user['id']); 
            $favorites = json_decode(json_decode($houseowner[0]['favorites']), true);
            if (!$favorites)
            {
                $favorites = array();
            }
            if ('add' === $_POST['action'])
            {
                if (isset($favorites[$_POST['id']]))
                {
                    EliteHelper::ajaxReturnFailure('FAVORITE_LIST_EXIST');
                    exit;
                }
                $favorites[$_POST['id']] = true;
                if (count($favorites) >= 6)
                {
                    EliteHelper::ajaxReturnFailure('FAVORITE_LIST_FULL');
                    exit;
                }
            } 
            else if ('delete' === $_POST['action']) 
            {
                $tmp = array();
                foreach ($favorites as $key => $favorite)
                {
                    if ($key != $_POST['id'])
                    {
                        $tmp[$key] = $favorite;
                    }
                }
                $favorites = $tmp;
            }
            LandLords::getInstance()->updateLandLordInfo($user['id'], null, json_encode($favorites));
            if ('add' === $_POST['action']) 
            {
                $infos = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, null, $_POST['id']);
                $data = array(
                    'id' => $_POST['id'],
                    'name' => $infos[0]['name'],
                    'role' => 1
                );
                $html = ContentGenerator::getContent('common_favorite_single', $data);
                EliteHelper::ajaxReturnSuccess(array('html' => $html, 'message' => EliteHelper::getLangString('SEARCH_RESULT_ADD_FAVORITE_SUCCESS')));
            }
            else {
                EliteHelper::ajaxReturnSuccess(array('message' => EliteHelper::getLangString('SEARCH_RESULT_DELETE_FAVORITE_SUCCESS')));
            }
        }
    }
    else {
        EliteHelper::ajaxReturnFailure('FAVORITE_ROLE_WRONG');
        exit;
    }
}
?>
