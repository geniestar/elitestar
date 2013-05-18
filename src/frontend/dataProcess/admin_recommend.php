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
        foreach($data['results'] as $key => $result)
        {
            $checkArray = array('ha', 'hb', 'hc', 'hd');
            $additionalHelpArray = array();
            $additionalHelps = json_decode(json_decode($result['additional_help']), true);
            $additionalHelps['haa'] = isset($additionalHelps['haa'])? date('d/M/Y', strtotime($result['arrival_time'])). ' ' .$additionalHelps['haa']:'';
            foreach ($checkArray as $checkIndex)
            {
                if ($additionalHelps[$checkIndex])
                {
                    $additionalHelpArray[] = array(
                        'class' => $checkIndex,
                        'additional' => isset($additionalHelps[$checkIndex . 'a'])?$additionalHelps[$checkIndex . 'a']:''
                    );
                }
            }
            $data['results'][$key]['additionalHelpArray'] = $additionalHelpArray;
        }
    }
    else
    {
        $data['showSettings'] = true;
        foreach($data['results'] as $key => $result)
        {
            $data['results'][$key]['arrivalTime'] = date('d/M/Y', strtotime($result['arrival_time']));
            $data['results'][$key]['durationStart'] = date('d/M/Y', strtotime($result['duration_start']));
            $data['results'][$key]['durationEnd'] = date('d/M/Y', strtotime($result['duration_end']));
            $duration = strtotime($data['backpacker']['duration_end']) - strtotime($data['backpacker']['duration_start']);
            $durationText = '';
            $month = floor($duration/(60*60*24*30));

            $year = 0;

            if ($month > 12)
            {
                $year = floor($month/12);
                $month = $month - $year*12;
            }

            if ($year)
            {
                $durationText .= sprintf(EliteHelper::getLangString('SEARCH_RESULT_DURATION_YEAR'), $year);
            }

            if ($month)
            {
                $durationText .= sprintf(EliteHelper::getLangString('SEARCH_RESULT_DURATION_MONTH'), $month);
            }
            if (!$durationText)
            {
                $durationText = EliteHelper::getLangString('SEARCH_RESULT_DURATION_LESS_THAN_MONTH');
            }
            $data['results'][$key]['durationText'] = $durationText;
            $bedText = EliteHelper::getLangString('COMMON_SEARCH_BEDS_SINGLE') . $result['beds_single'] . ' ' . EliteHelper::getLangString('COMMON_SEARCH_BEDS_DOUBLE') . $result['beds_double'];
            $data['results'][$key]['bedText'] = $bedText;
        }
    }
}
?>
