<?php
namespace admin\attachment;

class add extends \Admin {
    
    function default_method(){
        if($_POST){
            $fileobj = (object)$_POST;
            $fileobj->file = $this->get_controller("attachment", "edit", true)->getFileobj($_FILES, $err);
            if($this->add($fileobj, $err))
                $msg['text'] = 'Файл "'.$fileobj->file->name.'" успешно добавлен.';
            else
                $msg['text'] = 'При добавлении файла приозошла ошибка: <br>'.$err;
                
            $this->get_controller("attachment", "edit", true)->showRes($msg);
        }
        else{
            $this->layout_show("admin/form-add-view.html");
        }
        
    }
    
    function add($fileobj, &$err){
        $res = false;
        if ($fileobj->file){
            
            if ($fileobj->type == "")
                $fileobj->type = $fileobj->file->type;
            
            if ($fileobj->title == "")
                $fileobj->title = $fileobj->file->name;
            
            if($this->get_controller("attachment", "edit", true)->uploadFile($fileobj->file, $err)){
                
                $fileobj->type = mysql_real_escape_string($fileobj->type);
                $fileobj->title = mysql_real_escape_string($fileobj->title);
                $fileobj->description = mysql_real_escape_string($fileobj->description);
                $fileobj->file->name = mysql_real_escape_string($fileobj->file->name);
                
                $query = "INSERT INTO attachments (filename, type, title, description)
                    VALUES ('".$fileobj->file->name."', '".$fileobj->type."', '".$fileobj->title."', '".$fileobj->description."')";
                
                if(mysql_query($query))
                    $res = true;
                else
                    $err = mysql_error ();
            }
        }
        return $res;
    }
    
}
?>
