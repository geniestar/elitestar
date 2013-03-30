<?php
include_once ('MustacheRenderer.php');
include_once ('ConfigReader.php');
/**
 * contentGenerator
 * exmaple: contentGenerator::getContent(contentGenerator::COUNTRY_TW, 'fbFans', array('who' => 'mustache'));
 */
class ContentGenerator extends MustacheRenderer
{
    const TEMPLATE_FILES_ROOT_PATH = '../templates';
    const DATA_PROCESS_FILES_ROOT_PATH = '../dataProcess';

    static private $instances = array();

    private $contentName;

    /**
     * construct
     *
     * @param string $contentName content name
     *
     * @return void
     */
    protected function __construct($contentName)
    {
        parent::__construct();
        $this->contentName = $contentName;
    }

    /**
     * getTemplateFilesRootPath
     *
     * @return root path
     */
    protected function getTemplateFilesRootPath()
    {
        return self::TEMPLATE_FILES_ROOT_PATH;
    }

    /**
     * getTemplateFileName
     *
     * @return template file name by ycb config
     */
    protected function getTemplateFileName()
    {
        return $this->contentName . '.mustache';
    }

    /**
     * getProcessedData
     *
     * @param array $data data array
     *
     * @return processed data
     */
    protected function getProcessedData($data)
    {
        include (self::DATA_PROCESS_FILES_ROOT_PATH . '/' . $this->contentName . '.php');
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

        $data['i18n'] = ConfigReader::getInstance()->readConfig('lang/' . $language, 'languages');
        return $data;
    }

    /**
     * _getInstance
     *
     * @param string $contentName content name
     *
     * @return instance of contentGenerator
     */
    static private function _getInstance($contentName)
    {
        if (!isset(self::$instances[$contentName]))
        {
            self::$instances[$contentName] = new ContentGenerator($contentName);
        }
        return self::$instances[$contentName];
    }

    /**
     * getContent
     *
     * @param string $contentName story name
     * @param array  $data        data array
     *
     * @return redered html
     */
    static public function getContent($contentName, $data)
    {
        return self::_getInstance($contentName)->renderContent($data);
    }
}

//var_dump(ContentGenerator::getContent('head', array('who' => 'mustache')));

?>
