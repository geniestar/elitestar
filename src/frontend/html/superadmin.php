<?php
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
include('/usr/share/pear/elitestar/lib/ContentGenerator.php');
include('/usr/share/pear/elitestar/lib/Messages.php');
$user = EliteUsers::getInstance()->getCurrentUser();
if (!$user || !isset($user['isSuper']))
{
    header('Location: /');
    exit;
}

if ('messages' == $_GET['action'])
{
    $total = Messages::getInstance()->querySuggestionsTotal();
    $messages = Messages::getInstance()->querySuggestions();
    $html .= '<div class="big-title">' . sprintf(EliteHelper::getLangString('SUPER_ADMIN_MESSAGES_TITLE'), $total) . '</div>';
    $html .= '<div id="messages">';
    $html .= ContentGenerator::getContent('admin_messages', array('messages' => $messages));
    $html .= '</div>';
}

else if ('dreports' == $_GET['action'])
{
    /* get reports for each month*/
    $startPostfix = ' 00:00:00';
    $endPostfix = ' 23:59:59';
    $startYear = date("y");
    $startMonth = date("m");
    $startDay = 1;
    $year = $startYear;
    $month = $startMonth;
    $day = $startDay;
    $now = time();
    $dayEnd = array(
        '1' => 31,
        '2' => 28,
        '3' => 31,
        '4' => 30,
        '5' => 31,
        '6' => 30,
        '7' => 31,
        '8' => 31,
        '9' => 30,
        '10' => 31,
        '11' => 30,
        '12' => 31,
    );
    $reports = array();
    while (true)
    {
        $key = $year . '-' . $month . '-' . (($day>9)?$day:'0'.$day);
        $report[$key] = array();
        $startTime = strtotime($year . '-' . $month . '-'. $day . $startPostfix);
        $endTime = strtotime($year . '-' . $month . '-' . $day . $endPostfix);
        if ($now < $startTime)
        {
            break;
        }

        /* user*/
        $sql = 'SELECT count(*) as count from users where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['users'] = $r[0]['count'];
        /*backpackers*/
        $sql = 'SELECT count(*) as count from backpackers where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['backpackers'] = $r[0]['count'];
        /*houseowners*/
        $sql = 'SELECT count(*) as count from landlords where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['houseowners'] = $r[0]['count'];
        /*houseobject*/
        $sql = 'SELECT count(*) as count from houseobjects where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['houseobjects'] = $r[0]['count'];
        /*messages*/
        $sql = 'SELECT count(*) as count from messages where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['messages'] = $r[0]['count'];
        $day++;

        if ($dayEnd[$month . ''] === $day)
        {
            $day = 1;
            $month++;
            if($month == 13)
            {
                $month = 1;
                $year++;
            }
        }

    }
    $html = ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
    $html .= '<div class="traffic-link"> Traffics report please go to <a href="http://www.google.com/analytics/">Google Analytics</a></div>';
}
else if ('mreports' == $_GET['action'])
{
    /* get reports for each month*/
    $startPostfix = '-01 00:00:00';
    $endPostfix = '-30 23:59:59';
    $startYear = 2013;
    $startMonth = 7;
    $year = $startYear;
    $month = $startMonth;
    $now = time();
    $reports = array();
    $html = '';
    while (true)
    {
        $key = $year . '-' . (($month>9)?$month:'0'.$month);
        $report[$key] = array();
        $startTime = strtotime($year . '-' . $month . $startPostfix);
        $endTime = strtotime($year . '-' . $month . $endPostfix);
        if ($now < $startTime)
        {
            break;
        }
        /* user*/
        $sql = 'SELECT count(*) as count from users where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['users'] = $r[0]['count'];
        /*backpackers*/
        $sql = 'SELECT count(*) as count from backpackers where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['backpackers'] = $r[0]['count'];
        /*houseowners*/
        $sql = 'SELECT count(*) as count from landlords where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['houseowners'] = $r[0]['count'];
        /*houseobject*/
        $sql = 'SELECT count(*) as count from houseobjects where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['houseobjects'] = $r[0]['count'];
        /*messages*/
        $sql = 'SELECT count(*) as count from messages where created_time > ' . $startTime . ' AND created_time < ' . $endTime;
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports[$key]['messages'] = $r[0]['count'];

        $month++;
        if($month == 13)
        {
            $month = 1;
            $year++;
        }
    }
    $html .= ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
    $html .= '<div class="traffic-link"> Traffics report please go to <a href="http://www.google.com/analytics/">Google Analytics</a></div>';
}
else if ('simulator' == $_GET['action'])
{
    $sql = 'SELECT * from users where role != 99 order by name';
    $r = MySqlDb::getInstance()->query($sql, $inputParams);

    $html = '<center style="margin-top: 20px;"><div class="title">please enter the user id</div>';
    $html .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="action" value="simulator">';
    $html .= '<input style="margin: 10px 0;width:150px;" type="text" name="id" placeholder="userid">';
    $html .= '<input type="submit"></input>';
    $html .= '</from>';
    
    $html .= '<table class="reports"><tr><td>Name</td><td>Action</td></tr>';
    foreach ($r as $user)
    {
        $html .= '<tr><td>';
        $html .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="action" value="simulator">';
        $html .= '<input type="hidden" name="id" value="' . $user['id'] . '">';
        $html .= $user['name'] . '</td>';
        $html .= '<td><input type="submit" value="simulate"></input></td>';
        $html .= '</form></td></tr>';
    }
    $html .= '</table></center>';
}
else if ('delete' == $_GET['action'])
{
    $sql = 'SELECT * from users where role != 99 order by name';
    $r = MySqlDb::getInstance()->query($sql, $inputParams);
    $html = '<center style="margin-top: 20px;"><div class="title">please enter the user id to delete</div>';
    $html .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="action" value="delete">';
    $html .= '<input style="margin: 10px 0;width:150px;" type="text" name="id" placeholder="userid">';
    $html .= '<input type="submit"></input>';
    $html .= '</from></center>';
    $html .= '<table class="reports"><tr><td>Name</td><td>Action</td></tr>';
    foreach ($r as $user)
    {
        $html .= '<tr><td>';
        $html .= '<form action="account_action.php" method="POST" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="action" value="delete">';
        $html .= '<input type="hidden" name="id" value="' . $user['id'] . '">';
        $html .= $user['name'] . '</td>';
        $html .= '<td><input type="submit" value="delete"></input></td>';
        $html .= '</form></td></tr>';
    }
    $html .= '</table></center>';
}
else if ('backpackers' == $_GET['action'])
{
    $html = '';
    $states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
    foreach ($states as $state)
    {
        $fa = 0;
        $fb = 0;
        $fc = 0;
        $fd = 0;
        $fe = 0;
        $ff = 0;
        $fg = 0;
        $fh = 0;
        $ha = 0;
        $hb = 0;
        $hc = 0;
        $hd = 0;

        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'];
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['01. total'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and rent_low < 50';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['02. rent<50'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and rent_low > 50 and rent_low < 100';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['03. 50<rent<100'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and rent_low > 100 and rent_low < 150';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['04. 100<rent<150'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and rent_low > 150 and rent_low < 200';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['05. 150<rent<200'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and rent_low > 200 and rent_low < 250';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['06. 200<rent<250'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['07. single0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['08. single1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['09. single2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['10. single3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['11. single4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_single = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['12. single5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['14. double0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['15. double1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['16. double2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['17. double3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['18. double4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from backpackers where state=' . $state['id'] . ' and beds_double = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['19. double5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT *from backpackers where state=' . $state['id'];
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        foreach($r as $b)
        {
            $facilities = json_decode(json_decode($b['facilities']), true);
            if ($facilities['fa'] === '1') { $fa++; }
            if ($facilities['fb'] === '1') { $fb++; }
            if ($facilities['fc'] === '1') { $fc++; }
            if ($facilities['fd'] === '1') { $fd++; }
            if ($facilities['fe'] === '1') { $fe++; }
            if ($facilities['ff'] === '1') { $ff++; }
            if ($facilities['fg'] === '1') { $fg++; }
            if ($facilities['fh'] === '1') { $fh++; }
            $helps = json_decode(json_decode($b['additional_help']), true);
            if ($helps['ha'] === '1') { $ha++; }
            if ($helps['hb'] === '1') { $hb++; }
            if ($helps['hc'] === '1') { $hc++; }
            if ($helps['hd'] === '1') { $hd++; }
        }
        $reports['20. ' . EliteHelper::getLangString('SEARCH_RESULT_FA')][$state['class']] = $fa;
        $reports['21. ' . EliteHelper::getLangString('SEARCH_RESULT_FB')][$state['class']] = $fb;
        $reports['22. ' . EliteHelper::getLangString('SEARCH_RESULT_FC')][$state['class']] = $fc;
        $reports['23. ' . EliteHelper::getLangString('SEARCH_RESULT_FD')][$state['class']] = $fd;
        $reports['24. ' . EliteHelper::getLangString('SEARCH_RESULT_FE')][$state['class']] = $fe;
        $reports['25. ' . EliteHelper::getLangString('SEARCH_RESULT_FF')][$state['class']] = $ff;
        $reports['26. ' . EliteHelper::getLangString('SEARCH_RESULT_FG')][$state['class']] = $fg;
        $reports['27. ' . EliteHelper::getLangString('SEARCH_RESULT_FH')][$state['class']] = $fh;
        $reports['28. ' . EliteHelper::getLangString('SEARCH_RESULT_HA')][$state['class']] = $ha;
        $reports['29. ' . EliteHelper::getLangString('SEARCH_RESULT_HB')][$state['class']] = $hb;
        $reports['30. ' . EliteHelper::getLangString('SEARCH_RESULT_HC')][$state['class']] = $hc;
        $reports['31. ' . EliteHelper::getLangString('SEARCH_RESULT_HD')][$state['class']] = $hd;
    }
    $html .= ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
}
else if ('houseowners' == $_GET['action'])
{
    $html = '';
    $ha = 0;
    $hb = 0;
    $hc = 0;
    $hd = 0;
    $sql = 'SELECT count(*) as count from landlords';
    $r = MySqlDb::getInstance()->query($sql, $inputParams);
    $reports['01. total'][$state['class']] = $r[0]['count'];
    $sql = 'SELECT * from landlords';
    $r = MySqlDb::getInstance()->query($sql, $inputParams);
    foreach($r as $h)
    {
        $helps = json_decode(json_decode($h['additional_help']), true);
        if ($helps['ha'] === '1') { $ha++; }
        if ($helps['hb'] === '1') { $hb++; }
        if ($helps['hc'] === '1') { $hc++; }
        if ($helps['hd'] === '1') { $hd++; }
    }
    $reports['02. ' . EliteHelper::getLangString('SEARCH_RESULT_HA')]['total number'] = $ha;
    $reports['03. ' . EliteHelper::getLangString('SEARCH_RESULT_HB')]['total number'] = $hb;
    $reports['04. ' . EliteHelper::getLangString('SEARCH_RESULT_HC')]['total number'] = $hc;
    $reports['05. ' . EliteHelper::getLangString('SEARCH_RESULT_HD')]['total number'] = $hd;
    $html .= ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
}
else if ('houseobjects' == $_GET['action'])
{
    $html = '';
    $states = ConfigReader::getInstance()->readConfig('dimensions', 'states');
    foreach ($states as $state)
    {
        $fa = 0;
        $fb = 0;
        $fc = 0;
        $fd = 0;
        $fe = 0;
        $ff = 0;
        $fg = 0;
        $fh = 0;
        $w = 0;
        $e = 0;

        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'];
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['01. total'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rent_low < 50';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['02. rent<50'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rent_low > 50 and rent_low < 100';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['03. 50<rent<100'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rent_low > 100 and rent_low < 150';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['04. 100<rent<150'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rent_low > 150 and rent_low < 200';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['05. 150<rent<200'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rent_low > 200 and rent_low < 250';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['06. 200<rent<250'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['07. single0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['08. single1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['09. single2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['10. single3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['11. single4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_single = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['12. single5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['14. double0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['15. double1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['16. double2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['17. double3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['18. double4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and beds_double = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['19. double5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT *from houseobjects where state=' . $state['id'];
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        foreach($r as $h)
        {
            $wecharge = json_decode($h['wecharge'], true);
            if ($wecharge['w'] !== '') { $w++; }
            if ($wecharge['e'] !== '') { $e++; }
            $facilities = json_decode(json_decode($h['facilities']), true);
            if ($facilities['fa'] === '1') { $fa++; }
            if ($facilities['fb'] === '1') { $fb++; }
            if ($facilities['fc'] === '1') { $fc++; }
            if ($facilities['fd'] === '1') { $fd++; }
            if ($facilities['fe'] === '1') { $fe++; }
            if ($facilities['ff'] === '1') { $ff++; }
            if ($facilities['fg'] === '1') { $fg++; }
            if ($facilities['fh'] === '1') { $fh++; }
        }
        $reports['20. ' . EliteHelper::getLangString('SEARCH_RESULT_FA')][$state['class']] = $fa;
        $reports['21. ' . EliteHelper::getLangString('SEARCH_RESULT_FB')][$state['class']] = $fb;
        $reports['22. ' . EliteHelper::getLangString('SEARCH_RESULT_FC')][$state['class']] = $fc;
        $reports['23. ' . EliteHelper::getLangString('SEARCH_RESULT_FD')][$state['class']] = $fd;
        $reports['24. ' . EliteHelper::getLangString('SEARCH_RESULT_FE')][$state['class']] = $fe;
        $reports['25. ' . EliteHelper::getLangString('SEARCH_RESULT_FF')][$state['class']] = $ff;
        $reports['26. ' . EliteHelper::getLangString('SEARCH_RESULT_FG')][$state['class']] = $fg;
        $reports['27. ' . EliteHelper::getLangString('SEARCH_RESULT_FH')][$state['class']] = $fh;
        $reports['28. ' . EliteHelper::getLangString('REG_WATER')][$state['class']] = $w;
        $reports['29. ' . EliteHelper::getLangString('REG_ELECTRCITY')][$state['class']] = $e;


        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['30. rooms0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['31. rooms1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['32. rooms2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['33. rooms3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['34. rooms4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and rooms = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['35. rooms5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and toilets = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['35. toilets0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and toilets = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['36. toilets1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and toilets = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['37. toilets2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and toilets = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['38. toilets3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 0';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['39. parking_space0'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 1';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['40. parking_space1'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 2';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['41. parking_space2'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 3';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['42. parking_space3'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 4';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['43. parking_space4'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 5';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['44. parking_space5'][$state['class']] = $r[0]['count'];
        $sql = 'SELECT count(*) as count from houseobjects where state=' . $state['id'] . ' and parking_space = 6';
        $r = MySqlDb::getInstance()->query($sql, $inputParams);
        $reports['45. parking_space6'][$state['class']] = $r[0]['count'];

    }
    $html .= ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
}
$headData = array(
    'title' => EliteHelper::getLangString('COMMON_B_TITLE'),
    'css' => array(
        array('url' => '/css/common.css'),
        array('url' => '/css/search_common.css'),
        array('url' => '/css/admin_common.css'),
        array('url' => '/css/register.css'),
    )
);
$tailData = array(
    'js' => array(
        array('url' => '/js/admin_common.js'),
    )
);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php EliteHelper::initJsObject();?>
        <?php echo ContentGenerator::getContent('head', $headData);?>
    </head>
    <body class="<?php echo $role;?>">
        <?php echo ContentGenerator::getContent('common_banner', array('title' => EliteHelper::getLangString('COMMON_B_TITLE')));?>
        <?php echo ContentGenerator::getContent('common_menu', array('user' => $user));?>
        <div class="main-container">
            <div class="col-combined col">
                <div class="row">
                    <?php echo ContentGenerator::getContent('superadmin_adminmenu', array('action' => $_GET['action']));?>
                </div>
                <div class="row">
                    <?php echo $html;?>
                </div>
            </div>
        </div>
        <?php EliteHelper::passParamsAndStringsToJs();?>
        <?php echo ContentGenerator::getContent('tail', $tailData);?>
    </body>
</html>
