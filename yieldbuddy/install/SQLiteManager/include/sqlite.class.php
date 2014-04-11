<?php

class sqlite {
	
	var $dbPath;
	
	var $connId;
	
	var $error;
	
	var $errorMessage;
	
	var $readOnly;
	
	var $resId;
	
	var $dbVersion;
	
    function __construct($dbPath) {
    	$this->dbPath = $dbPath;
    	
    }
    
    function isReadOnly() {
    	return $this->readOnly;
    }
    
    function getVersion() {
    	return $this->dbVersion;
    }
    
   function getConnId() {
		if(is_resource($this->connId) || is_object($this->connId)) {
			return $this->connId;
		} else {
			return false;
		}
    } 
    
    function escape($string) {
		if(function_exists('sqlite_escape_string')) {
			$res = sqlite_escape_string($string);
		} else {
			$res = str_replace("'", "''", $string);
		}
		return $res;
    }    

	/**
	 * Get version number of SQLite database file
	 *
	 * @param string $fullPath full path with filename of the database file
	 * @return version number or false
	 */
	function getDbVersion($fullPath){
		if(!file_exists($fullPath)) $fullPath = DEFAULT_DB_PATH . $fullPath;
		$fp = @fopen($fullPath, 'r');
		if ($fp){
			$signature = fread($fp, 47);
			if($signature == "** This file contains an SQLite 2.1 database **") $dbVersion = 2;
			elseif(substr($signature, 0, 15) == "SQLite format 3") $dbVersion = 3;
			else $dbVersion = false;
			fclose($fp);
			return $dbVersion;
		}
	}   
}
?>