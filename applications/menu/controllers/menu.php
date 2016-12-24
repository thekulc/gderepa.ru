<?
namespace menu;

class menu extends \Controller {  
    
    function default_method()
    {
        /*$menu = $this->menu();
        
        return $menu;*/
        //return $this->layout_get('index.html', array('menu'=>$menu));
    }
    
    function getList(){
        $query = "SELECT *, ifnull((select alias from aliases where address=menu.address limit 1), (select menu.address)) as alias 
            from menu WHERE position != '-1' order by position asc";
        
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            while($menuItem = mysql_fetch_object($query)){
                if ($menuItem->alias == $GLOBALS['url4parse']){
                    $menuItem->active = true;
                }
                else{
                    $menuItem->active = false;
                }
                
                $res[] = $menuItem;
                
            }
        else $res = null;
        return $res;
    }
    
    function menu($isMenu=NULL){
        $menu = array();
        if ($isMenu)
            $pages = "select id, menu_title, menu_place, 
                (select alias from aliases where (node_id=pages.id and application='page')) as alias 
                from pages 
                where menu_place > 0 order by menu_place desc";
        else $pages = "select id, menu_title, menu_place,
                    IFNULL((select alias from aliases where (node_id=pages.id and application='page')), 
                            concat('page/id', pages.id)) as href 
                    from pages
                    where menu_place >= 0 order by menu_place asc";
        $pages = mysql_query($pages);
        if(mysql_num_rows($pages)>0){
            while ($page = mysql_fetch_assoc($pages)) {
                if($page['href']=='/')
                    $page['href']="";
                $menu[] = $page;
            }
        }
        
        return $menu;
    }
    
}

?>
