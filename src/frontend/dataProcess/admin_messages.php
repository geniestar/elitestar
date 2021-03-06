<?php
foreach($data['messagesSets'] as $talker => $messages)
{
    foreach($messages['messages'] as $key2 => $message)
    {
        $data['messagesSets'][$talker]['messages'][$key2]['created_time'] = date('Y/M/d h:i:s', $message['created_time']);
        $data['messagesSets'][$talker]['messages'][$key2]['message'] = str_replace("\n", '<br>', $message['message']);
        $talkerInfo = EliteUsers::queryUser($data['messagesSets'][$talker]['talker'], null, null, true);
        $data['messagesSets'][$talker]['talkerName'] = $talkerInfo[0]['name'];
        if ($talkerInfo[0]['photo'])
        {
            $photoUrl = './ugc/' . $talkerInfo[0]['photo'];
        }
        else
        {
            $photoUrl = './img/default_pic.jpg'; 
        }
        $data['messagesSets'][$talker]['photoUrl'] = $photoUrl;
    }
    if ($messages['count'] > 5)
    {
        $data['messagesSets'][$talker]['previous'] = true;
    }
}

/*suggestion*/
if ($data['messages'])
{
foreach($data['messages'] as $key => $message)
{
    $data['messages'][$key]['created_time'] = date('Y/M/d h:i:s', $message['created_time']);
    foreach($message['replies'] as $key2 => $reply)
    {
        $data['messages'][$key]['replies'][$key2]['created_time'] = date('Y/M/d h:i:s', $reply['created_time']);
    }
    if ('superuser' === $message['receiver'])
    {
        $data['messages'][$key]['isSuggestion'] = true;
    }
    $mailInfo = json_decode($message['senderInfo']['mail'], true);
    $data['messages'][$key]['senderInfo']['mail'] = $mailInfo['value'];
}
}
?>
