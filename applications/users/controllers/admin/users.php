<?
namespace admin\users;

class users extends \Admin {

    function default_method()
    {
        if ($_POST['checkLogin'])
        {
            //if (!preg_match(iconv("utf-8","windows-1251",'/^[а-яa-z0-9]{1}[а-яa-z0-9_\-\.]{1,30}@([а-яa-z0-9\-]{1,30}\.{0,1}[а-яa-z0-9\-]{1,5}){1,3}\.[а-яa-z]{2,5}$/i'),mb_strtolower(iconv("utf-8","windows-1251",$_POST['email']))))
            //        $res['error']['email'] = "Адрес почты неверен";
            $q = "SELECT COUNT(id) as cU FROM users WHERE login='{$_POST['checkLogin']}'";
            $q=mysql_query($q);
            $q=mysql_fetch_assoc($q);
            if ($q['cU']<=0)
                echo 'true';
            else echo 'false';
            exit();
        }
               
        if (isset($this->id)){
                $user = $this->getUserById($this->id);
                $roles = $this->getRoles();
                return $this->layout_show('/admin/users/user.html', array('user'=>$user, 'roles'=>$roles));
        }
        else $this->getUsers();
        
    }

    function delete($idUser)
    {
        $idUser = $_GET['id'];
        $err = false;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");        
        $query = "DELETE FROM users WHERE id_user = {$idUser}";
        if (!mysql_query($query)) $err = true;
        $query = "SELECT id_ad FROM adverts WHERE user_id = '{$idUser}'";
        $query = mysql_query($query);
        while ($value = mysql_fetch_assoc($query)) { 
            if (!$this->get_controller("adverts","adverts",TRUE)->delete($value['id_ad'],false)) $err = true;
        }
        if (!$err) mysql_query("COMMIT");
        else mysql_query("ROLLBACK");
        $this->redirect($_SERVER['HTTP_REFERER']);
    }


    

    function getUniq($login)
    {
        $res=microtime();
        $res = md5($res.$login);
        return $res;
    }
    
    function add()
    {
        if ($_POST)
        {
            if ($_POST['isAdmin'])
                $_POST['isAdmin'] = "1";
            else $_POST['isAdmin'] = "0";
            $pass = md5($_POST['password']);
            $uniq = $this->getUniq($_POST['email']);
            $_POST['DOB'] = date("Y-m-d", strtotime($_POST['DOB']));
            $query = "INSERT INTO `users` 
                (`login`, `pass_cashe`) 
                VALUES ('{$_POST['email']}', '{$pass}')";
            echo $query;
            $this->layout = "/source/";
            mysql_query($query) or die(mysql_error());
            echo $message = $this->layout_get("mailTemplate.html", array('completing'=>$uniq));
            send_mail(ADMIN_MAIL, $_POST['email'], "Подтверждение регистрации", $message);
            include_once 'mailNotify.php';
            //auth($_POST['email'], $pass['password']);
            echo "<br/>Ok";

        }
        else if ($_SESSION['user']['admin'])
            return $this->layout_show("/admin/newUser.html"); 
        else $this->redirect("/"); 
    }
    
    function auth($log,$pass)
    {
        $query = "SELECT salt, password, FIO, id_user FROM users WHERE email='".$log."'";
            $query = mysql_query($query);
            if(mysql_num_rows($query)>0)
            {
                $login = mysql_fetch_array($query);
                $md5 = $login['pass'];
                $pwd = $this->get_pass($login['salt'], $pass);
                if ($pwd == $md5)
                {
                    echo "Пользователь ".$login['FIO']." успешно авторизован";
                    $_SESSION['userID']=$login['id_user'];
                }
                else echo "Пользователь с таким именем и паролем не найден";
            }
    }
    
    
    
    function getUsers()
    {
        $query = "SELECT * FROM users";
        if ($_GET['orderBy']) $query .= " order by ".$_GET['orderBy'];
        if ($_GET['sort']) $query .= " ".$_GET['sort'];
        $query = mysql_query($query);
        $res = array();
        while ($row = mysql_fetch_assoc($query)) {
            $res[$row['id']] = $row;
        }
        echo $this->id;
        if ($res)
            $this->layout_show('/admin/users/index.html', array('users'=>$res, 'count'=>count($res)));
        else echo mysql_error ();
        
    }
    
    /*
     * Новые пользователи
     * 
     */
    
    var $user = null;
    
    var $adminID = 1;
    
    function initUser($user){
        if ($user){
            $this->user = $user;
            $this->user['roles'] = $this->getUserRoles();
            $this->user['admin'] = $this->isAdmin();
        }
        else $this->user = null;
    }
    
        
    function getUserByLogin($login){
        if (!isset($login))
            return null;
        
        $query = "SELECT * FROM users WHERE login = '{$login}' limit 1";
        $query = mysql_query($query);
        $user = mysql_fetch_assoc($query);
        $this->initUser($user);
        return $this->user;
    }
    
    function getUserById($id){
        if (!isset($id))
            return null;
        $query = "SELECT * FROM users WHERE id = '{$id}' limit 1";
        $query = mysql_query($query);
        $user = mysql_fetch_assoc($query);
        $this->initUser($user);
        return $this->user;
    }
    
    
    function getUserPassword($password, $solt){
        if ($password)
            return md5($password.$solt);
        else
            return null;
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
    
    function isAdmin($user = null){
        $res = false;
        if(!$user)
            if($this->user){
                if (!$this->user['roles'])
                    $this->getUserRoles();
            }
        else if (!$this->user['roles'])
            $this->getUserRoles();
        
        $arRes = array_search($this->adminID, array_keys($this->roles));
        
        if(isset($arRes))
            $res = true;
        else $res = false;
        
        $this->isAdmin= $res;
        return $res;
    }
    
    function getRoles(){
        $roles = null;
        $query = "SELECT * FROM roles";
        $query = mysql_query($query);
        while ($role = mysql_fetch_array($query)) {
            $roles[$role['id']] = $role;
        }
        return $roles;
    }
    
    function generateSolt($length = 6){
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }
    
    function addRights($roles, $user_id){
        if ($user_id){
            $query = "DELETE FROM user_role WHERE user_id = '{$user_id}'";
            mysql_query($query);
            foreach ($roles as $key => $value) {
                $this->writeRights($user_id, $value);
            }
        }
    }
    
    function writeRights($user_id, $role_id){
        $query = "INSERT INTO user_role (user_id, role_id) VALUES ('{$user_id}', '{$role_id}')";
        $query = mysql_query($query);
        
    }
    
}
?>
