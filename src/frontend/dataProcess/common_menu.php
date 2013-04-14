<?php
if(is_array($data['user']))
{
    $data['settingsText'] = EliteHelper::getLangString('COMMON_MENU_SETTINGS_HI') . $data['user']['name'];
    $data['isLogin'] = true;
    if (0 == $data['user']['role'])
    {
        $data['showProfit'] = true;
    }
    else
    {
        $data['showProfit'] = false;
    }
}
else
{
    $data['settingsText'] = EliteHelper::getLangString('COMMON_MENU_SETTINGS_LOGIN');
    $data['isLogin'] = false;
}
?>
