<?php
namespace studios;

class add extends \Controller{
	function default_method(){
		if(isset($_POST['name'])){
			$data['page']['title'] = "Новая студия " . $_POST['name'];
			$newStudio = $this->create($err);
			if ($err){
				$data['message'] = $err;
				$data['studio'] = $_POST;
				$this->layout_show('studios/add.html', $data);
			}
			else{
				$this->redirect('/studios/id'.$newStudio['id']);
			}
		}
		else{
			$data['page']['title'] = "Добавление студии";
			$data['page']['breadcrumb'][0]['href'] = "/studios";
			$data['page']['breadcrumb'][0]['title'] = "Студии";
			$this->layout_show('studios/add.html', $data);
		}
	}

	function create(&$err){
		$studio = array();
		$studio['name'] = $_POST['name'];
		$studio['description'] = $_POST['description'];
		$studio['mapCoords'] = $_POST['mapCoords'];
		$studio['address'] = $_POST['address'];
		$studio['vakil_id'] = $_POST['vakil_id'];
		return $this->model->newStudio($studio, $err);
	}

}
?>