<?
namespace admin\blocks;

class add extends \Admin {

    function default_method()
    {
        $object = null;
        if($_POST){
            $message = $this->add($_POST);
            $message['href'] = "/admin/blocks/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$message));
        }
        else 
            $object = $this->layout_get("admin/add.html");
        
        $this->layout = "/applications/blocks/layouts/";
        return $this->layout_show('admin/index.html', 
                array('object'=>$object));
    }
    
    function add($post){
        $message = null;
        if (isset($post['isPub']))
                $post['isPub'] = TRUE;
        else $post['isPub'] = FALSE;
        $post['body'] = mysql_real_escape_string($post['body']);
        $query = "INSERT INTO blocks (name, isPub, body)
            VALUES ('{$post['name']}', '{$post['isPub']}', '{$post['body']}')";
        $query = mysql_query($query);
        if (mysql_error($query))
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] .= "Блок <b>".$post['name']."</b> успешно добавлен.";
        return $message;
    }
    
}
?>
