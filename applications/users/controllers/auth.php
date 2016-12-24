<?
namespace users;

class auth extends \Controller {  
    
    function default_method()
    {
        if($_POST)
        {
            if ($_POST['login'] == "") $res['error']['email'] = "Логин не может быть пустым";
            if ($_POST['password'] == "") $res['error']['password'] = "Пароль не может быть пустым";
            
            if (!$res['error'])
            {
                $login = mysql_real_escape_string($_POST['login']);
                if ($u = mysql_fetch_assoc(mysql_query("select id, login, pass_cashe, isAdmin from users where login='{$login}'")))
                {
                    if (md5($_POST['password']) == $u['pass_cashe'])
                    {
                        $this->set_global("auth", "1");
                        $_SESSION['user']['id'] = $u['id'];
                        $_SESSION['user']['login'] = $u['login'];
                        $_SESSION['user']['admin'] = $u['isAdmin'];
                        $alias = get_alias_from_node($u['id'], "users");
                        if (array_count_values($alias)>0)
                            $_SESSION['user']['alias'] = "~".$alias['alias'];
                        else $_SESSION['user']['alias'] = "id".$u['id'];

                        $this->redirect($_SERVER['HTTP_REFERER']);
                        
                    }
                    else $res['error']['no'] = "Данные неверны";
                }
                else $res['error']['no_found'] = "Пользователь с таким логином не найден";
            }
        }
        echo json_encode($res);
    }
    
    function auth($log, $pass)
    {
        if ($log == "") $res['error']['email'] = "Логин не может быть пустым";
            if ($pass == "") $res['error']['password'] = "Пароль не может быть пустым";
            
            if (!$res['error'])
            {
                $login = mysql_real_escape_string($log);
                if ($u = mysql_fetch_assoc(mysql_query("select id_user,FIO,salt,password,money from users where email='{$login}'")))
                {
                    if (md5(md5($pass).md5($u['salt'])) == $u['password'])
                    {
                        $this->set_global("auth", "1");
                        $_SESSION['user']['id_user'] = $u['id_user'];
                        $_SESSION['user']['FIO'] = $u['FIO'];
                        $_SESSION['user']['money'] = $u['money'];
                        $_SESSION['user']['admin'] = $u['isAdmin'];
                        $this->layout = "/";
                        $res['success'] = $this->layout_get("/source/authPanel.html", array('user'=>$_SESSION['user']));
                    }
                    else $res['error']['no'] = "Данные неверны";
                }
                else $res['error']['no_found'] = "Пользователь с таким логином не найден";
            }
    }
}
?>
