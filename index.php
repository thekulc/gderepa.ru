<?php # Core version 1.2. Add models, layouts moved to templates/Default. Update config.php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
if(!session_id()) session_start();

$zone = @$_SESSION['user']['timezone'] ? $_SESSION['user']['timezone'] : "Europe/Moscow";
date_default_timezone_set($zone);

if((defined('DEV_MODE') && DEV_MODE==1) OR isset($_GET['debug'])) 
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
else 
	error_reporting(0);

require_once('core/functions.php');
require_once('core/config.php');
require_once('core/database.php');
require_once('core/model.php');
require_once('core/router.php');

?>
