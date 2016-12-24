<?
namespace message;

class send extends \Controller {  
    
    function default_method()
    {
        $obj = "";
        $obj = $this->send($_POST);
        return $this->layout_show('index.html', array('object'=>$obj, "page"=>array("title"=>"Отчет о доставке")));
    }
    
    function send($post){
        $isSend = TRUE;
        $email = "SELECT * FROM users where get_message=TRUE";
        $email = mysql_query($email);
        $to = array();
        if (mysql_num_rows($email)>0)
            while ($email_element = mysql_fetch_assoc($email)) {
                $incorrect = explode("@", $email_element['login']);
                if ($incorrect[0] != '')
                    $to[] = $incorrect[0]." <".$email_element['email'].">";
                else $to[] = $email_element['login']." <".$email_element['email'].">";
            }
        else $isSend=null;

        $to = implode(", ", $to);

        $isSend = $this->formValid($post);

        if($isSend==1)
        {
            $post['FIO'] = mysql_real_escape_string($post['FIO']);
            $message = array();
            $message['from'] = SEND_MAIL_ADDRESS;
            $message['to'] = $to;
            $message['subject'] = "New message from ".$_SERVER['HTTP_HOST'];
            $message['message'] = mysql_real_escape_string($post['message']);
            $message['FIO'] = $post['FIO'];
            $message['email'] = mysql_real_escape_string($post['email']);
            $this->newMessage($message);
            $msg = $this->getMessageBody($message);
            $isSend = send_mail($message['from'], $message['to'], $message['subject'] , $msg);
        }

        return $isSend;
    }
    
    function getMessageBody($message){
        $unreadCount = $this->get_controller("message", "message", TRUE)->getCount();
        $body = "";
        
        $body = "<p>На сайте <a href='http://".$_SERVER['HTTP_HOST']."/'>".$_SERVER['HTTP_HOST']."</a> добавлено новое сообщение!</p>";
        $body .= "<p style='margin-bottom: 0'><b>".$message['FIO']." (<a href='mailto:".$message['email']."'>".$message['email']."</a>)</b> написал:</p>";
        $body .= "<p style='margin-top: 0'>".$message['message']."</p>";
        $body .= "<p>Непрочитанных сообщений <b>".$unreadCount."</b>, <a href=\"http://".$_SERVER['HTTP_HOST']."/admin/message/\">посмотреть</a>.</p>";
        
        return $body;
    }
    
    function newMessage($message){
        $query = "INSERT INTO message (FIO, email, message) VALUES (
                '".$message['FIO']."',
                '".$message['email']."',
                '".$message['message']."'
            );";
        return mysql_query($query);
    }
    
    function formValid($post){
        $res = true;
        /*if (!$this->IsCaptchaValid($post))
            $res = "Неверно введен код с картинки<br>";*/
        if (sl($post['FIO'])<=0){
            $res .= "Введите ваше имя<br>";
        }
        if (!$this->IsMailValid($post['email'] = trim($post['email'])))
            $res .= "Email введен некорректно. Проверьте email и повторите попытку.<br>";
        return $res;
    }
    
    function IsCaptchaValid($ADataArray)
    {
        return isset($_SESSION['captcha_keystring']) && isset($ADataArray["captcha"]) 
                && $_SESSION['captcha_keystring'] === $ADataArray["captcha"];
    }
    
    function IsMailValid($AMail){
        return preg_match("/^[a-z0-9_\.-]+@(?:[a-z0-9-]+\.)+[a-z]{2,6}$/i", $AMail);
        
    }
}
?>
