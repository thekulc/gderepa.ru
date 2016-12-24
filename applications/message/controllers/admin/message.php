<?
namespace admin\message;

class message extends \Admin {

    function default_method()
    {
        $object = null;
        if ($this->id){
            $object = $this->layout_get("admin/message.html", array('message'=>$this->get($this->id)));
            $this->isRead($this->id);
        }
        else {
            $object = $this->layout_get("admin/table.html", array('messages'=>$this->get()));
        }
        return $this->layout_show('admin/index.html', 
                array('count'=>$this->getCount(), 'object'=>$object));
    }
    
    function getCount(){
        $count = "select count(id) as cnt from message where isRead=0";
        $count = mysql_query($count);
        $count = mysql_fetch_assoc($count);
        return $count['cnt'];
    }
    
    function get($id = null){
        $result = array();
        if ($id == null){
            $messages = "select * from message";
        }
        else $messages = "select * from message where id = '{$id}'";
        $messages = mysql_query($messages);
        if (mysql_num_rows($messages)>0){
            while ($message = mysql_fetch_assoc($messages)) {
                $result[] = $message;
            }
        }
        else $result = NULL;
        return $result;
    }
    
    function isRead($id = null){
        $result = null;
        if ($id!=null)
            $query = "UPDATE message SET isRead = true where id = '{$id}'";
        $result = mysql_query($query);
        return $result;
    }
}
?>
