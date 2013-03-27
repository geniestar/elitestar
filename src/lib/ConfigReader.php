<?php
include ('./MySqlDb.php');
/**
 * config reader
 *
 */
class ConfigReader
{
    const CONFIG_ROOT_PATH = '../../conf/';
    private static $_instance;
    private $_configs = array();

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
        if (apc_fetch('CONFIG' . $fileName))
        {
            $config = apc_fetch('CONFIG' . $fileName);
        }
        else
        {
            $config = yaml_parse_file(self::CONFIG_ROOT_PATH . $fileName . '.yaml');
            apc_store('CONFIG' . $fileName, $config);
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
var_dump(ConfigReader::getInstance()->readConfig('common', 'value1.sub_value'));
?>
