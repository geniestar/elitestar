<?php

$data['countries'] = array();

$countries = ConfigReader::getInstance()->readConfig('dimensions', 'countries');
foreach ($countries as $key => $value)
{
    $data['countries'][] = array(
        'id' => $key,
        'name' => $value
    );
}
?>
