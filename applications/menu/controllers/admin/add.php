<?
namespace admin\menu;

class add extends \Admin {  
    
    function default_method()
    {
        $object = null;
        if($_POST){
            $message = $this->add($_POST);
            $message['href'] = "/admin/menu/";
            $object = $this->layout_get("admin/message.html", array('message'=>$message));
        }
        else 
            $object = $this->layout_get("admin/menu/add.html");
        
        return $this->layout_show('admin/menu/index.html', 
                array('object'=>$object));
    }
    
    function add($post){
        $message = "";
        $post['alias_id'] = "";
        $post['title'] = mysql_real_escape_string($post['title']);
        $post['address'] = mysql_real_escape_string($post['address']);
        if ($post['image']){
            
        }
            
        
        
        $query = "INSERT INTO menu (title, address, position, alias_id)
            VALUES ('{$post['title']}', '{$post['address']}', '{$post['position']}', '{$post['alias_id']}')";
        $query = mysql_query($query);
        if (mysql_error($query))
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] .= "Пункт меню <b>".$post['title']."</b> успешно добавлен.";
        return $message;
    }
    
    function uploadImage($files){
        
    }
    
}
?>
