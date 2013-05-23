<?php
    $url = $data['url'];
    $data['updatedTimeUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 'td');
    $data['priceUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 'pd');
    $data['durationUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 'dd');

    if (isset($_GET['sort']) && 'td' === $_GET['sort'] || !isset($_GET['sort']))
    {
        $data['updatedTimeUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 't');
        $data['updatedTimeSelected'] = true;
        $data['updatedTimeD'] = true;   
    }
    else if (isset($_GET['sort']) && 't' === $_GET['sort'])
    {
        $data['updatedTimeSelected'] = true;
    }
    else if (isset($_GET['sort']) && 'pd' === $_GET['sort'])
    {
        $data['priceUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 'p');
        $data['priceSelected'] = true;
        $data['priceD'] = true;
    }
    else if (isset($_GET['sort']) && 'p' === $_GET['sort'])
    {
        $data['priceSelected'] = true;
    }
    else if (isset($_GET['sort']) && 'dd' === $_GET['sort'])
    {
        $data['durationUrl'] = EliteHelper::replaceUrlParam($url, 'sort', 'd');
        $data['durationSelected'] = true;
        $data['durationD'] = true;
    }
    else if (isset($_GET['sort']) && 'p' === $_GET['sort'])
    {
        $data['durationSelected'] = true;
    }

    $data['get'] = $_GET;
?>
