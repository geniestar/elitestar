<?php
$data['updatedText'] = EliteHelper::getLangString('SEARCH_RESULT_UPDATED_TIME') . '-' . date('d/M/Y', $data['houseObject']['updated_time']);
$data['todayText'] = date('d/M/Y', time());

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$data['class'] = $states[$data['houseObject']['state']]['class'];
$data['place'] = $states[$data['houseObject']['state']]['name'] . ', ' . $states[$data['houseObject']['state']]['suburbs'][$data['houseObject']['city']] . ', ' . $data['houseObject']['address'];
$duration = date('d/M/Y', strtotime($data['houseObject']['duration_start'])) . ' - ' . date('d/M/Y', strtotime($data['houseObject']['duration_end']));
$data['durationText'] = $duration;

$additionalHelpArray = array();
$checkArray = array('ha', 'hb', 'hc', 'hd');
$additionalHelps = json_decode(json_decode($data['houseObject']['owner'][0]['additional_help']), true);
foreach ($checkArray as $checkIndex)
{
    if ($additionalHelps[$checkIndex])
    {
        $additionalHelpArray[] = array(
            'class' => $checkIndex,
            'additional' => isset($additionalHelps[$checkIndex . 'a'])?str_replace(',', '</br>', $additionalHelps[$checkIndex . 'a']):''
        );
    }
}
$data['additionalHelpArray'] = $additionalHelpArray;
//var_dump($additionalHelpArray);
//var_dump($data);
$facilitiesArray = array();
$checkArray = array('fa', 'fb', 'fc', 'fd', 'fe', 'ff', 'fg');
$facilities = json_decode(json_decode($data['houseObject']['facilities']), true);

foreach ($checkArray as $checkIndex)
{
    if ($facilities[$checkIndex])
    {
        $facilitiesArray[] = array(
            'class' => $checkIndex,
            'additional' => isset($facilities[$checkIndex . 'a'])?$facilities[$checkIndex . 'a']:''
        );
    }
}

$data['facilitiesArray'] = $facilitiesArray;

$weChargeArray = array();
$weChargeRaw = json_decode($data['houseObject']['wecharge'], true);
if ($weChargeRaw)
{
    if (isset($weChargeRaw['w']) && $weChargeRaw['w'])
    {
        $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_W') . $weChargeRaw['w'] . EliteHelper::getLangString('SEARCH_RESULT_AUD');
    }
    else
    {
        $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_W') . EliteHelper::getLangString('SEARCH_RESULT_CHARGE_INCLUDED');
    }
    if (isset($weChargeRaw['e']) && $weChargeRaw['e'])
    {
        $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_E') . $weChargeRaw['e'] . EliteHelper::getLangString('SEARCH_RESULT_AUD');
    }
    else
    {
        $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_E') . EliteHelper::getLangString('SEARCH_RESULT_CHARGE_INCLUDED');
    }
}
$data['weChargeText'] = implode($weChargeArray, ' / ');
$data['photos'] = json_decode(json_decode($data['houseObject']['photos']), true);
if (count($data['photos']) > 3 )
{
    $data['isScroll'] = true;
}

$descriptions = json_decode($data['houseObject']['description'], true);
if ($descriptions)
{
    $data['descriptions'] = array();
    foreach ($descriptions as $description)
    {
        $descriptionText = EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $description['position']) . '(' . $description['km'] . EliteHelper::getLangString('REG_MORE_DESCRIPTION_KM') . $description['mins'] . EliteHelper::getLangString('REG_MORE_DESCRIPTION_MINS') . ' ' . EliteHelper::getLangString('REG_MORE_DESCRIPTION_BY') . ' ' . EliteHelper::getLangString('REG_MORE_DESCRIPTION_' . $description['vehicle']) . ')';
        if (isset($description['more']))
        {
            $descriptionText .= '-' . $description['more'];
        }
        $descriptionText = '● ' . $descriptionText;
        $data['descriptions'][] = $descriptionText;
    }
}
if ($data['user'] && 1 === $data['user']['role'])
{
    $data['showActionBtns'] = true;
}
if ($data['user'] && isset($data['user']['id']))
{
    $data['showContact'] = true;
}

$mail = json_decode($data['houseObject']['user']['mail'], true);
$phone = json_decode($data['houseObject']['user']['phone'], true);

if (is_array($mail) && $mail['publish']==1)
{
    $data['houseObject']['user']['mail'] = $mail['value'];
}
else
{
    $data['houseObject']['user']['mail'] = '';
}
if (is_array($phone) && $phone['publish']==1)
{
    $data['houseObject']['user']['phone'] = $phone['value'];
}
else
{   
    $data['houseObject']['user']['phone'] = '';
}

EliteHelper::setStringToJs('SEARCH_RESULT_MAP_FAILURE');
?>
