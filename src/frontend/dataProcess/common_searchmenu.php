<?php
$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');

if (isset($_GET['state']) && isset($states[$_GET['state']]))
{
    $state = $_GET['state'];
}

foreach ($states[$state]['suburbs'] as $key => $value)
{
    $data['cities'][] = array(
        'id' => $key,
        'name' => $value,
        'selected' => (isset($_GET['city']) && '' !== $_GET['city'] && $key===intval($_GET['city']))?true:false,
    );
}
$data['states'] = array();
foreach($states as $key => $state)
{
    $data['states'][] = array(
        'class' => $state['class'],
        'id' => $key,
    );
}
if (isset($_GET['state']) && isset($states[$_GET['state']]))
{
    $data['states'][$_GET['state']]['default'] = true;
    $data['selectedState'] = $_GET['state'];
}

$data['houseDimemsions'] = ConfigReader::getInstance()->readConfig('dimensions', 'house_dimemsions');
$acceptableRents = $data['houseDimemsions']['acceptable_rent'];

$data['acceptableRents'] = array();
foreach ($acceptableRents as $key => $value)
{
    $data['acceptableRents'][] = array(
        'id' => $key,
        'name' => $value,
        'selected' => (isset($_GET['rent']) && $value==$_GET['rent'])?true:false,
    );
}
if (isset($_GET['bs']) && isset($data['houseDimemsions']['beds']['single'][$_GET['bs']]))
{
    $data['houseDimemsions']['beds']['single'][$_GET['bs']]['selected'] = true;
}
if (isset($_GET['bd']) && isset($data['houseDimemsions']['beds']['double'][$_GET['bd']]))
{
    $data['houseDimemsions']['beds']['double'][$_GET['bd']]['selected'] = true;
}
$data['todayText'] = date('Y/M/d');
$data['nextYearText'] = date('Y/M/d', time() + 60*60*24*365);
$data['get'] = $_GET;
?>
