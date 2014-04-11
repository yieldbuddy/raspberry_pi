<?php
/**
* Web based SQLite management
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: main.php,v 1.45 2006/04/14 20:18:23 freddy78 Exp $ $Revision: 1.45 $
*/

session_start();
ob_start();
include_once "include/defined.inc.php";
include_once INCLUDE_LIB."config.inc.php";

if(ADVANCED_EDITOR && file_exists(SPAW_PATH."spaw_control.class.php") 
	&& isset($_COOKIE["SQLiteManager_HTMLon"]) && !$_COOKIE["SQLiteManager_HTMLon"]) {
	$spaw_root = SPAW_PATH;
	$spaw_dir = str_replace($_SERVER["DOCUMENT_ROOT"], "", $spaw_root);
	$spaw_base_url = "http://".$_SERVER["HTTP_HOST"].$spaw_dir;
	include_once SPAW_PATH."spaw_control.class.php";
}
if(JSCALENDAR_USE && file_exists(JSCALENDAR_PATH . 'calendar.php')) {
		include_once JSCALENDAR_PATH . 'calendar.php';
		$GlobalCalendar = new DHTML_Calendar(JSCALENDAR_PATH, $langSuffix, 'calendar-win2k-1', false);
} 
if(empty($dbsel) && ( !isset($GLOBALS["action"]) || ($GLOBALS["action"]!="auth") || (($GLOBALS["action"]=="auth") && !$SQLiteManagerAuth->isAdmin()) )){
displayHeader("main");
?>
<h2 class="sqlmVersion"><?php echo $traduct->get(2)." ".SQLiteManagerVersion ?></h2>
	<h4 class="serverInfo"><?php echo $traduct->get(3)." ".$SQLiteVersion ?> / <?php echo $traduct->get(150)." ".phpversion() ?></h4>
	<?php if(READ_ONLY) echo '<table width="80%" align="center"><tr><td style="font-size: 10px; border: 1px solid red; color: red; align: center">'.$traduct->get(154).'</td></tr></table>'; ?>

	<table align="center" cellspacing="0" cellpadding="0" class="home">
		<tr>
			<td class="boxtitle">SQLite
			</td>
			<td class="boxtitlespace">&nbsp;
			</td>
			<td class="boxtitle">SQLiteManager
			</td>
		</tr>
		<tr>
			<td style="white-space: nowrap"><?php include INCLUDE_LIB."add_database.php"; ?></td>
			<td class="space">&nbsp;</td>
			<td align="left" style="white-space: nowrap"><form action="main.php" method="POST">
				<table align="center" cellspacing="10" cellpadding="0">
				<tr><td style="white-space: nowrap"><?php echo $traduct->get(141); ?> :&nbsp;</td>
				<td><select name="Langue" onChange="submit()"><?php echo getAvailableLanguage(); ?></select></td></tr>
				<tr><td style="white-space: nowrap"><?php echo $traduct->get(142); ?> :&nbsp;</td>
				<td><select name="Theme" onChange="submit()"><?php echo getAvailableTheme(); ?></select></td></tr>
				</table>
        </form>
				    &nbsp;&raquo;&nbsp;<a href="http://www.sqlite.org/" target="docs" class="Browse"><?php echo $traduct->get(4); ?></a>
				<br>&nbsp;&raquo;&nbsp;<a href="http://www.sqlite.org/lang.html" target="docs" class="Browse"><?php echo $traduct->get(5); ?></a>
				<br>&nbsp;&raquo;&nbsp;<a href="http://www.sqlitemanager.org" target="docs" class="Browse"><?php echo $traduct->get(231); ?></a>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="space">&nbsp;</td>
      		<td>
				<?php if(WITH_AUTH){ ?> <hr style="border: 1px dashed blue"> <?php } ?>
				<div style="text-align:left; padding:5px;">
				<?php if(!READ_ONLY && WITH_AUTH && $SQLiteManagerAuth->isAdmin()) { ?>
				&raquo;&nbsp;<a href="?action=auth" class="Browse"><?php echo $traduct->get(156); ?></a>
				<?php } ?>
				<?php if(!READ_ONLY && WITH_AUTH && ALLOW_CHANGE_PASSWD) {
					if($GLOBALS["action"] != "passwd") { ?>
				<br>&raquo;&nbsp;<a href="main.php?action=passwd" class="Browse"><?php echo $traduct->get(157); ?></a><br><br>
					<?php } else {
					// manage passwd
					echo "<blockquote>";
					$SQLiteManagerAuth->changePasswd();
					echo "</blockquote>";
					} }
					if(WITH_AUTH){
					?>
				&raquo;&nbsp;<a href="index.php?action=logout" target="_parent" class="Browse"><?php echo $traduct->get(158); ?></a>
					<?php } ?>
				</div>
			</td>
		</tr>
	</table>
<?php
} elseif(!isset($GLOBALS["action"]) || ($GLOBALS["action"]!="auth")) {
	// gestion de la base selectionné
	include_once INCLUDE_LIB."SQLiteDbConnect.class.php";
	$tempDir = dirname($tabInfoDb['location']);
	if($tempDir == '.') $baseLocation = DEFAULT_DB_PATH . $tabInfoDb['location'];
	else $baseLocation = $tabInfoDb['location'];
	$workDb = new SQLiteDbConnect($baseLocation);
	
	displayHeader("main");

	displayMenuTitle();	

	$workDb->includeUDF();
	if(ALLOW_FULLSEARCH){
		include_once INCLUDE_LIB."sqlite_fulltextsearchex.class.php";
		$sqlite_fts = new sqlite_fulltextsearchex ();
		$sqlite_fts->register ($workDb->connId);
		$sqlite_fts->use_against_cache = true; 
	}
	
	switch($action){
		case '':
		case 'properties':
		default:
			if($workDb->isReadable()) {
				if($table || $TableName) $fileProp = 'tableproperties';
				elseif($view || $ViewName || isset($_POST['ViewName'])) $fileProp = 'viewproperties';
				elseif($trigger || $TriggerName) $fileProp = 'triggerproperties';
				elseif($function) $fileProp = 'functproperties';
				else $fileProp = 'dbproperties';
				if (isset($GLOBALS['plugin'])) {
					include_once 'plugins/'.$GLOBALS['plugin'].'/'.(isset($GLOBALS['file'])?$GLOBALS['file']:'plugin').'.php';
					if (isset($GLOBALS["action"]) && function_exists('plugin_'.$GLOBALS["action"])) {
					$function = 'plugin_'.$GLOBALS["action"];
					$function();
					break;
					}
				}
				include_once INCLUDE_LIB.$fileProp.'.php';
			}
			break;
		case 'browseItem':
			if($workDb->isReadable()) include INCLUDE_LIB.'browse.php';
			break;
		case 'sql':
			if($workDb->isReadable()) {
				include INCLUDE_LIB.'sql.php';
				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				  echo "<script  type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."';</script>";
			}
			break;
		case "export":
			if($workDb->isReadable()) {
				include_once INCLUDE_LIB."SQLiteExport.class.php";
				$export = new SQLiteExport($workDb);			
			}
			break;
		case "del":
			$query = "DELETE FROM database WHERE id=".$dbsel.";";
			if($dbsel) {
				$db->query($query);
				// Remove attached databases
				$db->query("DELETE FROM attachment WHERE base_id=".$dbsel." OR attach_id=".$dbsel.";");				
			}				
			$redirect = "<script  type=\"text/javascript\">parent.location='index.php';</script>";
			break;
		case "add_view":
			if($workDb->isReadable()) {
				$action = "add";
				include_once INCLUDE_LIB."viewproperties.php";
			}				
			break;
		case "add_trigger":
			if($workDb->isReadable()) {
				$action = "add";
				include_once INCLUDE_LIB."triggerproperties.php";
			}
			break;
		case "add_function":
			if($workDb->isReadable()) {
				$action = "add";
				include_once INCLUDE_LIB."functproperties.php";
			}
			break;
		case "options":
			if($workDb->isReadable()) {
				include_once INCLUDE_LIB."SQLiteDbOption.class.php";
				$Option = new SQLiteDbOption($workDb);
				if ($_SERVER['REQUEST_METHOD'] == 'POST' || (isset($_REQUEST['attach_action']) && $_REQUEST['attach_action']=='del'))
				  echo "<script  type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."';</script>";			
			}
			break;
	}
	
} elseif(isset($GLOBALS["action"]) && ($GLOBALS["action"]=="auth")){
	displayHeader("main");
	$SQLiteManagerAuth->manageAuth();
}

if(isset($redirect) && !empty($redirect)){
        ob_end_clean();
        echo $redirect;
}
@ob_end_flush();
?>
