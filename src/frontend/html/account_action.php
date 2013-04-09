<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
define('USER_PHOTO_PATH', '/var/www/html/elitestar/ugc/');
define('USER_PHOTO_PREFIX', 'uphoto');
define('OBJECT_PHOTO_PREFIX', 'ophoto');

if (EliteHelper::checkEmpty(array('email', 'password', 'name', 'email', 'phone', 'role', 'country'), $_POST))
{
    /* user exist */
    if (is_array(EliteUsers::getInstance()->queryUser($_POST['email'], $_POST['password'], false, true)))
    {
         exit;
    }
    $photoFilename = '';
    if (isset($_FILES['user-photo']) && $_FILES['user-photo']['tmp_name'] && EliteHelper::checkIsImage($_FILES['user-photo']['name']))
    {
        $photoFilename = md5(USER_PHOTO_PREFIX . $_POST['email']) . '.' . EliteHelper::getExtensionName($_FILES['user-photo']['name']);
        system('cp ' . $_FILES['user-photo']['tmp_name'] . ' ' . USER_PHOTO_PATH . $photoFilename);
        system('convert ' . USER_PHOTO_PATH . $photoFilename . ' -resize \'120x120\' -gravity Center -crop \'80x80+0+0\' -quality \'100%\' ' . USER_PHOTO_PATH . $photoFilename);
    }
    EliteUsers::getInstance()->createUser($_POST['email'], $_POST['password'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['role'], $_POST['country'], $photoFilename);

    /* start to update role table*/
    if ('b' === $_POST['role'])
    {
        if(EliteHelper::checkEmpty(array('state', 'city', 'duration_start', 'duration_end', 'bed_single', 'bed_double', 'acceptable_rent'), $_POST))
        {
            BackPackers::getInstance()->createBackPacker($_POST['email'], $_POST['state'], $_POST['city'], getTime($_POST['duration_start']), getTime($_POST['duration_end']), getLowRent($_POST['acceptable_rent']), getHighRent($_POST['acceptable_rent']), $_POST['bed_single'], $_POST['bed_double'], getFacilities($_POST, 'b'), getServices($_POST, 'b'), '');



//BackPackers::getInstance()->createBackPacker('testaccount5', 1, 2, '2013-05-03 12:00:00', '2013-10-03 12:00:00', 180, 200, 1, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'), array(123,124));
        }
        else
        {
            exit;
        }
    }
}
else
{
    /* some filed empty*/
    exit;
}

function getfacilities($data, $prefix)
{
    $facilities = array();
    $facilities['fa'] = isset($data[$prefix . '-fa'])?$data[$prefix . '-fa']:0;
    $facilities['fb'] = isset($data[$prefix . '-fb'])?$data[$prefix . '-fb']:0;
    $facilities['fc'] = isset($data[$prefix . '-fc'])?$data[$prefix . '-fc']:0;
    $facilities['fd'] = isset($data[$prefix . '-fd'])?$data[$prefix . '-fd']:0;
    $facilities['fe'] = isset($data[$prefix . '-fe'])?$data[$prefix . '-fe']:0;
    $facilities['ff'] = isset($data[$prefix . '-ff'])?$data[$prefix . '-ff']:0;
    $facilities['fg'] = isset($data[$prefix . '-fg'])?$data[$prefix . '-fg']:0;
    $facilities['fh'] = isset($data[$prefix . '-fh'])?$data[$prefix . '-fh']:0;
    return json_encode($facilities);
}

function getServices($data, $prefix)
{
    $services = array();
    $services['ha'] = isset($data[$prefix . '-ha'])?$data[$prefix . '-ha']:0;
    $services['haa'] = isset($data[$prefix . '-haa'])?$data[$prefix . '-haa']:'';
    $services['hb'] = isset($data[$prefix . '-hb'])?$data[$prefix . '-hb']:0;
    $services['hba'] = isset($data[$prefix . '-hba'])?$data[$prefix . '-hba']:'';
    $services['hc'] = isset($data[$prefix . '-hc'])?$data[$prefix . '-hc']:0;
    $services['hd'] = isset($data[$prefix . '-hd'])?$data[$prefix . '-hd']:0;
    return json_encode($services);
}

function getTime($timeString)
{
    $timeRule = '/(\w+)\/(\w+)\/(\w+)/';
    $matchArray = array();
    preg_match($timeRule, $timeString, $matchArray);
    if ($matchArray)
    {
        return date("Y-m-d H:i:s", strtotime($matchArray[3] . ' ' . $matchArray[2] . ' ' . $matchArray[1]));
    }
    else
    {
        return date("Y-m-d H:i:s", time());
    }
}

function getLowRent($rentString)
{
    $rentRule = '/(\w+)-(\w+)/';
    $highestRule = '/(\w+)↑/';

    $matchArray = array();
    preg_match($rentRule, $rentString, $matchArray);
    if ($matchArray)
    {
        return $matchArray[1];
    }
    else
    {
        preg_match($highestRule, $rentString, $matchArray);
        if ($matchArray)
        {
            return $matchArray[1];
        }
    }
}

function getHighRent($rentString)
{
    $rentRule = '/(\w+)-(\w+)/';
    $highestRule = '/(\w+)↑/';

    $matchArray = array();
    preg_match($rentRule, $rentString, $matchArray);
    if ($matchArray)
    {
        return $matchArray[2];
    }
    else
    {
        preg_match($highestRule, $rentString, $matchArray);
        if ($matchArray)
        {
            return 9999;
        }
    }
}

//EliteUsers::getInstance()->createUser('testaccount2', 'test123', 'test user', 'test@yahoo.com', '0922', 'role');
//BackPackers::getInstance()->createBackPacker('testaccount5', 1, 2, '2013-05-03 12:00:00', '2013-10-03 12:00:00', 180, 200, 1, 2, array('bbb'=>'aaa'), array('ccc'=>'asb'), array(123,124));
//var_dump(LandLords::getInstance()->createLandLord('testaccount2', array('ccc'=>'asb'), array(1,2,3)));
//var_dump(HouseObjects::getInstance()->createHouseObject('testaccount1', 1, 3, 'address', 'house name','2013-05-03 12:00:00', 1, 2, 2, 1, 2, array('bbb'=>'aaa'), 180, 200, 'no photo', array(), 'des'));
var_dump($_POST);
var_dump($_FILES);
?>
