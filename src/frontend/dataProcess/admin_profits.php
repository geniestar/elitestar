<?php
foreach ($data['houseobjects'] as $key => $houseobject)
{
    $data['houseobjects'][$key]['duration_start'] = date("Y/M/d", strtotime($houseobject['duration_start']));
    $data['houseobjects'][$key]['duration_end'] = date("Y/M/d", strtotime($houseobject['duration_end']));
    $data['houseobjects'][$key]['duration_start_time'] = strtotime($houseobject['duration_end']);
    $data['houseobjects'][$key]['tenants'] = $houseobject['beds_single'] + $houseobject['beds_double']*2;
}
?>
