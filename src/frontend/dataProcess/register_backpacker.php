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

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

$data['states'] = array();
foreach($states as $key => $state)
{
    $data['states'][] = array(
        'class' => $state['class'],
        'id' => $key,
    );
}
EliteHelper::setParamsToJs('states', $states);

?>
