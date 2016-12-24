<?
namespace admin\index;

class index extends \Admin {

    function default_method()
    {
        if($_SESSION['user']['admin']==true){
            return $this->redirect('/admin/users');
        }
        if($_POST){
            $this->get_controller("users","login", true)->auth($errs);
            if ($errs){
                pr($errs);
            }
            elseif ($_SESSION['user']['roles'][1]['role_name'] == 'admin'){
                $_SESSION['user']['admin'] = true;
                return $this->redirect('/admin/users');
            }
            else{
                return $this->redirect('/');
            }
        }
        
        return $this->layout_show('admin/login.html', array('errors' => $errs)); 
    }
    
    function getUsers(){
        $q = "SELECT * FROM `users` WHERE 1";
        $q = mysql_query($q);
        $res = array();
        
        while ($row = mysql_fetch_assoc($q)) {
            $res[] = $row;
        }
        
        
    }
    
    
}
?>
