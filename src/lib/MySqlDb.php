<?php

/**
 * MysqlDb is the wrapper of Mysql PDO functions
 */
class MySqlDb
{
    const MYSQL_HOST = '127.0.0.1';
    const MYSQL_DBNAME = 'elitestar';

    private $_mysqli;
    private $_copy;
    private static $_instance;

    /**
     * __construct 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_mysqli = new mysqli(self::MYSQL_HOST, 'root', 'elitestar', self::MYSQL_DBNAME);
        $this->_copy = create_function('$a', 'return $a;');
    }  

    /**
     * Destructor 
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if (is_object($this->_mysqli))
        {
            $this->_mysqli->close();
        }
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
            self::$_instance = new MySqlDb();
        }
        return self::$_instance;
    }

    /**
     * convert array item to reference items
     *
     * @params array $arr input array
     *
     * @access private
     * @return converted array
     */
    private function _refValues($arr)
    {
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
            {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }
    
    /**
     * query
     *
     * @params string $sql sql command with prepare statement
     * @params array  $inputParams
     *
     * @access public
     * @return results
     */
    public function query($sql, $inputParams = array())
    {
        if ($statement = $this->_mysqli->prepare($sql))
        {
            /*process input params*/
            $preparedParams  = array();
            foreach($inputParams as $inputParam)
            {
                $preparedParams[] = $inputParam;
            }

            /*bind params*/
            $type = str_repeat('s', count($preparedParams));
            if (!empty($preparedParams))
            {
                call_user_func_array(array($statement, 'bind_param'), array_merge((array)$type, $this->_refValues($preparedParams)));
            }

            $meta = $statement->result_metadata();

            /* define result fileds*/
            $results = array();
            while ($meta && $field = $meta->fetch_field())
            {
                $results[$field->name] = &$row[$field->name];
            }
            if ($results)
            {
                call_user_func_array(array($statement, 'bind_result'), $results);
            }

            $r = $statement->execute();
            $allResults = array();

            /*fetch the results*/
            while($tmp = $statement->fetch())
            {
                if (is_array($results))
                {
                    $tmp = array_map($this->_copy, $results);
                    $allResults[] = $tmp;
                }
            }
            $statement->close();
            /*if no results array, just return*/
            if (!$allResults || empty($allResults))
            {
                return $r;    
            }
        }
        return $allResults;
    }
}

//$inputParams = array('testaccount7', 'cc03e747a6afbbcbf8be7668acfebe', 'Kaeson Ho', '0925083472', 'test@yahoo.com', 1);
//$sql = 'INSERT INTO users VALUE(?, ?, ?, ?, ?, ?)';
//$r = MySqlDb::getInstance()->query($sql, $inputParams);
//var_dump($r);
//$sql = 'SELECT * FROM users';
//$inputParams = array();
//$r = MySqlDb::getInstance()->query($sql, $inputParams);
//var_dump($r);
?>
