<?
namespace admin\users;

class edit extends \Admin {

    function default_method()
    {
        if (!$_POST && !$this->id) return;
        
        $idU = substr($this->id,2);
        $k = implode(", ", array_keys($_POST));
        $arr = array();
        
        
        if ($_POST['password']!=""){
            $_POST['password'] = mysql_real_escape_string($_POST['password']);
            $_POST['pass_cashe']=$this->changePassword($idU, $_POST['password']);
        }
        
        if ($_POST['get_message'])
            $_POST['get_message'] = "1";
        else $_POST['get_message'] = "0";
        
        unset ($_POST['password']);
        
        foreach ($_POST as $k => $v){
            if($k!="roles")
                $arr[] = "{$k}='{$v}'";
        }
        
        $arr = implode(", ", $arr);
        
        $query = "UPDATE users SET {$arr} WHERE id = '{$idU}'";
        if (!mysql_query($query)){
            return;
        }
        else {
            $this->get_controller ("users", "users", true)->addRights($_POST['roles'], $idU);
        }
        $this->redirect("/admin/users/");
    }
    //4297f44b13955235245b2497399d7a93
    
    function changePassword($id, $newPass){
        $res = null;
        if ($id && $newPass){
            $user = $this->get_controller("users", "users", true)->getUserById($id);
            $res = $this->get_controller("users", "users", true)->getUserPassword($newPass,$user['solt']);
        }
        return $res;
    }
}
?>