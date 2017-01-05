<?php
namespace studios;

class ajax extends \Controller {  

	const maxDownloadAudios = 50;
    
    function default_method()
    {
		$response = array();
		
		switch ($_GET["request"]) {
			case "getEventByTime": $response['event'] = $this->getEventByTime( $_GET["str_time"], $_GET["studio_id"] ); break;
			case "getWeekByOffset": $response['week'] = $this->getWeekByOffset( $_REQUEST ); break;

			//default:;
		}
		echo json_encode($response);

	}

	function getWeekByOffset($requset){
        $studio_id = $requset['studio_id'];
        $offset = $requset['offset'];
        /** @var $week \studios\studios */
        $week = self::get_controller("studios")->getWeek($offset, 1, $studio_id);

        return $week;
    }

	function getEventByTime($str_time, $studio_id){
		$event = $this->model->getRow("SELECT ev.*, u.FIO as owner, u.avatar FROM events ev LEFT JOIN users u on ev.owner_id = u.id WHERE `date` = ?s", $str_time);
		if ($event) {
            $lDate = new \DateTime($event['date']);
            $event['start_time'] = clone $lDate;
            $event['duration_time'] = new \DateInterval("P0000-00-00T" . $event['duration_time']);
            $event['end_time'] = $lDate->add($event['duration_time']);
        }
        else{
		    /** @var $mdlCalendar \studios\mdl_calendar */
            $mdlCalendar = $this->get_model("mdl_calendar", "studios");
            /** @var  $studios \studios\studios */
            $studios = $this->get_controller("studios");

            $date = new \DateTime($str_time);
            $timetable = $studios->getDateTimetable($date, $mdlCalendar->getStudioTimetables($studio_id));

            $event = $studios->getFreeTimeEvent($date, $timetable);
        }
		return $event;
	}
}
?>
