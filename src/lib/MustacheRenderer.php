<?php
include_once ('mustache.php/src/Mustache/Engine.php');
include_once ('mustache.php/src/Mustache/Loader.php');
include_once ('mustache.php/src/Mustache/Loader/FilesystemLoader.php');
include_once ('mustache.php/src/Mustache/Parser.php');
include_once ('mustache.php/src/Mustache/Tokenizer.php');
include_once ('mustache.php/src/Mustache/Compiler.php');
include_once ('mustache.php/src/Mustache/Template.php');
include_once ('mustache.php/src/Mustache/Context.php');
include_once ('mustache.php/src/Mustache/HelperCollection.php');
include_once ('mustache.php/src/Mustache/LambdaHelper.php');
include_once ('mustache.php/src/Mustache/Autoloader.php');
include_once ('mustache.php/src/Mustache/Logger.php');

/**
 * mustacheRenderer use mustache framework to generate html content
 */
abstract class MustacheRenderer
{
    private $mustacheObject;
    const TEMPLATE_CACHE_PATH = '/tmp/mustache';

    /**
     * getTemplateFilesRootPath
     *
     * @return root path
     */
    abstract protected function getTemplateFilesRootPath();

    /**
     * getTemplateFileName
     *
     * @return template file name by ycb config
     */
    abstract protected function getTemplateFileName();

    /**
     * getProcessedData
     *
     * @param array $data data array
     *
     * @return processed data
     */
    abstract protected function getProcessedData($data);

    /**
     * construct
     *
     * @return void
     */
    protected function __construct()
    {
        $this->mustacheObject = new Mustache_Engine($option = array(
                'loader' => new Mustache_Loader_FilesystemLoader($this->getTemplateFilesRootPath()),
                'cache' => self::TEMPLATE_CACHE_PATH
            )
        );
    }

    /**
     * _getMustacheObject
     *
     * @return mustache object
     */
    private function _getMustacheObject()
    {
        return $this->mustacheObject;
    }

    /**
     * renderContent render html by mustache library
     *
     * @param array $data data array
     *
     * @return redered html
     */
    protected function renderContent($data)
    {
        return $this->_getMustacheObject()->render($this->getTemplateFileName(), $this->getProcessedData($data));
    }
}

?>
