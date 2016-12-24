<?php
namespace users;

class mdl_users extends \Model{

	var $defaultRoleId = 2;
	
	function getUserById($id, &$err){
		$user = null;
		if (is_int($id) && $id > 0){
			$query = "SELECT * FROM users WHERE id = '{$id}' limit 1";
	        $query = mysql_query($query);
	        $user = mysql_fetch_assoc($query);
		}
		else{
			$err = "ID пуст";
		}
		return $user;
	}

	function getUserByLogin($login){
		$login = mysql_real_escape_string($login);
		$query = "SELECT * FROM users WHERE login = '{$login}' limit 1";
        $query = mysql_query($query);
        $user = mysql_fetch_assoc($query);
        if ($user)
        	$user['roles'] = $this->getUserRoles($user['id']);
        return $user;
	}

	function getUserByEmail($email){
		$email = mysql_real_escape_string($email);
		$query = "SELECT * FROM users WHERE `email` = '". $email . "' limit 1";
        $query = mysql_query($query);
        $user = mysql_fetch_assoc($query);
        if ($user)
        	$user['roles'] = $this->getUserRoles($user['id']);
        return $user;
	}

	function getUserRoles($userId){
        $res = null;
        if($userId){
            $query = "SELECT *, (select name from roles where id = role_id) as role_name FROM `user_role` WHERE user_id = '{$userId}'";
            $query = mysql_query($query);
            $res = array();
            while ($role = mysql_fetch_assoc($query)) {
                $res[$role['role_id']] = $role;
            }
        }
        return $res;
    }

    function newUser($user, &$err){
    	if (!isset($user['roles']) OR count($user['roles']) <= 0){
    		$lRole = $this->getRoleById($this->defaultRoleId);
    		$role[$lRole['id']] = $lRole;
    	}
    	if (!$this->getUserByEmail($user['email'])){
            
            if (!isset($user['login']) || trim($user['login']) == "")
                $user['login'] = $user['email'];
            
			$query = "INSERT INTO `users` SET " . $this->prepareStringByArray($user);
    		if(mysql_query($query)){
    			$user['id'] = mysql_insert_id();
    			$this->setUserRoles($user['id'], $role, $err);
    		}
    		else{
    			$err = mysql_error();
    		}
    	}
        $newUser = $this->getUserById($user['id'], $err);
    	return $newUser;
    }

    function setUserRoles($userId, $roles, &$err){
    	$userRoles = null;
    	if (is_int($userId) && is_array($roles)){
    		$insertArray = array();
    		foreach ($roles as $role) {
    			$insertArray[] = array($role['id'] => $userId);
    		}
    		$query = "INSERT INTO `user_role` (role_id, user_id) VALUES " . $this->prepareInsertStringByArray($insertArray, ', ');
    		if (mysql_query($query))
    			$userRoles = $this->getUserRoles($userId);
    		else
    			$err = mysql_error();
    	}
    	else
    		$err = "Не указан id пользователя или роли";
    	return $userRoles;
    	
    }

    function getRoleById($id){
    	$query = "SELECT id, name as role_name FROM `roles` WHERE id = '{$id}' LIMIT 1";
        $query = mysql_query($query);
        return mysql_fetch_assoc($query);
    }
}