<?
namespace admin\taxonomy;

class add extends \Admin {

    function default_method()
    {
        $object = null;
        if($_POST){
            $object = $this->add($_POST);
            $object['href'] = "/admin/taxonomy/";
            $this->layout = "/source/";
            $object = $this->layout_get("message.html", array('message'=>$object));
            $this->layout = "/applications/taxonomy/layouts/";
            return $this->layout_show('admin/mess.html', array('data'=>$object));
        }
        else{
            $obj = Array();
            $obj['terms'] = $this->get_controller("taxonomy", "taxonomy", true)->get();
            $obj['types'] = $this->get_controller("mObject", "add", true)->get_types();
            $obj['options']['default_type'] = 3;
            $obj['options']['size'] = 10;
            return $this->layout_show('admin/term.html', $obj);
        }
        
    }
    
    function add($post){
        $message = "";
        $post['name'] = mysql_real_escape_string($post['name']);
        $post['alias'] = mysql_real_escape_string($post['alias']);
        $post['sort_order'] = (int) mysql_real_escape_string($post['sort_order']);
        $post['parent_id'] = mysql_real_escape_string($post['parent_id']);
        $post['type_id'] = (int) mysql_real_escape_string($post['type_id']);
        
        $query = "INSERT INTO taxonomy (name, parent_id, sort_order, type_id) 
            VALUES ('{$post['name']}', {$post['parent_id']}, '{$post['sort_order']}', {$post['type_id']})";
        $query = mysql_query($query);
        
        $taxonomyID = mysql_insert_id();
        
        if (mysql_error())
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else{
            $message['text'] .= "Термин <b>\"".$post['name']."\"</b> успешно добавлен.";
            
            $old_alias = $this->get_controller("aliases","aliases",true)->getAliasFromAlias($post['alias']);
            
            
            if ($post['alias']!="")
            {
                $alias = $old_alias[0];
                $alias['alias'] = $post['alias'];
                $alias['address'] = "/taxonomy/id".$taxonomyID;
                if (isset($old_alias[0]['id'])){
                    //$mess = $this->get_controller("aliases","edit",true)->edit($alias);
                    $mess['text'] = "Синоним добавлен не был. Синоним уже используется. <a href='/admin/aliases/'>Все синонимы</a>, <a href='/admin/taxonomy/'>таксономия</a>";
                }
                else {
                    $mess = $this->get_controller("aliases","add", true)->add($alias);
                }

            }
           
            if ($mess['text']!="")
                $message['text'] .= "<br>".$mess['text'];
            
        }
        
        
        
        
        
        return $message;
    }
}
?>
