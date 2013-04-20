<?php
$data['updatedText'] = EliteHelper::getLangString('SEARCH_RESULT_UPDATED_TIME') . '-' . date('d/M/Y', $data['houseObject']['updated_time']);

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$data['place'] = $states[$data['houseObject']['state']]['name'] . ' , ' . $states[$data['backpacker']['state']]['suburbs'][$data['houseObject']['city']];
$duration = date('d/M/Y', strtotime($data['houseObject']['duration_start'])) . ' - ' . date('d/M/Y', strtotime($data['houseObject']['duration_end']));
$data['durationText'] = $duration;

//var_dump($data);
?>
