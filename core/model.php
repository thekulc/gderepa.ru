<?php
class Model{
	
	function __construct (){
		
	}

	function selectQuery($query, $idKey, &$err){
		$res = array();
		if ($query){
			$res = $this->getFetchedArray($query, $idKey, $err);
        }
		else $err = "Имя таблицы не задано";
        return $res;
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

    function selectArray($tableName = null, $where = null, $what = NULL, $order = null, &$err = NULL){
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
            $res = $this->getFetchedArrayA($sql, false, $err);
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

    function prepareInsertStringByArray($arrayObjects, $separateKeyValue = " = ", $separateObjects = ", "){
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

    function prepareStringByArray($obj, $exclude = array(), $separateKeyValue = " = ", $separateObjects = ", "){
        $res = "";
        $tmp = (array) $obj;
        foreach ($tmp as $key => $value) {
            if(!in_array($key, $exclude))
                $res .= "`" . $key . "`" . $separateKeyValue . "'" . mysql_real_escape_string($value) . "'" . $separateObjects;
        }
        return substr($res, 0, strlen($res)-2);
    }
}