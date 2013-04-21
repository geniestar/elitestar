<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
define('USER_PHOTO_PATH', '/var/www/html/elitestar/ugc/');
define('USER_PHOTO_PREFIX', 'uphoto');
define('OBJECT_PHOTO_PREFIX', 'ophoto');

if (EliteHelper::checkEmpty(array('id', 'password', 'name', 'email', 'phone', 'role', 'country'), $_POST))
{
    /* user exist */
    if (is_array(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password'], false, true)))
    {
         exit;
    }
    $photoFilename = '';
    if (isset($_FILES['user-photo']) && $_FILES['user-photo']['tmp_name'] && EliteHelper::checkIsImage($_FILES['user-photo']['name']))
    {
        $photoFilename = md5(USER_PHOTO_PREFIX . $_POST['id']) . '.' . EliteHelper::getExtensionName($_FILES['user-photo']['name']);
        system('cp ' . $_FILES['user-photo']['tmp_name'] . ' ' . USER_PHOTO_PATH . $photoFilename);
        system('convert ' . USER_PHOTO_PATH . $photoFilename . ' -resize \'134x160\' -gravity Center -crop \'76x93+0+0\' -quality \'100%\' ' . USER_PHOTO_PATH . $photoFilename);
    }
    EliteUsers::getInstance()->createUser($_POST['id'], $_POST['password'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['role'], $_POST['country'], $photoFilename);

    /* start to update role table*/
    if (1 == $_POST['role'])
    {
        if(EliteHelper::checkEmpty(array('state', 'city', 'arrival_time', 'duration_start', 'duration_end', 'bed_single', 'bed_double', 'rent', 'name'), $_POST))
        {
            BackPackers::getInstance()->createBackPacker($_POST['id'], $_POST['state'], $_POST['city'], EliteHelper::getTime($_POST['arrival_time']), EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rent'], $_POST['rent'], $_POST['bed_single'], $_POST['bed_double'], getFacilities($_POST, 'b'), getServices($_POST, 'b'), $_POST['name'], null);
            var_dump(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password']));
            var_dump(BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $_POST['id']));
        }
        else
        {
            exit;
        }
    }
    else if(0 == $_POST['role'])
    {
        if(EliteHelper::checkEmpty(array('state', 'city', 'address', 'housename', 'duration_start', 'duration_end', 'rooms', 'bed_single', 'bed_double', 'toilets', 'parking_space', 'rent'), $_POST))
        {
            LandLords::getInstance()->createLandLord($_POST['id'], getServices($_POST, 'h'), null);
            $photos = getHousePhotos($_POST);
            HouseObjects::getInstance()->createHouseObject($_POST['id'], $_POST['state'], $_POST['city'], $_POST['address'], $_POST['housename'], EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rooms'], $_POST['bed_single'], $_POST['bed_double'], $_POST['toilets'], $_POST['parking_space'], getWECharge($_POST, 'h'), getFacilities($_POST, 'h'), $_POST['rent'], $_POST['rent'], $photos['0'], json_encode($photos), getDescription($_POST));

            var_dump(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password']));
            var_dump(LandLords::getInstance()->queryLandLord($_POST['id']));  
            var_dump(HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $_POST['id']));
        }
    }
}
else
{
    /* some filed empty*/
    exit;
}

function getWECharge($data, $prefix)
{
    $charge = array();
    if ($data['uew'])
    {
        $charge['w'] = $data['uew-c'];
    }
    if ($data['uee'])
    {
        $charge['e'] = $data['uee-c'];
    }
    return json_encode($charge);
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
    $haaArray = array();
    $haa = '';
    for ($i = 1; $i<=4; $i++)
    {
        if (isset($data[$prefix . '-haa-' . $i]))
        {
            $haaArray[] = 'x' . $i . ':' . $data[$prefix . '-haa-' . $i] . 'AUD';
        }
    }
    if (isset($data[$prefix . '-haa-o']))
    {
        $haaArray[] = $data[$prefix . '-haa-o'];
    }
    $haa = implode($haaArray, ',');
    $services['haa'] = $haa?$haa:'';
    $services['hb'] = isset($data[$prefix . '-hb'])?$data[$prefix . '-hb']:0;
    $services['hc'] = isset($data[$prefix . '-hc'])?$data[$prefix . '-hc']:0;
    $services['hd'] = isset($data[$prefix . '-hd'])?$data[$prefix . '-hd']:0;
    return json_encode($services);
}

function getHousePhotos()
{
    $photos = array();
    for ($i = 1; $i < 6; $i++)
    {
        if (isset($_FILES['photo-' . $i]) && $_FILES['photo-' . $i]['tmp_name'] && EliteHelper::checkIsImage($_FILES['photo-' . $i]['name']))
        {
            $photoFilename = md5(OBJECT_PHOTO_PREFIX . $i . $_POST['email']) . '.' . EliteHelper::getExtensionName($_FILES['photo-' . $i]['name']);
            system('cp ' . $_FILES['photo-' . $i]['tmp_name'] . ' ' . USER_PHOTO_PATH . $photoFilename);
            system('convert ' . USER_PHOTO_PATH . $photoFilename . ' -resize \'180x120\' -gravity Center -crop \'144x93+0+0\' -quality \'100%\' ' . USER_PHOTO_PATH . 'c_' . $photoFilename);
        $photos[] = $photoFilename;
        }
    }
    if (empty($photos))
    {
        $photos[] = '';
    }
    return $photos;
}

function getDescription($data)
{
    $i = 0;
    $descriptions = array();
    while (isset($data['km-' . $i]) && $data['km-' . $i])
    {
        $description = array(
            'position' => $data['position-' . $i],
            'km' => $data['km-' . $i],
            'mins' => $data['mins-' . $i],
            'vehicle' => $data['vehicle-' . $i],
            'more' => $data['desc-' . $i]
        );
        $descriptions[] = $description;
        $i++;
    }
    return json_encode($descriptions);
}
?>
