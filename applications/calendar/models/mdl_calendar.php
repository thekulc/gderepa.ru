<?php
namespace calendar;

class mdl_calendar extends \Model {
    
    public function getData($tbl){
        return $this->selectArray($tbl);
    }
	
	function getSettings(){

		$data = $this->selectArray("vk_settings");
		$res = array();
		foreach($data as $s){
			$res[$s['name']] = $s['value'];
		}
		return $res;
	}

	function authUserByCookie($hash){
        ///Comment: Update на новую версию базы не отлажен

		//$vk_user = $this->selectArray("vk_users", "md5(access_token)='$hash'");
        $vk_user = $this->getAll("SELECT * FROM `vk_users` WHERE md5(`access_token`)=?s", $hash);
		$user['vk_user'] = json_decode($vk_user[0]['user_json'],true);
        $where['vk_user_id'] = $user['vk_user']['user_id'];
		$user['user'] = $this->selectArray("users", $where)[0];
		return $user;
	}
	
	function getUserByVKUser($VKUser, $createIfNull = false){
        $user = array();
		if ($createIfNull){
			$user = $this->getUserFromVKUser($VKUser);
            $sql = "INSERT IGNORE INTO `users` SET ?u";
            $this->query($sql, $user);
			$user = $this->getUserByVKUser($VKUser, false)[0];
		}
		else{
            $where['vk_user_id'] = $VKUser['user_id'];
			$user = $this->selectArray("users", $where);
		}

		return $user;
	}

    function addVKUser($vk_user){
        $insert['user_id'] = $vk_user['user_id'];
        $insert['access_token'] = $vk_user['access_token'];
        $insert['user_json'] = json_encode($vk_user);
        $update['access_token'] = $insert['access_token'];
        $update['user_json'] = $insert['user_json'];

        $sql = "INSERT INTO ?n SET ?u ON DUPLICATE KEY UPDATE `lastRequest`=NOW(), ?u";
        return $this->query($sql, "vk_users", $insert, $update);
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

    function getMonthEvents($date){
		if (count(explode("-",$date)) == 2)
			$dtStart = $date . '-01 00:00:00';
		else
			$dtStart = $date . ' 00:00:00';
		$sql = "SELECT *, (SELECT `FIO` FROM `users` WHERE id = `owner_id` LIMIT 1) as owner FROM `events` LEFT JOIN timetables ON timetables.id = events.timetable_id WHERE events.`date` BETWEEN date_add(?s, interval -1 month) AND date_add(?s, interval +2 month) AND timetable_id=?i ORDER BY events.`date` DESC";
		$res = $this->getAll($sql, $dtStart, $dtStart, 14);
        return $res;
    }
}
?>
