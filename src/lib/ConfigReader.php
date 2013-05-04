<?php
/**
 * config reader
 *
 */
class ConfigReader
{
    const CONFIG_ROOT_PATH = '/usr/share/pear/elitestar/conf/';
    private static $_instance;
    private $_configs = array();
    private $_env = 'stage';

    /**
     * __construct 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        
    }  

    /**
     * Destructor 
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
    }

    /**
     * getInstance
     *
     * @access
     * @return instance
     */
    public static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new ConfigReader();
        }
        return self::$_instance;
    }

    public function readConfig($fileName, $indexes)
    {
        $indexArray = explode('.', $indexes);
        if (apc_fetch('CONFIG' . $fileName) && 'dev' !== $this->_env)
        {
            $config = apc_fetch('CONFIG' . $fileName);
        }
        else
        {
            $config = yaml_parse_file(self::CONFIG_ROOT_PATH . $fileName . '.yaml');
            if ($config)
            {
                apc_store('CONFIG' . $fileName, $config);
            }
        }
        $currentReturnValue = $config;
        foreach ($indexArray as $index)
        {
            if (!isset($currentReturnValue[$index]))
            {
                return false;
            }
            else
            {
                $currentReturnValue = $currentReturnValue[$index];
            }
        }
        if (empty($currentReturnValue))
        {
            return false;
        }
        else
        {
            return $currentReturnValue;
        }
    }
}
//var_dump(ConfigReader::getInstance()->readConfig('states', 'states'));
//var_dump(ConfigReader::getInstance()->readConfig('common', 'rents'));
?>
