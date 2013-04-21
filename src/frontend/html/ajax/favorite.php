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
            if (count($favorites) >= 5)
            {
                 EliteHelper::ajaxReturnFailure('FAVORITE_LIST_FULL');
                 exit;
            }
            else
            {
                $favorites[$_POST['id']] = true;
                BackPackers::getInstance()->updateBackPackerInfo($user['id'], null, null, null, null, null, null, null, null, null, json_encode($favorites));

            }
        }
    }
    else if (1 == $_POST['role'] && 0 == $user['role'])
    {
        //$houseowner = LandLords::getInstance()->queryLandLord($_POST['id']);
    }
    error_log(print_r($_POST));
}
?>
