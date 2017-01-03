<?php

require_once (ROOT . "core/classes/ClassSafeMySQL.php");

class Model extends \SafeMySQL {

    public $db;
	
	function __construct (){
        $opts['user'] =     USER;
        $opts['pass'] =     PASSWORD;
        $opts['db'] =       DATABASE;
        $opts['charset'] =  CHARSET;
        $opts['host'] =     HOST;
		parent::__construct($opts);
	}

    function selectQueryArray($query, $idKey, &$err){
        $res = array();
        if ($query){
            $res = $this->getFetchedArrayA($query, $idKey, $err);
        }
        else $err = "Имя таблицы не задано";
        return $res;
    }
    
    function select($tableName = null, $where = null, $what = NULL, $order = null, &$err = NULL){
		$res = array();
		if ($tableName){
			if ($what)
				$sql = "SELECT ".$what." FROM " . $tableName;
			else 
				$sql = "SELECT * FROM " . $tableName;
			if ($where)
				$sql .= " WHERE ".$where;
			
			if($order)
				$sql .= " ORDER BY " . $order;
			//pr($sql);
			$res = $this->getFetchedArray($sql, false, $err);
        }
		else $err = "Имя таблицы не задано";
        return $res;
    }

    function selectArray($tableName = null, $where = array(), $what = "*", $order = null, &$err = NULL){
        $res = array();
        if ($tableName){
            $sql = "SELECT ?p FROM ?n";
            if (count($where) > 0) {
                $sql .= " WHERE ?n = ?s";
                if($order) {
                    $sql .= " ORDER BY ?p";
                    $res = $this->getAll($sql,$what, $tableName, $where, $order);
                }
                else{
                    $res = $this->getAll($sql, $what, $tableName, array_keys($where)[0], $where[array_keys($where)[0]] ) ;
                }
            }
            else{
                $res = $this->getAll($sql, $what, $tableName);
            }
        }
        else $err = "Имя таблицы не задано";
        return $res;
    }

    private function getFetchedArrayA($sql, $idKey, &$err){
        $res = array();
        if (strlen($sql) > 0){
            $result = mysql_query($sql);
            $res = $this->doFetchA($result, $idKey, $err);
        }
        else{
            $err = "Запрос к базе пуст!";
        }
        return $res;
    }
    
    private function doFetchA($sqlRes, $idKey, &$err) {
        $res = array();
        if (mysql_num_rows($sqlRes) > 0){
            while ($obj = mysql_fetch_assoc($sqlRes)) {
                if ($idKey)
                    $res[$obj['id']] = $obj;
                else
                    $res[] = $obj;
            }
        }
        else {
            $err = mysql_error();
        }
        return $res;
    }
    
    private function getFetchedArray($sql, $idKey, &$err){
        $res = array();
        if (strlen($sql) > 0){
            $result = mysql_query($sql);
            $res = $this->doFetch($result, $idKey, $err);
        }
        else{
            $err = "Запрос к базе пуст!";
        }
        return $res;
    }
    
    private function doFetch($sqlRes, $idKey, &$err) {
        $res = array();
        if (mysql_num_rows($sqlRes) > 0){
            while ($obj = mysql_fetch_object($sqlRes)) {
				if ($idKey)
					$res[$obj->id] = $obj;
				else
					$res[] = $obj;
            }
        }
        else {
            $err = mysql_error();
        }
        return $res;
    }

    public function prepareInsertStringByArray($arrayObjects, $separateKeyValue = " = ", $separateObjects = ", "){
        $resultStr = "";
		if (count ( $arrayObjects ) == 1)
			$resultStr .= $this->prepareStringByArray(array_shift($arrayObjects), array(), $separateKeyValue, $separateObjects) . ', ';
		else
			foreach ($arrayObjects as $arrayObj) {
				if (is_array($arrayObj))
					$resultStr .= "(" . $this->prepareStringByArray($arrayObj, array(), $separateKeyValue, $separateObjects) . '), ';
			}
        return substr($resultStr, 0, strlen($resultStr)-2);
    }

    function prepare5StringByArray($obj, $exclude = array(), $separateKeyValue = " = ", $separateObjects = ", "){
        $res = "";
        $tmp = (array) $obj;
        foreach ($tmp as $key => $value) {
            if(!in_array($key, $exclude))
                $res .= "`" . $key . "`" . $separateKeyValue . "'" . mysql_real_escape_string($value) . "'" . $separateObjects;
        }
        return substr($res, 0, strlen($res)-2);
    }
}