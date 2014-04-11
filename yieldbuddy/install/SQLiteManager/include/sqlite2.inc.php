<?php
/**
* Web based SQLite management
* Wrapper for Default SQLite 2.x Module
* @package SQLiteManager
* @author Tanguy Pruvot
* @version $Id: sqlite2.inc.php,v 1.2 2004/12/04 18:14:49 tpruvot Exp $ $Revision: 1.2 $
*/

function sqlitem_array_query($dhb,$query,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  return sqlite_array_query($dhb,$query,$result_type,$decode_binary);
}

function sqlitem_busy_timeout($dhb,$milliseconds=0) {
  return sqlite_busy_timeout($dhb,$milliseconds);
}

function sqlitem_changes($dhb) {
  return sqlite_changes($dhb);
}

function sqlitem_close($dhb) {
  return sqlite_close($dhb);
}

function sqlitem_column($result,$index_or_name,$decode_binary=TRUE) {
  return sqlite_column($result,$index_or_name,$decode_binary);
}

function sqlitem_create_aggregate($dhb,$function_name,$step_func,$finalize_func,$num_args=null) {
  return sqlite_create_aggregate($dhb,$function_name,$step_func,$finalize_func,$num_args);
}

function sqlitem_create_function($dhb,$function_name,$callback,$num_arg=null) {
  return sqlite_create_function($dhb,$function_name,$callback,$num_arg);
}

function sqlitem_current($result,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  return sqlite_current($result,$result_type,$decode_binary);
}

function sqlitem_error_string($err_code) {
  return sqlite_error_string($err_code);
}

function sqlitem_escape_string($str) {
  return sqlite_escape_string($str);
}

function sqlitem_fetch_array($dbh,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  return sqlite_fetch_array($dbh,$result_type,$decode_binary);
}

function sqlitem_fetch_single($result,$result_type=SQLITE_BOTH,$decode_binary=TRUE) {
  return sqlite_fetch_single($result,$result_type,$decode_binary);
}

function sqlitem_fetch_string($result,$result_type=SQLITE_BOTH) {
  return sqlite_fetch_string($result,$result_type);
}

function sqlitem_field_name($result,$index) {
  return sqlite_field_name($result,$index);
}

function sqlitem_has_more($result) {
  return sqlite_has_more($result);
}

function sqlitem_last_error($dbh) {
  return sqlite_last_error($dbh);
}

function sqlitem_last_insert_rowid($dbh) {
  return sqlite_last_insert_rowid($dbh);
}

function sqlitem_libencoding() {
  return sqlite_libencoding();
}

function sqlitem_libversion() {
  return sqlite_libversion();
}

function sqlitem_next($result) {
  return sqlite_next($result);
}

function sqlitem_num_fields($result) {
  return sqlite_num_fields($result);
}

function sqlitem_num_rows($result) {
  return sqlite_num_rows($result);
}

function sqlitem_open($filename,$mode,&$error_message) {
  return sqlite_open($filename,$mode,$error_message);
}

function sqlitem_popen($filename,$mode,&$error_message) {
  return sqlite_popen($filename,$mode,$error_message);
}

function sqlitem_query($p1,$p2) {
  return sqlite_query($p1,$p2);
}

function sqlitem_rewind($result) {
  return sqlite_rewind($result);
}

function sqlitem_seek($result,$numrow) {
  return sqlite_seek($result,$numrow);
}

function sqlitem_udf_decode_binary($data) {
  return sqlite_udf_decode_binary($data);
}

function sqlitem_udf_encode_binary($data) {
  return sqlite_udf_encode_binary($data);
}

function sqlitem_unbuffered_query($dbh,$query) {
  return sqlite_unbuffered_query($dbh,$query);
}

?>
