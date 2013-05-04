<?php

    $finalReports = array();
    $keys = array('');
    foreach($data['reports'] as $colKey => $report)
    {
        $keys = array('');
        foreach($report as $key => $value)
        {
            $keys[] = $key;
        }
    }
    $finalReports[] = $keys;
    foreach($data['reports'] as $colKey => $report)
    {
        $reportNumbers = array();
        $reportNumbers[] = $colKey;
        foreach($report as $key => $value)
        {
            $reportNumbers[] = $value;
        }
        $finalReports[] = $reportNumbers;
    }
    $data['finalReports'] = $finalReports;
?>
