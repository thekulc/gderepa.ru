<?
namespace admin\page;

class delete extends \Admin {

    function default_method()
    {
        $object = NULL;
        $message['href']="/admin/page/";
        //$this->layout = "/source/";
		
        if ($_GET['id']){
            $id = $_GET['id'];
            $message = $this->delete($id);
            $object = $this->layout_get("admin/message.html", array('message'=>$message));
        }
        else if($this->id){
            $path = "uploads".DS."files".DS."images".DS;
            $id = $this->id;
            $field = $this->get_controller("page","page",true)->get($id);
            $this->get_controller("page", "edit", true)->fileRemove($path, $field['icon']);
            $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/page/id".$id);
            $menu = $this->get_controller("menu","menu",true)->getFromAddress("/page/id".$id);
            if(!isset($alias[0]['id']))
                $alias[0]['alias'] = "нет";
            $message['text'] = "Вы хотите удалить страницу <b>".
                    $field['title']."</b>, синоним <b>".
                    $alias[0]['alias']."</b> и пункт меню <b>".
                    $menu['title']."</b>.
                <br>Это действие отменить невозможно.";
            $message['action']="?id=".$field['id'];
            $object = $this->layout_get("admin/confirm.html", array('message'=>$message));
        }
        $this->layout_show("admin/page/index.html", array('data'=>$object));
    }
    
    function delete($id){
        $message = null;
        $page = $this->get_controller("page","page",true)->get($id);
        $query = "DELETE from pages WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Страница <b>".$page['title']."</b> успешно удалена";
        
        $message['href'] = "/admin/page/";
        
        $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/page/id".$id);
        $menu = $this->get_controller("menu","menu",true)->getFromAddress("/page/id".$id);
        
        if ($alias[0]['id']){
            $mess = $this->get_controller('aliases','delete',true)->delete($alias[0]['id']);
            $message['text'] .= "</br>".$mess['text'];
        }
        if ($menu['id']){
            $mess = $this->get_controller('menu','delete',true)->delete($menu['id']);
            $message['text'] .= "</br>".$mess['text'];
        }
        
        return $message;
    }
}
?>
