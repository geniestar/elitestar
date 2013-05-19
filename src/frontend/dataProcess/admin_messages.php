<?php
foreach($data['messagesSets'] as $talker => $messages)
{
    foreach($messages['messages'] as $key2 => $message)
    {
        $data['messagesSets'][$talker]['messages'][$key2]['created_time'] = date('Y/M/d h:i:s', $message['created_time']);
        $data['messagesSets'][$talker]['messages'][$key2]['message'] = str_replace("\n", '<br>', $message['message']);
    }
    if ($messages['count'] > 5)
    {
        $data['messagesSets'][$talker]['previous'] = true;
    }
}
?>
