<?php
Controller::set_global('thisurl', $url4parse);
if (isset($_SESSION['user'])){
	Controller::set_global('user', $_SESSION['user']);
	Controller::set_global('vk_user', $_SESSION['vk_user']);
}
?>