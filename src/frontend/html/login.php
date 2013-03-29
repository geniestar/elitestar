<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
$user = EliteUsers::getInstance()->login($_POST['id'], $_POST['password'], $_POST['isremember']);

if ($user)
{
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
    header('Location: error.php');         
}

?>
