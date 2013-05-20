<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

if ($_POST['state'])
{
    EliteHelper::ajaxReturnSuccess(array('state' => $states[$_POST['state']]));
}
else
{
    EliteHelper::ajaxReturnSuccess(array('states' => $states));
}

?>
