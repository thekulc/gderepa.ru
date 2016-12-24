<?
namespace message;

class getCap extends \Controller {  
    
    function default_method()
    {
        echo captchaCreate();
    }
    
    function formValid($post){
        $res = "";
        if (!$this->FormIsValid($post))
            $res = "Капча введена неверно";
        if (strlen($post['FIO'])<=0){
            $res .= "Введите ваше имя";
        }
        return $res;
    }
    
    function FormIsValid($ADataArray)
    {
        return isset($_SESSION['captcha_keystring']) && isset($ADataArray["captcha"]) 
                && $_SESSION['captcha_keystring'] === $ADataArray["captcha"];
    }
}
?>
