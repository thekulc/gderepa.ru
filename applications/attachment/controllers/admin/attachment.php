<?
namespace admin\attachment;

class attachment extends \Admin {

    function default_method()
    {
        if ($this->id)
            $this->layout_show('admin/form-edit-view.html', array("attachment" => $this->get($this->id)));
        else
            $this->layout_show('admin/table-view.html', array("attachments"=>$this->get_list()));
        
        return;
    }
    
    var $path;
    var $directory;
    
    function getUri(){
        return $this->path = "/uploads/files/images/";
    }
    
    function initDirectory(){
        return $this->directory = ROOT . DS . "uploads" . DS . "files" . DS . "images";
    }
    
    function get_list(){
        $res = $this->select(null, "*, (SELECT title FROM objects WHERE id = object_id LIMIT 1) as object_title");
        return $res;
    }
    
    function get($id){
        return current($this->select("id = ".$id));
    }
    
    function getByEventId($eventId){
        return $this->select("id in (select attachment_id from galery_attachments where galery_id = (select id from galeries where event_id = ".$eventId."))");
    }
    
    function getByObjectId($object_id){
        return $this->select(" object_id = {$object_id}");
    }
            
    function select($where = null, $what = null, $select = null, &$err = NULL){
        $this->path = $this->getUri();
        
        if ($select)
            $sql = $select;
        else {
            if ($what)
                $sql = "SELECT ".$what." FROM attachments";
            else 
                $sql = "SELECT * FROM attachments";
            if ($where)
                $sql .= " WHERE ".$where;
        }
        
        $result = mysql_query($sql);
        $res = array();
        if (mysql_num_rows($result)>0){
            while ($obj = mysql_fetch_object($result)) {
                $obj->uri = $this->path.$obj->filename;
                $res[] = $obj;
            }
        }
        else {
            $res = NULL;
            $err = mysql_error();
        }
        return $res;
    }
    
}
?>
