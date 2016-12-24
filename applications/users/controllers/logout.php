<?
namespace users;

class logout extends \Controller {  
    
    function default_method()
    {
    	$this->logout();
        $this->redirect("/");
    }

    function logout(){
    	unset ($_SESSION['user']);
        $expires = time()+60*60*24*30;
        setcookie('usr', "", $expires, '/');
        setcookie('key', "", $expires, '/');
    }
    
}
?>
