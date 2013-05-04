<?php
    if (0 == $data['user']['role'])
    {
        $data['showProfit'] = true;
    }
    else
    {
        $data['showProfit'] = false;
    }
    if ('basic'==$data['action'])
    {
        $data['s_basic'] = true;
    }
    else if ('settings'==$data['action'])
    {
        $data['s_settings'] = true;
    }
    else if ('messages'==$data['action'])
    {
        $data['s_messages'] = true;
    }
    else if ('suggestion'==$data['action'])
    {
        $data['s_suggestion'] = true;
    }
    else if ('profits'==$data['action'])
    {
        $data['s_profits'] = true;
    }
?>
