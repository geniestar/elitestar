<?php

$mail = json_decode($data['user']['mail'], true);
$phone = json_decode($data['user']['phone'], true);

if (is_array($mail))
{
    $data['user']['mail'] = $mail['value'];
    $data['user']['mail_p'] = ($mail['publish']==1)?true:false;
}
if (is_array($phone))
{
    $data['user']['phone'] = $phone['value'];
    $data['user']['phone_p'] = ($phone['publish']==1)?true:false;
}

?>
