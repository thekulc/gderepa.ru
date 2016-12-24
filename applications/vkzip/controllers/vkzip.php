<?
namespace vkzip;

class vkzip extends \Controller {  
    
    function default_method()
    {
		$page['page'];
		$page['page']['title'] = 
			$_SESSION['vk_user']['first_name'] 
			. " " . $_SESSION['vk_user']['nickname']
			. " " . $_SESSION['vk_user']['last_name'];
		
		if (isset($_GET['logout'])){
			unset($_SESSION['vk_user']);
			setcookie('user_id', "", -1, '/');
			$this->redirect('/vkzip');
		}
		
		if (empty($_SESSION['vk_user'])){
			$page['page']['title'] = "Главная";
			$page['page']['description'] = "Выгрузка vk аккаунта в архив.";
			$page['page']['keywords'] = "скачать аккаунт vk";;
			
			$page['sections'] = $this->getSections(); 
			$page['layout'] = "auth_form.html";
		}
		else{
			if (isset($_GET['audio'])){
				$page['audios'] = $this->getUserAudios( $_SESSION["vk_user"]['user_id'] )['audios'];
			}
			$page['layout'] = "auth_in.html";
		}
		return $this->layout_show($page['layout'], $page);
    }
	
	function getUserAudios($userId){
		$data;
		$audios['all'] = $this->select("vk_audios", "user_id", $userId);
		$audios['albums'] = $this->select("vk_albums", "user_id", $userId);
		
		if ( count($audios['all']) > 0 ){
			$data['audios'] = $audios;
		}
		else{
			/////////////////////// 
			///////////////////////   В проверить полученные данные и сделать инсерт в базу
			
			
			
			
			$audios['all'] = $this->VKSendRequest("audio.get", array('owner_id' => $userId, 'count' => 50))['response'];
			$audios['count']['all'] = $data['audios']['all'][0];
			unset($audios['all'][0]);
			
			$audios['albums'] = $this->VKSendRequest("audio.getAlbums", array('owner_id' => $userId, 'offset' => 0, 'count' => 50))['response'];
			unset($audios['albums'][0]);
		}
		$data['audios'] = $audios;
		
		
		return $data;
	}
	
	function getAudiosByAlbum($album_id, $user_id){
		$data;
		$album = $this->select("vk_albums", "album_id", $album_id);
		if ($album){
			$audios = json_decode($album['album_json']['items']);
			$data = $audios;
		}
		else{
			$album = $this->VKSendRequest("audio.get", array('album_id' => $album_id));
			$insert['album_json'] = json_encode($album) ;
			$insert['album_id'] = $album_id;
			$insert['title'] = $album['title'];
			$insert['user_id'] = $user_id;
			
			$this->insert($insert, "vk_albums");
			
			$audio;
			foreach ($album['items'] as $item){
				$audio[$item['id']] = $item;
			}
			
			
		}
		
		
		return $audios;
	}
	
	function VKSendRequest($method, $request_params, $access_token = ""){
		if ($access_token)
			$request_params['access_token'] = $access_token;
		else {
			$request_params['access_token'] = $_SESSION['vk_user']['access_token'];
		}
		$host = 'https://api.vk.com';
		$request = "/method/" . $method . '?' . http_build_query($request_params);
		return json_decode(file_get_contents($host . $request . "&sig=" . md5($request . $_SESSION['vk_user']['secret'])), true);
	}
	
	function getSections($allows = true){
		$res = null;
		return $this->select("vk_sections", null,null,"GROUP by `allow`,`sort_order` ORDER BY `allow` DESC, `sort_order` ASC");
	}
    
    function insert($arrayData, $into, &$err){
		$res = false;
        $arrayStr = $this->getStr($arrayData);
		if ( count($arrayData) > 0 AND count($into) > 0 ){
			$sql = "INSERT INTO " . $into . " SET " . $arrayStr;
			if (mysql_query($sql))
				$res = true;
			else 
				$err = mysql_error ();
		}
		return $res;
	}
	
	function getStr($array){
        $res = "";
        foreach ($array as $key => $value) {
            $res .= $key . " = '" . $value . "', ";
        }
        
        return substr($res, 0, -2);
    }
	
	function select($tableName, $where = null, $what = NULL, $order = null, &$err = NULL){
		if (is_string($tableName) && strlen($tableName) > 0){
			if ($what)
				$sql = "SELECT ".$what." FROM ".$tableName;
			else 
				$sql = "SELECT * FROM ".$tableName;
			if ($where)
				$sql .= " WHERE ".$where;
			
			if($order)
				$sql .= " " . $order;
			
			$res = $this->getFetchedArray($sql, $err);
		}
		else{
			$err = "Имя таблицы не указано";
		}
        return $res;
    }
    
    private function getFetchedArray($sql, &$err){
        $res = array();
        if (strlen($sql) > 0){
            $result = mysql_query($sql);
            $res = $this->doFetch($result, $err);
        }
        else{
            $err = "Запрос к базе пуст!";
        }
        return $res;
    }
    
    private function doFetch($sqlRes, &$err) {
        $res = array();
        if (mysql_num_rows($sqlRes) > 0){
            while ($obj = mysql_fetch_object($sqlRes)) {
                $res[] = $obj;
            }
        }
        else {
            $err = mysql_error();
        }
        return $res;
    } 
}