<?
namespace admin\menu;

class delete extends \Admin {

    function default_method()
    {
        $object = NULL;
        $message['href']="/admin/menu/";
        
        if ($_GET['id']){
            $id = $_GET['id'];
            $message = $this->delete($id);
            $object = $this->layout_get("admin/message.html", array('message'=>$message));
        }
        else if($this->id){
            $id = substr($this->id,2);
            $menu = $this->get_controller("menu","menu",true)->get($id);
			
            $message['text'] = "Вы хотите удалить пункт меню <b>".$menu['title'].
                "?</b><br>Это действие отменить невозможно.";
            $message['action']="?id=".$menu['id'];
            $object = $this->layout_get("admin/confirm.html", array('message'=>$message));
        }
        $this->layout_show("admin/menu/index.html", array('object'=>$object));
    }
    
    function delete($id){
        $message = null;
        $menu = $this->get_controller("menu","menu",true)->get($id);
        $query = "DELETE from menu WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Пункт меню <b>".$menu['title']."</b> успешно удален";
        $message['href'] = "/admin/menu/";
        
        return $message;
    }
}
?>
