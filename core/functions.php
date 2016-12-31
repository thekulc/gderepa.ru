<?php

function __autoload($class){    
    if(!defined('DEV_MODE') || !DEV_MODE) Controller::error_page(404);
    else debug("Класс {$class} не найден",true);
}

function pr($a) {
    if (is_array($a) || is_object($a)) {
        echo "<pre>";
        print_r($a);
        echo "</pre>";        
    }
    
    else echo "<pre>{$a}</pre>";    
}



function debug($text,$critical=false,$mail=false) {
    if ($mail) write_log($text);
    else if(defined('DEV_MODE') && DEV_MODE) pr($text);
    
    else write_log($text);
    if ($critical) exit();
}

function send_mail($from, $to, $subject, $message){
    $headers="MIME-Version: 1.0\r\n";
    $headers.="Content-type: text/html; charset=utf-8\r\n";
    $from_name = $from['name'];
    $from_addr = $from['mail'];
    
    $headers.="From: ".$from_name." <".$from_addr.">\r\n";
    $headers.="Return-path: <{$from_addr}>\r\n";
    
    if (mail($to, $subject, $message, $headers, "-f{$from['mail']}")){
        return true;
    }
    else debug("Письмо к {$to} не отправлено",false,"mail");
}

function send_debug_mail($text){
    $res = "<b>Ошибка на странице:</b> ".WEBROOT.$_SERVER['REQUEST_URI']."<br><br>";
    $res .= $text."<br><br>";
    if($_SERVER['REMOTE_ADDR']) $res.="<b>IP:</b> ".$_SERVER['REMOTE_ADDR']."<br>";
    $user=$_SESSION['user']?$_SESSION['user']->login:$_SERVER['PHP_AUTH_USER'];
    if($user) $res.="<b>Пользователь:</b> ".$user."<br>";
    if($_SERVER['HTTP_USER_AGENT']) $res.="<b>Агент:</b> ".$_SERVER['HTTP_USER_AGENT']."<br>";
    if($_SERVER['HTTP_REFERER']) {
        $url=parse_url($_SERVER['HTTP_REFERER']);
        $res.="<b>Переход с:</b> <a href='".$_SERVER['HTTP_REFERER']."'>".$url['host']."</a><br><br>";
    }
    if($_SERVER['REQUEST_TIME']) $res.="<b>Зафиксировано:</b> ".date("d.m.Y, H:i:s",$_SERVER['REQUEST_TIME'])."<br><br>";
    if($_REQUEST) {
        $res.="<b>REQUEST:</b> <br>";
        foreach ($_REQUEST as $key=>$value) $res.="[{$key}] = {$value}<br>";
        $res.="<br><br>";
        
    }
    $web=parse_url(ROOT);
    send_mail("errors@{$web['host']}", ADMIN_MAIL, 'ERROR '.$_SERVER['REQUEST_URI'], $res);
}

function write_log($text='',$filename=''){
    if($filename) unset($GLOBALS['open_log']);
    else $filename = $GLOBALS['open_log'] ? $GLOBALS['open_log'] : date("d.m.Y");
    
    if(is_file(ROOT."logs".DS.$filename.".log")) $res = file_get_contents(ROOT."logs".DS.$filename.".log");
    $ip = $_SERVER['REMOTE_ADDR'];
    $res = date("d.m.Y, H:i:s")." ({$ip})\t {$text}\r\n".$res;
    file_put_contents(ROOT."logs".DS.$filename.".log", $res);
}

//обрезание текста до нужного количества символов
function cut($text,$size=256){
    $text=iconv("utf-8","windows-1251",htmlspecialchars_decode($text));
    $text=strip_tags(str_replace(array("\n","\t","\r"),"",$text));
    $length=strlen($text);
    if ($length<=$size) return iconv("windows-1251","utf-8",$text);
    else return iconv("windows-1251","utf-8",trim(substr($text,0,$size-3)).'...');
}

function make_path($path)
{
    $ar_replace = array("/","\\");
    $path = str_replace($ar_replace, DS, $path);
    
    return ROOT.$path;
}

function get_pass($pass,$num=false)
{
    if ($pass == '') return false;
    if (!$num) $num = 5;

    $new = array(
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','!','@','#','$','%','^','&','*','1','2','3','4','5','6','7','8','9','0'
    );
    $anti = array();
    shuffle($new);
    for ($i = 0; $i < $num; $i++) {
            $anti[] = $new[$i];
    }
    $salt = implode($anti);
    $new_pass = md5(md5($pass).md5($salt));
    return array('salt' => $salt,'password' => $new_pass);
}

function curl_get_query($url) {
    $ch = curl_init();
     curl_setopt ($ch, CURLOPT_URL, $url);
     curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch,CURLOPT_TIMEOUT,5);
     $result = curl_exec ($ch);
     curl_close($ch);
     return $result;
}

function DBQuery($AConnection, $AQuery, $APlaceholders=array())
{
    if (is_array($APlaceholders))
    {
        foreach ($APlaceholders as $lKey => $lVal)
        {        
            $APlaceholders[$lKey] = '"' . mysql_real_escape_string($lVal, $AConnection) . '"';
        }
    }
    
    $lQry = strtr($AQuery, $APlaceholders);
       
    return mysql_query($lQry);
}

function sl($AString)
{
    if (function_exists("mb_strlen"))
    {
        return mb_strlen($AString, "utf-8");
    }   
    else
    {
        return strlen($AString);
    }    
}


function array_to_object($array = array()) {
    if (!empty($array)) {
        $data = false;
        foreach ($array as $akey => $aval) {
            $data -> {$akey} = $aval;
        }
        return $data;
    }
    return false;
}

function objectToString($obj, $exclude = array(), $separateKeyValue = " = ", $separateObjects = ", "){
        $res = "";
        $tmp = (array) $obj;
        foreach ($tmp as $key => $value) {
            if(!in_array($key, $exclude))
                $res .= $key . $separateKeyValue . "'" . mysql_real_escape_string($value) . "'" . $separateObjects;
        }
        return substr($res, 0, strlen($res)-2);
    }

?>