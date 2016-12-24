<?
namespace admin\page;

class edit extends \Admin {

    function default_method()
    {
        if ($_POST){
            $_POST['id'] = $this->id;
            $message = $this->edit($_POST);
        }
        else $message['text'] = 'Данные для обработки не получены.';
        $message['href']='/admin/page/';
        $this->layout = "/source/";
        $object = $this->layout_get("message.html", array('message'=>$message));
        $this->layout = "/applications/page/layouts/";
        return $this->layout_show('admin/index.html', array('data'=>$object));
    }
    
    function edit($post){
        $page = $this->get_controller("page","page",true)->get($post['id']);
        $path = "uploads".DS."files".DS."images".DS;
        if ($post['icon_remove']){
            if($this->fileRemove($path, $page['icon']))
                $post['icon'] = NULL;
        }
        elseif ($page['icon'] == "")
            $post['icon'] = $filename = $this->fileUpload($path, $_FILES['icon']);
            else
                $post['icon'] = $page['icon'];
        
        unset($post['icon_remove']);
        
        $message = Array();
        $post['title'] = mysql_real_escape_string($post['title']);
        $post['body'] = mysql_real_escape_string($post['body']);
        $post['inhead'] = mysql_real_escape_string($post['inhead']);
        $query = "UPDATE pages SET 
            title = '{$post['title']}', 
            body= '{$post['body']}',
            icon= '{$post['icon']}',
            parent_id = '{$post['parent_id']}',
            inhead = '{$post['inhead']}'
            WHERE id ='{$post['id']}'";
        $query = mysql_query($query);
        
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] = "Страница <b>'{$_POST['title']}'</b> успешно изменена";

        $blocks = $post['blocks'];
        $this->get_controller("blocks", "block_to_page", true)->add($blocks, $post['id']);
        
        $old_menu = $this->get_controller("menu","menu",true)->getFromAddress("/page/id".$post['id']);
        if($post['menu_title']!=""){
            $menu = $old_menu;
            $menu['title']=$post['menu_title'];
            $menu['position']=$post['menu_place'];
            $menu['address']="/page/id".$post['id'];
            if (isset($old_menu['id']))
                $mess = $this->get_controller("menu","edit",true)->edit($menu);
            else $mess = $this->get_controller("menu","add",true)->add($menu);
        }
        else if(isset($old_menu['id'])){
            $mess = $this->get_controller("menu","delete",true)->delete($old_menu['id']);
        }
        
        if ($mess['text']!="")
            $message['text'] .= "<br>".$mess['text'];
        
        $mess = "";
        
        $old_alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/page/id".$page['id']);
        if ($post['alias']!="")
        {
            $alias = $old_alias[0];
            $alias['alias'] = $post['alias'];
            $alias['address'] = "/page/id".$page['id'];
            if (isset($old_alias[0]['id'])){
                $mess = $this->get_controller("aliases","edit",true)->edit($alias);
            }
            else {
                $mess = $this->get_controller("aliases","add", true)->add($alias);
            }
            
        }
        else if(isset($old_alias[0]['id']))
            $mess = $this->get_controller("aliases","delete",true)->delete($old_alias[0]['id']);
        
        if ($mess['text']!="")
            $message['text'] .= "<br>".$mess['text'];
        return $message;
    }
    
    function fileUpload($path, $file){
        
        $res = "";
        $path = ROOT.$path;
        
        
        if (is_dir($path) && $file['name']!= ""){
            $filename = explode(DS, $file["tmp_name"]);
            $filename = explode(".", $filename[count($filename)-1]);
            $filename = $filename[0];
            $dest = explode(".", $file['name']);
            $dest = ".".$dest[count($dest)-1];
            $res = $filename.$dest;
            $filename = $path . $res;
            if (!copy($file["tmp_name"], $filename))
                $res['error'] = error_get_last();
            
        }
        else
            $res['error'] = error_get_last();
        
        return $res;
    }
    
    function fileRemove($path, $name){
        $res = "";
        $path = ROOT.$path;
        if (is_dir($path) && $name != ""){
            if (unlink($path.$name))
                $res = true;
            else
                $res = error_get_last ();
        }
        return $res;
    }
    
}
?>
