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
    $startYear = 2013;
    $startMonth = 4;
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
        $key = $year . '-' . (($month>9)?$month:'0'.$month) . '-' . (($day>9)?$day:'0'.$day);
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
    $startMonth = 4;
    $year = $startYear;
    $month = $startMonth;
    $now = time();
    $reports = array();
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
    $html = ContentGenerator::getContent('superadmin_reports', array('reports' => $reports));
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
