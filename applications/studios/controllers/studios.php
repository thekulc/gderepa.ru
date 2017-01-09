<?php
namespace studios;

class studios extends \Controller {  

	var $studio;
    var $_rus;
	/** @var  $mdlCalendar \studios\mdl_calendar */
    var $mdlCalendar;

    function default_method()
    {
        if (isset($_GET['logout'])){
            unset($_SESSION['vk_user']);
            unset($_SESSION['user']);
            setcookie('data', "",time()-3600,"/","/");
            $this->redirect('/studios');
        }

        $data['page']['layout'] = "studios/studios_main.html";
		if ($this->id){
			$this->studio = $this->model->getStudioByIdAndDomain($this->id, array(6));
			$this->studio['contacts'] = nl2br($this->studio['contacts']);
			$method = $this->getMethodName( "method" . ucfirst ($this->more[0]) );

			if ( $method ){
                $this->mdlCalendar = $this->get_model("mdl_calendar", "studios");
                $this->_rus = $this->mdlCalendar->getRusMonthName();

				$data = $this->{$method}($_REQUEST);

                $data['lang']['_rus']['monthes'] = $this->_rus;
                $data['lang']['_rus']['weekDays'] = $this->mdlCalendar->getRusWeekdayName();
                $data['page']['breadcrumb'][0]['href'] = "/studios";
                $data['page']['breadcrumb'][0]['title'] = "Студии";
                $data['page']['breadcrumb'][1]['href'] = "/studios/" . $this->studio['domain'];
                $data['page']['breadcrumb'][1]['title'] = $this->studio['name'];
                $data['page']['layout'] = "studios/studios_main.html";
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

    function methodTimetable($request){
        $data = array();

        if ($request['date']) {
            $data['page']['title'] = "Расписание на месяц";
            $data['menu']['active'] = "timetable";
            $lDate = date_create_from_format("Y-m", $request['date']);
            $data['calendar'] = $this->getMonth($lDate, $offset);
            $data['calendarOffset'] = $offset + 1;
        }
        else {
            $data['page']['title'] = "Расписание на ближайшие дни";
            $data['menu']['active'] = "timetable";
            $data['page']['byWeek'] = true;
            $lDate = new \DateTime();
            $data['calendar'] = $this->getWeek(0,2, $this->studio['id'], true);
            $data['calendarOffset'] = 0;
        }

//        $_today = new \DateTime();
//        if ($lDate->format("Y-m") != $_today->format("Y-m"))

//        else


        if ($data){
            $dt = date_create_from_format("Y-m-d", $lDate->format("Y-m") . "-01" );
            $data['calendarNav'] = $this->getCalendarNav($dt, 11, 1);
        }

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
	    $choosedWeek = $offset;

        $firstDay = clone $today;

        if ($firstDay->format("N") <> 0)
            $firstDay->add(\DateInterval::createFromDateString(- $firstDay->format("N") + 1 . " day"));

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

    /** @param $date \DateTime */
    function getMonth( $date, &$offset, $weekCount = 2, $events = true ) {
	    $today = new \DateTime();

        $lDate = date_create_from_format("Y-m-d", $date->format("Y-m"."-01"));

        $offset = intval($lDate->format("W")) - intval($today->format("W"));

        return $this->getWeek($offset, $weekCount, $this->studio['id'], $events);
    }

    function daysInWeek($weekNum, $year)
    {
        $result = array();
        //$datetime = new \DateTime('00:00:00');
        $datetime = date_create_from_format("Y h:i:s", $year . ' 00:00:00');
        $datetime->setISODate((int)$datetime->format('o'), intval($weekNum), 1);
        $interval = new \DateInterval('P1D');
        $week = new \DatePeriod($datetime, $interval, 6);

        foreach($week as $day){
            $result[] = $day;
        }
        return $result;
    }

    /** @param $date \DateTime
     * @return int Count week
     */
    function weeks_in_month ( $date ){
        $startDate = $date;
        $loopDate = $startDate;
        $week = 1;
        for ($i = $startDate->format('d'); $i <= $date->format("t"); $i++) {
            if ( $loopDate->format('w') % 7 == 0 ) {
                $week++;
            }
            $loopDate->modify('+1 day');
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
        if ($aDate){
            $aDate = clone $aDate;
            if (count($this->_rus)<=0)
                $this->_rus = $this->mdlCalendar->getRusMonthName();

            for ($i = 0; $i < $count; $i++){
                $aDate->add( \DateInterval::createFromDateString("+ 1 month") );
                $res[$i]["monthName"] = $this->_rus[$aDate->format('m')];
                $res[$i]["link"] = $aDate->format('Y-m');
            }
        }
        return $res;
    }

    function getCalendarNav($date, $postCount = 1, $prevCount = 0, $start){
        $res = array();
        if (empty($this->_rus))
            $this->_rus = $this->mdlCalendar->getRusMonthName();
        if (empty($start))
            $start = new \DateTime();

        $res['today'] = clone $start;
        $res['choosed'] = clone $date;

        $prev = clone $start;
        for ($i = 0; $i < $prevCount; $i++){
            $res['prev'][] = clone $prev->add( \DateInterval::createFromDateString('- '. $i . ' month') );
        }

        $next = clone $start;
        for ($i = 1; $i <= $postCount; $i++){
            $res['next'][] = clone $next->add( \DateInterval::createFromDateString('+ 1 month') );
        }

        return $res;
    }

    function getPrevNavDates($lDate){
        $res = array();
        if (empty($this->_rus))
            $this->_rus = $this->mdlCalendar->getRusMonthName();

        $res['today']['date'] = date();
        $res['today']['day'] = (int)date('d');
        $res['today']['monthName'] = $this->_rus [date('m')];
        $res['today']['year'] = date('Y');
        $res['today']['link']['day'] = date('Y-m');

        //$choosed = date_create_from_format("Y-m", $lDate);
        $choosed = clone $lDate;
        $res['choosed']['link'] = $choosed->format('Y-m');
        $res['choosed']['monthName'] = $this->_rus [$choosed->format('m')];

        $choosed->add( \DateInterval::createFromDateString('- 1 month') );
        $res['prevDate']['link'] = $choosed->format('Y-m');
        $res['prevDate']['monthName'] = $this->_rus [$choosed->format('m')];

        $choosed->add( \DateInterval::createFromDateString('+ 2 month') );
        $res['nextDate']['link'] = $choosed->format('Y-m');
        $res['nextDate']['monthName'] = $this->_rus [$choosed->format('m')];

        return $res;
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
