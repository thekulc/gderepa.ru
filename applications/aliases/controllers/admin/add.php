<?
namespace admin\aliases;

class add extends \Admin {

    function default_method()
    {
        if ($_POST){
            $message = $this->add($_POST);
            $message['href'] = '/admin/aliases/';
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$message));
        }
        else 
            $object = $this->layout_get("admin/add.html");
        
        $this->layout = "/applications/aliases/layouts/";
        $this->layout_show("admin/index.html", array('count'=>"1", 'object'=>$object));
    }
    
    function add($post){
        $message = null;
        if ($post){
            $post["alias"] = mysql_real_escape_string($post["alias"]);
            $post["address"] = mysql_real_escape_string($post["address"]);
            $query = "INSERT INTO aliases (alias, address) 
                VALUES ('{$post["alias"]}', '{$post["address"]}')";
            $query = mysql_query($query);
            if ($query!=true)
                $message['text'] = "Произошла ошибка: ".mysql_error();
            else $message['text'] .= "Синоним <b>".$post['alias']."</b> успешно добавлен.";
        }
        return $message;
        
    }
}
?>
