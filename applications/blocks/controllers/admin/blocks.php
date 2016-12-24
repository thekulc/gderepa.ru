<?
namespace admin\blocks;

class blocks extends \Admin {

    function default_method()
    {
        if ($this->id){
            $block = $this->get($this->id);
            $obj = $this->layout_get("admin/add.html", array("block"=>$block));
        }
        else{
            $blocks = $this->getList();
            $obj = $this->layout_get("admin/table.html", array("blocks"=>$blocks));
        }
        return $this->layout_show('admin/index.html', array("object"=>$obj));
    }
    
    function get($id){
        $res = null;
        $query = "SELECT * from blocks where id = '{$id}'";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            $res = mysql_fetch_assoc ($query);
        return $res;
    }
    
    function getList(){
        $res = null;
        $query = "SELECT * from blocks";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            while ($block = mysql_fetch_assoc($query)) {
                $res[] = $block;
            }
        return $res;
    }
    
}
?>
