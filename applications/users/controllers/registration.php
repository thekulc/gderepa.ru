<?
namespace users;

class registration extends \Controller {  
    
    function default_method()
    {
        $errs = null;
        $user = $this->registration($errs);
        if (isset($_GET['backurl']))
        	$this->redirect($_GET['backurl']);
        else{
            $mes = array();
        	if ($errs){
                $mes['message'] = $errs;
        	}
        	elseif ($user){
        		$mes['success'] = "Отлично! Осталось подтвердить email &laquo;" . $user['email'] . "&raquo;, и можно работать";
        	}
        	else{
                $mes['message'] = "Произошла непредвиденная ошибка. регистрация не может быть продолжена. " . implode("<br>", error_get_last());
            }
            echo json_encode($mes);
        }
    }
    
    function registration(&$errs){
    	$registeredUser = null;
        if (is_string(filter_input(INPUT_POST, 'email'))){
        	$user = array();
            $user['email'] = filter_input(INPUT_POST, 'email');
            if (!$this->model()->getUserByEmail($user['email'])){
            	$user['solt'] = $this->generateSolt();
            	$user['pass_cashe'] = $this->get_controller('users', 'login')->getPassword(filter_input(INPUT_POST, 'password'), $user['solt']);
            	$registeredUser = $this->model()->newUser($user);
            }
            else{
            	$errs = "Пользователь с таким адресом уже зарегистрирован";
            }
        }
        else{
            $errs = "Данные для нового пользователя не получены";
        }
        return $registeredUser;
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

}
?>
