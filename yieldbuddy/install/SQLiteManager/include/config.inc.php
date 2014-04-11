<?php
/**
* Web based SQLite management
* Check if the config database is OK
* and set a tab with the list of user's databases
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: config.inc.php,v 1.28 2006/04/18 06:43:20 freddy78 Exp $ $Revision: 1.28 $
*/

include_once INCLUDE_LIB."grab_global.php";
include_once INCLUDE_LIB."SQLite.i18n.php";
include_once INCLUDE_LIB."SQLiteAutoConnect.class.php";
include_once INCLUDE_LIB."common.lib.php";

$SQLiteFactory = new SQLiteAutoConnect();

function LastAction() {
  global $workDb, $db;
  if(isset($workDb))
   if ($workDb->connId && ($workDb->baseName!=":memory:")) {
   		if($workDb->connId->dbVersion == 2) $workDb->close();
		else $workDb = null;
		if($db->dbVersion == 2) $db->close();
		else $db = null;
  }
}
register_shutdown_function('LastAction');
if(isset($noframe)){
	session_register("noframe");
	$_SESSION["noframe"] = $noframe = true;
}

if(!file_exists("./theme/".$localtheme."/define.php")) {
	unset($_COOKIE["SQLiteManager_currentTheme"]);
	$localtheme = "default";
}
include_once("./theme/".$localtheme."/define.php");

if ( phpversion() < '5.3.0' ) {
	$bExtOk = CheckExtension('sqlite') || ( CheckExtension('pdo') && CheckExtension('pdo_sqlite') );
} else {
	$bExtOk = CheckExtension('pdo_sqlite');
}

if( !$bExtOk ) {
	displayError($traduct->get(6));
	exit;
} else {
	// Search SQLite versions (if available version2 and version3)
	$tabSQLiteVersion = array();
	if(function_exists('sqlite_open')) {
		$tabSQLiteVersion[] = sqlite_libversion();
	}	
	if(class_exists('PDO') && in_array('sqlite', PDO::getavailabledrivers())) {
		$dbVersion = new PDO('sqlite::memory:', '', '');
		$query = "SELECT sqlite_version();";
		$res = $dbVersion->query($query);
		$tabSQLiteVersion[] = $res->fetchColumn();	
		unset($dbVersion);	 
	}
	$SQLiteVersion = implode(' - ', $tabSQLiteVersion);
	$sqliteVersionAvailable = array();
	foreach($tabSQLiteVersion as $versionAvailable) {
		$sqliteVersionAvailable[] = substr($versionAvailable, 0, 1);
	}
	if(!defined('SQLiteDb')) {
		if(isset($sqliteVersionAvailable)) {
			define ("SQLiteDb", dirname(__FILE__) . "/config".(($sqliteVersionAvailable[0] == 2)? '' : $sqliteVersionAvailable[0] ).".db");
		} else {
			define("SQLiteDb", dirname(__FILE__) . "/config.db");
		}
	}
	
	$tempError = error_reporting();
	error_reporting(E_ALL & ~(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE));

	$db = $SQLiteFactory->sqliteGetInstance(SQLiteDb);
	
	if(!$db){
		displayError($traduct->get(7)." : $error");
		exit;
	}

	define("READ_ONLY", !is_writeable(SQLiteDb));
	/*
	if(!is_writeable(SQLiteDb)){	
		displayError($traduct->get(8));
		exit;
	}
	*/
	
	error_reporting($tempError);
	
	if(WITH_AUTH){
		include_once INCLUDE_LIB."SQLiteAuth.class.php";
		$SQLiteManagerAuth = new SQLiteAuth();
	}

	$query = "SELECT count(*) FROM database";
	if($db->query($query)){
			if(!$db->fetch_array()){
				displayHeader("");
				$noDb = true;
				include_once INCLUDE_LIB."add_database.php";
				if(empty($action) || $error) exit;
			}
	}

	// check if exist ':memory: database
	$query = "SELECT * FROM database WHERE location LIKE ':memory:'";
	if($db->query($query)){
		$tempMem = $SQLiteFactory->sqliteGetInstance(':memory:');
	}
	$tabDb = $db->array_query($query, SQLITE_ASSOC);
	
	if($dbsel){
		$tabInfoDb = $db->array_query("SELECT * FROM database WHERE id=$dbsel", SQLITE_ASSOC);
		$tabInfoDb = isset($tabInfoDb[0])?$tabInfoDb[0]:'';
	}
	
	$query = "SELECT name FROM sqlite_master WHERE type='table' AND name='attachment';";
	$existAttachTable = $db->array_query($query, SQLITE_ASSOC);
	if(empty($existAttachTable)) {
		// create table for attachment management
		$query = "CREATE TABLE attachment (
					id INTEGER PRIMARY KEY ,
					base_id INTEGER ,
					attach_id INTEGER) ;";
		$db->query($query);
	}
	$attachDbList = array();
	$attachLocation = array();
	if(!empty($dbsel)){
		// Get attach database list for dbsel
		$query = "SELECT attach_id, location, name FROM attachment LEFT JOIN database ON database.id=attachment.attach_id WHERE base_id=".$dbsel;
		$attachList = $db->array_query($query, SQLITE_ASSOC);
		$attachDbList = array();
		$attachInfo = array();
		foreach($attachList as $key=>$value) {
			$attachDbList[] = $value["attach_id"];	
			$attachInfo[$value["attach_id"]]["location"] = $value["location"];
			$attachInfo[$value["attach_id"]]["name"] = $value["name"];
		}	
	}		
}

?>
