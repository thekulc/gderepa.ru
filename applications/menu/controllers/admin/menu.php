<?
namespace admin\menu;

class menu extends \Admin {

    function default_method()
    {
        if ($this->id){
            $menuItem = $this->get($this->id);
            $obj = $this->layout_get("admin/menu/add.html", array("menuItem"=>$menuItem));
        }
        else{
            $menu = $this->getList();
            $obj = $this->layout_get("admin/menu/table.html", array("menu"=>$menu));
        }
        
        return $this->layout_show('admin/menu/index.html', array("object"=>$obj));
    }
    
    function get($id){
        $query = "SELECT *, (select alias from aliases where address=menu.address limit 1) as alias from menu where id='{$id}'";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            $res = mysql_fetch_assoc($query);
        else $res = null;
        return $res;
    }
    
    function getFromAddress($address){
        $query = "SELECT *, (select alias from aliases where id=menu.alias_id limit 1) as alias 
            from menu where address = '{$address}'";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            $res = mysql_fetch_assoc($query);
        else $res = null;
        return $res;
    }
    
    function getList(){
        $query = "SELECT *, (select alias from aliases where address=menu.address limit 1) as alias from menu";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            while($menuItem = mysql_fetch_assoc($query))
                $res[] = $menuItem;
        else $res = null;
        return $res;
    }
}
?>
