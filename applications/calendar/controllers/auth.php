<?php	
namespace calendar;

class auth extends \Controller {  
    
    function default_method()
    {
		$page['page']['layout'] = "auth_fail.html";
		$page['page']['title'] = "Ответ на запрос авторизации";
		
		if (isset($_GET['code'])){
			$user_code = $_GET['code'];
			$vk_user;

			$settings = $this->getParams();
			
			$url = 'https://oauth.vk.com/access_token?' .
				'client_id=' . $settings['client_id'] .
				'&code=' . $user_code .
				'&client_secret=' . $settings['client_secret'] .
				'&redirect_uri=' . $settings['redirect_uri'];
			
			$resp = file_get_contents($url);
			$vk_user = json_decode($resp, true);
			if ( isset($vk_user['access_token']) AND count( $vk_user['access_token'] ) > 0 ) {
				$page['page']['layout'] = "auth_success.html";
				
				$reqUser['user_id'] = $vk_user['user_id'];
				$reqUser['fields'] = "domain,nickname,photo_200_orig,photo_50";
				
				$_SESSION['vk_user'] = array_merge(
					$_SESSION['vk_user'],
					$vk_user,
					$this->VKSendRequest("users.get", $reqUser)['response'][0]
				);

				$_SESSION['user'] = $this->model->getUserByVKUser( $_SESSION['vk_user'], true );

				$this->model->addVKUser($_SESSION['vk_user']);
                pr($_SESSION);
				setcookie("data", md5($vk_user['access_token']));
			}
			else{
				unset($_SESSION['vk_user']);
			}
		}
		elseif (isset($_GET['setUserSectionPermissions'])){
			$auth_url = $this->setUserSectionPermissions($_GET);
			$this->redirect($auth_url); 
		}
		else{
			unset($_SESSION['vk_user']);
		}
		return $this->layout_show("auth/" . $page['page']['layout'], $page);
	}
	
	function setUserSectionPermissions($request){
		$vkParams = $this->getParams();
		$vkParams["scope"] .= "," . $request['sections'];
		$_SESSION['vk_user']['scope'] = $vkParams["scope"];
		return 'https://oauth.vk.com/authorize?' . http_build_query($vkParams);
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
	
	function getParams(){
		$data = $this->model->getSettings();
		$data['response_type'] = 'code';
		$data['display'] = 'popup';
		$data['v'] = '5.60';
		return $data;
	}
	
	
}