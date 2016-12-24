<?
namespace admin\message;

class delete extends \Admin {

    function default_method()
    {
        if ($_GET['id']){
            $message = $this->delete($_GET['id']);
            $message['href']="/admin/message/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$message));
            $this->layout = "/applications/message/layouts/";
            $this->layout_show("admin/index.html", array('object'=>$object));
        }
        else if($this->id){
            $id = substr($this->id,2);
            $alias = $this->get_controller("message","message",true)->get($id);
            $alias = $alias[0];
            $mess['text'] = "Вы хотите удалить сообщение от <b>".$alias['FIO']." (".$alias['email'].")</b>.<br>Это действие нельзя будет отменить.";
            $mess['action']="?id=".$alias['id'];
            $mess['href']="/admin/message/";
            $this->layout = "/source/";
            $object = $this->layout_get("confirm.html", array('message'=>$mess));
            $this->layout = "/applications/message/layouts/";
            $this->layout_show("admin/index.html", array('count'=>$this->get_controller("message","message",true)->getCount($id), 'object'=>$object));
        }
    }
    
    function delete($id){
        $message = null;
        $alias = $this->get_controller("message","message",true)->get($id);
        $query = "DELETE from message WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Сообщение от <b>".$alias['FIO']." (".$alias['email'].")</b> успешно удалено";
        return $message;
    }
    
}
?>
