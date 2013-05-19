<?php
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$data['houseDimemsions'] = ConfigReader::getInstance()->readConfig('dimensions', 'house_dimemsions');
$positionDesc = array();
foreach($data['houseDimemsions']['description_position'] as $position)
{
    $data['positions'][] = array(
        'value' => $position['value'],
        'name' => EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $position['value'])
    );
    $positionDesc[$position['value']] = EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $position['value'] . '_DESC');
}
EliteHelper::setParamsToJs('positionDesc', $positionDesc);
if (count($data['houseobjects']) > 2)
{
    $data['isScroll'] = true;
}
?>
