<?
namespace blocks;

class blocks extends \Controller {  
    
    function default_method()
    {
        return $this->layout_show('index.html');
    }
    
    function getList($keys = false){
        $res = NULL;
        $blocks = "SELECT * from blocks where isPub = true";
        $blocks = mysql_query($blocks);
        if (mysql_num_rows($blocks)>0)
            while ($block = mysql_fetch_assoc($blocks)) {
                if ($keys==true)
                    $res[$block['id']] = $block;
                else
                    $res[$block['name']] = $block;
            }
        return $res;
    }
    
    
}
?>
