<?
namespace admin\page;

class add extends \Admin {

    function default_method()
    {
        $object = null;
		$data;
        if($_POST){
            $data['message'] = $this->add($_POST);
            $data['message']['href'] = "/admin/page/";
            $layout = "admin/message.html";
            //$object = $this->layout_get("admin/message.html", $data);
        }
        else{ 
			$layout = "admin/page/add.html";
            $data['data']['terms'] = $this->get_controller("page", "page", true)->getTerms();
			$data['data']['blocks'] = $this->get_controller("blocks")->getList();
            //$object = $this->layout_get("admin/page/add.html", array('blocks'=>$blocks, "terms"=> $terms));
        }
        //$this->layout = "/applications/page/layouts/";
        return $this->layout_show($layout, $data);
    }
    
///TODO
    //!!----------------------uploadFile
    function add($post){
        $message = "";
        $post['body'] = mysql_real_escape_string($post['body']);
        $post['title'] = mysql_real_escape_string($post['title']);
        $post['menu_title'] = mysql_real_escape_string($post['menu_title']);
        $post['inhead'] = mysql_real_escape_string($post['inhead']);
        $query = "INSERT INTO pages (title, body, inhead, parent_id) 
            VALUES ('{$post['title']}', '{$post['body']}', '{$post['inhead']}', '{$post['parent_id']}')";
        
        $query = mysql_query($query);
        $pageId = mysql_insert_id();
        $blocks = $post['blocks'];
        $this->get_controller("blocks", "block_to_page", true)->add($blocks, $pageId);
        
        if (mysql_error())
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else{
            $message['text'] .= "Страница <b>".$post['title']."</b> успешно добавлена.";
            if($post['menu_title']!=""){
                $menu['title']=$post['menu_title'];
                $menu['position']=$post['menu_place'];
                $menu['address']="/page/id".$pageId;
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

            $old_alias = $this->get_controller("aliases","aliases",true)->getAliasFromAddress("/page/id".$pageId);
            if ($post['alias']!="")
            {
                $alias = $old_alias[0];
                $alias['alias'] = $post['alias'];
                $alias['address'] = "/page/id".$pageId;
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
            
        }
        
        
        
        
        
        return $message;
    }
}
?>
