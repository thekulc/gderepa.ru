<?php
namespace studios;

class mdl_calendar extends \Model{
    var $_rus;
    var $_weekDays;

    function getStudioByIdAndDomain($domain, $vakilRolesArray){
		$studio = array();
		if (intval($domain) <= 0){
			$studio = $this->getStudioByDomain($domain, $vakilRolesArray);
		}
		else{
			$studio = $this->getStudioById($domain, $vakilRolesArray);
		}
		return $studio;
	}
	
	function getStudioByDomain($domain, $vakilRolesArray){
	    $where['domain'] = $domain;
		$studio = $this->selectArray("studios", $where, "*, (SELECT ifnull ( `studios`.`domain`, concat(\"id\",`studios`.`id`) ) ) as 'alias'")[0];
		$studio['vakils'] = $this->getVakilsByStudioId($studio['id'], $vakilRolesArray);
		return $studio;
	}
	
	function newStudio($studio, &$err){
		$newStudio = null;
        $vakil_id = $studio['vakil_id'];
		unset($studio['vakil_id']);
        $sql = "INSERT INTO `studios` SET ?u";
        $this->query($sql, $studio);
        $id = $this->insertId();
		if ($id){
			$newStudio = $this->getStudioById($id);
			if ($newStudio)
				$this->setUserRole($vakil_id, $id, 4, $err);
			
		}
		return $newStudio;
	}

	function getStudioById($id){
	    $where['id'] = $id;
		$res = $this->selectArray('studios', $where);
		return $res[0];
	}

	function setUserRole($user_id, $studio_id, $role_id, &$err){
		$res = false;
		if ($user_id != "" && $studio_id && $role_id != ''){
			$insert["user_id"] = $user_id;
			$insert["studio_id"] = $studio_id;
			$insert["role_id"] = $role_id;
            $sql = "INSERT IGNORE INTO `user_studio` SET ?u";
            $res = $this->query($sql, $insert);
		}
		return $res;
	}

	function getStudiosByUserId($user_id, $vakilRolesArray = array(), &$err){
		$res = null;
		$query = "SELECT *, (SELECT ifnull ( `studios`.`domain`, concat(\"id\",`studios`.`id`) ) ) as 'alias' from `studios` WHERE `studios`.`id` IN (select `studio_id` from `user_studio` where `user_id` = ?i)";
		$studios = $this->getAll($query, $user_id);

		if(count($vakilRolesArray) > 0){
			foreach ($studios as $studio) {
				$studio['vakils'] = $this->getVakilsByStudioId($studio['id'], $vakilRolesArray);
				$res[$studio['id']] = $studio;
			}
		}
		return $res;
	}

	function getVakilsByStudioId($studio_id, $rolesArray, &$err){
		if (count($rolesArray) <= 0 )
            $rolesArray = array(4);

		$query = "SELECT * FROM `users` WHERE users.id IN (select user_id from user_studio WHERE studio_id = ?i AND role_id IN (?a))";
		$res = $this->getAll($query, $studio_id, $rolesArray);
        return $res;
	}

	function updateTimetable($post, $studio_id, &$err){
		$res = false;
		$query = $this->getTimetableUpdateQuery($post, $studio_id, $err);
		if ($query){
			//$res = mysql_query($query);
			if (!$res){
				$err = mysql_error();
			}
		}
		return $res;

	}

	function getTimetableInsertQuery($post, $studio_id, &$err){
		$str = "";
		if ($studio_id != ""){

			
			$date = $post['date'];
			unset($post['date']);
			
			$tArr = array();
			$params = array();
			foreach ($post as $key => $value) {
				switch ($key) {
					case 'forEveryday':
						$params = array($studio_id,'','');
						break;
					case 'forDate':
						$params = array($studio_id, $date,'');
						break;
					default:
						$params = array($studio_id, '',$key);
						break;
				}
				$tempArr = $this->getTimetableStr($value, $params);

				if($tempArr){
					$tArr[] = $tempArr;
				}
			}
			if (count($tArr)>0){
				// TODO
				//Проверить, есть ли в базе такие данные. 
				//Те, которых нет, - внести, которые есть, но отличаются - обновить, остальные удалить

				$str = "INSERT INTO timetables (`start_time`, `duration_time`, `cost`, `studio_id`, `date`, `dayofweek`) VALUES ";
				$str .= implode(",", $tArr);
				//$str .= " ON DUPLICATE ";
			}
			else
				$str = "";
		}
		else{
			$err = "ID студии не указан";
		}
		return $str;
	}

	function getTimetableStr($t, $appendVals = null){
		$d = 0;
		$count = count($t);
		$str = null;
		foreach ($t as $k => $v)
		{
		    $i = 0;
		    $d++;
		    $max_i = 0;
		    
		    foreach ($v as $vv)
		    {
		        $vv = $vv ? "'".$vv."'" : "''";
		        $arr[$i][] = $vv;
		        $i++;
		        if ($max_i < $i) $max_i = $i;
		    }
		    if ($count == $d)
		    {
		        $arr = array_chunk($arr,$max_i);
		        foreach ($arr[0] as $g => $gg)
		        {
		        	if(trim($gg[2]) != "''"){
	        	        foreach ($appendVals as $apVal) {
	        		    	array_push($gg, "'". $apVal ."'");
	        		    }

			            $str[] = "(".implode(",",$gg).")";
		        	}
		        }
		        if (count($str)>0)
		        	$str = implode(",",$str);
		    }
		}
		return $str;
	}

	function getEventsByPeriod($firstDay, $lastDay, $studio_id){
        $sql = "SELECT ev.*, 
              (SELECT `FIO` FROM `users` WHERE id = `owner_id` LIMIT 1) as owner
            FROM `events` ev
            WHERE (
              DATE (ev.date) BETWEEN ?s AND ?s
            ) and studio_id = ?i
            ORDER BY ev.`date` ASC
        ";
        return $this->getAll($sql, $firstDay, $lastDay, $studio_id);
    }

    function getStudioTimetables($studio_id){
	    $format = "H:i:s";
        $sql = "SELECT * FROM timetables WHERE studio_id=?i ORDER BY `dayofweek` ASC, `date` ASC";
        $result = $this->query($sql, $studio_id);
        while ($timetable = $this->fetch($result)){
            $end = new \DateTime($timetable['end_time']);
            $timetable['end_time'] = $end->format($format);
            if (!is_null($timetable['date']))
                $timetables['dates'][$timetable['date']] = $timetable;
            elseif (!is_null($timetable['dayofweek']))
                $timetables['dayofweeks'][$timetable['dayofweek']] = $timetable;
            else
                $timetables['allDays'] = $timetable;
        }
        return $timetables;
    }

    function getMonthEvents($date, $studio_id){
        if (count(explode("-",$date)) == 2)
            $dtStart = $date . '-01 00:00:00';
        else
            $dtStart = $date . ' 00:00:00';
        $sql = "SELECT *, (SELECT `FIO` FROM `users` WHERE id = `owner_id` LIMIT 1) as owner FROM `events` WHERE events.`date` BETWEEN date_add(?s, interval -1 month) AND date_add(?s, interval +2 month) AND studio_id=?i ORDER BY events.`date` DESC";

        return $this->getAll($sql, $dtStart, $dtStart, $studio_id);
    }

    function getRusMonthName(){
        $this->_rus = array(
            '01'=>'Января',
            '02'=>'Февраля',
            '03'=>'Марта',
            '04'=>'Апреля',
            '05'=>'Мая',
            '06'=>'Июня',
            '07'=>'Июля',
            '08'=>'Августа',
            '09'=>'Сентября',
            '10'=>'Октября',
            '11'=>'Ноября',
            '12'=>'Декабря');
        return $this->_rus;
    }

    function getRusWeekdayName(){
        $this->_weekDays = array(
            1=>'Понедельник',
            2=>'Вторник',
            3=>'Среда',
            4=>'Четверг',
            5=>'Пятница',
            6=>'Суббота',
            7=>'Воскресенье'
        );
        return $this->_weekDays;
    }

}