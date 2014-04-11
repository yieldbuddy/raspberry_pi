<?php
include_once dirname(__FILE__) . '/sqlite.class.php';

class sqlite_2 extends sqlite {

    function __construct($dbPath) {
    	$this->dbVersion = 2;
    	if($dbPath == ':memory:') {
    		$this->readOnly = false;    		
    	} else {
    		$this->readOnly = !is_writable($dbPath);
    	} 
    	parent::__construct($dbPath);
    	$this->connect($dbPath);
    }
    
    function connect($dbPath) {
    	if(DEBUG) {
    		if($dbPath == ':memory:') $this->connId = sqlite_popen($dbPath, 0666, $this->error);
    		else $this->connId = sqlite_open($dbPath, 0666, $this->error);
    	} else {
    		if($dbPath == ':memory:') $this->connId = @sqlite_popen($dbPath, 0666, $this->error);
    		else $this->connId = @sqlite_popen($dbPath, 0666, $this->error);
    	}
    	return $this->connId;
    }
    
    function getError($errorCode = null) {
    	if(!$this->error) $this->error = sqlite_last_error($this->connId);
    	if($errorCode == null) $errorCode = $this->error;
    	$this->errorMessage = sqlite_error_string($errorCode);
    	return $this->errorMessage;
    }
    
    function getErrorMessage() {
    	return $this->errorMessage;
    }
    
    function query($sqlString, $buffered = true, $assign = true) {
 		if(substr(trim($sqlString), -1) != ';') $sqlString .= ';';
    	if($buffered) {
    		if(DEBUG) {
    			$resId = sqlite_query($this->connId, $sqlString);
    		} else {
    			$resId = @sqlite_query($this->connId, $sqlString);
    		}
    	} else {
    		if(DEBUG) $resId = sqlite_unbuffered_query($this->connId, $sqlString);
    		else $resId = @sqlite_unbuffered_query($this->connId, $sqlString);
    	}
    	if($assign) $this->resId = $resId;
    	return $resId;
    }
    
    function array_query($sqlString, $result_type=SQLITE_BOTH, $decode_binary=true) {
    	if(DEBUG) return sqlite_array_query($this->connId, $sqlString, $result_type, $decode_binary);
    	else return @sqlite_array_query($this->connId, $sqlString, $result_type, $decode_binary);
    }
    
    function num_rows($resId = null) {
    	if($resId == null) $resId = $this->resId;
    	if(DEBUG) $out =  sqlite_num_rows($resId);
    	else $out =  @sqlite_num_rows($resId);
    	return $out;
    }
    
    function fetch_single($resId=null, $result_type=SQLITE_BOTH) {
    	if($resId == null) $resId = $this->resId;
    	if(DEBUG) $out =  sqlite_fetch_string($resId,$result_type);
    	else $out =  @sqlite_fetch_string($resId,$result_type);
    	return $out;
    }
    
    function fetch_array($resId=null, $result_type=SQLITE_BOTH,$decode_binary=true) {
    	if($resId == null) $resId = $this->resId;
    	if(DEBUG) $out =  sqlite_fetch_array($resId,$result_type, $decode_binary);
    	else $out =  @sqlite_fetch_array($resId,$result_type, $decode_binary);
    	return $out;
    }

    function last_insert_id() {
    	return sqlite_last_insert_rowid($this->connId);
    }    
    
    function changes() {
    	if(DEBUG) $out =  sqlite_changes($this->connId);
    	else $out =  @sqlite_changes($this->connId);
    	return $out;    	
    }
        
    function num_fields($resId = null) {
    	if($resId == null) $resId = $this->resId;
    	if(DEBUG) $out =  sqlite_num_fields($resId);
    	else $out =  @sqlite_num_fields($resId);
    	return $out;    	
    }

    function field_name($resId = null, $index) {
    	if($resId == null) $resId = $this->resId;
    	if(DEBUG) $out =  sqlite_field_name($resId, $index);
    	else $out =  @sqlite_field_name($resId, $index);
    	return $out;    	
    }

	function create_function($function_name, $callback, $num_arg=null) {
		if(DEBUG) return sqlite_create_function($this->connId, $function_name, $callback, $num_arg);
		else return @sqlite_create_function($this->connId, $function_name, $callback, $num_arg);
	}
	
	function create_aggregate($function_name, $step_func, $finalize_func, $num_args=null) {
		if(DEBUG) return sqlite_create_aggregate($this->connId, $function_name, $step_func, $finalize_func, $num_args);
		else return @sqlite_create_aggregate($this->connId, $function_name, $step_func, $finalize_func, $num_args);
	}
        
    function sqlite_version() {
    	return sqlite_libversion();
    }
    
    function close() {
    	if(DEBUG) return sqlite_close($this->connId);
    	else return @sqlite_close($this->connId);
    }
    
	function sqlitem_busy_timeout($milliseconds=0) {
		if(DEBUG) $out = sqlite_busy_timeout($this->connId, $milliseconds);
		else $out = @sqlite_busy_timeout($this->connId, $milliseconds);
		return $out;
	}

	function beginTransaction() {
		$this->query('BEGIN TRANSACTION;', false, false);
	}
	
	function commitTransaction() {
		$this->query('COMMIT TRANSACTION;', false, false);
	}
	
	function rollBackTransaction() {
		$this->query('ROLLBACK TRANSACTION;', false, false);
	}
}
?>