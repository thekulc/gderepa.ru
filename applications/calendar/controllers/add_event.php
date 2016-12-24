<?
namespace calendar;

class add_event extends \Controller {  
    function default_method() {
        $event = array();
        $lDate = filter_input(INPUT_GET, 'date');
        echo 'lDate';
        /*
        if (is_string($lDate)){
            $event['owner_id'] = $_SESSION['user']['id'];
            $event['title'] = "Репает " . $_SESSION['user']['FIO'];
            $event['type_id'] = 4;
            $event['date'] = date('Y-m-d 00:00:00', strtotime($lDate));
            echo $this->model()->insert($event);
        }*/
    }
}