<?php
$config = array();

//engine
$config['caching']			= 0;
$config['dev_mode']			= 0;
$config['alias']			= 1;
$config['admin_mail']		= 'thekulc@gmail.com';
$config['excluded_folders']	= 'source,uploads,templates';
$config['layout']			= 'Default';

//$config['socialAuth']['client_id']		= "4901558";
//$config['socialAuth']['client_secret']	= "oSuJ5AOUeUriNdDeRIUK";
//$config['socialAuth']['redirect_uri']	= "http://rs/users/auth";

//database
$config['host']				= 'mysql';
$config['database']			= 'gderepa';
$config['user']				= 'gderepausr';
$config['password']			= 'AdGpPN2txDxFe7Ch';
$config['charset']			= 'utf8';

//kulc
$config['FROM_NAME']		= 'Little Byte';
$config['FROM_MAIL']		= 'contact@littlebyte.co';
$config['SEND_MAIL_ADDRESS']= 'contact@littlebyte.co';
$config['TOKEN']			= 'token';

$config['ASEP'] = "~";
$config['DS'] = DIRECTORY_SEPARATOR;
$config['ADMIN_ROOT'] = dirname(__FILE__) . $config['DS'] . "admin" . $config['DS'];
$config['WEBROOT'] = $_SERVER['HTTP_HOST'];

?>
