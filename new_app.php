<?php
header('Content-Type: text/html; charset=utf-8');
if (@!$argv[1])
{
    echo "
        <form method='get'>
        Имя приложения: <input type='text' name='app_name' />
        <input type='submit' value='Создать' />
        </form>        
        ";
    if (@$_GET['app_name'] != "") new_application("applications/".$_GET['app_name'],$_GET['app_name'],true);    
}
else new_application("applications/".$argv[1],$argv[1],true);   

function new_application($application,$app_name,$first,$mdir = false)
{
    if (!$mdir) $mdir = "core/repository/blank";

    if(!is_dir($application)) mkdir($application, 0777);
    else if ($first)
    {
        echo "Такое приложение уже создано"."<br>";
        exit();
    }

    $dir = opendir($mdir);

    while(false !== ($check = readdir($dir)))
    {
        if($check != '.' && $check != '..')
        {
            if(is_dir($mdir .'/'. $check))
            {          
                //$check = str_replace("blank", $application, $check);
                mkdir($application .'/'. $check, 0777);
                new_application($application .'/'. $check, $app_name, false, $mdir .'/'. $check);
            } 
            elseif(is_file($mdir .'/'. $check))
            {
                copy($mdir .'/'. $check, $application .'/'. $check);
                $new_name = str_replace("%blank%",$app_name,$check);
                rename($application .'/'. $check, $application .'/'. $new_name);   

                $text = file_get_contents($application .'/'. $new_name);
                $text = str_replace("%blank%",$app_name,$text);
                file_put_contents($application .'/'. $new_name, $text, LOCK_EX);
            }
        } 
    }
}

?>