<?
namespace admin\aliases;

class aliases extends \Admin {

    function default_method()
    {
        $object = null;
        pr($this->get_layout_path());
        if ($this->id)
            $object = $this->layout_get("admin/alias.html", array('alias'=>$this->getFullAliases($this->id)));
        else $object = $this->layout_get("admin/table.html", array('aliases'=>$this->getFullAliases()));
        return $this->layout_show('admin/index.html', 
                array('count'=>$this->getCount(), 'object'=>$object));
    }
    
    function getCount(){
        $count = "select count(id) as cnt from aliases";
        $count = mysql_query($count);
        $count = mysql_fetch_assoc($count);
        return $count['cnt'];
    }
    
    function getAliasFromAlias($alias){
        $res = null;
        $alias = "select * from aliases where alias = '{$alias}'";
        $alias = mysql_query($alias);
        if (mysql_num_rows($alias)>0)
            $res[] = mysql_fetch_assoc($alias);
        else $res = NULL;

        return $res;
    }
    
    function getAliasFromAddress($address){
        $res = null;
        $alias = "select * from aliases where address like '{$address}'";
        $alias = mysql_query($alias);
        if (mysql_num_rows($alias)>0)
            $res[] = mysql_fetch_assoc($alias);
        else $res = NULL;
        return $res;
    }
    
    function getAliases($id = null){
        $result = array();
        if ($id!= null)
            $aliases = "select * from aliases";
        else $aliases = "select * from aliases where id = '{$id}'";
        $aliases = mysql_query($aliases);
        if (mysql_num_rows($aliases)>0)
            while ($alias = mysql_fetch_assoc($aliases)) {
                $result[] = $alias;
            }
        else $result = NULL;
        return $result;
    }
    
    function getFullAliases($id = NULL){
        $result = array();
        if ($id)
            $aliases = "select * from aliases where id = '{$id}' limit 1";
        else
            $aliases = "select * from aliases";
        $aliases = mysql_query($aliases);
        if (mysql_num_rows($aliases)>0)
            while ($alias = mysql_fetch_assoc($aliases)) {
                $result[] = $alias;
            }
        else $result = NULL;
        return $result;
        
    }
    
}
?>
