<?php
if (count($data['results']) > 0)
{
    $data['show'] = true;
    foreach($data['results'] as $key => $result)
    {
        $mail = json_decode($result['user']['mail'], true);
        $phone = json_decode($result['user']['phone'], true);

        if (is_array($mail) && $mail['publish']==1)
        {
            $data['results'][$key]['user']['mail'] = $mail['value'];
        }
        else
        {
            $data['results'][$key]['user']['mail'] = '';
        }
        if (is_array($phone) && $phone['publish']==1)
        {
            $data['results'][$key]['user']['phone'] = $phone['value'];
        }
        else
        {   
            $data['results'][$key]['user']['phone'] = '';
        }
    }
}

if ($data['role'] == 1)
{
    $data['showHouse'] = true;
    foreach($data['results'] as $key => $result)
    {
        $weChargeArray = array();
        $weChargeRaw = json_decode($result['wecharge'], true);
        if ($weChargeRaw)
        {
            if (isset($weChargeRaw['w']) && $weChargeRaw['w'])
            {
                $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_W') . $weChargeRaw['w'] . EliteHelper::getLangString('SEARCH_RESULT_AUD');
            }
            else
            {
                $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_W') . EliteHelper::getLangString('SEARCH_RESULT_CHARGE_INCLUDED');
            }
            if (isset($weChargeRaw['e']) && $weChargeRaw['e'])
            {
                $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_E') . $weChargeRaw['e'] . EliteHelper::getLangString('SEARCH_RESULT_AUD');
            }
            else
            {
                $weChargeArray[] = EliteHelper::getLangString('SEARCH_RESULT_CHARGE_E') . EliteHelper::getLangString('SEARCH_RESULT_CHARGE_INCLUDED');
            }
        }
        $data['results'][$key]['weChargeText'] = implode($weChargeArray, ' / ');
        $duration = date('d/M/Y', strtotime($result['duration_start'])) . ' - ' . date('d/M/Y', strtotime($result['duration_end']));
        $data['results'][$key]['durationText'] = $duration;
    }
}
else
{
    if ($data['type'] === 'service')
    {
        $data['showService'] = true;
    }
    else
    {
        $data['showSettings'] = true;
    }
}
?>
