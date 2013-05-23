<?php
$houseowner = isset($data['houseowner'])?$data['houseowner']:null;
if ($houseowner)
{
    $additionalHelp = json_decode(json_decode($houseowner['additional_help']), true);
    $data['helpsCheck'] = array();
    foreach ($additionalHelp as $help => $checked)
    {
        if ('1' === $checked)
        {
            $data['helpsCheck'][$help] = true;
        }
        else
        {
            $data['helpsCheck'][$help] = false;
        }
    }
    if (isset($additionalHelp['haa']))
    {
        $haaDetails = explode(',', $additionalHelp['haa']);
        foreach ($haaDetails as $haaDetail)
        {
            $tmp = explode(':', $haaDetail);
            if (isset($tmp[1]))
            {
                $tmp2 = explode('AUD', $tmp[1]);
                $data['haa_' . $tmp[0]] = $tmp2[0];
            }
            else
            {
                $data['haa_o'] = str_replace('<br>', "\n", $tmp[0]);
            }
        }
    }
}
?>
