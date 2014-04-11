<?php
include_once dirname(__FILE__) . '/sqlite.class.php';
if( !defined('PDO_ATTR_TIMEOUT') ) 		define('PDO_ATTR_TIMEOUT', 		PDO::ATTR_TIMEOUT);
if( !defined('PDO_FETCH_ASSOC') ) 		define('PDO_FETCH_ASSOC', 		PDO::FETCH_ASSOC);
if( !defined('PDO_FETCH_NUM') ) 		define('PDO_FETCH_NUM', 		PDO::FETCH_NUM);
if( !defined('PDO_FETCH_BOTH') ) 		define('PDO_FETCH_BOTH', 		PDO::FETCH_BOTH);
if( !defined('PDO_ATTR_PERSISTENT') ) 	define('PDO_ATTR_PERSISTENT', 	PDO::ATTR_PERSISTENT);
if( !defined('PDO_ATTR_CASE') ) 		define('PDO_ATTR_CASE', 		PDO::ATTR_CASE);
if( !defined('PDO_CASE_NATURAL') ) 		define('PDO_CASE_NATURAL', 		PDO::CASE_NATURAL);
if( !defined('PDO_ATTR_AUTOCOMMIT') ) 	define('PDO_ATTR_AUTOCOMMIT', 	PDO::ATTR_AUTOCOMMIT);
if( !defined('PDO_ATTR_ERRMODE') ) 		define('PDO_ATTR_ERRMODE', 		PDO::ATTR_ERRMODE);
if( !defined('PDO_ERRMODE_EXCEPTION') ) define('PDO_ERRMODE_EXCEPTION', PDO::ERRMODE_EXCEPTION);
if( !defined('PDO_ERRMODE_SILENT') ) 	define('PDO_ERRMODE_SILENT', 	PDO::ERRMODE_SILENT);

if( !defined('SQLITE_BOTH') ) 	define('SQLITE_BOTH', 	PDO_FETCH_BOTH);
if( !defined('SQLITE_ASSOC') ) 	define('SQLITE_ASSOC', 	PDO_FETCH_ASSOC);
if( !defined('SQLITE_NUM') ) 	define('SQLITE_NUM', 	PDO_FETCH_NUM);

class sqlite_3 extends sqlite {


    function __construct($dbPath) {
     	$this->dbVersion = 3;
    	if($dbPath == ':memory:') {
    		$this->readOnly = false;    		
    	} else {
    		$this->readOnly = !is_writable($dbPath);
    	} 
    	parent::__construct($dbPath);
    	if(class_exists('PDO') && $this->connect($dbPath)) {
    		return $this->connId;
    	} else {
    		$this->getError();
    		return false;
    	}
    }
    
    function connect($dbPath) {
		try {
			$user = '';
			$password = '';
			$arrayAttrib = array();
			if($dbPath == ':memory:') $arrayAttrib[PDO_ATTR_PERSISTENT] = true;
			$this->connId = new PDO('sqlite:'.$dbPath, $user, $password, $arrayAttrib); 
			
			$this->connId->setAttribute(PDO_ATTR_CASE, PDO_CASE_NATURAL);
			$this->connId->query("PRAGMA count_changes=1;");
		} catch (PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false;
		}
		if(DEBUG) {
			$this->connId->setAttribute(PDO_ATTR_ERRMODE, PDO_ERRMODE_EXCEPTION);
		} else {
			$this->connId->setAttribute(PDO_ATTR_ERRMODE, PDO_ERRMODE_SILENT);
		}
    	return $this->connId;
    }
    
    function getError($errorCode = null) {
		if(is_resource($this->resId)) {
			$errorInfo = $this->resId->errorInfo();
		} else {
			$errorInfo = $this->connId->errorInfo();
		}
		if(is_array($errorInfo) && isset($errorInfo[2])) {
			$this->error = true;
			$this->errorMessage = $errorInfo[2];
		} else {
			$this->errorMessage = 'not an error';
		}
    	return $this->errorMessage;
    }
    
    function getErrorMessage() {
    	return $this->errorMessage;
    }
    
    function escape($string) {
		if(function_exists('sqlite_escape_string')) {
			$res = sqlite_escape_string($string);
		} else {
			$res = str_replace("'", "''", $string);
		}
		return $res;
    }
    
    function query($sqlString, $buffered = true, $assign = true) {
    	try {
    		if($assign && is_object($this->resId)) $this->resId->closeCursor();
			if(substr(trim($sqlString), -1) != ';') $sqlString .= ';';
	    	if(DEBUG) $resId = $this->connId->query($sqlString);
	    	else $resId = @$this->connId->query($sqlString);
	    	if($assign) $this->resId = $resId;
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	$tempErrorInfo = $this->connId->errorInfo();
    	if(is_array($tempErrorInfo) && isset($tempErrorInfo[0]) && ($tempErrorInfo[0] != '00000')) {
			$this->error = true;
			$this->errorMessage = $tempErrorInfo[2];
			return false;
    	}
    	return $resId;
    }
    
    function array_query($sqlString, $result_type=SQLITE_BOTH, $decode_binary=true) {
    	try {
			$result_type = $this->convertResultType($result_type);
			$q = $this->query($sqlString); 
			$rows = $q->fetchAll($result_type);
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
		return $rows;
    }
    
    function num_rows($resId = null) {
    	try {
	    	if($resId == null) $resId = $this->resId;
	    	if(DEBUG) $out = $resId->rowCount();
	    	else $out =  @$resId->rowCount();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;
    }
    
    function fetch_single($resId=null, $result_type=SQLITE_BOTH) {
    	try {
			$result_type = $this->convertResultType($result_type);
	    	if($resId == null) $resId = $this->resId;
	    	if(DEBUG) $out =  $resId->fetchColumn();
	    	else $out =  @$resId->fetchColumn();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;
    }
    
    function fetch_array($resId=null, $result_type=SQLITE_BOTH, $decode_binary=true) {
		try {
			$result_type = $this->convertResultType($result_type);
	    	if($resId == null) $resId = $this->resId;
	    	if(DEBUG) $out =  $resId->fetch($result_type);
	    	else $out =  @$resId->fetch($result_type);
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;
    }

    function last_insert_id() {
		try {
	    	if(DEBUG) $out = $this->connId->lastInsertId();
	    	else $out = @$this->connId->lastInsertId();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;	
    }    
    
    function changes($resId = null) {
    	try {
			if($resId == null) $resId = $this->resId;    		
    		if(is_object($resId)) $out = $this->num_rows($resId);
    		else $out = 0;
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;
    }
        
    function num_fields($resId = null) {
    	try {
	    	if($resId == null) $resId = $this->resId;
	    	if(DEBUG) $out =  $resId->columnCount();
	    	else $out =  @$resId->columnCount();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    	return $out;    	
    }

    function field_name($resId = null, $index) {
		try {
	    	if($resId == null) $resId = $this->resId;
	    	if(DEBUG) $tempColInfo = $resId->getColumnMeta($index);
	    	else $tempColInfo = @$resId->getColumnMeta($index);
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}	
		return $tempColInfo["name"];
    }

	function create_function($function_name, $callback, $num_arg=null) {
		try {
			if(method_exists($this->connId, 'sqliteCreateFunction')) {
				if(DEBUG) return $this->connId->sqliteCreateFunction($function_name, $callback, $num_arg);
				else return @$this->connId->sqliteCreateFunction($function_name, $callback, $num_arg);
			} else {
				return false;
			}
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
	}
	
	function create_aggregate($function_name, $step_func, $finalize_func, $num_args=null) {
		if(method_exists($this->connId, 'sqliteCreateAggregate')) {
			try {
				if(DEBUG) return $this->connId->sqliteCreateAggregate($function_name, $step_func, $finalize_func, $num_args);
				else return @$this->connId->sqliteCreateAggregate($function_name, $step_func, $finalize_func, $num_args);
	    	} catch(PDOException $e) {
				$this->error = true;
				$this->errorMessage = $e->getMessage();
				return false; 
	    	}
		} else {
			return false;
		}
	}
        
    function sqlite_version() {
		try {
	    	$query = "SELECT sqlite_version();";
	    	$res = $this->query($query, true, false);
	    	$Result = $this->fetch_single($res, SQLITE_NUM);
	    	if($Result) return $Result;
	    	else return false;
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
    }
    
    function close() {
    	// set obj to null
    }

	function sqlitem_busy_timeout($milliseconds=0) {
		try {
			$this->connId->setAttribute(PDO_ATTR_TIMEOUT, $milliseconds);
			return true;
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
	}
	
	function convertResultType($type) {
		$fetch_style = PDO_FETCH_BOTH;
		if($type == SQLITE_ASSOC) 		$fetch_style = PDO_FETCH_ASSOC;
		elseif($type == SQLITE_NUM) 	$fetch_style = PDO_FETCH_NUM;
		elseif($type == SQLITE_BOTH)  	$fetch_style = PDO_FETCH_BOTH;	
		return 	$fetch_style;
	}
	
	function beginTransaction() {
		try {
			$this->connId->beginTransaction();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
	}
	
	function commitTransaction() {
		try {
			$this->connId->commit();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
	}
	
	function rollBackTransaction() {
		try {
			$this->connId->rollBack();
    	} catch(PDOException $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
			return false; 
    	}
	}
}
?>
