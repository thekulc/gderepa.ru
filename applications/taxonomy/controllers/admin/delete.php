<?
namespace admin\taxonomy;

class delete extends \Admin {

    function default_method()
    {
        $object = NULL;
        $message['href']="/admin/taxonomy/";
        $this->layout = "/source/";
        
        if ($_GET['id']){
            $id = $_GET['id'];
            $message = $this->delete($id);
            $object = $this->layout_get("message.html", array('message'=>$message));
        }
        else if($this->id){
            $id = substr($this->id,2);
            $term = $this->get_controller("taxonomy","taxonomy",true)->get($id);
            $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/taxonomy/id".$id);
            if(!isset($alias[0]['id']))
                $alias[0]['alias'] = "<i>нет</i>";
            $message['text'] = "Вы хотите удалить термин таксономии <b>\"".
                    $term[0]['name']."\"</b>, синоним <b>".
                    $alias[0]['alias']."</b>?<br>Кроме того все материалы, являющиеся непосредственными потомками удаляемого будут являться коренными.
                <br>Это действие отменить невозможно.";
            $message['action']="?id=".$term[0]['id'];
            $object = $this->layout_get("confirm.html", array('message'=>$message));
        }
        $this->layout = "/applications/taxonomy/layouts/";
        $this->layout_show("admin/mess.html", array('data'=>$object));
    }
    
    function delete($id){
        $message = null;
        $term = $this->get_controller("taxonomy","taxonomy",true)->get($id);
        $query = "DELETE from taxonomy WHERE id = '{$id}'";
        $query = mysql_query($query);
        $query = "UPDATE taxonomy SET parent_id = -1 WHERE parent_id = ".$id;
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Термин <b>\"".$term[0]['name']."\"</b> успешно удален.";
        
        $message['href'] = "/admin/taxonomy/";
        
        $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/taxonomy/id".$id);
        
        if ($alias[0]['id']){
            $mess = $this->get_controller('aliases','delete',true)->delete($alias[0]['id']);
            $message['text'] .= "</br>".$mess['text'];
        }
        if ($menu['id']){
            $mess = $this->get_controller('menu','delete',true)->delete($menu['id']);
            $message['text'] .= "</br>".$mess['text'];
        }
        
        return $message;
    }
}
?>
