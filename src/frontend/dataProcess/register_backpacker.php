<?php

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

// just defult value;
foreach ($states[1]['suburbs'] as $key => $value)
{
    $data['cities'][] = array(
        'id' => $key,
        'name' => $value
    );
}

$data['todayText'] = date('Y/M/d');
$data['nextYearText'] = date('Y/M/d', time() + 60*60*24*365);

$data['houseDimemsions'] = ConfigReader::getInstance()->readConfig('dimensions', 'house_dimemsions');


?>
