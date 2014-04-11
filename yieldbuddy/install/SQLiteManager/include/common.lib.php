<?php
/**
* Web based SQLite management
* Some functions
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: common.lib.php,v 1.126 2006/04/18 07:18:52 freddy78 Exp $ $Revision: 1.126 $
*/

/**
* check if SQLite extension is loaded, and if not load it.
*/
function CheckExtension($extName) {

	$SQL_SERVER_OS = strtoupper(substr(PHP_OS, 0, 3));
	if($SQL_SERVER_OS == 'WIN') { $preffix= 'php_'; $suffix = '.dll'; }
	elseif($SQL_SERVER_OS == 'NET') { $preffix= 'php_'; $suffix = '.nlm'; }
	elseif(($SQL_SERVER_OS == 'LIN') || ($SQL_SERVER_OS == 'DAR')) { $preffix= ''; $suffix = '.so'; }

	$extensions = get_loaded_extensions();
	foreach ($extensions as $key=>$ext) $extensions[$key] = strtolower($ext);
	if (!extension_loaded($extName) && !in_array($extName, $extensions)) {
		if(DEBUG) {
			$oldLevel = error_reporting();
			error_reporting(E_ERROR);
			$extensionLoaded = dl($preffix.$extName.$suffix);
			error_reporting($oldLevel);
		} else {
			$extensionLoaded = @dl($preffix.$extName.$suffix);
		}
    		return ($extensionLoaded);
	}
	else return true;

}

/**
* Display error message
*
* @param string $message
*/
function displayError($message){
	echo '
	<center>
		<table width="80%" style="border: 1px solid red;" class="error">
			<tr><td align="center"><b>'.$GLOBALS['traduct']->get(9).' :</b></td></tr>
			<tr><td align="left"><span style="color: red"><b>'.$message.'</b></span></td></tr>
		</table>
	</center>';
	return;
}

/**
* return the condition
*/
function displayCondition($authType){
	global $SQLiteManagerAuth;
	return (!WITH_AUTH || (
							isset($SQLiteManagerAuth) &&  $SQLiteManagerAuth->getAccess($authType)
							));
 }

/**
* Get plugins array
*/
function getPlugins(){
	$res = array();
	if ($dir = @opendir('plugins')) {
		while ($element = readdir($dir)) {
			if (substr($element,0,1) != '.' && $element!='CVS')
				if (is_dir('plugins/'.$element))
					$res[] = 'plugins/'.$element.'/';
		}
		closedir($dir);
	}
	return $res;
}

/**
* Display the global menu on the right pan, it' dependant of context
*/
function displayMenuTitle(){
	global $SQLiteManagerAuth, $workDb;
	$linkBase = 'main.php?dbsel='.$GLOBALS['dbsel'];
	foreach($GLOBALS['dbItems'] as $Items) if(isset($GLOBALS[strtolower($Items)])) $linkBase .= '&'.strtolower($Items).'='.$GLOBALS[strtolower($Items)];
	$out = '';

	$menuItems[] = array('txt'=> $GLOBALS['traduct']->get(72),'url'=> $linkBase.'&action=properties');
	if(isset($GLOBALS['table']) && ($GLOBALS['table']!='')){

		if(isset($_REQUEST['currentPage'])) $valCurrent = $_REQUEST['currentPage'];
		else $valCurrent = $GLOBALS['action'];

		$Context='Table';
		//Items : txt:Text, url:Link, enabled, type, hint, confirm, ...
		$menuItems[] = array('txt'=> $GLOBALS['traduct']->get(73),'url'=> $linkBase.'&action=browseItem');
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(74),'url'=> $linkBase.'&action=sql', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(206),'url'=> $linkBase.'&action=select');
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(75),'url'=> $linkBase.'&action=insertElement&currentPage='.$valCurrent, 'enabled'=> (displayCondition('data') &&  !$workDb->isReadOnly()));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(222),'url'=> $linkBase.'&action=operation', 'enabled'=> (displayCondition('properties')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(76),'url'=> $linkBase.'&action=export', 'enabled'=> (displayCondition('export')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(77),'url'=> $linkBase.'&action=empty', 'confirm'=> $GLOBALS['traduct']->get(79), 'enabled'=> (displayCondition('empty') &&  !$workDb->isReadOnly()));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(15),'url'=> $linkBase.'&action=delete', 'confirm'=> $GLOBALS['traduct']->get(80), 'enabled'=> (displayCondition('del') &&  !$workDb->isReadOnly()));

	} elseif(isset($GLOBALS['view']) && ($GLOBALS['view']!='')) {

		$Context='View';
		$menuItems[] = array('txt'=> $GLOBALS['traduct']->get(73),'url'=> $linkBase.'&action=browseItem');
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(74),'url'=> $linkBase.'&action=sql', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(206,'Select'),'url'=> $linkBase.'&action=select', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(76),'url'=> $linkBase.'&action=export', 'enabled'=> (displayCondition('export')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(81),'url'=> $linkBase.'&action=add', 'enabled'=> (displayCondition('properties')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(15),'url'=> $linkBase.'&action=delete', 'confirm'=> $GLOBALS['traduct']->get(82), 'enabled'=> (displayCondition('del') &&  !$workDb->isReadOnly()));

	} elseif(isset($GLOBALS['function']) && ($GLOBALS['function']!='')) {

		$Context='Function';
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(74),'url'=> $linkBase.'&action=sql', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(76),'url'=> $linkBase.'&action=export', 'enabled'=> (displayCondition('export')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(81),'url'=> $linkBase.'&action=add', 'enabled'=> (displayCondition('properties')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(15),'url'=> $linkBase.'&action=delete', 'confirm'=> $GLOBALS['traduct']->get(78), 'enabled'=> (displayCondition('del') &&  !$workDb->isReadOnly()));

	} elseif(isset($GLOBALS['trigger']) && ($GLOBALS['trigger']!='')) {

		$Context='Trigger';
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(74),'url'=> $linkBase.'&action=sql', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(81),'url'=> $linkBase.'&action=add', 'enabled'=> (displayCondition('properties')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(15),'url'=> $linkBase.'&action=delete', 'confirm'=> $GLOBALS['traduct']->get(83), 'enabled'=> (displayCondition('del') &&  !$workDb->isReadOnly()));

	} else {

		$Context='Database';
		$hintContext = html_entity_decode($GLOBALS['traduct']->get(131), ENT_NOQUOTES, $GLOBALS['charset']);
		$menuItems[] = array('txt'=> $GLOBALS['traduct']->get(84),'url'=> $linkBase.'&action=options');
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(74),'url'=> $linkBase.'&action=sql', 'enabled'=> (displayCondition('execSQL')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(76),'url'=> $linkBase.'&action=export', 'enabled'=> (displayCondition('export')));
	    $menuItems[] = array('txt'=> $GLOBALS['traduct']->get(15),'url'=> $linkBase.'&action=del', 'confirm'=> $GLOBALS['traduct']->get(85), 'enabled'=> (displayCondition('del')));

	}
	if (!isset($hintContext)) $hintContext = $GLOBALS['itemTranslated'][$Context];

	if(ALLOW_EXEC_PLUGIN){
		$plugins = getPlugins();
		foreach($plugins as $plugin_path) {
			$plugin_menu = array();
		    include_once($plugin_path.'plugin.php');
			if (@$plugin_menu[$Context]) {
				$menu = 'plugin_'.$Context.'Menu';
				$pluginItems = @$$menu;
				if (isset($pluginsItems) && @count($pluginsItems))
					foreach ($pluginsItems as $key=>$item)
						if (array_key_exists('hint',$item))
							$pluginItems[$key]['hint'] = $hintContext.' : '.$plugin_name.' : '.$item['hint'];
						elseif (isset($item['txt']))
							$pluginItems[$key]['hint'] = $hintContext.' : '.$plugin_name.' : '.$item['txt'];
				if (isset($pluginItems))
					foreach ($pluginItems as $item) $pluginsItems[] = $item;
			}
		}

		if (isset($pluginsItems)) {
			$menuItems[] = array('type'=>'_cmSplit');
			$menuItems[] = array('type'=>'folder', 'txt'=> $GLOBALS['traduct']->get(211,'Plugins'), 'url'=>'#');
			foreach ($pluginsItems as $item) $menuItems[] = $item;
			$menuItems[] = array('type'=>'endfolder');
		}
		$menuItems[] = array('type'=>'_cmSplit');

	}
/* Javascript Menu */
	$out .= "\n\t\t\t".'<script type="text/javascript">'.
          "\n\t\t\t\t".'var jsMenu = [';
  $icon=''; $inFolder='';
	$target='main';
	foreach ($menuItems as $item) {
	  $confirm = '';
	  $title = (isset($item['txt']))? $item['txt'] : '';
    $icon = (array_key_exists('icon',$item))?'<img class="seq1" src="'.$item['icon'].'"><img class="seq2" src="'.$item['icon'].'">':'&nbsp;';
    $description = (array_key_exists('hint',$item))? $item['hint'] : $hintContext.' : '.$title;
    $description = addslashes(html_entity_decode($description, ENT_NOQUOTES, $GLOBALS['charset']));
		if (!array_key_exists('enabled',$item) || @$item['enabled']) {
			$url = (isset($item['url']))? $item['url'] : '';
			if (isset($item['confirm'])) {
				$confirm = addslashes(html_entity_decode($item['confirm'], ENT_NOQUOTES, $GLOBALS['charset']));
			}
		} else {
			$url = '';
			$title = '<i>'.$title.'</i>';
			$description = '[DISABLED] '.$description;
		}

		if ($url=='') $url='#';
		$line = "\n\t\t\t\t".$inFolder."['$icon', '$title', '$url', '$target', '$description', '$confirm'";

		if (!isset($item['type'])) {
			$line .= '],';
		}
		else
			if ($item['type']=='endfolder') {
				$out = substr($out,0,strlen($out)-1);
				$line = "\n\t\t\t\t".'],';
				$inFolder='';
			} elseif ($item['type']=='folder')
			{
				$line .= ',';
				$inFolder .= "\t";
			} else {
				$line = "\n\t\t\t\t".$item['type'].',';
			}

		$out .= $line;
	}
	$out = substr($out,0,strlen($out)-1);
	global $jsmenu_style;
	if (!isset($jsmenu_style)) $jsmenu_style = 'hbr';

	$out .= "\n\t\t\t\t"."];".
  "\n\t\t\t\t"."cmDraw ('CommandMenu', jsMenu, '$jsmenu_style', cmThemeOffice, 'ThemeOffice');".
  "\n\t\t\t"."</script>"."\n\t\t";
/* */

  echo '<!-- common.lib.php : displayMenuTitle() -->'."\n";
	echo '	<div align="center">
		<table class="menu" cellspacing="0" cellpadding="0">
		<tr><td>
		<div id="CommandMenu">'.$out.'</div>
		</td></tr>
		</table>
	</div>';
}

/**
* Display pan header
*
* @param string $frame target where the header will be display
* @param bool $withTitle display title
*/
function displayHeader($frame, $withTitle=true){
	global $workDb;
	$GlobalTheme = $GLOBALS['localtheme'].'/'.$frame;
	if(is_readable('theme/'.$GLOBALS['localtheme'].'/menu/theme.css'))
		$menuTheme = $GLOBALS['localtheme'];
  else
  	$menuTheme = 'default';
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
	<title><?php echo $GLOBALS['traduct']->get(3)." ".$GLOBALS['SQLiteVersion'] ?></title>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="content-type" content="text/html;charset=<?php echo $GLOBALS['charset'] ?>">
	<style type="text/css">
	/* to add later in all themes, now can be supersed by theme*/
	table.menu { border-bottom: 1px solid black; width: 80%;}
	table.menuButtons { width: 70%; }
	td.viewPropTitle { border: 1px solid white; }
	td.viewProp {	border: 1px solid white; }
	table.home { width: 90%; text-align: center; }
	td.boxtitle { width: 49%; text-align: center; }
	td.boxtitlespace { width: 2%; font-size:0px; padding:0px; }
	h5 { margin-bottom: 3px; font-size: 12px; }
	table.query { width: 60%; margin-top: 10px; }
	div.BrowseOptions { text-align:left; }
	table.BrowseOption { text-align:left; }
	div.TableOptions { text-align: left; }
	table.Indexes { margin: 5px; width: 70%; border: thin grey solid; text-align: center; }
	body { font-size: 12px; }
  .Tip { font-size: 10px; background-color : Silver; }
  .time { font-size: 10px; float: center }
  div.Rights { border: 1px solid blue; }
	</style>
	<link href="theme/<?php echo $menuTheme?>/menu/theme.css" rel="stylesheet" type="text/css">
	<link href="theme/<?php echo $GlobalTheme?>.css" rel="stylesheet" type="text/css">
	<script src="include/function.js" type="text/javascript" language="javascript"></script>
	<script src="include/JSCookMenu.js" type="text/javascript" language="javascript"></script>
	<script src="theme/<?php echo $menuTheme?>/menu/theme.js" type="text/javascript" language="javascript"></script>
<?php
if(isset($GLOBALS['GlobalCalendar'])) echo $GLOBALS['GlobalCalendar']->get_load_files_code() . "\n";
?>
	</head>

	<body>
	<?php
	if( ($frame == 'main') && ($withTitle) && ($GLOBALS['dbsel']) ){
		echo '<h2 class="sqlmVersion">'.$GLOBALS['traduct']->get(131).' : <a href="main.php?dbsel='.$GLOBALS['dbsel'].'" style="text-decoration: none;"><span style="color: red;">'.$GLOBALS['tabInfoDb']['name'].'</span></a>';
		foreach($GLOBALS['dbItems'] as $Items) {
			if(!empty($GLOBALS[strtolower($Items)])) echo ' - '.$GLOBALS['itemTranslated'][$Items].' <a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;'.strtolower($Items).'='.$GLOBALS[strtolower($Items)].'" style="text-decoration: none;" target="main"><span style="color: blue;">'.$GLOBALS[strtolower($Items)].'</span></a>';
		}
		echo '</h2>'."\n";
		if($workDb->isReadOnly()){
			if($workDb->isReadable()) $message = $GLOBALS['traduct']->get(155);
			else $message = $GLOBALS['traduct']->get(232);
			echo '<table width="80%" align="center"><tr><td style="font-size: 10px; border: 1px solid red; color: red; align: center;">'.$message.'</td></tr></table>';
		}
	}
}

/**
* Display add form dependent of context
*
* @param string $type represent the context
*/
function formAddItem($type){
	switch($type){
		case 'Table':
			echo '	<form name="add'.$type.'" action="main.php" method="POST" target="main">
					<span style="font-size: 12px;">'.$GLOBALS['traduct']->get(43).' ==>&nbsp;'.$GLOBALS['traduct']->get(19).
					' : <input type="text" name="TableName" size="20" class="small-input"> -
					<input type="text" name="nbChamps" size="3" class="small-input">&nbsp;'.$GLOBALS['traduct']->get(44).'&nbsp;
					<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'">
					<input type="hidden" name="dbsel" value="'.$GLOBALS['dbsel'].'">
					<input type="hidden" name="action" value="add_'.strtolower($type).'">
					</span>
					</form>';
			break;
		case 'View':
			echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=add_view" class="propItemTitle" target="main">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(87).'</a>';
			break;
		case 'Trigger':
			echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=add_trigger" class="propItemTitle" target="main">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(88).'</a>';
			break;
		case 'Function':
			echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=add_function" class="propItemTitle" target="main">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(89).'</a>';
			break;
	}
}

/**
* Display query has been execute
*
* @param string $query
* @param bool $withLink if true some links will be display
*/
function displayQuery($query, $withLink=true, $changesLines=''){
	global $SQLiteManagerAuth;
	if(empty($query) && isset($GLOBALS['DisplayQuery'])) $query = $GLOBALS['DisplayQuery'];
	$linkBase = 'main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$GLOBALS['table'];
	if($posLim = strpos($query, 'LIMIT')) {
		$queryLink = substr($query, 0, ($posLim-1));
	} else $queryLink = $query;
	if(isset($GLOBALS['DbGrid']) && is_object($GLOBALS['DbGrid'])){
		if(!empty($GLOBALS['DbGrid']->infoNav['start']) || !empty($GLOBALS['DbGrid']->infoNav['end']) || !empty($GLOBALS['DbGrid']->infoNav['all'])){
			$infoNav = '<span style="font-size: 12px">'.$GLOBALS['traduct']->get(136).' '.$GLOBALS['DbGrid']->infoNav['start'].'-'.$GLOBALS['DbGrid']->infoNav['end'].'/'.$GLOBALS['DbGrid']->infoNav['all'].'&nbsp;&nbsp;</span>';
		}
	}
	if(!isset($infoNav)) $infoNav = '';
	if(WITH_AUTH && isset($SQLiteManagerAuth) &&  !$SQLiteManagerAuth->getAccess('execSQL')) $withLink = false;
	$modifyLink 	= '[<a href="'.$linkBase.'&amp;action=sql&amp;sql_action=modify&amp;displayResult=&amp;DisplayQuery='.urlencode($queryLink).'" class="titleHeader" target="main">'.$GLOBALS['traduct']->get(14).'</a>]';
	$explainLink 	= '[<a href="'.$linkBase.'&amp;action=sql&amp;sql_action=explain&amp;displayResult=1&amp;DisplayQuery='.urlencode($queryLink).'" class="titleHeader" target="main">'.$GLOBALS['traduct']->get(145).'</a>]';
	$replayLink		= '[<a href="'.$linkBase.'&amp;action=sql&amp;displayResult=1&amp;DisplayQuery='.urlencode($queryLink).'" class="titleHeader" target="main">'.$GLOBALS['traduct']->get(223).'</a>]';
	echo '
		<table class="query" cellspacing="0" align="center">';
	if($changesLines != '') echo '<tr><td bgcolor="#CCCCCC"><span class="sqlsyntaxe">&nbsp;'.$changesLines.' '.$GLOBALS['traduct']->get(71).'</span></td></tr>';
	echo '		<tr>
				<td class="queryTitle" bgcolor="'.$GLOBALS['displayQueryTitleColor'].'"  style="white-space: nowrap">
					'.$GLOBALS['traduct']->get(90).' : '.(($withLink)? '&nbsp;&nbsp;'.$infoNav.$modifyLink.'&nbsp;'.$explainLink.'&nbsp;'.$replayLink : '' ).'</td></tr>
			<tr><td class="queryBody" bgcolor="'.$GLOBALS['displayQueryBgColor'].'" style="white-space: nowrap"><div class="sqlsyntaxe">'.highlight_query($query).'</div></td></tr>
		</table><br/>
		';
}

/**
* highlight query for a proch future!!
*
* @param string $query SQL command string
*/
function highlight_query($query){
	include_once INCLUDE_LIB.'ParsingQuery.class.php';
	$Colorize = new ParsingQuery($query, 1);
	$Colorize->explodeQuery();
	$Colorize->colorWordList();
	return $Colorize->highlightQuery();
}

/**
* Display function list for the insert / modify form
*
* @param string $champ name of the current champ
*/
function SQLiteFunctionList($champ, $userDefine=""){
	$out = '<select name="funcs['.$champ.']">'."\n".'
				<option value="" />'."\n";
	foreach($GLOBALS['SQLfunction'] as $funct) $out .= '<option value="'.$funct.'">'.$funct.'</option>'."\n";
	$tabUDF = $GLOBALS["workDb"]->functInfo;
	$out .= '<option value="" />'."\n";
	if(is_array($tabUDF)) foreach($tabUDF as $udfInfo) if($udfInfo['funct_type']==1) $out .= '<option value="'.$udfInfo['funct_name'].'">'.$udfInfo['funct_name'].'</option>';
	$out .= '</select>';
	return $out;
}

/**
* Display operator list for the select form
* @author Maurício M. Maia <mauricio.maia@gmail.com>
*
* @param string $champ name of the current champ
*/
function SQLiteSelectList($champ, $userDefine=""){
	$out = '<select name="operats['.$champ.']">'."\n".'
				<option value="" />'."\n";
	foreach($GLOBALS['SQLselect'] as $operat) {
		if(($operat != "fulltextsearch") || ALLOW_FULLSEARCH)
		$out .= '<option value="'.$operat.'">'.$operat.'</option>'."\n";
	}
	$out .= '</select>';
	return $out;
}

/**
* Display input TYPE for the insert / modify form
*
* @param array $info data info for the current champ
* @param mixed $data current value of the champ
*/
function SQLiteInputType($info, $data, $allowDefault=true, $allow_advanced=true){
	static $tabIndex;
	$allowBigger=false;
	if(empty($tabIndex)) $tabIndex = 1;
	if (!$allowBigger)
		if(preg_match('#CHAR|TEXT|LOB#i', $info['type'])) {
			preg_match('#\((.*)\)#', $info['type'], $length);
			if(isset($length[1]) && $length[1]){
				$maxlength = ' maxlength="'.$length[1].'"';
				if($length[1]<=20) $maxlength = ' size="'.($length[1]+1).'" '.$maxlength;
			} else $maxlength = ' size="20"';
		} else $maxlength = ' size="20"';

	if($allowDefault && $info['notnull'] && ($data == '')){
		if(strstr($info['type'], '(')) {
			$localType = trim(preg_replace('#\(.*\)#', '', $info['type']));
		} else {
			$localType = $info['type'];
		}
		if($info['dflt_value']!= '') $data = $info['dflt_value'];
		elseif(isset($GLOBALS['SQLiteType'][$localType])) $data=$GLOBALS['SQLiteType'][$localType];
	}
	if((strtoupper(substr($info['type'],0,4))!='TEXT') && (strtoupper(substr($info['type'],0,4))!='BLOB')) {
		if(JSCALENDAR_PATH && isset($GLOBALS['GlobalCalendar']) && (($info['type'] == 'DATE') || ($info['type'] == 'DATETIME'))) {
	        $id = $GLOBALS['GlobalCalendar']->_gen_id();
			$out = "<input size=\"15\"
							id=\"".$GLOBALS['GlobalCalendar']->_field_id($id)."\"
							type=\"text\"
							class=\"text\"
							name=\"valField[".$info['name']."]\"
							value=\"".htmlentities($data, ENT_COMPAT, $GLOBALS['charset'])."\"".
							$maxlength."
							tabindex=".($tabIndex++).((!$info['notnull'])? "
							onChange=\"if(this.value!='') setCheckBox('editElement', 'nullField[".$info['name']."]', false); else setCheckBox('editElement', 'nullField[".$info['name']."]', true);\"" : '' ).">";
	        $out .= '<a href="#" id="'. $GLOBALS['GlobalCalendar']->_trigger_id($id) . '">' . '<img align="middle" border="0" src="' . $GLOBALS['GlobalCalendar']->calendar_lib_path . 'img.gif" alt="" /></a>';

	        $options = array(	'inputField' => $GLOBALS['GlobalCalendar']->_field_id($id),
	                            'button'     => $GLOBALS['GlobalCalendar']->_trigger_id($id));
	        if($info['type'] == 'DATETIME') {
	        	$GLOBALS['GlobalCalendar']->calendar_options['ifFormat'] = '%Y-%m-%d %H:%M';
	        	$GLOBALS['GlobalCalendar']->calendar_options['daFormat'] = '%Y-%m-%d %H:%M';
	        } else {
	        	$GLOBALS['GlobalCalendar']->calendar_options['ifFormat'] = '%Y-%m-%d';
	        	$GLOBALS['GlobalCalendar']->calendar_options['daFormat'] = '%Y-%m-%d';
	        }
	        $out .= $GLOBALS['GlobalCalendar']->_make_calendar($options);
		} else {
			$out = "<input type=\"text\" class=\"text\" name=\"valField[".$info['name']."]\" value=\"".htmlentities($data, ENT_COMPAT, $GLOBALS['charset'])."\"".$maxlength." tabindex=".($tabIndex++).((!$info['notnull'])? " onChange=\"if(this.value!='') setCheckBox('editElement', 'nullField[".$info['name']."]', false); else setCheckBox('editElement', 'nullField[".$info['name']."]', true);\"" : '' ).">";
		}
	} else {
		if(ADVANCED_EDITOR && $allow_advanced && isset($GLOBALS['spaw_dir'])
				&& (isset($_COOKIE["SQLiteManager_HTMLon"]) && !$_COOKIE["SQLiteManager_HTMLon"])){
			$GLOBALS["spawEditorByName"][$info['name']] = new SPAW_Wysiwyg("valField[".$info['name']."]", $data, $GLOBALS["langSuffix"], SPAW_TOOLBAR_STYLE, '', (TEXTAREA_NB_COLS*6), (TEXAREA_NB_ROWS*16));
			// Show SPAW Editor
			$out = $GLOBALS["spawEditorByName"][$info['name']]->show();
		} else {
			$out = "<textarea name=\"valField[".$info['name']."]\" cols=".TEXTAREA_NB_COLS." rows=".TEXAREA_NB_ROWS." tabindex=".($tabIndex++).((!$info['notnull'])? " onChange=\"if(this.value!='') setCheckBox('editElement', 'nullField[".$info['name']."]', false); else setCheckBox('editElement', 'nullField[\'".$info['name']."\']', true);\"" : "" ).">".
        		   htmlentities($data, ENT_NOQUOTES, $GLOBALS['charset']).
		    		'</textarea>';
		}

	}
	if(isset($out)) return $out;
}

/**
* Return Available language
*/
function getAvailableLanguage(){
	$out = "";
	$listLangue = $GLOBALS['langueTranslated'];
	natsort($listLangue);
	while(list($lgId, $lgLib) = each($listLangue)){
		$out .= '<option value="'.$lgId.'"'.(($GLOBALS['currentLangue']==$lgId)? ' selected="selected"' : '' ).'>'.(($GLOBALS['langueTranslated'][$lgId])? $GLOBALS['langueTranslated'][$lgId] : $lgLib).'</option>'."\n";
	}
	return $out;
}

/**
* Return available theme
*/
function getAvailableTheme(){
	$out = "";
	//natsort($GLOBALS['themeTranslated']);
	while(list($key,$themeId) = each($GLOBALS['availableTheme'])){
		$themeLib = ($GLOBALS['themeTranslated'][$themeId])? $GLOBALS['themeTranslated'][$themeId] : $themeId;
		$out .= '<option value="'.$themeId.'"'.(($GLOBALS['localtheme']==$themeId)? ' selected="selected"' : '' ).'>'.$themeLib.'</option>'."\n";
	}
	return $out;
}

/**
* Apply a function from record form
*
* @param string $function the function name
* @param mixed $value paramaters to apply function
*/
function applyFunction($function, $value){
	$newValue = $value;
	if(in_array($function, $GLOBALS['SQLfunction'])){
		if($function == 'MD5') $newValue = "php('md5', $value)";
		elseif($function == 'NOW') $newValue = "php('date', 'Y-m-d')";
		else $newValue = strtolower($function)."($value)";
	} else {
		foreach($GLOBALS['workDb']->functInfo as $functInfo) {
			if($function == $functInfo['funct_name']) $newValue = $function."('$value')";
		}
	}
	return $newValue;
}

/**
* Convert a hash table to GET url string
*
* @param array $tab table of key=>value
*/
function arrayToGet($tab){
	$strOut = array();
	while(list($var, $value) = each($tab)) $strOut[] = $var."=".$value;
	return implode("&", $strOut);
}

/**
* Cleaning field name, remove non authorized caractere
*
* @param string string is the field name
* @param string $allow PCRE representation of caractère authorized
*/
function cleanFieldName($string, $allow = 'a-z_0-9[[:space:]]'){
	return preg_replace('#[^'.$allow.']#i', '', trim($string));
}

/**
* Return a tab with all position of a caractere
*
* @param string $string haystack string
* @param char $seperation needle string to find
*/
function strpos_all($string, $separator){
	static $tabPos=array();
	$pos = strpos($string, $separator);
	if((string)$pos!=""){
	if(count($tabPos)>=1) $addPrec = ($tabPos[count($tabPos)-1] +1);
	else $addPrec = 0;
		array_push($tabPos, ($pos + $addPrec));
		$substring = substr($string, ($pos + 1), (strlen($string) - ($pos+1)) );
		strpos_all($substring, $separator);
	}
	return $tabPos;
}

/**
* Function for error handling
* return PHP error from SQLite error to enhancement error displaying
*/
function phpSQLiteErrorHandling($errno, $errstr, $errfile, $errline){
	preg_match('/:(.*)/', $errstr, $errorResult);
	if(isset($errorResult[1])) $GLOBALS['phpSQLiteError'] = $errorResult[1];
}

/**
* Create SELECT from array value
*
* @param array $tabData array("ID"=>"VALUE", ...)
* @param string form $varNamevar name
* @param int $varValue value to selected
* @return string HTML out
*/
function createSelect($tabData, $varName, $varValue){
	$out = '';
	if(isset($tabData) && !empty($tabData)){
		$out .= '<SELECT name="'.$varName.'">'."\n".'<option value="" />'."\n";
		foreach($tabData as $id=>$value) $out .= '<option value="'.$id.'"'.(($id==$varValue)? ' selected="selected"' : '' ).'>'.$value.'</option>'."\n";
		$out .='</SELECT>'."\n";
	}
	return $out;
}

/**
* Send image HTML!
* if exist img filename in theme, this is display also the './pics/'.$filename is display
*
* @param string $src filename
* @param sring $alt string is display when mouse over
* @param int $border image border size in px
* @param int $width width size in px
* @param int height heigh size in px
* @return string img HTML tag
*/
function displayPics($src, $alt="", $border=0, $width="", $height=""){
	$filename = basename($src);
	if(file_exists('./theme/'.$GLOBALS['localtheme'].'/pics/'.$filename)){
		$imgSrc = './theme/'.$GLOBALS['localtheme'].'/pics/'.$filename;
	} else {
		$imgSrc = './theme/default/pics/'.$filename;
	}
	return '<img src="'.$imgSrc.'" border="'.$border.'" alt="'.$alt.'" title="'.$alt.'"'.(($width)? ' width='.$width : '' ).(($height)? ' height='.$height : '' ).'>';
}

/**
*  Add brackets when spaces in objects, remove existing ones for concatenations [table].[col] > [table.col]
*
* @param string $object object with or without bracket
* @return string object with brackets
*/
function brackets($object,$quotes=true){
	$object = preg_replace('#\[|\]#','',$object);
	if (strstr($object,' ')) {
		$object = "[$object]";
	}
	if($quotes)
	    $object = quotes($object);

	return $object;
}

/**
*  Add quotes on values, and support for Like '%text%'
*
* @param string $text string value
* @param string $like string add like %
* @return escaped string with quotes
*/
function quotes($text,$like=''){
	if (substr($like,0,1)=='%') $text  = '%'+$text;
	if (substr($like,1,1)=='%') $text .= '%';
	$out = "'".$GLOBALS['db']->escape($text)."'";
	return $out;
}

function SQLiteStripSlashes($str) {
    if (get_magic_quotes_gpc())
        $str = stripslashes($str);

    return $str;
}

function unquote($s, $quotes = "''``", &$left_quote=null) {
    if (strlen($s) < 2) {
        $left_quote = false;
        return $s;
    }
    $q = substr($s, 0, 1);
    $qleft = strpos($quotes, $q);
    if ($qleft === false) {
        $left_quote = false;
        return $s;
    }
    $qright = $quotes{$qleft + 1};
    if (substr($s, -1) === $qright) {
        $left_quote = $quotes{$qleft};
        return substr($s, 1, -1);
    }
    return $s;
}
?>
