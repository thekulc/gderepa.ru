<?
namespace admin\users;

class add extends \Admin {

    function default_method($new)
    {
        if ($_POST)
        {
            $res = $this->addUser($_POST);
            if($res)
                pr($res);
            else $this->redirect('/admin/users/');
            
        }
        else{
            
            $roles = $this->get_controller("users", "users", TRUE)->getRoles();
            return $this->layout_show("admin/users/add.html", array('roles'=>$roles));
        }
        
    }
    
    function addUser($post){
        
        $rePost = array();
        foreach ($post as $key => $value) {
            if ($key!='roles' && $key != "password")
                $rePost[$key] = mysql_real_escape_string ($value);
        }
        
        $solt = $this->get_controller("users", "users", true)->generateSolt();
        $post['password'] = mysql_real_escape_string($post['password']);
        $rePost['pass_cashe'] = $this->get_controller("users", "users", true)->getUserPassword($post['password'],$solt);
        $rePost['solt'] = $solt;
        
        $query = "INSERT INTO users set ";
        foreach ($rePost as $key => $value) {
            $query .= $key." = '".$value."', ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        
        if (mysql_query($query)){
            $user_id = mysql_insert_id();
            $this->get_controller("users", "users", true)->addRights($post['roles'], $user_id);
        }
        return mysql_error();
    }
    
    
}
?>
