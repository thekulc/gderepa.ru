<?
namespace admin\blocks;

class block_to_page extends \Admin {

    function default_method()
    {
        
    }
    
    function deleteByPageID($pageId, &$error){
        $query = "DELETE FROM blocks_page WHERE page_id = '{$pageId}'";
        $query = mysql_query($query);
        $error = mysql_error();
        return $query;
    }
    
    function add($blocks=null, $pageId=null){
        $res = false;
        if ($pageId!=NULL){
            $res = $this->deleteByPageID($pageId, $error);
            foreach ($blocks as $block) {
                $query = "INSERT INTO blocks_page (page_id, block_id) VALUES ('".$pageId."', '".$block."')";
                $res = mysql_query($query);
            }
        }
        return $res;
    }
    
}
?>
