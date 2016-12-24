<?php
namespace calendar;

class ajax extends \Controller {  

	const maxDownloadAudios = 50;
    
    function default_method()
    {
		$response;
		
		switch ($_GET["request"]) {
			case "getEventByTime": $response = $this->getEventByTime( $_GET["str_time"] ); break;
			//default:;
		}
		echo json_encode($response);
		return;
	}
	
	function getEventByTime($str_time){
		$format = "Y-m-d H:i:s";
		$formatMysql = "%Y-%m-%d %H:%i:%s";
		$interval = "+ 2 hour";
		
		$d = date_create($str_time);
		$data['date'] = $d->format($format);
		$where = "
			STR_TO_DATE('".$data['date']."','$formatMysql') ".
				"< date_add(`date`, interval $interval) ".
				"and ".
			"date_add(STR_TO_DATE('".$data['date']."','$formatMysql'), interval $interval) ".
				"> `date`
		";
		$data['event'] = array_shift($this->model->selectArray("events", $where));
		if (count($data['event']) <= 0){
			$data['event']['date'] = $str_time;
			$data['event']['duration'] = "2 hours";
			$data['event']['type_id'] = 4;
		}
		return $data;
	}
}
?>
