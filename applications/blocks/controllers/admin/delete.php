<?
namespace admin\blocks;

class delete extends \Admin {

    function default_method()
    {
        $object = NULL;
        $message['href']="/admin/blocks/";
        $this->layout = "/source/";
        
        if ($_GET['id']){
            $id = $_GET['id'];
            $message = $this->delete($id);
            $object = $this->layout_get("message.html", array('message'=>$message));
        }
        else if($this->id){
            $id = substr($this->id,2);
            $block = $this->get_controller("blocks","blocks",true)->get($id);
            $message['text'] = "Вы хотите удалить страницу <b>".$block['name'].
                "?</b><br>Это действие отменить невозможно.";
            $message['action']="?id=".$block['id'];
            $object = $this->layout_get("confirm.html", array('message'=>$message));
        }
        $this->layout = "/applications/blocks/layouts/";
        $this->layout_show("admin/index.html", array('object'=>$object));
    }
    
    function delete($id){
        $message = null;
        $block = $this->get_controller("blocks","blocks",true)->get($id);
        $query = "DELETE from blocks WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Блок <b>".$block['name']."</b> успешно удален.";
        $message['href'] = "/admin/blocks/";
        
        return $message;
    }
}
?>
