<?php
namespace calendar;

class calendar extends \Controller {  
    
    var $_date;
    const separator = '-';
	var $_rus;
	const format = 'd-m-Y';
	const defaultFormat = 'Y-m-d H:i:s';
    
    function default_method()
    {
		
		if (isset($_GET['logout'])){
			unset($_SESSION['vk_user']);
			unset($_SESSION['user']);
			setcookie('data', "",time()-3600,"/","/");
			$this->redirect('/');
		}
		else{/*
			if (!$_SESSION['user'] ){
				if($_COOKIE['data']){
					$userData = $this->model->authUserByCookie($_COOKIE['data']);
					$_SESSION['user'] = $userData['user'];
					$_SESSION['vk_user'] = $userData['vk_user'];
				}
			}
			*/
			setlocale(LC_ALL, 'ru_RU.utf-8', 'rus_RUS.utf-8', 'ru_RU.utf8');
			$this->_rus = $this->model->getRusMonthName();
	//pr($_SESSION['vk_user']);

			$date = filter_input(INPUT_GET, "date");
			
			if (!is_string(filter_input(INPUT_GET, "date")) || filter_input(INPUT_GET, "date") == "")
				$date = date('Y-m');
			else
				$date = filter_input(INPUT_GET, "date");
			
			$data = array();
			$data = $this->chooseAction($date);
			//pr($data['localdate']);
			$data['user'] = $_SESSION['user'];
			$data['errs'] = $errs;
			$data['page']['title'] = 'Расписание на месяц';
			$data['lang']['_rus'] = $this->_rus;
			return $this->layout_show('calendar/calendar_main.html', $data);
		}
		
    }
    
    function chooseAction($aDate) {
        $res = array();
        $lDate = $this->getDateObjByStr($aDate);
        if (isset($lDate['date']['year']) AND isset($lDate['date']['month'])){
            $res['calendar'] = $this->getMonth($aDate);
            $res['localdate'] = $this->getPrevNavDates($aDate);
			$res['postLocal_dates'] = $this->getNextMonthesNav($aDate, 1);
        }
        return $res;
    }
	
	function getNextMonthesNav($aDate, $count = 3){
		$res[0]["monthName"];
		$res[0]["link"];
		$objDate = date_create_from_format("Y-m", $aDate);
		if ($objDate){
			if (count($this->_rus)<=0)
				$this->_rus = $this->model->getRusMonthName();
			
			for ($i = 0; $i < $count; $i++){
				$objDate->add( \DateInterval::createFromDateString("+ 1 month") );
				$res[$i]["monthName"] = $this->_rus[$objDate->format('m')];
				$res[$i]["link"] = $objDate->format('Y-m');
			}
		}
		return $res;
	}
	
	function getPrevNavDates($lDate){
		$res = array();
		if (count($this->_rus)<=0)
			$this->_rus = $this->model->getRusMonthName();
		
		$res['today']['date'] = date();
		$res['today']['day'] = (int)date('d');
		$res['today']['monthName'] = $this->_rus[date('m')];
		$res['today']['year'] = date('Y');
		$res['today']['link']['day'] = date('Y-m');
			
		$choosed = date_create_from_format("Y-m", $lDate);
		$res['choosed']['link'] = $choosed->format('Y-m');
		$res['choosed']['monthName'] = $this->_rus[$choosed->format('m')];
		
		$choosed->add( \DateInterval::createFromDateString('- 32 day') );
		$res['prevDate']['link'] = $choosed->format('Y-m');
		$res['prevDate']['monthName'] = $this->_rus[$choosed->format('m')];
		
		$choosed->add( \DateInterval::createFromDateString('+ 63 day') );
		$res['nextDate']['link'] = $choosed->format('Y-m');
		$res['nextDate']['monthName'] = $this->_rus[$choosed->format('m')];
		
		return $res;
	}
    
    function getMonth($date, $events = true) {
        $resArr = array();
        $day = new \DateTime($date);
        $i = 0;
        $week = 0;
        $weekDay = 0;
        $format = "d-m-Y";
        $days = array();
        $interval = \DateInterval::createFromDateString('1 day');
        if (($weekDay = $day->format('N')) > 1) {
            $day = $day->sub(\DateInterval::createFromDateString($weekDay - 1 . ' days'));
            for ($i = 0; $i < $weekDay; $i++) {
                $days[$week][$i] = $day->format($format);
                $day->add($interval);
            }
        }
        do {
            if ($i++ > 0 && ($weekDay = $day->format('N')) == 1) {
                $week++;
            }
            $days[$week][$weekDay - 1] = $day->format($format);
            $day = $day->add($interval);
        } while ($week < 4);
		
        if (($lastWeek = count($days) - 1) > 1 && ($lastWeekDaysCount = count($days[$lastWeek])) < 7) {
            
            for ($i = $lastWeekDaysCount; $i < 7; $i++) {
                $days[$lastWeek][$i] = $day->format($format);
                $day->add($interval);
            }
        }
		//pr($days);
        return $this->getMonthArrayByDays($days, $date, $events);
    }
	
	private function getMonthArrayByDays($days, $date, $events = true){
		$MonthArrayByDays = array();
		if ($events)
            $events = $this->model->getMonthEvents($date);
        else
            $events = false;
		if ($days){
			
			$timeGrid = $this->getTimeGrid();
			for ($i = 0; $i < count($days); $i++) {
				foreach ($days[$i] as $day) {
					$MonthArrayByDays[$i][$day]['date'] = $day;
					$MonthArrayByDays[$i][$day]['events'] = $this->getGridArray(null, $timeGrid, $day);
					if ($events){
						$dayEvents = $this->getEventByDate($events, $day);
						$MonthArrayByDays[$i][$day]['events'] = $this->getGridArray($dayEvents, $timeGrid, $day);
					}
				}
			}
		}
		else
			$MonthArrayByDays = null;
		return $MonthArrayByDays;
	}
	
	function getGridArray($dayEvents, $timeGrid, $day){
		foreach ($timeGrid as $tStart => $period){
			if ($dayEvents[$tStart]){
				
				$timeGrid[$tStart] = $dayEvents[$tStart];
				$timeGrid[$tStart]->start = date_create_from_format("d-m-Y H:i", $day." ".$tStart);
				date_date_set($period['end'], $timeGrid[$tStart]->start->format("Y"), $timeGrid[$tStart]->start->format("m"), $timeGrid[$tStart]->start->format("d"));
				$timeGrid[$tStart]->end = $period['end'];
				$timeGrid[$tStart]->free = false;
			}
			else{
				
				$timeGrid[$tStart]['start'] = date_create_from_format("d-m-Y H:i", $day." ".$tStart);
				date_date_set($period['end'], $timeGrid[$tStart]['start']->format("Y"), $timeGrid[$tStart]['start']->format("m"), $timeGrid[$tStart]['start']->format("d"));
				$timeGrid[$tStart]['end'] = $period['end'];
				$timeGrid[$tStart]["free"] = true;
			}
		}
		return $timeGrid;
	}
	
	function getEventByDate($events, $day) {
		$dayEvents = array();
		foreach ($events as $event) {
			$eDate = date_create($event->date);
			if ($day == $eDate->format(self::format)){
				$dayEvents[$eDate->format("H:i")] = $event;
			}
		}
		return $dayEvents;
	}
	
	function getTimeGrid(){
		$start = date_create_from_format("H:i:s", "9:00:00");
		$end = date_create_from_format("H:i:s", "21:00:00");
		$duration = \DateInterval::createFromDateString("2 hours");
		
		$grid = array();
		
		while($start < $end){
			$lStart = clone($start);
			$grid[$start->format("H:i")]["start"] = $lStart;
			$lEnd = clone($start);
			$lEnd = date_add($lEnd, $duration);
			$grid[$start->format("H:i")]["end"] = $lEnd;
			date_add($start, $duration);
			$lStart = null;
			$lEnd = null;
		}
		$start = null;
		
		return $grid;
	}
    
    function getDateObjByStr($dateStr) {
        $lDate = explode(self::separator, $dateStr);
        $temp = $this->validYear($lDate[0]);
		$result = null;
        if ($temp){
            $res['year'] = $temp;
            $temp = $this->validMonth($lDate[1]);
            if ($temp){
                $res['month'] = str_pad($temp, 2, "0", STR_PAD_LEFT);
				$monthName = $this->_rus[$temp];
				$link = $res['year'].self::separator.$res['month'];
                $temp = isset($lDate[2]) ? $this->validDay($lDate[2]) : null;
                if ($temp){
                    $res['day'] = $temp;
					$link .= self::separator.str_pad($res['day'], 2, "0", STR_PAD_LEFT);
                }
                
            }
        }
        $result['date'] = $res;
		$result['monthName'] = $monthName;
		$result['link'] = $link;
        return $result;
    }
    
    function getDateObjByStr1($dateStr) {
        $lDate = explode(self::separator, $dateStr);
        $temp = $this->validYear($lDate[0]);
        if ($temp){
            $res['year'] = $temp;
            $temp = $this->validMonth($lDate[1]);
            if ($temp){
                $res['month'] = $temp;
                $temp = isset($lDate[2]) ? $this->validDay($lDate[2]) : null;
                if ($temp){
                    $res['day'] = $temp;
                }
                
            }
        }
        
        return $res;
    }
    
    private function validDay($day) {
        $res = null;
        if (strlen($day) > 0){
            $day = intval($day);
            if ($day > 0 && $day < 32){
                $res = $day;
            }
        }
        return $res;
    }
    
    private function validMonth($month) {
        $res = null;
        if (strlen($month) > 0){
            $month = intval($month);
            if ($month > 0 && $month < 13){
                $res = $month;
            }
        }
        return $res;
    }
    
    private function validYear($year) {
        $res = null;
        if (strlen($year) > 0){
            $year = intval($year);
            if ($year > 2000 && $year < 3000){
                $res = $year;
            }
        }
        return $res;
    }
    
    function getFormattedDate(){
        $year = $this->_date['year'];
        $month = $this->_date['month'];
        $day = $this->_date['day'];
        if (isset ($year) && isset($month) && isset($day) && $year != "" && $month != "" && $day != "")
            return str_pad($day, 2, "0", STR_PAD_LEFT) . self::separator . str_pad($month, 2, "0", STR_PAD_LEFT) . self::separator . $year;
    }
}
?>
