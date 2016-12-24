<?
namespace users;

class users extends \Controller {  
    
    function default_method()
    {
        if (isset($this->id))
        {
            $user = $this->model()->getUserById($id);
            $this->layout_show("users/user_page.html",array('user'=>$user,'catTitle'=>$user['login']." (".$user['FIO'].")"));
        }
        else{
            
        }
        
    }

    function initUser($user){
        if ($user){
            $this->user = $user;
            $this->user['roles'] = $this->getUserRoles();
            $this->user['admin'] = $this->isAdmin();
        }
        else $this->user = null;
    }
    
    private function getUserRoles(){
        $res = null;
        if($this->user){
            $query = "SELECT *, (select name from roles where id = role_id) as role_name FROM `user_role` WHERE user_id = '".$this->user['id']."'";
            $query = mysql_query($query);
            $res = array();
            while ($role = mysql_fetch_assoc($query)) {
                $res[$role['role_id']] = $role;
            }
        }
        $this->roles = $res;
        return $res;
    }
}
?>
