<?php
include('/usr/share/pear/elitestar/lib/EliteUsers.php');
include('/usr/share/pear/elitestar/lib/EliteHelper.php');
include('/usr/share/pear/elitestar/lib/BackPackers.php');
include('/usr/share/pear/elitestar/lib/LandLords.php');
include('/usr/share/pear/elitestar/lib/HouseObjects.php');
define('USER_PHOTO_PATH', '/var/www/html/elitestar/ugc/');
define('USER_PHOTO_PREFIX', 'uphoto');
define('OBJECT_PHOTO_PREFIX', 'ophoto');
if (isset($_POST['action']) && 'forget_pw' == $_POST['action'] && isset($_POST['id']))
{
    sendPassword($_POST['id']);
    header('Location: /');
    exit;
}
if (!isset($_POST['edit']))
{
if (EliteHelper::checkEmpty(array('id', 'password', 'name', 'email', 'phone', 'role', 'country'), $_POST))
{
    /* user exist */
    if (is_array(EliteUsers::getInstance()->queryUser($_POST['id'], null, false, true)))
    {
        header('Location: error.php?error=USER_ID_INVALID');
        exit;
    }
    $photoFilename = '';
    if (isset($_FILES['user-photo']) && $_FILES['user-photo']['tmp_name'] && EliteHelper::checkIsImage($_FILES['user-photo']['name']))
    {
        $photoFilename = md5(USER_PHOTO_PREFIX . $_POST['id']) . '.' . EliteHelper::getExtensionName($_FILES['user-photo']['name']);
        system('cp ' . $_FILES['user-photo']['tmp_name'] . ' ' . USER_PHOTO_PATH . $photoFilename);
        system('convert ' . USER_PHOTO_PATH . $photoFilename . ' -resize \'134x160\' -gravity Center -crop \'76x93+0+0\' -quality \'100%\' ' . USER_PHOTO_PATH . $photoFilename);
    }
    EliteUsers::getInstance()->createUser($_POST['id'], $_POST['password'], $_POST['name'], json_encode(array('value' => $_POST['email'], 'publish' => $_POST['email_p'])), json_encode(array('value'=> $_POST['phone'], 'publish' => $_POST['phone_p'])), $_POST['role'], $_POST['country'], $photoFilename);

    /* start to update role table*/
    if (1 == $_POST['role'])
    {
        if(EliteHelper::checkEmpty(array('state', 'city', 'arrival_time', 'duration_start', 'duration_end', 'bed_single', 'bed_double', 'rent', 'name'), $_POST))
        {
            BackPackers::getInstance()->createBackPacker($_POST['id'], $_POST['state'], $_POST['city'], EliteHelper::getTime($_POST['arrival_time']), EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rent'], $_POST['rent'], $_POST['bed_single'], $_POST['bed_double'], getFacilities($_POST, 'b'), getServices($_POST, 'b'), $_POST['name'], null);
            //var_dump(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password']));
            //var_dump(BackPackers::getInstance()->findBackPackers(null, null, 0, 20, BackPackers::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $_POST['id']));
        }
        else
        {
            header('Location: error.php?error=FIELDS_EMPTY');
            exit;
        }
    }
    else if(0 == $_POST['role'])
    {
        if(EliteHelper::checkEmpty(array('state', 'city', 'address', 'housename', 'duration_start', 'duration_end', 'rooms', 'bed_single', 'bed_double', 'toilets', 'parking_space', 'rent'), $_POST))
        {
            LandLords::getInstance()->createLandLord($_POST['id'], getServices($_POST, 'h'), null);
            $photos = getHousePhotos($_POST['id']);
            HouseObjects::getInstance()->createHouseObject($_POST['id'], $_POST['state'], $_POST['city'], $_POST['address'], $_POST['housename'], EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rooms'], $_POST['bed_single'], $_POST['bed_double'], $_POST['toilets'], $_POST['parking_space'], getWECharge($_POST, 'h'), getFacilities($_POST, 'h'), $_POST['rent'], $_POST['rent'], $photos['0'], json_encode($photos), getDescription($_POST));

            //var_dump(EliteUsers::getInstance()->queryUser($_POST['id'], $_POST['password']));
            //var_dump(LandLords::getInstance()->queryLandLord($_POST['id']));  
            //var_dump(HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $_POST['id']));
        }
        else
        {
            header('Location: error.php?error=FIELDS_EMPTY');
            exit;
        }
    }

    sendEmail($_POST['email']);
    $user = EliteUsers::getInstance()->login($_POST['id'], $_POST['password'], false);

    if ($user)
    {
        if ($user['role'] === EliteUsers::ROLE_LANDLORD)
        {
            header('Location: find_backpacker.php');
        }
        else
        {
            header('Location: find_house.php');
        }
    }
}
else
{
    /* some filed empty*/
    header('Location: error.php?error=USER_ID_INVALID');
    exit;
}
}
else
{
    $user = EliteUsers::getInstance()->getCurrentUser();
    if (!$user)
    {
        header('Location: error.php?error=NEED_LOGIN');
        exit;
    }
    if ($_POST['basic-info'])
    {
        if (isset($_POST['password']) && isset($_POST['original_password']) && isset($_POST['retype_password']) && $_POST['password'] && $_POST['original_password'] && $_POST['retype_password'])
        {
            if (EliteUsers::queryUser($user['id'], $_POST['original_password']) && ($_POST['password'] === $_POST['retype_password']))
            {
                EliteUsers::getInstance()->updateUserInfo($user['id'], $_POST['password'], null, json_encode(array('value' => $_POST['email'], 'publish' => $_POST['email_p'])), json_encode(array('value'=> $_POST['phone'], 'publish' => $_POST['phone_p'])), null, null, null);
                setcookie('p', md5($_POST['password']), time()+60*60*24*365);
            }
            else
            {
                header('Location: error.php?error=ORIGINAL_PW_WRONG');
                exit;
            }
        }
        else
        {
            EliteUsers::getInstance()->updateUserInfo($user['id'], null, null, json_encode(array('value' => $_POST['email'], 'publish' => $_POST['email_p'])), json_encode(array('value'=> $_POST['phone'], 'publish' => $_POST['phone_p'])), null, null, null);
        }
        setcookie('a', 1, time() + 3600);
        header('Location: admin.php?action=basic');
        exit;
    }
    else
    {
        if (0 == $_POST['role'])
        {
            if ('service' === $_POST['tab'])
            {
                LandLords::getInstance()->updateLandLordInfo($user['id'], getServices($_POST, 'h'), null); 
                setcookie('a', 1, time() + 3600);
                header('Location: admin.php?action=settings&tab=service');
                exit;
            }
            else
            {
                if ($_POST['objectid'])
                {
                    $photos = getHousePhotos($user['id']);
                    $oldhouseobject = HouseObjects::getInstance()->findHouseObjects(null, null, 0, 20, HouseObjects::SORT_BY_PRICE_DESC, null, null, null, null, null, null, null, null, $user['id'], $_POST['objectid']);
                    $ophotos = json_decode(json_decode($oldhouseobject[0]['photos']), true);
                    if ($ophotos)
                    {
                        $photos = array_merge($ophotos, $photos);
                    }
                    HouseObjects::getInstance()->updateHouseObjectInfo($user['id'], $_POST['objectid'], $_POST['state'], $_POST['city'], $_POST['address'], $_POST['housename'], EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rooms'], $_POST['bed_single'], $_POST['bed_double'], $_POST['toilets'], $_POST['parking_space'], getWECharge($_POST, 'h'),getFacilities($_POST, 'h'), $_POST['rent'], $_POST['rent'], $photos[0], json_encode($photos), getDescription($_POST));
                }
                else
                {
                    if(EliteHelper::checkEmpty(array('state', 'city', 'address', 'housename', 'duration_start', 'duration_end', 'rooms', 'bed_single', 'bed_double', 'toilets', 'parking_space', 'rent'), $_POST))
                    {
                        $photos = getHousePhotos($_POST['id']);
                        HouseObjects::getInstance()->createHouseObject($user['id'], $_POST['state'], $_POST['city'], $_POST['address'], $_POST['housename'], EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['rooms'], $_POST['bed_single'], $_POST['bed_double'], $_POST['toilets'], $_POST['parking_space'], getWECharge($_POST, 'h'), getFacilities($_POST, 'h'), $_POST['rent'], $_POST['rent'], $photos['0'], json_encode($photos), getDescription($_POST));

                    }
                    else
                    {
                        header('Location: error.php?error=FIELDS_EMPTY');
                        exit;
                    }
                }
                setcookie('a', 1, time() + 3600);
                header('Location: admin.php?action=settings&tab=settings&objectId=' . $_POST['objectid']);
                exit;
            }
        }
        else
        {
            BackPackers::getInstance()->updateBackPackerInfo($user['id'], $_POST['state'], $_POST['city'], $_POST['rent'], EliteHelper::getTime($_POST['arrival_time']), EliteHelper::getTime($_POST['duration_start']), EliteHelper::getTime($_POST['duration_end']), $_POST['bed_single'], $_POST['bed_double'], getFacilities($_POST, 'b'), getServices($_POST, 'b'), $_POST['name'], null);
        }
        setcookie('a', 1, time() + 3600);
        header('Location: admin.php?action=settings');
        exit;
    }
}

function getWECharge($data, $prefix)
{
    $charge = array();
    if ($data['uew'])
    {
        $charge['w'] = $data['uew-c'];
    }
    else
    {
        $charge['w'] = '';
    }
    if ($data['uee'])
    {
        $charge['e'] = $data['uee-c'];
    }
    else
    {
        $charge['e'] = '';
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
    if ('h' == $prefix)
    {
        $haaArray = array();
        $haa = '';
        for ($i = 1; $i<=4; $i++)
        {
            if (isset($data[$prefix . '-haa-' . $i]) && $data[$prefix . '-haa-' . $i] !== '')
            {
                $haaArray[] = 'x' . $i . ':' . $data[$prefix . '-haa-' . $i] . 'AUD';
            }
        }
        if (isset($data[$prefix . '-haa-o']))
        {
            $haaArray[] = $data[$prefix . '-haa-o'];
        }
        $haa = implode($haaArray, ',');
    }
    else
    {
        if ($data[$prefix . '-ha'])
        {
            $haa = $data[$prefix . '-haa'];
        }
    }

    $services['haa'] = $haa?$haa:'';
    $services['hb'] = isset($data[$prefix . '-hb'])?$data[$prefix . '-hb']:0;
    $services['hc'] = isset($data[$prefix . '-hc'])?$data[$prefix . '-hc']:0;
    $services['hd'] = isset($data[$prefix . '-hd'])?$data[$prefix . '-hd']:0;
    return json_encode($services);
}

function getHousePhotos($id)
{
    $photos = array();
    for ($i = 1; $i < 6; $i++)
    {
        if (isset($_FILES['photo-' . $i]) && $_FILES['photo-' . $i]['tmp_name'] && EliteHelper::checkIsImage($_FILES['photo-' . $i]['name']))
        {
            $photoFilename = md5(OBJECT_PHOTO_PREFIX . $id . $_FILES['photo-' . $i]['name'] . time()) . '.' . EliteHelper::getExtensionName($_FILES['photo-' . $i]['name']);
            system('cp ' . $_FILES['photo-' . $i]['tmp_name'] . ' ' . USER_PHOTO_PATH . $photoFilename);
            system('convert ' . USER_PHOTO_PATH . $photoFilename . ' -resize \'180x120\' -gravity Center -crop \'144x93+0+0\' -quality \'100%\' ' . USER_PHOTO_PATH . 'c_' . $photoFilename);
            $photos[] = $photoFilename;
        }
    }
    return $photos;
}

function getDescription($data)
{
    $i = 1;
    $descriptions = array();
    for ($i=1; $i<=10; $i++)
    {
        if (isset($data['km'][$i]) && $data['km'][$i] && isset($data['mins'][$i]) && $data['mins'][$i] && isset($data['position'][$i]) && $data['position'][$i] && isset($data['vehicle'][$i]) && $data['vehicle'][$i])
        {
            $description = array(
                'position' => $data['position'][$i],
                'km' => $data['km'][$i],
                'mins' => $data['mins'][$i],
                'vehicle' => $data['vehicle'][$i],
                'more' => $data['desc'][$i]
            );
            $descriptions[] = $description;
        }
    }
    return json_encode($descriptions);
}

function sendEmail($email)
{
    $to      = $email;
    $subject = EliteHelper::getLangString('WELCOME_MAIL_TITLE');
    $message = EliteHelper::getLangString('WELCOME_MAIL_DESC');
    $headers = 'From: elitestar@gmail.com' . "\r\n";
    mail($to, $subject, $message, $headers);
}

function sendPassword($id)
{
    $user = EliteUsers::getInstance()->queryUser($id, null, false, true);
    $userInfo = EliteUsers::getInstance()->queryUser($id, null, null, true);
    if ($user && $userInfo) 
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789!@#$%^&*()');
        shuffle($seed);
        $newPw = implode('', array_slice($seed, 0, 8));
        EliteUsers::getInstance()->updateUserInfo($id, $newPw, null, null, null, null, null, null);
        $to      = $userInfo[0]['mail'];
        $subject = EliteHelper::getLangString('FORGOT_PASSWORD_MAIL');
        $message = EliteHelper::getLangString('FORGOT_PASSWORD_DESC1');
        $message .= '<br><br><b>' . $newPw . '</b><br>';
        $message .= EliteHelper::getLangString('FORGOT_PASSWORD_DESC2');
        $headers = "MIME-Version: 1.0\r\n" .
                   "Content-type: text/html; charset=$sCharset\r\n" .
                   "From: elitestar@gmail.com" . "\r\n";
        mail($to, $subject, $message, $headers);
    }
}
?>
