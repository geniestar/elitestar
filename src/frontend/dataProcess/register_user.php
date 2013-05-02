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


EliteHelper::setStringToJs('REG_FILED_EMPTY');
EliteHelper::setStringToJs('REG_PW_NOT_MATCH');
EliteHelper::setStringToJs('REG_READ_TOS');
EliteHelper::setStringToJs('REG_PW_LENGTH');
EliteHelper::setStringToJs('REG_ID_EMAIL');
?>
