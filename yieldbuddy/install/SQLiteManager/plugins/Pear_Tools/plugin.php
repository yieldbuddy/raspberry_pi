<?php
/* -----------------------------------------------------------------------------

  First plugin for SQLiteManager 1.0.5+ by Tanguy dot Pruvot at laposte dot net

----------------------------------------------------------------------------- */
function is_plugin_compatible_Pear_Tools() {
  return (substr(phpversion(), 0, 1) == '5');
}

$plugin_key    = 'Pear_Tools';
$plugin_name   = 'PEAR SQLite Tools';
$plugin_author = 'Tanguy Pruvot';
$plugin_email  = 'tanguy.pruvot@laposte.net';
$plugin_web    = 'http://tpruvot.free.fr';


//Please keep this syntax for other plugins
$plugin_version = '0.9.0 beta';

//Date format : YYYY-MM-DD
$plugin_date    = '2004-11-27';

//SQLiteManager min version
$sqlmgr_min_ver = '1.0.5';

//SQLite version range
$sqlite_min_ver = '2.8.0';
//$sqlite_max_ver = '2.9.9';

//Menus
$plugin_menu = array(
'Home'     => 0,
'Database' => is_plugin_compatible_Pear_Tools(),
'Table'    => 0,
'View'     => 0,
'Trigger'  => 0,
'Function' => 0);

$plugin_pics = $plugin_path.'pics/';
$linkBase = 'main.php?dbsel='.$GLOBALS['dbsel']."&plugin=".$plugin_key;

//Menu Items : txt:Title, url:Link, enabled, type, hint, confirm, icon ...

//************** Database Menu *****************
$plugin_DatabaseMenu = array(

array('type'=> 'folder', 'txt'=> $plugin_name),
array('txt'=> 'Import XML File...',
      'url'=> $linkBase.'&action=ImportXML',
//      'icon'=> $plugin_pics.'table.png', 
      'enabled'=> (!$workDb->isReadOnly() && displayCondition('data'))
      ),
array('txt'=> 'Export XML File...',
      'url'=> $linkBase.'&action=ExportXML',
//      'icon'=> $plugin_pics.'table.png', 
      'enabled'=> (!$workDb->isReadOnly() && displayCondition('data'))
      ),
array('type'=> '_cmSplit'), 
array('txt'=> 'Plugin Homepage...', 'url'=> $plugin_web, 'icon'=> $plugin_path.'pics/url.png'),

array('type'=> 'endfolder'),

);

//************** Table Menu *****************
$plugin_TableMenu = array(

array('type'=> 'folder', 'txt'=> $plugin_name),
array('txt'=> 'Import XML File...',
      'url'=> $linkBase.'&action=ImportXML'.'&table='.$GLOBALS['table'],
//      'icon'=> $plugin_pics.'table.png', 
      'enabled'=> (!$workDb->isReadOnly() && displayCondition('data'))
      ),
array('txt'=> 'Export XML File...',
      'url'=> $linkBase.'&action=ExportXML'.'&table='.$GLOBALS['table'],
//      'icon'=> $plugin_pics.'table.png', 
      'enabled'=> (!$workDb->isReadOnly() && displayCondition('data'))
      ),
array('type'=> '_cmSplit'), 
array('txt'=> 'Plugin Homepage...', 'url'=> $plugin_web, 'icon'=> $plugin_path.'pics/url.png'),

array('type'=> 'endfolder'),

);

//************** View Menu *****************
$plugin_ViewMenu = array(

array('type'=> 'folder', 'txt'=> $plugin_name),

array('type'=> '_cmSplit'), 
array('txt'=> 'Plugin Homepage...', 'url'=> $plugin_web, 'icon'=> $plugin_path.'pics/url.png'),

array('type'=> 'endfolder'),

);

//************** Trigger Menu *****************
//$plugin_TriggerMenu = array();
//************** Function Menu *****************
//$plugin_FunctionMenu = array();

//##############################################################################

//Include only when execution needed
if (isset($GLOBALS['plugin']) && $GLOBALS['plugin']==$plugin_key && !$workDb->isReadOnly()) {
  include_once 'sqlite_xml.php';
}

?>
