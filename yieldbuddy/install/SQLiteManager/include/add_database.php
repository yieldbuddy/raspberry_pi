<?php
/**
* Web based SQLite management
* add database form
* check if the database is Ok
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: add_database.php,v 1.31 2006/04/17 09:06:46 freddy78 Exp $ $Revision: 1.31 $
*/

$tempError = error_reporting();
error_reporting(E_ALL & ~(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE));
$dbFilename = '';
$error = false;
if($action == 'saveDb'){
	if(!empty($_POST['dbname']) && !empty($_POST['dbpath'])){
		if(isset($_POST['dbpath'])) $dbFilename = SQLiteStripSlashes($_POST['dbpath']);
		if ($_POST['uploadDB']){
			if(is_dir(DEFAULT_DB_PATH) && is_writable(DEFAULT_DB_PATH)){
				if(move_uploaded_file($_FILES['dbRealpath']['tmp_name'], DEFAULT_DB_PATH.$_FILES['dbRealpath']['name'])) $dbFilename = DEFAULT_DB_PATH.$_FILES['dbRealpath']['name'];
			} else {
				$error = true;
				$message = '<li><span style="color: red; font-size: 11px;">'.$GLOBALS['traduct']->get(144).'</span></li>';
			}
		}
		if(DEMO_MODE && $_POST['dbname'] != ':memory:') $dbFilename = DEFAULT_DB_PATH.basename(str_replace("\\", '/', $dbFilename));
		$tempDir = dirname($dbFilename);
		if($tempDir == '.') $dbFile = DEFAULT_DB_PATH . $dbFilename;
		else $dbFile = $dbFilename;
		if(!$error) {
			if(isset($_POST['dbVersion']) && $_POST['dbVersion'] && !file_exists($dbFile)) {
				$newDb = $SQLiteFactory->sqliteGetInstance($dbFile, $_POST['dbVersion']);
				$newDb->query("CREATE TABLE tempFred (id integer);");
				$newDb->query("DROP TABLE tempFred;");
			} else {
				$newDb = $SQLiteFactory->sqliteGetInstance($dbFile);
			}
			if($newDb){
				if($newDb->dbVersion == 2) $newDb->close();
				else $newDb = null;
				$query = 'INSERT INTO database (name, location) VALUES ('.quotes(SQLiteStripSlashes($_POST['dbname'])).', '.quotes($dbFilename).')';
				if(!$db->query($query)) {
					$error = true;
					$message .= '<li><span style="color: red; font-size: 11px;">'.$GLOBALS['traduct']->get(100).'</span></li>';
				} else {
					if(DEBUG) $dbsel = $db->last_insert_id();
					else $dbsel = @$db->last_insert_id();
				}
			}
		} else {
			$error = true;
			$message .= '<li><span style="color: red; font-size: 11px;">'.$GLOBALS['traduct']->get(101).'</span></li>';
		}
	} else {
		$error = true;
		$message .= '<li><span style="color: red; font-size: 11px;">'.$GLOBALS['traduct']->get(102).'</span></li>';
	}
}
error_reporting($tempError);
if(!READ_ONLY && (!WITH_AUTH || (isset($SQLiteManagerAuth) &&  $SQLiteManagerAuth->getAccess('properties')))) {
	if(empty($action) || ($action=='passwd') || $error){
		if(!isset($_POST['dbname'])) 	$_POST['dbname'] = '';
		if(!isset($_POST['dbpath'])) 	$_POST['dbpath'] = '';
		if(!isset($_POST['dbFilename'])) $_POST['dbFilename'] = '';
		echo '	<form name="database" action="main.php" enctype="multipart/form-data" method="POST" onSubmit="checkPath();" target="main">
				<table width="400">
				<tr><td colspan="2" align="center">'.$GLOBALS['traduct']->get(103).'</td></tr>';
		if($error) echo '<tr><td colspan="2" align="center">'.$GLOBALS['traduct']->get(9).' : '.((isset($message))? $message : 'unknown' ).'</td></tr>';

		$disabled = false;
		if(isset($_POST['dbVersion'])) $forceDbVersion = $_POST['dbVersion'];
		if(count($sqliteVersionAvailable) == 1) {
			if(!isset($_POST['dbVersion'])) $forceDbVersion = $sqliteVersionAvailable[0];
			if($sqliteVersionAvailable[0] == 2) {
				$disabled = 3;
			} else {
				$disabled = 2;
			}
		} else {
			if(!isset($_POST['dbVersion'])) $forceDbVersion = 2;
		}
		echo '	<tr><td align="right">'.$GLOBALS['traduct']->get(19).' :&nbsp;</td><td><input type="text" class="text" name="dbname" value="'.$_POST['dbname'].'" size="20"></td></tr>
				<tr><td align="right">'.$GLOBALS['traduct']->get(229).':&nbsp;</td>
				<td>'.
				'2 :<input type="radio" name="dbVersion" value="2"'.(($forceDbVersion == 2)? ' checked' : '' ).(($disabled == 2)? ' disabled' : '' ).'> '.
				str_repeat('&nbsp;', 5).
				'3 :<input type="radio" name="dbVersion" value="3"'.(($forceDbVersion == 3)? ' checked' : '' ).(($disabled == 3)? ' disabled' : '' ).'>
				<span style="font-size: 10px">'.$GLOBALS['traduct']->get(230).'</span></td></tr>
				<tr><td align="center" colspan="2"><hr width="80%"</td></tr>
				<tr><td align="right" rowspan="3" style="white-space: nowrap; vertical-align: middle;">'.$GLOBALS['traduct']->get(104).' :&nbsp;</td><td><input type="file" class="file" name="dbRealpath" value="'.$_POST['dbpath'].'" size="20" onChange="checkPath();"></td></tr>
				<tr><td><input type="checkbox" name="uploadDB" value=1>&nbsp;'.$GLOBALS['traduct']->get(143).'</td></tr>
				<tr><td><input type="text" class="text" name="dbpath" value="'.$dbFilename.'" size="40"></td></tr>
				<tr><td colspan="2" align="center"><input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'"></td></tr>
				</table>
				<input type="hidden" name="action" value="saveDb">
				</form>';
	} else {
		if(!$noDb) echo "<script type=\"text/javascript\">parent.main.location='main.php?dbsel=$dbsel'; parent.left.location='left.php?dbsel=$dbsel';</script>";
		else echo "<script type=\"text/javascript\">parent.location='index.php?dbsel=$dbsel';</script>";
	}
}
?>
