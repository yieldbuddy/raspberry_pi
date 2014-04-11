<?php
/**
* Web based SQLite management
* Wrapper for Default SQLite 2.x Module
* @package SQLiteManager
* @author Tanguy Pruvot
* @version $Id: sqlite3.inc.php,v 1.17 2005/11/11 21:14:40 freddy78 Exp $ $Revision: 1.17 $
*/
define('DEBUG_QUERIES',0);
if(!defined('PDO_ATTR_TIMEOUT')) 	define('PDO_ATTR_TIMEOUT', PDO::ATTR_TIMEOUT);
if(!defined('PDO_FETCH_ASSOC')) 	define('PDO_FETCH_ASSOC', PDO::FETCH_ASSOC);
if(!defined('PDO_FETCH_NUM')) 		define('PDO_FETCH_NUM', PDO::FETCH_NUM);
if(!defined('PDO_FETCH_BOTH')) 		define('PDO_FETCH_BOTH', PDO::FETCH_BOTH);

$last_result=array();

function sqlitem_array_query($dhb,$query,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  $q = sqlitem_query($dhb,$query); 
  $rows = array();
  while ($r = sqlitem_fetch_array($q,$result_type,$decode_binary)) {
   $rows[] = $r;
  }
  return $rows;
}

function sqlitem_busy_timeout($dhb,$milliseconds=0) {
  try {
    $dhb->setAttribute(PDO_ATTR_TIMEOUT, $milliseconds);
    return true;
  } catch (PDOException $e) { return false;}
}

function sqlitem_changes(&$dhb) {
	return $dhb->rowCount();
}

function sqlitem_close($dhb) {
  $GLOBALS['PDO_DB']=false;
  return true;
}

function sqlitem_column($result,$index_or_name,$decode_binary=TRUE) {
  if (is_numeric($index_or_name)) {
    $cols = array_keys($last_result);
    return $cols[$index_or_name];
  } else
    return $last_result[$index_or_name];
}

function sqlitem_create_aggregate($dhb,$function_name,$step_func,$finalize_func,$num_args=null) {
	if(method_exists($dhb, 'sqliteCreateAggregate')) {
		return $dhb->sqliteCreateAggregate($function_name,$step_func,$finalize_func,$num_args);
	} else {
		return false;
	}
}

function sqlitem_create_function($dhb,$function_name,$callback,$num_arg=null) {
	if(method_exists($dhb, 'sqliteCreateFunction')) {
		return $dhb->sqliteCreateFunction($function_name,$callback,$num_arg);
	} else {
		return false;
	}
}

function sqlitem_current($stmt,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  $res = sqlitem_fetch_array($stmt,$result_type,$decode_binary);
//  $stmt->
  return sqlite_current($result,$result_type,$decode_binary);
}

function sqlitem_error_string($dbh) {
	$err_code = $dbh->errorInfo();
	if(is_array($err_code) && isset($err_code[2])) {
		return $err_code[2];
	} else {
		return false;
	}
}

function sqlitem_escape_string($str) {
	if(function_exists('sqlite_escape_string')) {
		$res = sqlite_escape_string($str);
	} else {
		$res = str_replace("'","''",$str);
	}
	return $res;
}

function sqlitem_fetch_array($stmt,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  if($result_type == SQLITE_ASSOC) 		$fetch_style = PDO_FETCH_ASSOC;
  elseif($result_type == SQLITE_NUM) 	$fetch_style = PDO_FETCH_NUM;
  elseif($result_type == SQLITE_BOTH)  	$fetch_style = PDO_FETCH_BOTH;
  
  $res = $stmt->fetch($fetch_style);
  /*
  if ($stmt->errorCode() != PDO_ERR_NONE){
    $error = $stmt->errorInfo();
    print_r($error); die();
  }
  */
  if (($result_type & SQLITE_BOTH) || ($result_type & SQLITE_ASSOC)) {
    $GLOBALS['last_result_names'] = true;
  } else {
   $GLOBALS['last_result_names'] = false;
  }
  $GLOBALS['last_result'] = $res; 
  return $res;
}

function sqlitem_fetch_single($stmt,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  if($result_type == SQLITE_ASSOC) 		$fetch_style = PDO_FETCH_ASSOC;
  elseif($result_type == SQLITE_NUM) 	$fetch_style = PDO_FETCH_NUM;
  elseif($result_type == SQLITE_BOTH)  	$fetch_style = PDO_FETCH_BOTH;
  
  $res = $stmt->fetch($fetch_style);
  //$stmt = null;
  if(is_array($res)) foreach($res as $key=>$value) return $value;
  else return false;
}

function sqlitem_fetch_string($result,$result_type=SQLITE_BOTH) {
  return sqlitem_fetch_single($result,$result_type);
}

function sqlitem_field_name($result,$index) {
	$tempColInfo = $result->getColumnMeta($index);
	return $tempColInfo["name"];
}

function sqlitem_has_more($result) {
die('sqlitem_has_more not implemented in PDO');
  return sqlite_has_more($result);
}

function sqlitem_last_error($dbh) {
  return $dbh->errorCode();
}

function sqlitem_last_insert_rowid($dbh) {
  return $dbh->lastInsertId;
}

function sqlitem_libencoding() {
die('sqlitem_libencoding not implemented in PDO');
  return sqlite_libencoding();
}

function sqlitem_libversion() {
  global $SQLiteVersion;
  return $SQLiteVersion;
}

function sqlitem_next($result) {
die('sqlitem_next');
  return sqlite_next($result);
}

function sqlitem_num_fields($result) {
  return $result->columnCount();
}

function sqlitem_num_rows(&$result) {
  return $result->rowCount();
}

function sqlitem_open($filename,$mode,&$error_message) { 
  global $PDO_DB;
  try {
    $user = '';
    $password = '';
    $PDO_DB = new PDO('sqlite:'.$filename, $user, $password); 
    return $PDO_DB;
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

function sqlitem_popen($filename,$mode,&$error_message) {
  return sqlitem_open($filename,$mode,$error_message);
}

function sqlitem_query($p1,$p2) {

  if (is_object($p1)) {
    $dbh=$p1;
    $query =$p2;
  } else {
    $query =$p1;
    $dbh=$p2;
  }
  $stmt = $dbh->prepare($query);
  if (!$stmt) {
	  $res = $dbh->query($query);
  	  return $res;
  } else {
      $stmt->execute();
      return $stmt;
  }     
  return $stmt;
}

function sqlitem_rewind($result) {
die('sqlitem_rewind not implemented in PDO');
  return sqlite_rewind($result);
}

function sqlitem_seek($result,$numrow) {
die('sqlitem_seek not implemented in PDO');
  return sqlite_seek($result,$numrow);
}

function sqlitem_udf_decode_binary($data) {
die('sqlitem_udf_decode_binary not implemented in PDO');
  return sqlite_udf_decode_binary($data);
}

function sqlitem_udf_encode_binary($data) {
die('sqlitem_udf_encode_binary not implemented in PDO');
  return sqlite_udf_encode_binary($data);
}

function sqlitem_unbuffered_query($dbh,$query) {
  return sqlitem_query($dbh,$query);
}

?>
