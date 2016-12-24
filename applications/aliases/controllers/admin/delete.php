<?
namespace admin\aliases;

class delete extends \Admin {

    function default_method()
    {
        
        if ($_GET['id']){
            $message = $this->delete($_GET['id']);
            $message['href']="/admin/aliases/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$message));
            $this->layout = "/applications/aliases/layouts/";
            $this->layout_show("admin/index.html", array('object'=>$object));
        }
        else if($this->id){
            $id = substr($this->id,2);
            $alias = $this->get_controller("aliases","aliases",true)->getFullAliases($id);
            $alias = $alias[0];
            $mess['text'] = "Вы хотите удалить синоним <b>".$alias['alias']."</b>.<br>Это действие нельзя будет отменить.";
            $mess['action']="?id=".$alias['id'];
            $mess['href']="/admin/aliases/";
            $this->layout = "/source/";
            $object = $this->layout_get("confirm.html", array('message'=>$mess));
            $this->layout = "/applications/aliases/layouts/";
            $this->layout_show("admin/index.html", array('count'=>"1", 'object'=>$object));
        }
    }
    
    function delete($id){
        $message = null;
        $alias = $this->get_controller("aliases","aliases",true)->getFullAliases($id);
        $query = "DELETE from aliases WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Синоним <b>".$alias[0]['alias']."</b> успешно удален";
        return $message;
    }
    
    function deleteByAddress($address){
        $message = null;
        $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress($address);
        $query = "DELETE from aliases WHERE id = '{$alias[0]['id']}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Синоним <b>".$alias[0]['alias']."</b> успешно удален";
        return $message;
    }
    
}
?>
