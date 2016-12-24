<?
namespace admin\menu;

class edit extends \Admin {

    function default_method()
    {
        if ($_POST && isset($this->id)){
            $_POST['id'] = substr($this->id,2);
            $message = $this->edit($_POST);
        }
        $message['href']='/admin/menu/';
        $object = $this->layout_get("admin/message.html", array('message'=>$message));
        
        return $this->layout_show('admin/menu/index.html', 
                array('object'=>$object));
    }
    
    function edit($menuItem){
        
        if ($menuItem['alias']!=""){
            $alias = $this->get_controller("aliases","aliases",true)->getAliasFromAlias($menuItem['alias']);
            if ($alias)
                $alias = $alias[0]['id'];
            else {
                $newAlias = array();
                $newAlias['alias'] = $menuItem['alias'];
                $newAlias['address'] = $menuItem['address'];
                $message = $this->get_controller("aliases","add",true)->add($newAlias);
                $message['text'].="</br>";
            }
        }
        $menuItem['title'] = mysql_real_escape_string($menuItem['title']);
        $menuItem['address'] = mysql_real_escape_string($menuItem['address']);
        $query = "UPDATE menu SET title = '{$menuItem['title']}', address='{$menuItem['address']}', 
            position='{$menuItem['position']}',
            alias_id='{$alias}'
            WHERE id ='{$menuItem['id']}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] .= "Произошла ошибка: ".mysql_error();
        else $message['text'] .= "Пункт меню <b>'{$menuItem['title']}'</b> успешно изменен";
        return $message;
    }
    
}
?>
