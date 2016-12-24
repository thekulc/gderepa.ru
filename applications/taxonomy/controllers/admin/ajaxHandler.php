<?php
namespace admin\taxonomy;

class ajaxHandler extends \Admin {

    function default_method(){
        $action = filter_input(INPUT_GET, "action");
        switch ($action) {
            case 'getObjectsByTerm':
                $term_id = (int) filter_input(INPUT_GET, 'id');
                $objects = $this->get_controller("mObject", "mObject", true)->getObjectsByTerm($term_id);
                
                echo json_encode($objects);
                
                break;
            case 'getObjects':
                $objects = $this->get_controller("mObject", "mObject", true)->getShortList();
                echo json_encode($objects);
                
                break;
            case 'link':
                $object_id = filter_input(INPUT_GET, "object_id");
                $term_id = filter_input(INPUT_GET, "term_id");
                echo $this->linkObjToTerm($object_id, $term_id);
                break;
            
            case 'unlink':
                $object_id = filter_input(INPUT_GET, "object_id");
                $term_id = filter_input(INPUT_GET, "term_id");
                echo $this->unlinkObjectFromTerm($object_id, $term_id);
                break;
            default:
                break;
        }
        
        return;
    }
    
    private function linkObjToTerm($object_id, $term_id){
        $object_id = intval($object_id);
        $term_id = intval($term_id);
        $err = "";
        $message = "";
        if ($this->get_controller("mObject", "edit", true)->linkObjectToTerm($object_id, $term_id, $err)){
            $message = "Linked";
        }
        else {
            $message = "Error:<br>" . $err;
        }
        return $message;
    }
    
    private function unlinkObjectFromTerm($object_id, $term_id){
        $object_id = intval($object_id);
        $term_id = intval($term_id);
        $err = "";
        $message = "";
        if ($this->get_controller("mObject", "edit", true)->unlinkObjectFromTerm($object_id, $term_id, $err)){
            $message = "Unlinked";
        }
        else {
            $message = "Error:<br>" . $err;
        }
        return $message;
    }
    
}
