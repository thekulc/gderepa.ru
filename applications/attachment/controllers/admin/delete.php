<?php
namespace admin\attachment;

class delete extends \Admin {
    
    function default_method(){
        $this->chooseAction($_GET);
    }
    
    function chooseAction($get){
        $id = substr($this->id,2);
        $attachment = $this->get_controller("attachment", "attachment", true)->get($id);
        if ($get['id']){
            if ($this->delete($attachment, $err))
                $message['text'] = 'Файл "'.$attachment->filename.'" успешно удален.';
            else
                $message['text'] = "При удалении файла произошла ошибка:<br>".$err;
            $this->showDialog($message, 2);
        }
        elseif ($id){
            $message['text'] = "Вы хотите удалить файл <b>".$attachment->filename.
                "?</b>";
            $message['action']="?id=".$attachment->id;
            
            $this->showDialog($message, 1);
        }
    }
    
    function delete($attachment, &$err){
        $res = false;
        if ($attachment->id){
            if ($this->get_controller("attachment", "edit", true)->removeFile($attachment->filename, $err)){
                $query = "DELETE FROM attachments WHERE id='".$attachment->id."'";
                if(mysql_query($query))
                    $res = true;
                else
                    $err = mysql_error ();
            }
        }
        else
            $err = "file not found";
        return $res;
    }
    
    function showDialog($message, $step = 1){
        $this->layout = "/source/";
        $message['href'] = "/admin/attachment/";
        switch ($step) {
            case 1:
                
                $object = $this->layout_get("confirm.html", array('message'=>$message));
                
                break;
            case 2:
                
                $object = $this->layout_get("message.html", array('message'=>$message));
                
                break;
            default:
                
                break;
        }
        $this->layout = "/applications/attachment/layouts/";
        $this->layout_show("admin/index.html", array('object'=>$object));
    }
    
}
?>
