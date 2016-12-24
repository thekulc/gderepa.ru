<?
namespace menu;

class add extends \Controller {  
    
    function default_method()
    {
        //pr($this->id);
        pr($this->more);
        return $this->layout_show('index.html', array('title'=> $this->id));
    }
    
    function menu($isMenu=NULL){
        $menu = array();
        if ($isMenu)
            $pages = "select id, menu_title, menu_place, 
                (select alias from aliases where (node_id=pages.id and application='page')) as alias 
                from pages 
                where !isNULL(menu_place)";
        else $pages = "select id, menu_title, menu_place, 
                (select alias from aliases where (node_id=pages.id and application='page')) as alias 
                from pages";
        $pages = mysql_query($pages);
        if(mysql_num_rows($pages)){
            $i = 0;
            while ($page = mysql_fetch_array($pages)) {
                $menu[$i]['id'] = $page['id'];
                $menu[$i]['title'] = $page['menu_title'];
                if ($page['alias'])
                    $menu[$i]['href'] = "/".ASEP.$page['alias'];
                else $menu[$i]['href'] = "/page/".$page['id'];
                $i++;
            }
        }
        return $menu;
    }
    
}
?>
