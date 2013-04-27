<?php
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

$data['states'] = array();
foreach($states as $key => $state)
{
    $data['states'][] = array(
        'id' => $state['id'],
        'name' => $state['name']
    );
}

// just defult value;
foreach ($states[0]['suburbs'] as $key => $value)
{
    $data['cities'][] = array(
        'id' => $key,
        'name' => $value
    );
}
$data['photos'] = array();
for ($i = 0; $i < 6; $i++)
{
    $data['photos'][] = $i+1;
}

$data['todayText'] = date('Y/M/d');
$data['nextYearText'] = date('Y/M/d', time() + 60*60*24*365);
$data['houseDimemsions'] = ConfigReader::getInstance()->readConfig('dimensions', 'house_dimemsions');
$data['positions'] = array(array('value' => '', 'name' => ''));
$positionDesc = array();
foreach($data['houseDimemsions']['description_posiiton'] as $position)
{
    $data['positions'][] = array(
        'value' => $position['value'],
        'name' => EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $position['value'])
    );
    $positionDesc[$position['value']] = EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $position['value'] . '_DESC');
}
$data['vehicle'] = array(array('value' => '', 'name' => ''));
foreach($data['houseDimemsions']['description_vehicle'] as $vehicle)
{
    $data['vehicle'][] = array(
        'value' => $vehicle['value'],
        'name' => EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $vehicle['value'])
    );
}

EliteHelper::setParamsToJs('positionDesc', $positionDesc);
?>
