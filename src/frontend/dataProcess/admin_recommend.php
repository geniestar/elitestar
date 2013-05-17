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
?>
