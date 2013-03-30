<?php
include_once ('ConfigReader.php');
/**
 * EliteHelper
 *
 */
class EliteHelper
{
    static private $langs = array();
    static private $errors = array();
    static private $stringToJs = array();

    /**
     * getLangString
     *
     * @param string $index string index
     *
     * @return lang string
     */
    static public function getLangString($index)
    {
        if (empty(self::$langs))
        {
            $language = self::getLanguage();
            self::$langs = ConfigReader::getInstance()->readConfig('lang/' . $language, 'languages');
        }
        if (isset(self::$langs[$index]))
        {
            return self::$langs[$index];
        }
        return '';
    }

    /**
     * getErrorString
     *
     * @param string $index string index
     *
     * @return lang string
     */
    static public function getErrorString($index)
    {
        if (empty(self::$errors))
        {
            self::$errors = configBundle::getConfig('errors', getenv('country'), getenv('env'));
        }
        if (isset(self::$errors[$index]))
        {
            return self::$errors[$index];
        }
        return '';
    }

    /**
     * initJsObject
     *
     * @return void
     */
    static public function initJsObject()
    {
        echo '<script type="text/javascript">';
        echo 'var YAHOO = {ImageUploader: {}};';
        echo '</script>';
    }
    /**
     * setStringToJs
     *
     * @param string $index string index
     *
     * @return void
     */
    static public function setStringToJs($index)
    {
        self::$stringToJs[$index] = self::getLangString($index);
    }

    /**
     * passStringsToJs
     *
     * @return void
     */
    static public function passStringsToJs()
    {
        echo '<script type="text/javascript">';
        echo 'YAHOO.ImageUploader.lang=' . json_encode(self::$stringToJs);
        echo '</script>';
    }
    
    static public function getLanguage()
    {
        $language = 'en-us';
        if ('en-us' === $_GET['lang'] || 'zh-hant-tw' === $_GET['lang'])
        {
            /*set lang by url param*/
            $language = $_GET['lang'];
            setcookie('l', $language, time()+60*60*24*365);
        }
        else if ('en-us' === $_COOKIE['l'] || 'zh-hant-tw' === $_COOKIE['l'])
        {
            /* by cookie*/
            $language = $_COOKIE['l'];
        }
        else
        {
            /* by broswer setting */
            $headers = apache_request_headers();
            $acceptLanguage = '';
            if (isset($headers['Accept-Language']))
            {
                $acceptLanguage = strtolower($headers['Accept-Language']);
            }
            $enPos = strpos($acceptLanguage, 'en');
            $twPos = strpos($acceptLanguage, 'tw');
            if (false === $enPos)
            {
                $language = 'zh-hant-tw';
            }
            else if (false === $twPos)
            {
                $language = 'en-us';
            }
            else
            {
                if ($enPos > $twPos)
                {
                    $language = 'zh-hant-tw';
                }
                else
                {
                    $language = 'en-us';
                }
            }
        }
        return $language;
    }
}
?>
