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
EliteHelper::setStringToJs('REG_EMAIL_FORMAT');
EliteHelper::setStringToJs('REG_ADDRESS_WRONG');
EliteHelper::setStringToJs('REG_USER_ID_INVALID');
EliteHelper::setStringToJs('REG_INVALID_DATE');

?>
