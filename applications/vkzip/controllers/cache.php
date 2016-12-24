<?php	
namespace vkzip;

class cache extends \Controller {  
    
    function default_method()
    {
		
	}
	
	function getAudiosByUserId($userId){
		//select($tableName, $where = null, $what = NULL, $order = null, &$err = NULL){
		$this->get_controller("vkzip")->select("cache","user_id"=$userId);
	}
	
}