<?php
if ($data['backpacker']['user'][0]['photo'])
{
    $data['photoUrl'] = './ugc/' . $data['backpacker']['user'][0]['photo'];
}
else
{
    $data['photoUrl'] = './img/default_pic.jpg';
}

$states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
$data['place'] = $states[$data['backpacker']['state']]['name'] . ' , ' . $states[$data['backpacker']['state']]['suburbs'][$data['backpacker']['city']];
?>
