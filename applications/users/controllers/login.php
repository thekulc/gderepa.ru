<?
namespace users;

class login extends \Controller{
	function default_method(){
		$errs = null;
		$this->auth($errs);
        $result['errors'] = $errs;
		if (!$errs && isset($_GET['backurl']))
			$this->redirect($_GET['backurl']);
		else
			echo json_encode($result);
        
	}

	function auth(&$errs){
        $auto_auth = filter_input(INPUT_POST, 'auto_auth') === 'on' ? true : false;
        $is_cookie = false;
        if (is_string(filter_input(INPUT_POST, 'email'))){
            $email = filter_input(INPUT_POST, 'email');
            $pass = filter_input(INPUT_POST, 'password');
        }
        elseif (filter_input(INPUT_COOKIE, 'usr') && filter_input(INPUT_COOKIE, 'key')){
            $email = filter_input(INPUT_COOKIE, 'usr');
            $pass = filter_input(INPUT_COOKIE, 'key');
            $is_cookie = true;
        }
        $_SESSION['user'] = $this->loginByEmail($email, $pass, $auto_auth, $is_cookie, $errs);
        return $_SESSION['user'];
    }
    
    function loginByEmail($email, $password, $auto_auth, $cookieLogin = false, &$errs){
        $errs = null;
        $user = null;
        if ($email == "") $errs[] = "Логин не может быть пустым";
        if ($password == "") $errs[] = "Пароль не может быть пустым";
        
        if (!$errs){
            $user = $this->model()->getUserByEmail($email);
            if($user){
                
                if (!$cookieLogin)
                    $password = $this->getPassword($password, $user['solt']);
                
                if($password == $user['pass_cashe']){
                    
                    if($auto_auth)
                        $this->autoAuth($user['email'], $user['pass_cashe']);
                }
                else{
                	$user = null;
                    $errs[] = "Логин или пароль некорректен";
                }
            }
            else $errs[] = 'Пользователь с таким логином не зарегистрирован: '.$email;
        }
        return $user;
    }
    
    private function autoAuth($email, $pass_hash) {
        $expires = time()+60*60*24*30;
        setcookie('usr', $email, $expires, '/');
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