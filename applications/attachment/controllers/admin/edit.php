<?php
namespace admin\attachment;

class edit extends \Admin {

    function default_method()
    {
        $_POST["id"] = substr($this->id, 2);
        
        $attachment = (object) $_POST;
        $err = null;
        $attachment->file = $this->getFileObj($_FILES, $err);
        
        if($this->update($attachment, $err))
            $message['text'] = "Файл $attachment->title успешно обновлен";
        else
            $message['text'] = "При обновлении файла $attachment->title произошла ошибка:<br> $err";
        
        
        
        $this->showRes($message);
        
    }
    
    function showRes($msg){
        $msg['href']='/admin/attachment/';
        $this->layout = "/source/";
        $object = $this->layout_get("message.html", array('message'=>$msg));
        $this->layout = "/applications/attachment/layouts/";
        return $this->layout_show('admin/index.html', 
                array('object'=>$object));
    }
            
    function update($attachment, &$err){
        $res = false;
        $editedAttachment = $this->get_controller("attachment", "attachment", true)->get($attachment->id);
        
        if ($attachment->file->name == "" && $editedAttachment->filename == ""){
            $err = "file not found";
            return null;
        }
        
        if ($this->uploadFile($attachment->file)){
            
            if ($attachment->type == "")
                $attachment->type = $attachment->file->type;
            
            $this->removeFile($editedAttachment->filename);
            
            $fileobj->type = mysql_real_escape_string($fileobj->type);
            $fileobj->title = mysql_real_escape_string($fileobj->title);
            $fileobj->description = mysql_real_escape_string($fileobj->description);
            $fileobj->file->name = mysql_real_escape_string($fileobj->file->name);
            
            $query = "UPDATE attachments SET filename = '".$attachment->file->name."', type = '".$attachment->type."', title = '".$attachment->title."', description = '".$attachment->description."'";
        }
        else {
            $fileobj->type = mysql_real_escape_string($fileobj->type);
            $fileobj->title = mysql_real_escape_string($fileobj->title);
            $fileobj->description = mysql_real_escape_string($fileobj->description);
            $fileobj->file->name = mysql_real_escape_string($fileobj->file->name);
            $query = "UPDATE attachments SET type = '".$attachment->type."', title = '".$attachment->title."', description = '".$attachment->description."'";
        }
        
        $query .= " WHERE id = '".$attachment->id."'";
        if (!mysql_query($query))
            $err = mysql_error ();
        else $res = TRUE;
        
        return $res;
    }
    
    function uploadFile($fileobj, &$err = null){
        
        $res = false;
        
        if (!isset($fileobj)){
            $err = 'file not found';
            return null;
        }
        
        $uDir = $this->get_controller("attachment", "attachment", true)->initDirectory();
        
        $uFile = $uDir . DS . $fileobj->name;
        
        if(move_uploaded_file($fileobj->tmp_name, $uFile)){
            $res = true;
        }
        else{
            $res = false;
            $err = error_get_last();
        }
        
        return $res;
        
    }
    
    function removeFile($filename){
        $res = false;
        if ($filename){
            $uDir = $this->get_controller("attachment", "attachment", true)->initDirectory();
            $uFile = $uDir . DS . $filename;
            $res = unlink($uFile);
        }
        return $res;
    }
    
    function getFileobj($files, &$err = null){
        $file = (object) current($files);
        if($file->name == "")
            return null;
        
        $type = explode("/", $file->type);
        $file->lType = $type[0];
        
        if ($file->lType == "image"){
            switch ($type[1]) {
                case "png":
                    $destination = "png";
                    break;
                case "jpeg":
                    $destination = "jpeg";
                    break;
                case "jpg":
                    $destination = "jpg";
                    break;
                default:
                    break;
            }
        }
        else{
            $destination = $type[1];
        }
        
        $name = explode(DS, $file->tmp_name);
        $name = $name[count($name)-1];
        $name = explode(".", $name);
        
        $file->name = $name[0].".".$destination;
        
        unset($file->error);
        unset($file->size);
        
        return $file;
    }
    
}

?>
