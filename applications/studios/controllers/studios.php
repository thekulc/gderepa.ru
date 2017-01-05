<?php
namespace studios;

class studios extends \Controller {  

	var $studio;
	/** @var  $mdlCalendar \studios\mdl_calendar */
    var $mdlCalendar;

    function default_method()
    {
        $data['page']['layout'] = "studios/studios_main.html";
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

			$data['studio'] = $this->studio;
			
		}
		else{
			$data['page']['title'] = "Список студий";
            $data['page']['layout'] = "studios/studios_main.html";
		}
		
        return $this->layout_show($data['page']['layout'], $data);
    }
	
	function methodWeek($request){
        $offset = intval($request['offset']);
        $this->mdlCalendar = $this->get_model("mdl_calendar", "studios");
		$data['menu']['active'] = "week";
		$data['page']['title'] = "Расписание на ближайшие дни";
		$data['page']['breadcrumb'][0]['href'] = "/studios";
		$data['page']['breadcrumb'][0]['title'] = "Студии";
		$data['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
		$data['page']['breadcrumb'][1]['title'] = $this->studio['name'];
		$data['page']['layout'] = "studios/studios_main.html";

		$data['lang']['_rus']['monthes'] = $this->mdlCalendar->getRusMonthName();
		$data['lang']['_rus']['weekDays'] = $this->mdlCalendar->getRusWeekdayName();

		$data['localdate'] = new \DateTime();

        $data['calendar'] = $this->getWeek($offset, 2, $this->studio['id']);
        $data['offset'] = $offset + 1;
		return $data;
	}

	function getWeek($offset = 0, $count = 1, $studio_id, $events = true){
	    if (!isset($this->mdlCalendar))
            $this->mdlCalendar = $this->get_model("mdl_calendar", "studios");

	    $today = new \DateTime();
        $format = "Y-m-d";
        if ($offset <> 0)
            $today->add(\DateInterval::createFromDateString(+ intval($offset) . " week"));

	    $choosedWeek = intval($today->format("W"));

        $firstDay = clone $today;
        $firstDay->add(\DateInterval::createFromDateString(- $today->format("N") + 1 . " day"));

        $lastDay = clone $firstDay;
        $lastDay->add(\DateInterval::createFromDateString("+ $count week"));

        if ($events)
            $events = $this->getEventsByPeriod($firstDay->format($format), $lastDay->format($format), $studio_id);

        $interval = \DateInterval::createFromDateString("1 day");
	    $week = array();
        $timetables = $this->mdlCalendar->getStudioTimetables($studio_id);
        if ($timetables) {
            for ($weeks = 0; $weeks < $count; $weeks++) {
                for ($i = 1; $i <= 7; $i++) {
                    $week[$choosedWeek + $weeks][$firstDay->format($format)]['events'] = $this->getTimeTableByDate($firstDay, $events[$firstDay->format($format)], $timetables);
                    $week[$choosedWeek + $weeks][$firstDay->format($format)]['date'] = current($week[$choosedWeek + $weeks][$firstDay->format($format)]['events'])['start_time'];
                    $firstDay = $firstDay->add($interval);
                }
            }
        }
        return $week;
    }

    function getTimeTableByDate(\DateTime $date, $events, $timetables){
        $formatDay = "H:i";
	    $timetable = $this->getDateTimetable($date, $timetables);
        $start = new \DateTime($timetable['start_time']);
        $iteratorTime = clone $start;
        $iteratorTime->setDate($date->format("Y"),$date->format("m"),$date->format("d"));
        $end = new \DateTime($timetable['end_time']);
        $duration = new \DateInterval("P0000-00-00T" . $timetable['duration_time']);
        $period = $end->diff($start);
        $itemCount = $period->h / $duration->h;
        for ($i = 0; $i < $itemCount; $i++){
            $dayStartTime = $iteratorTime->format("H:i");
            if ($events[$dayStartTime]) {
                $table[$dayStartTime] = $events[$dayStartTime];
            }
            else{
                $table[$dayStartTime] = $this->getFreeTimeEvent($iteratorTime, $timetable);
            }
            $iteratorTime->add($duration);
        }
        return $table;
    }

    function getFreeTimeEvent($start, $timetable){
        //pr(new \DateInterval("P0000-00-00T" . $timetable['duration_time']));

        $res['start_time'] = clone $start;
        $res['end_time'] = clone $res['start_time'];
        $res['duration_time'] = new \DateInterval("P0000-00-00T" . $timetable['duration_time']);
        $res['end_time'] -> add($res['duration_time']);
        $res['studio_id'] = $timetable['studio_id'];
        $res['type_id'] = 4;
        return $res;
    }

    function getDateTimetable( $date = \DateTime, &$timetables){
        if (!empty($timetables['dates'][$date->format("Y-m-d")])){
            $dateTimetable = $timetables['dates'][$date->format("Y-m-d")];
        }
        elseif (!empty($timetables['dayofweeks'][$date->format("N")])){
            $dateTimetable = $timetables['dayofweeks'][$date->format("N")];
        }
        else{
            $dateTimetable = $timetables['allDays'];
        }
        return $dateTimetable;
    }

    function getEventsByPeriod($firstDay, $lastDay, $studio_id){
        $events = $this->mdlCalendar->getEventsByPeriod($firstDay, $lastDay, $studio_id);
        foreach ($events as $event){
            $lDate =  new \DateTime($event['date']);
            $event['start_time'] = clone $lDate;
            $event['duration_time'] = new \DateInterval("P0000-00-00T" . $event['duration_time']);
            $event['end_time'] = clone $lDate;
            $event['end_time']->add($event['duration_time']);
            $res[$lDate->format("Y-m-d")][$lDate->format("H:i")] = $event;
        }
        return $res;
    }

    function getNextMonthesNav($aDate, $count = 3){
        $res = array();
        $res[0]["monthName"];
        $res[0]["link"];
        $objDate = date_create_from_format("Y-m", $aDate);
        if ($objDate){
            if (count($this->_rus)<=0)
                $this->_rus = $this->mdlCalendar->getRusMonthName();

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
        $_rus = $this->mdlCalendar->getRusMonthName();

        $res['today']['date'] = date();
        $res['today']['day'] = (int)date('d');
        $res['today']['monthName'] = $_rus [date('m')];
        $res['today']['year'] = date('Y');
        $res['today']['link']['day'] = date('Y-m');
        $choosed = date_create_from_format("Y-m", $lDate);
        $res['choosed']['link'] = $choosed->format('Y-m');
        $res['choosed']['monthName'] = $_rus [$choosed->format('m')];

        $choosed->add( \DateInterval::createFromDateString('- 1 month') );
        $res['prevDate']['link'] = $choosed->format('Y-m');
        $res['prevDate']['monthName'] = $_rus [$choosed->format('m')];

        $choosed->add( \DateInterval::createFromDateString('+ 2 month') );
        $res['nextDate']['link'] = $choosed->format('Y-m');
        $res['nextDate']['monthName'] = $_rus [$choosed->format('m')];

        return $res;
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
            $this->mdlCalendar = $this->get_model("mdl_calendar", "studios");
            $res['calendar'] = $this->getMonth($lDate);
            $res['localdate'] = $this->getPrevNavDates($lDate);
            $res['postLocal_dates'] = $this->getNextMonthesNav($lDate, 1);
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

	function getMonth($date, $events = true) {
        $day = new \DateTime($date);
        $i = 0;
        $week = 0;
        $format = "d-m-Y";
        $days = array();
        $interval = \DateInterval::createFromDateString('1 day');
        $weekDay = $day->format('N');
        if ($weekDay > 1) {
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
            $events = $this->mdlCalendar->getMonthEvents($date, $this->studio['id']);
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
                $timeGrid[$tStart]['start'] = date_create_from_format("d-m-Y H:i", $day." ".$tStart);
                date_date_set($period['end'], $timeGrid[$tStart]['start']->format("Y"), $timeGrid[$tStart]['start']->format("m"), $timeGrid[$tStart]['start']->format("d"));
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
        $format = "d-m-Y";
        $dayEvents = array();
        foreach ($events as $event) {
            $eDate = date_create($event['date']);
            if ($day == $eDate->format($format)){
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
}
?>
