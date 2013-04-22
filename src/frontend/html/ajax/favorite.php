<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
$user = EliteUsers::getInstance()->getCurrentUser();
if ($user)
{
    if (0 == $_POST['role'] && 1 == $user['role'])
    {
        if (isset($_POST['id']))
        {
            $backpacker = BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id']);
            $favorites = json_decode(json_decode($backpacker[0]['favorites']), true);
            if ('add' === $_POST['action'])
            {
                $favorites[$_POST['id']] = true;
                if (count($favorites) >= 5)
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
            BackPackers::getInstance()->updateBackPackerInfo($user['id'], null, null, null, null, null, null, null, null, null, json_encode($favorites));
        }
    }
    else if (1 == $_POST['role'] && 0 == $user['role'])
    {
        //$houseowner = LandLords::getInstance()->queryLandLord($_POST['id']);
    }
    else {
        EliteHelper::ajaxReturnFailure('FAVORITE_ROLE_WRONG');
        exit;
    }
}
?>
