<?
namespace calendar;

class delete_event extends \Controller {  
    function default_method() {
        $event = $this->getEventById($this->id);
        
        if ($event->owner_id == $_SESSION['user']['id']){
            echo $this->delete($event->id);
        }
    }
    
    function delete($id) {
        $sql = "DELETE FROM events WHERE id = " . mysql_real_escape_string($id);
        if (mysql_query($sql))
            return true;
        else
            return mysql_error ();
    }
            
    function getEventById($id){
        return array_pop($this->get_controller('calendar')->select('id = ' . $id));
    }
    
}