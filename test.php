<?php
$mysqli = new mysqli('localhost', 'root', 'elitestar', 'elitestar');

//$sql = 'SELECT * FROM users WHERE id =? AND phone=?';
$sql = 'INSERT INTO users VALUE(?, ?, ?, ?, ?, ?)';
$id = 'testaccount4';
$phone = '0925083472';
$pw = 'cc03e747a6afbbcbf8be7668acfebe';
$name = 'Kaeson Ho';
$mail = 'test@yahoo.com';
$role = '1';

$inputParams = array('testaccount5', 'cc03e747a6afbbcbf8be7668acfebe', 'Kaeson Ho', '0925083472', 'test@yahoo.com', 1);

function refValues($arr)
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

if ($statement = $mysqli->prepare($sql))
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
        call_user_func_array(array($statement, 'bind_param'), array_merge((array)$type, refValues($preparedParams)));
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

    $statement->execute();

    /*fetch the results*/
    while($statement->fetch())
    {
        var_dump($results);
    }
    $statement->close();
}

?>
