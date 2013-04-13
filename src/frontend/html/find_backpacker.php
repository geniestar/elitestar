<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
$user = EliteUsers::getInstance()->getCurrentUser();
var_dump($user);
var_dump(BackPackers::getInstance()->findBackPackers($state = 1, $city = 2, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, 170, 200));
var_dump(BackPackers::getInstance()->findBackPackers());
?>
