<?
namespace admin\taxonomy;

class edit extends \Admin {

    function default_method()
    {
        $object = null;
        if($_POST){
            $_POST['id'] = substr($this->id,2);
            $object = $this->edit($_POST);
            $object['href'] = "/admin/taxonomy/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$object));
            $this->layout = "/applications/taxonomy/layouts/";
            return $this->layout_show('admin/mess.html', array('data'=>$object));
        }
        else{
            $this->redirect("/admin/taxonomy/id".substr($this->id,2));
        }
        
    }
    
    function edit($post){
        $message = "";
        
        $vals = objectToString($post, array('id', 'alias'));
        
        $query = "UPDATE taxonomy SET {$vals} WHERE id = ".$post['id'];
        $query = mysql_query($query);
        
        if (mysql_error())
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else{
            $message['text'] .= "Термин <b>\"".$post['name']."\"</b> успешно изменен.";
            $this->get_controller("aliases","delete",true)->deleteByAddress("/taxonomy/id".$post['id']);
            $old_alias = $this->get_controller("aliases","aliases",true)->getAliasFromAlias($post['alias']);
            if ($old_alias[0]['alias'] == "" && $post['alias'] != "")
            {
                $alias = $old_alias[0];
                $alias['alias'] = $post['alias'];
                $alias['address'] = "/taxonomy/id".$post['id'];
                $mess = $this->get_controller("aliases","add", true)->add($alias);
            }
            else{
                $mess['text'] = "Синоним добавлен не был. <a href='/admin/aliases/'>Все синонимы</a>, <a href='/admin/taxonomy/'>таксономия</a>";
            }
           
            if ($mess['text']!="")
                $message['text'] .= "<br>".$mess['text'];
            
        }
        
        
        
        
        
        return $message;
    }
}
?>
