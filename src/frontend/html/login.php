<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
$user = EliteUsers::getInstance()->login($_POST['id'], $_POST['password'], $_POST['isremember']);

if ($user)
{
    // if only user id, must be something wrong, account wrong or disable by admin
    $sql = 'SELECT * FROM landlords WHERE user_id=\'' . $user['id'] . '\'';
    $landlord = MySqlDb::getInstance()->query($sql, $inputParams);
    $sql = 'SELECT * FROM backpackers WHERE user_id=\'' . $user['id'] . '\'';
    $backpacker = MySqlDb::getInstance()->query($sql, $inputParams);
    if (!$landlord && !$backpacker && $user['id']!=='superuser')
    {
        header('Location: error.php?error=ACCOUNT_ERROR_OR_ALREADY_BEEN_DISABLED'); 
        exit;
    }

    if ($user['role'] === EliteUsers::ROLE_LANDLORD)
    {
        header('Location: find_backpacker.php');
    }
    else
    {
        header('Location: find_house.php');
    }
}
else 
{
    header('Location: error.php?error=USER_ID_OR_PASSWORD_ERROR'); 
}

?>
