<?
namespace admin\page;

class page extends \Admin {

    function default_method()
    {
        if ($this->id){
            $page = $this->get($this->id);
            $blocks = $this->get_controller("blocks")->getList();
            $terms = $this->getTerms();
            $data = $this->layout_get("admin/page/edit.html", array('page'=>$page, 'blocks'=>$blocks, 'terms'=>$terms));
        }
        else {
            $pages = $this->get_list();
            
            $data = $this->layout_get("admin/page/table.html", array('pages'=>$pages));
        }
        return $this->layout_show('admin/page/index.html', array('data'=>$data));
    }
    
    function get_list(){
        $res = array();
        $query = "select *, (select alias from aliases where address=concat('/page/id', pages.id)) as alias,
            (select title from menu where address=concat('/page/id',pages.id) limit 1) as menu_title,
            (select position from menu where address=concat('/page/id',pages.id) limit 1) as menu_place
             from pages";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            while ($page = mysql_fetch_assoc($query)) {
                $res[] = $page;
            }
        else $res = null;
        return $res;
    }
    
    function get_short_list(){
        $res = array();
        $query = "select id, id as pid, parent_id, title, icon, 
                    ifnull(
                      (select alias from aliases where address=concat('/page/id', pid)), 
                      (select concat('/page/id', pid)) 
                    ) as alias,
                    (select concat('/page/id', pid)) as address,
                    (select title from menu where address=concat('/page/id',pid) limit 1) as menu_title,
                    (select position from menu where address=concat('/page/id',pid) limit 1) as menu_place
                from pages order by menu_place asc";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            while ($page = mysql_fetch_assoc($query)) {
                $res[] = $page;
            }
        else $res = null;
        return $res;
    }
    
    function get($id){
        $res = array();
        $query = "select *,
            (select title from menu where address=concat('/page/id',pages.id) limit 1) as menu_title,
            (select position from menu where address=concat('/page/id',pages.id) limit 1) as menu_place,
            (select alias from aliases where address=concat('/page/id', pages.id)) as alias
            from pages where id = '{$id}'";
        
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0){
            $res = mysql_fetch_assoc($query);
            $res['blocks'] = $this->getBlocks($res['id']);
        }
        else $res = null;
        return $res;
    }
    
    function getBlocks($pageId){
        return $this->get_controller("page")->get_blocks($pageId);
    }
    
    function getTerms($id = null){
        $terms = array();
        
        $query = "SELECT id, title, parent_id as pID, (SELECT title FROM pages WHERE id = pID) as parent_title FROM pages";
        
        if ($id)
            $query .= "WHERE id = ".$id;
                
        $termsQ = mysql_query($query);
        if (mysql_num_rows($termsQ)>0){
            while ($term = mysql_fetch_assoc($termsQ)) {
                $terms[] = $term;
            }
        }
        return $terms;
    }
}
?>
