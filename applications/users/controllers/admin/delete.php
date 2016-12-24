<?
namespace admin\users;

class delete extends \Admin {

    function default_method()
    {
        
        if ($_GET['id']){
            $message = $this->delete($_GET['id']);
            $message['href']="/admin/users/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$message));
            $this->redirect("/admin/users/");
        }
        else if($this->id){
            $id = substr($this->id,2);
            $user = $this->get_controller("users","users",true)->getUserById($id);
            
            $mess['text'] = "Вы хотите удалить пользователя <b>".$user['login']."</b> (".$user['FIO'].").<br>Это действие нельзя будет отменить.";
            $mess['action']="?id=".$user['id'];
            $mess['href']="/admin/users/";
            $this->layout = "/source/";
            $object = $this->layout_get("confirm.html", array('message'=>$mess));
            $this->layout = "/applications/users/layouts/";
            $this->layout_show("admin/delete.html", array('count'=>"1", 'object'=>$object));
        }
    }
    
    function delete($id){
        $message = null;
        $query = "DELETE from users WHERE id = '{$id}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else {
            $user = $this->get_controller("users","users",true)->getUserById($id);
            $message['text'] = "Пользователь <b>".$user['login']."</b> успешно удален";
            $query = "DELETE FROM user_role WHERE user_id = '{$id}'";
            if (mysql_query($query))
                $message['text'] .= "<br>Права пользователь успешно удалены";
            else $message['text'] .= "<br>При удалении прав пользователя произошла ошибка: ".mysql_error();
        }
        return $message;
    }
    
}
?>
