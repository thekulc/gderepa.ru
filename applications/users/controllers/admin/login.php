<?
namespace admin\users;

class login extends \Admin {

    function default_method($new)
    {
        
    }

    function auth(&$errs){
        $auto_auth = filter_input(INPUT_POST, 'auto_auth') === 'on' ? true : false;
        $is_coockie = false;
        if (is_string(filter_input(INPUT_POST, 'login'))){
            $login = filter_input(INPUT_POST, 'login');
            $pass = filter_input(INPUT_POST, 'password');
        }
        elseif (filter_input(INPUT_COOKIE, 'usr') && filter_input(INPUT_COOKIE, 'key')){
            $login = filter_input(INPUT_COOKIE, 'usr');
            $pass = filter_input(INPUT_COOKIE, 'key');
            $is_coockie = true;
        }
        $_SESSION['user'] = $this->login($login, $pass, $auto_auth, $is_coockie, $errs);
        return $_SESSION['user'];
    }
    
    function login($login, $password, $auto_auth, $cookieLogin = false, &$errs){
        $errs = null;
        $user = null;
        if ($login == "") $errs['login'] = "Логин не может быть пустым";
        if ($password == "") $errs['password'] = "Пароль не может быть пустым";
        
        if (!$errs){
            $user = $this->model()->getUserByLogin($login);
            
            if($user){
                
                if (!$cookieLogin)
                    $password = $this->getPassword($password, $user['solt']);
                
                if($password == $user['pass_cashe']){
                    
                    if($auto_auth)
                        $this->autoAuth($user['login'], $user['pass_cashe']);
                }
                else{
                    $user = null;
                    $errs['noauth'] = "Логин или пароль некорректен";
                }
            }
            else $errs['no_found'] = 'Пользователь с таким логином не зарегистрирован';
        }
        return $user;
    }
    
    private function autoAuth($login, $pass_hash) {
        $expires = time()+60*60*24*30;
        setcookie('usr', $login, $expires, '/');
        setcookie('key', $pass_hash, $expires, '/');
    }
    
    function getPassword($password, $solt){
        if ($password)
            return md5($password.$solt);
        else
            return "";
    }

}
?>
