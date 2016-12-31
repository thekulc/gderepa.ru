<?php
namespace calendar;

class mdl_calendar extends \Model {
    
    public function getData($tbl){
        return $this->select($tbl);
    }
	
	function getSettings(){
		$data = $this->select("vk_settings");
		$res = array();
		foreach($data as $s){
			$res[$s->name] = $s->value;
		}
		return $res;
	}

	function authUserByCookie($hash){
		$vk_user = $this->selectArray("vk_users", "md5(access_token)='$hash'");
		$user['vk_user'] = json_decode($vk_user[0]['user_json'],true);
		$user['user'] = $this->selectArray("users", "vk_user_id='". $user['vk_user']['user_id'] ."'")[0];
		return $user;
	}
	
	function getUserByVKUser($VKUser, $createIfNull = false){
        $user = array();
		if ($createIfNull){
			$user = $this->getUserFromVKUser($VKUser);
			$this->insert("users", array( 0 => $user), "IGNORE");
			$user = $this->getUserByVKUser($VKUser, false)[0];
		}
		else{
			$user = $this->selectArray("users", "`vk_user_id`='". $VKUser['user_id'] ."'");
		}
		return $user;
	}
    
    function insert($table, $arrKeyValue, $sqlPre = "", $sqlAfter=""){
        //pr($arrKeyValue);die();
        $sql = "INSERT ". $sqlPre ." INTO `".$table."` SET " . $this->prepareInsertStringByArray($arrKeyValue) . " " .$sqlAfter;
		
		//if ($table == "vk_users"){
		//pr($sql);die();
		//}
		
        if (!mysql_query($sql))
            return mysql_error ();
        else 
            return true;
    }
	
	function getUserFromVKUser($arrVKUser){
		$user['FIO'] = $arrVKUser['first_name'] . " " . $arrVKUser['nickname'] . " " . $arrVKUser['last_name'];
		$user['avatar'] = $arrVKUser['photo_200_orig'];
		$user['email'] = $arrVKUser['email'];
		$user['login'] = $arrVKUser['user_id'];
		$user['get_message'] = 1;
		$user['vk_user_id'] = $arrVKUser['user_id'];
		return $user;
	}
	
	function addVKUser($vk_user){
		$arrKeyValue[$vk_user['user_id']]['user_id'] = $vk_user['user_id'];
		$arrKeyValue[$vk_user['user_id']]['access_token'] = $vk_user['access_token'];
		$arrKeyValue[$vk_user['user_id']]['user_json'] = json_encode($vk_user);
		return $this->insert("vk_users", $arrKeyValue, "", "ON DUPLICATE KEY update `lastRequest` = NOW(), `access_token`='". $vk_user['access_token'] ."', `user_json`='". $arrKeyValue[$vk_user['user_id']]['user_json'] ."'");
	}

    function getRusMonthName(){
        return array(
            '01'=>'Январь',
            '02'=>'Февраль',
            '03'=>'Март',
            '04'=>'Апрель',
            '05'=>'Май',
            '06'=>'Июнь',
            '07'=>'Июль',
            '08'=>'Август',
            '09'=>'Сентябрь',
            '10'=>'Октябрь',
            '11'=>'Ноябрь',
            '12'=>'Декабрь');
    }

    function getArendators(){
        return @$this->selectQuery("SELECT *, (select contacts from users_description where users_description.user_id = users.id) as contacts FROM `users` inner join user_role on (users.id = user_role.user_id) where user_role.role_id in (1,3)", true);
    }
    
    function getMonthEvents($date){
		if (count(explode("-",$date)) == 2)
			$dtStart = $date . '-01 00:00:00';
		else
			$dtStart = $date . ' 00:00:00';
        return $this->select("events", "`date` BETWEEN date_add('{$dtStart}', interval -1 month) AND date_add('{$dtStart}', interval +2 month)", "id, date, title, description, type_id, owner_id, (SELECT FIO FROM users WHERE id = owner_id LIMIT 1) as owner", "date DESC");
    }
    
    private function getStr($event){
        $res = "";
        foreach ($event as $key => $value) {
            $res .= $key . " = '" . $value . "', ";
        }
        
        return substr($res, 0, -2);
    }



}
?>
