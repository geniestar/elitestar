<?php
    if ('messages'==$data['action'])
    {
        $data['s_suggestion'] = true;
    }
    else if ('dreports'==$data['action'])
    {
        $data['s_dreports'] = true;
    }
    else if ('mreports'==$data['action'])
    {
        $data['s_mreports'] = true;
    }
?>
