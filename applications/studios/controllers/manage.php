<?php
namespace studios;

class manage extends \Controller{
	function default_method(){
		
		if (!empty($this->id) AND explode("\\", get_class())[1] != $this->id){
			$data['studio'] = $this->model->getStudioByIdAndDomain($this->id);
			
			$data['page']['breadcrumb'][0]['href'] = "/studios";
			$data['page']['breadcrumb'][0]['title'] = "Студии";
			$data['page']['breadcrumb'][1]['href'] = "/studios/manage";
			$data['page']['breadcrumb'][1]['title'] = "Управление студиями";
			$data['page']['title'] = $data['studio']['name'];
			$layout = 'studios/manageOne.html';
		}
		else{
			$data['page']['title'] = "Управление студиями";
			$data['page']['breadcrumb'][0]['href'] = "/studios";
			$data['page']['breadcrumb'][0]['title'] = "Студии";
			$data['studios'] = $this->model->getStudiosByUserId($_SESSION['user']['id'], array(4,6), $err);
			$layout = 'studios/manageList.html';
		}
		
		
		$this->layout_show($layout, $data);
	}
}
?>