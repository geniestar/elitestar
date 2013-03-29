<?php
include_once ('MustacheRenderer.php');
/**
 * contentGenerator
 * exmaple: contentGenerator::getContent(contentGenerator::COUNTRY_TW, 'fbFans', array('who' => 'mustache'));
 */
class ContentGenerator extends MustacheRenderer
{
    const TEMPLATE_FILES_ROOT_PATH = '../frontend/templates';
    const DATA_PROCESS_FILES_ROOT_PATH = '../frontend/dataProcess';

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

var_dump(ContentGenerator::getContent('head', array('who' => 'mustache')));

?>
