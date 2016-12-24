<?
namespace studios;

class studios extends \Controller {  

	var $studio;
    
    function default_method()
    {
		$layout = "";
		if ($this->id){
			
			$this->studio = $this->model->getStudioByIdAndDomain($this->id, array(6));
			$this->studio['contacts'] = nl2br($this->studio['contacts']);
			
			$method = $this->getMethodName( "method" . ucfirst ($this->more[0]) );
			if ( $method ){
				
				$data = $this->{$method}($_REQUEST);
			}
			else{
				$data['menu']['active'] = "studio";
				$data['page']['breadcrumb'][0]['href'] = "/studios";
				$data['page']['breadcrumb'][0]['title'] = "Студии";
				$data['page']['title'] = $this->studio['name'];
				$data['page']['layout'] = "studios/studios_main.html";
			}
			
			/*
			
			
			*/
			
			/*
			switch ($this->more[0]){
				case "month": 
					$data = $this->getMonth($_GET['date']);
					$data['menu']['active'] = "month";
					$data['page']['title'] = "Расписание на месяц";
					$data['page']['breadcrumb'][0]['href'] = "/studios";
					$data['page']['breadcrumb'][0]['title'] = "Студии";
					$data['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
					$data['page']['breadcrumb'][1]['title'] = $this->studio['name'];
					$layout = "studios/studios_main.html";
				break;
				case "week": 
					//$data = $this->getMonth($_GET['date']);
					$data['menu']['active'] = "week";
					$data['page']['title'] = "Расписание на ближайшие дни";
					$data['page']['breadcrumb'][0]['href'] = "/studios";
					$data['page']['breadcrumb'][0]['title'] = "Студии";
					$data['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
					$data['page']['breadcrumb'][1]['title'] = $this->studio['name'];
					$layout = "studios/studios_main.html";
				break;
				default:
					
				break;
					
			}
			*/
			$data['studio'] = $this->studio;
			
		}
		else{
			$data['page']['title'] = "Список студий";
			$layout = "studios/studios_main.html";
		}
		
        return $this->layout_show($data['page']['layout'], $data);
    }
	
	function methodWeek($offset = 1){
		$data['menu']['active'] = "week";
		$data['page']['title'] = "Расписание на ближайшие дни";
		$data['page']['breadcrumb'][0]['href'] = "/studios";
		$data['page']['breadcrumb'][0]['title'] = "Студии";
		$data['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
		$data['page']['breadcrumb'][1]['title'] = $this->studio['name'];
		$data['page']['layout'] = "studios/studios_main.html";
		return $data;
	}
	
	function methodMonth($request){
		$date = $request['date'];
		$res;
		if ($date){
			$dt = date_create_from_format('Y-m', $date);
			if ($dt){
				$lDate = $date;
			}
		}
		else{
			$lDate = Date("Y-m");
		}
		if ($lDate){
			$res['calendar'] = $this->get_controller('calendar')->getMonth($lDate);
			$res['localdate'] = $this->get_controller('calendar')->getPrevNavDates($lDate);
			$res['postLocal_dates'] = $this->get_controller('calendar')->getNextMonthesNav($lDate, 1);
			
		}
		
		$res['menu']['active'] = "month";
		$res['page']['title'] = "Расписание на месяц";
		$res['page']['breadcrumb'][0]['href'] = "/studios";
		$res['page']['breadcrumb'][0]['title'] = "Студии";
		$res['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
		$res['page']['breadcrumb'][1]['title'] = $this->studio['name'];
		$res['page']['layout'] = "studios/studios_main.html";
		return $res;
	}
}
?>
