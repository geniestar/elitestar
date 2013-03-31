<?php
$language = EliteHelper::getLanguage();
if ('zh-hant-tw' === $language)
{
    $data['isTw'] = true;
}
else 
{
    $data['isEng'] = true;
}
?>
