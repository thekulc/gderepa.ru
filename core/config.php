<?php

define('ROOT', dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR);

require_once (ROOT . "config.php");

foreach ($config as $key => $value)
{
    if (isset($_GET['dev_mode']) && $_GET['dev_mode'] == "off" && $key == "dev_mode") $value = false;
    define(strtoupper($key),$value);
}
?>
