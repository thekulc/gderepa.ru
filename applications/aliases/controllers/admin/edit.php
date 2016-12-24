<?
namespace admin\aliases;

class edit extends \Admin {

    function default_method()
    {
        if ($_POST){
            $_POST['id'] = $id = substr($this->id,2);
            $message = $this->edit($_POST);
        }
        $message['href']='/admin/aliases/';
        $this->layout = "/source/";
        $object = $this->layout_get("message.html", array('message'=>$message));
        $this->layout = "/applications/aliases/layouts/";
        $this->layout_show("admin/index.html", array('count'=>"1", 'object'=>$object));
    }
    
    function edit($post){
        $message = null;
        $post['alias'] = mysql_real_escape_string($post['alias']);
        $post['address'] = mysql_real_escape_string($post['address']);
        $query = "UPDATE aliases SET alias = '{$post['alias']}', address='{$post['address']}' WHERE id ='{$post['id']}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Синоним <b>'{$post['alias']}'</b> успешно изменен";
        return $message;
    }
    
    function addEdit(){
        
    }
    
}
?>
