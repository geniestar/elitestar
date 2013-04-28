<?php
foreach($data['messages'] as $key => $message)
{
    $data['messages'][$key]['created_time'] = date('Y/M/d h:m:s', $message['created_time']);
    foreach($message['replies'] as $key2 => $reply)
    {
        $data['messages'][$key]['replies'][$key2]['created_time'] = date('Y/M/d h:m:s', $reply['created_time']);
    }
}
?>
