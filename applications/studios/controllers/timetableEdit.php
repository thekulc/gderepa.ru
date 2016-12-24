<?php
namespace studios;

class timetableEdit extends \Controller{
	function default_method(){
		if($_POST && isset($this->id)){
			$err = null;
			$res = $this->model->insertTimetable($_POST, $this->id, $err);
			if ($err){
				$data['message'] = $err;
				pr($data['message']);
			}
			else{
				echo '1'; //$this->redirect('/studios/id'.$this->id.'/timetable');
			}
		}
		else{
			if ($this->id){
				$studio = $this->model->getStudioById($this->id);
				$data['studio'] = $studio;
				$data['page']['title'] = "Управление расписанием студии &laquo;" . $studio['name'] . "&raquo;";
				//$data['page']['content'] = "Здесь можно настроить допустимое для бронирования расписание";
				$names = $this->getDaysOfWeekArray();
				$data['weekDays'] = $names['fullName'];
				$data['datepickerNames'] = '{months: ["'.implode('","', $this->getMonthName()).'"], weekdays: ["'.implode('","', $names['shortName']).'"]}';
				$this->layout_show('studios/timetable.html', $data);
			}
			
		}
	}

	function create(&$err){
		$studio = array();
		$studio['name'] = filter_input(INPUT_POST, 'name');
		$studio['description'] = filter_input(INPUT_POST, 'description');
		$studio['mapCoords'] = filter_input(INPUT_POST, 'mapCoords');
		$studio['address'] = filter_input(INPUT_POST, 'address');
		$studio['vakil_id'] = filter_input(INPUT_POST, 'vakil_id');
		return $this->model->newStudio($studio, $err);
	}

	function getDaysOfWeekArray(){
		return array(
			'fullName' => array(
				0 => 'Понедельник',
				1 => 'Вторник',
				2 => 'Среда',
				3 => 'Четверг',
				4 => 'Пятница',
				5 => 'Суббота',
				6 => 'Воскресенье'
			),
			'shortName' => array(
				6 => 'Вс',
				0 => 'Пн',
				1 => 'Вт',
				2 => 'Ср',
				3 => 'Чт',
				4 => 'Пт',
				5 => 'Сб'
			)
			);
	}

	function getMonthName(){
		return array(
			0 => 'Январь',
			1 => 'Февраль',
			2 => 'Март',
			3 => 'Апрель',
			4 => 'Май',
			5 => 'Июнь',
			6 => 'Июль',
			7 => 'Август',
			8 => 'Сентябрь',
			9 => 'Октябрь',
			10 => 'Ноябрь',
			11 => 'Декабрь'
		);
	}

}
?>