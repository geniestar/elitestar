<?php
$data['updatedText'] = EliteHelper::getLangString('SEARCH_RESULT_UPDATED_TIME') . '-' . date('d/M/Y', $data['backpacker']['updated_time']);
$data['todayText'] = date('d/M/Y', time());
if ($data['backpacker']['user']['photo'])
{
    $data['photoUrl'] = './ugc/' . $data['backpacker']['user']['photo'];
}
else
{
    $data['photoUrl'] = './img/default_pic.jpg';
}

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$data['class'] = $states[$data['backpacker']['state']]['class'];
$data['place'] = $states[$data['backpacker']['state']]['name'] . ' , ' . $states[$data['backpacker']['state']]['suburbs'][$data['backpacker']['city']];
$data['arrivalTime'] = date('d/M/Y', strtotime($data['backpacker']['arrival_time']));
$countries = ConfigReader::getInstance()->readConfig('dimensions', 'countries');
$data['countryFlag'] = $countries[$data['backpacker']['user']['country']];
$duration = strtotime($data['backpacker']['duration_end']) - strtotime($data['backpacker']['duration_start']);
$durationText = '';
$month = floor($duration/(60*60*24*30));

$year = 0;

if ($month > 12)
{
    $year = floor($month/12);
    $month = $month - $year*12;
}

if ($year)
{
    $durationText .= sprintf(EliteHelper::getLangString('SEARCH_RESULT_DURATION_YEAR'), $year);
}

if ($month)
{
    $durationText .= sprintf(EliteHelper::getLangString('SEARCH_RESULT_DURATION_MONTH'), $month);
}
if (!$durationText)
{
    $durationText = EliteHelper::getLangString('SEARCH_RESULT_DURATION_LESS_THAN_MONTH');
}

$data['durationText'] = $durationText;

$bedText = EliteHelper::getLangString('COMMON_SEARCH_BEDS_SINGLE') . $data['backpacker']['beds_single'] . ' ' . EliteHelper::getLangString('COMMON_SEARCH_BEDS_DOUBLE') . $data['backpacker']['beds_double'];
$data['bedText'] = $bedText;

$additionalHelpArray = array();
$checkArray = array('ha', 'hb', 'hc', 'hd');
$additionalHelps = json_decode(json_decode($data['backpacker']['additional_help']), true);

foreach ($checkArray as $checkIndex)
{
    if ($additionalHelps[$checkIndex])
    {
        $additionalHelpArray[] = array(
            'class' => $checkIndex,
            'additional' => isset($additionalHelps[$checkIndex . 'a'])?$additionalHelps[$checkIndex . 'a']:'',
            'hint' => EliteHelper::getLangString('SEARCH_RESULT_' . strtoupper($checkIndex))
        );
    }
}

$data['additionalHelpArray'] = $additionalHelpArray;

$facilitiesArray = array();
$checkArray = array('fa', 'fb', 'fc', 'fd', 'fe', 'ff', 'fg');
$facilities = json_decode(json_decode($data['backpacker']['facilities']), true);

foreach ($checkArray as $checkIndex)
{
    if ($facilities[$checkIndex])
    {
        $facilitiesArray[] = array(
            'class' => $checkIndex,
            'additional' => isset($facilities[$checkIndex . 'a'])?$facilities[$checkIndex . 'a']:'',
            'hint' => EliteHelper::getLangString('SEARCH_RESULT_' . strtoupper($checkIndex))
        );
    }
}
$data['showContact'] = false;
$data['facilitiesArray'] = $facilitiesArray;
if ($data['user'] && 0 === $data['user']['role'])
{
    $data['showActionBtns'] = true;
}
if ($data['user'] && isset($data['user']['id']))
{
    $data['showContact'] = true;
}
if ($data['user']['isSuper'])
{
    $data['showId'] = true;
}

$mail = json_decode($data['backpacker']['user']['mail'], true);
$phone = json_decode($data['backpacker']['user']['phone'], true);
if (is_array($mail) && $mail['publish']==1)
{
    $data['backpacker']['user']['mail'] = $mail['value'];
}
else
{
    $data['backpacker']['user']['mail'] = '';
}
if (is_array($phone) && $phone['publish']==1)
{
    $data['backpacker']['user']['phone'] = $phone['value'];
}
else
{   
    $data['backpacker']['user']['phone'] = '';
}
?>
