<?php
$mysqli = new mysqli('localhost', 'root', 'elitestar', 'elitestar');

//$sql = 'SELECT * FROM users WHERE id =? AND phone=?';
$sql = 'INSERT INTO users VALUE(?, ?, ?, ?, ?, ?)';
$id = 'testaccount3';
$phone = '0925083472';
$pw = 'cc03e747a6afbbcbf8be7668acfebe';
$name = 'Kaeson Ho';
$mail = 'test@yahoo.com';
$role = '1';

$preparedParams = array(&$id, &$pw, &$name, &$mail, &$phone, &$role);


if ($statement = $mysqli->prepare($sql))
{
    $type = str_repeat('s', count($params));
    if (!empty($params))
    {
        call_user_func_array(array($statement, 'bind_param'), array_merge((array)$type, $params));
    }

    $meta = $statement->result_metadata();

    /* define fileds*/
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
