<?
namespace admin\message;

class answer extends \Admin {

    function default_method()
    {
        if (isset($_GET['id']))
            $this->sendMail($_POST['answer']);
        else if($this->id){
            $id = substr($this->id,2);
            $mess = $this->get_controller("message","message",true)->get($id);
            $mess = $mess[0];
            $this->layout = "/source/";
            $mess['answer'] = $_POST['answer'];
            $object = $this->layout_get("mail_template.html", array('message'=>$mess, 'url'=>$_SERVER['HTTP_HOST']));
            $mess['text'] = $this->sendMail($mess, $object);
            $mess['action']="?id=".$mess['id'];
            $mess['href']="/admin/message/";
            
            $object = $this->layout_get("message.html", array('message'=>$mess));
            $this->layout = "/applications/message/layouts/";
            $this->layout_show("admin/index.html", array('count'=>$this->get_controller("message","message",true)->getCount($id), 'object'=>$object));
        }
    }
    
    function answer($id = null, $answer = ''){
        if ($id <= 0)
            return null;
        $answer = mysql_real_escape_string($answer);
        $query = "UPDATE message set answer = '{$answer}' where id = '{$id}'";
        mysql_query($query);
        if ($query != true)
            return false;
        else return true;
    }
    
    function sendMail($mess, $mail){
        
        $to = $mess['email'];
        
        $from['name'] = FROM_NAME;
        $from['mail'] = FROM_MAIL;
        
        $subject = "Ответ специалистов компании \"Master Media\"";
        $message = $mail;
        $this->answer($mess['id'],$mail);
        if(send_mail($from, $to, $subject, $message)){
                $mess = "Ваше сообщение для <b>".$mess['FIO']." (".$mess['email'].")</b> успешно отправлено.";
        }
        else{
            $err = error_get_last();
            $err=$err['message'];
            $mess = "Во время отправки сообщениря произошла ошибка: ".  $err;
        }
        $mess.="<br><hr><b>Ваше сообщение:</b><br><i><br>Кому:<br>".$to."<br>Тема:<br>".$subject."<br>Сообщение:<br>".$message."</i>";
        
        return $mess;
    }
    
}
?>
