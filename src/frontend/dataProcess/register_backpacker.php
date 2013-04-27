<?php

$backpacker = isset($data['backpacker'])?$data['backpacker']:null;
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

// just defult value;
foreach ($states[0]['suburbs'] as $key => $value)
{
    $data['cities'][] = array(
        'id' => $key,
        'name' => $value
    );
}

$data['todayText'] = date('Y/M/d');
$data['nextYearText'] = date('Y/M/d', time() + 60*60*24*365);

$data['houseDimemsions'] = ConfigReader::getInstance()->readConfig('dimensions', 'house_dimemsions');

$data['states'] = array();
foreach($states as $key => $state)
{
    $data['states'][] = array(
        'class' => $state['class'],
        'id' => $key,
        'selected' => (isset($backpacker['city']) && $key==$backpacker['city'])?true:false,
    );
}
if (!$backpacker)
{
    $data['states'][0]['default'] = true;
}
else
{
    $data['states'][$backpacker['state']]['default'] = true;
}

EliteHelper::setParamsToJs('states', $states);

if ($backpacker)
{
    $data['backpacker']['arrival_time'] = date("Y/M/d", strtotime($backpacker['arrival_time']));
    $data['backpacker']['duration_start'] = date("Y/M/d", strtotime($backpacker['duration_start']));
    $data['backpacker']['duration_end'] = date("Y/M/d", strtotime($backpacker['duration_end']));
    $data['houseDimemsions']['beds']['single'][$backpacker['beds_single']]['selected'] = true;
    $data['houseDimemsions']['beds']['double'][$backpacker['beds_double']]['selected'] = true;
    $facilities = json_decode(json_decode($backpacker['facilities']), true);
    $data['facilitiesCheck'] = array();
    foreach ($facilities as $facility => $checked)
    {
        if ('1' === $checked)
        {
            $data['facilitiesCheck'][$facility] = true;
        }
        else
        {
            $data['facilitiesCheck'][$facility] = false;
        }
    }
    $additionalHelp = json_decode(json_decode($backpacker['additional_help']), true);
    $data['helpsCheck'] = array();
    foreach ($additionalHelp as $help => $checked)
    {
        if ('1' === $checked)
        {
            $data['helpsCheck'][$help] = true;
        }
        else
        {
            $data['helpsCheck'][$help] = false;
        }
    }
    foreach ($data['houseDimemsions']['times'] as $key => $time)
    {
        if ($additionalHelp['haa'] == $time['value'])
        {
            $data['houseDimemsions']['times'][$key]['selected'] = true;
        }
    }
}

?>
