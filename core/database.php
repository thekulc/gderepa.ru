<?php        
 
mysql_connect (HOST, USER, PASSWORD) or die ("Соединение c базой не установлено!");
mysql_select_db(DATABASE) or die("База данных не найдена");
mysql_query("SET NAMES ".CHARSET);

/*
$mysqli = new mysqli(HOST, USER, PASSWORD);
if ($mysqli->connect_errno) die ("Соединение c базой не установлено!");
*/

/*
require_once (ROOT . "core/ModelSafeClass.php");
$opts['user'] = USER;
$opts['pass'] = PASSWORD;
$opts['db'] = DATABASE;
$opts['charset'] = CHARSET;
$opts['host'] = HOST;
$_db = new SafeMySQL($opts);*/

?>
