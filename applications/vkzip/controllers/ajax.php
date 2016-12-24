<?php
namespace vkzip;

class ajax extends \Controller {  

	const maxDownloadAudios = 50;
    
    function default_method()
    {
		$response;
		
		switch ($_GET["request"]) {
			case "getAlbum": $response = $this->get_controller('vkzip')->getAudiosByAlbum( $_GET["album_id"], $_SESSION['vk_user']['user_id'] ); break;
			case "startQueue": $response = $this->addAlbumToDownloadQueue($_GET["album_id"]); break;
			
			
			//default:;
		}
		echo json_encode($response);
		return;
	}
	
	function addAlbumToDownloadQueue($album_id){
		$res = true;
		$into = "downloadQueue";
		$insert['user_id'] = $_SESSION['vk_user']['user_id'];
		
		if ($album_id == "all"){
			$req['owner_id'] = $insert['user_id'];
			$req['count'] = self::maxDownloadAudios;
			$audios = $this->get_controller('vkzip')->VKSendRequest("audio.get", $req)['response'];
			unset($audios[0]);
		}
		elseif ($album_id > 0){
			$audios = $this->getAudiosByAlbum($album_id);
		}
		
		$i = 0;
		foreach ($audios as $audio){
			$insert['url'] = $audio['url'];
			
			if ($this->get_controller('vkzip')->insert($insert, $into, $err) == false)
				$res = false;
			if ($err) return $err;
			$i++;
			if($i > self::maxDownloadAudios)
				break;
		}
		if ($err){
			$res['error'] = $err;
			$res['status'] = "error";
		}
		else{
			$res['status'] = "success";
		}
		$res['status'] = "error";
		$res = json_encode($res);
		return $res;
		
	}
	
	function getAudiosByAlbum($album_id){
		return $this->get_controller('vkzip')->VKSendRequest("audio.get", array('album_id' => $album_id))["response"];
	}
}
?>