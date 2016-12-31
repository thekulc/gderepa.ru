<?php
namespace studios;

class mdl_studios extends \Model{
	
	function getStudioByIdAndDomain($domain, $vakilRolesArray){
		$studio = array();
		if (intval($domain) <= 0){
			$studio = $this->getStudioByDomain($domain, $vakilRolesArray);
		}
		else{
			$studio = $this->getStudioById($domain, $vakilRolesArray);
			$studio['domain'] = "id" . $studio['id'];
		}
		return $studio;
	}
	
	function getStudioByDomain($domain, $vakilRolesArray){
		$studio = $this->selectArray("studios", "`domain` = '" . $domain . "'")[0];
		$studio['vakils'] = $this->getVakilsByStudioId($studio['id'], $vakilRolesArray);
		return $studio;
	}
	
	function newStudio($studio, &$err){
		$newStudio = null;
		$query = "INSERT INTO `studios` SET " . $this->prepareStringByArray($studio, array('vakil_id'));
		mysql_query($query);
		if (mysql_errno() > 0){
			$err = mysql_error();
		}
		else{
			$newStudio = $this->getStudioById(mysql_insert_id());
			if ($newStudio)
				$this->setUserRole($studio['vakil_id'], $newStudio['id'], 4, $err);
			
		}
		return $newStudio;
	}

	function getStudioById($id){
		$res = $this->selectArray('studios', 'id='.$id);
		return $res[0];
	}

	function setUserRole($user_id, $studio_id, $role_id, &$err){
		$res = false;
		if ($user_id != "" && $studio_id && $role_id != ''){
			$query = "INSERT INTO `user_studio` SET user_id='{$user_id}', studio_id='{$studio_id}', role_id='{$role_id}'";
			mysql_query($query);
			if (mysql_errno()>0)
				$err = mysql_error();
			else
				$res = true;
		}
		return $res;
	}

	function getStudiosByUserId($user_id, $vakilRolesArray = array(), &$err){
		$res = null;
		$query = "SELECT * from studios WHERE studios.id IN (select studio_id from user_studio where user_id = '{$user_id}')";
		
		$studios = $this->selectQueryArray($query, true, $err);
		if(count($vakilRolesArray) > 0){
			foreach ($studios as $studio) {
				$studio['vakils'] = $this->getVakilsByStudioId($studio['id'], $vakilRolesArray);
				$res[$studio['id']] = $studio;
			}
		}
		return $res;
	}

	function getVakilsByStudioId($studio_id, $rolesArray, &$err){
		if ($rolesArray)
			$rolesArray = implode("," , $rolesArray);
		else
			$rolesArray = 4;
		$query = "SELECT * FROM `users` WHERE users.id IN (select user_id from user_studio WHERE studio_id = {$studio_id} AND role_id IN ({$rolesArray}))";
		
		return $this->selectQueryArray($query, false, $err);
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

				$str = "INSERT INTO timetables (start_time, duration_time, cost, studio_id, `date`, `dayofweek`) VALUES ";
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

}