<?php
$houseobject = isset($data['houseobject'])?$data['houseobject']:null;
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

$data['states'] = array();
foreach($states as $key => $state)
{
    $data['states'][] = array(
        'id' => $state['id'],
        'name' => $state['name'],
        'selected' => (isset($houseobject['state']) && $key==$houseobject['state'])?true:false,
    );
}

// just defult value;
foreach ($states[0]['suburbs'] as $key => $value)
{
    $data['cities'][] = array(
        'id' => $key,
        'name' => $value,
        'selected' => (isset($houseobject['city']) && $key==$houseobject['city'])?true:false,
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
foreach($data['houseDimemsions']['description_position'] as $position)
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

if ($houseobject)
{
    $data['houseobject']['arrival_time'] = date("Y/M/d", strtotime($houseobject['arrival_time']));
    $data['houseobject']['duration_start'] = date("Y/M/d", strtotime($houseobject['duration_start']));
    $data['houseobject']['duration_end'] = date("Y/M/d", strtotime($houseobject['duration_end']));
    $data['houseDimemsions']['beds']['single'][$houseobject['beds_single']]['selected'] = true;
    $data['houseDimemsions']['beds']['double'][$houseobject['beds_double']]['selected'] = true;
    $data['houseDimemsions']['rooms'][$houseobject['rooms']]['selected'] = true;
    $data['houseDimemsions']['toilets'][$houseobject['toilets']]['selected'] = true;
    $facilities = json_decode(json_decode($houseobject['facilities']), true);
    $data['houseobject']['wecharge'] = json_decode($houseobject['wecharge'], true);
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
    $data['houseobject']['photos'] = json_decode(json_decode($houseobject['photos']), true);
    $tmpDescription = json_decode($houseobject['description'], true);
    $description = array();
    foreach ($tmpDescription as $description)
    {
        $finalDescription = array (
            'sub-positions' => $data['positions'],
            'sub-vehicle' => $data['vehicle'],
            'km' => $description['km'],
            'mins' => $description['mins'],
            'more' => $description['more'],
        );
        foreach($data['positions'] as $key => $position)
        {
            if ($description['position'] == $position['value'])
            {
                $finalDescription['sub-positions'][$key]['selected'] = true;
            }
        }
        foreach($data['vehicle'] as $key => $vehicle)
        {
            if ($description['vehicle'] == $vehicle['value'])
            {
                $finalDescription['sub-vehicle'][$key]['selected'] = true;
            }
        }
        $data['description'][] = $finalDescription;
    }
    if (!$data['description'])
    {
        $data['description'] = array(
            array(
                'sub-positions' => $data['positions'],
                'sub-vehicle' => $data['vehicle'],
            )
        );
    }
  /*  $additionalHelp = json_decode(json_decode($houseobject['additional_help']), true);
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
    }*/
}
else
{
        $data['description'] = array(
            array(
                'sub-positions' => $data['positions'],
                'sub-vehicle' => $data['vehicle'],
            )
        );
}
EliteHelper::setParamsToJs('positionDesc', $positionDesc);
?>
