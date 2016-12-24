<?
namespace users;

class mailNotify extends \Controller {  
    
    function default_method()
    {
        //pr($this);
        $query = "SELECT id_user, FIO, isAdmin, money FROM users WHERE isCom = '{$_GET['completing']}'";
        $query = mysql_query($query);
        $user = array();
        if (mysql_num_rows($query)>0)
        {
            $user = mysql_fetch_assoc($query);
            $query = "UPDATE `users` SET `isCom` = '1' WHERE `id_user` = '{$user['id_user']}'";
            mysql_query($query);
            $this->set_global("auth", "1");
            $_SESSION['user']['id_user'] = $user['id_user'];
            $_SESSION['user']['FIO'] = $user['FIO'];
            $_SESSION['user']['money'] = $user['money'];
            $_SESSION['user']['admin'] = $user['isAdmin'];
        }
        $this->layout_show('/admin/notifyReport.html',array('user'=>$user));
    }
}
?>