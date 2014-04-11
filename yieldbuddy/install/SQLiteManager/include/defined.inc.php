<?php
/**
* Web based SQLite management
* Some defines
* @package SQLiteManager
* @author Fr�d�ric HENNINOT
* @version $Id: defined.inc.php,v 1.89 2006/04/17 18:58:20 freddy78 Exp $ $Revision: 1.89 $
*/
include_once "./include/user_defined.inc.php";

$baseDir = str_replace('\\','/',dirname(__FILE__));

if(!defined('INCLUDE_LIB')) define ('INCLUDE_LIB',$baseDir.'/');

define('SQLiteManagerVersion', '1.2.4');

if (!defined('DEBUG') && strpos(SQLiteManagerVersion,'CVS')) {
	define('DEBUG',true);
} else if(!defined('DEBUG')) {
	define('DEBUG', false);
}
if (DEBUG) {
  if (function_exists('apd_set_pprof_trace')) apd_set_pprof_trace();
}

// Default Folder for Uploaded file Database
if(!defined('DEFAULT_DB_PATH')) {
  define('DEFAULT_DB_PATH', substr($baseDir, 0, strlen($baseDir) - 7));
}

if(!defined('WITH_AUTH')) define('WITH_AUTH', false);

if(!defined('ALLOW_CHANGE_PASSWD')) define('ALLOW_CHANGE_PASSWD', true);

if(!defined('ALLOW_EXEC_PLUGIN')) define('ALLOW_EXEC_PLUGIN', false);

$availableLangue = array(	1=>'french', 2=>'english', 3=>'polish',
				4=>'german', 5=>'japanese', 6=>'italian',
				7=>'croatian', 8=>'brazilian_portuguese', 9=>'dutch',
				10=>'spanish', 11=>'danish', 12=>'traditional_chinese',
				13=>'simplified_chinese');

$availableTheme = array("default", "green", "PMA", "jall");

$dbItems = array('Table', 'View', 'Trigger', 'Function');

if(isset($_POST['Theme'])) {
	$localTheme = $_POST['Theme'];
	setcookie('SQLiteManager_currentTheme',$_POST['Theme'],1719241200,'/');
	$_COOKIE['SQLiteManager_currentTheme'] = $_POST['Theme'];
	echo "<script type=\"text/javascript\">parent.location='index.php';</script>";
} elseif(isset($_COOKIE['SQLiteManager_currentTheme'])) {
	$localtheme = $_COOKIE['SQLiteManager_currentTheme'];
} else {
	$localtheme = 'green';
}

// set cookie for FullText
if(isset($_GET['fullText'])) {
	$allFullText = $_GET['fullText'];
	setcookie('SQLiteManager_fullText',$_GET['fullText'],1719241200,'/');
	$_COOKIE['SQLiteManager_fullText'] = $_GET['fullText'];
} elseif(isset($_COOKIE['SQLiteManager_fullText'])) {
	$allFullText = $_COOKIE['SQLiteManager_fullText'];
} else {
	$allFullText = true;
}

// set cookie for see HTML
if(isset($_GET['HTMLon'])) {
	$allHTML = $_GET['HTMLon'];
	setcookie('SQLiteManager_HTMLon',$_GET['HTMLon'],1719241200,'/');
	$_COOKIE['SQLiteManager_HTMLon'] = $_GET['HTMLon'];
} elseif(isset($_COOKIE['SQLiteManager_HTMLon'])) {
	$allHTML = $_COOKIE['SQLiteManager_HTMLon'];
} else {
	$allHTML = true;
}

/**
* image to see 'ASC' order
*/
define('IMG_ASC', 	((file_exists('./theme/'.$localtheme.'/pics/down.gif'))? './theme/'.$localtheme.'/pics/down.gif' : './theme/default/pics/down.gif' ));/**
* image to see 'DESC' order
*/
define('IMG_DESC', 	((file_exists('./theme/'.$localtheme.'/pics/up.gif'))? './theme/'.$localtheme.'/pics/up.gif' : './theme/default/pics/up.gif' ));/**
* Image for paginate navigation, you can remove it.
*/
define('NAV_TOP', 	((file_exists('./theme/'.$localtheme.'/pics/top.gif'))? './theme/'.$localtheme.'/pics/top.gif' : './theme/default/pics/top.gif' ));/**
* Image for paginate navigation, you can remove it.
*/
define('NAV_PREC', 	((file_exists('./theme/'.$localtheme.'/pics/left.gif'))? './theme/'.$localtheme.'/pics/left.gif' : './theme/default/pics/left.gif' ));/**
* Image for paginate navigation, you can remove it.
*/
define('NAV_SUIV', 	((file_exists('./theme/'.$localtheme.'/pics/right.gif'))? './theme/'.$localtheme.'/pics/right.gif' : './theme/default/pics/right.gif' ));/**
* Image for paginate navigation, you can remove it.
*/
define('NAV_END', 	((file_exists('./theme/'.$localtheme.'/pics/end.gif'))? './theme/'.$localtheme.'/pics/end.gif' : './theme/default/pics/end.gif' ));/**
* Separator for navigation bar.
*/
define('NAV_SEP', 	'&nbsp;-&nbsp;');
/**
* Number of Link in the navigation bar.
*/
if(!defined('NAV_NBLINK')) define('NAV_NBLINK', 10);


define('DEMO_MODE', false);

if(!defined('ADVANCED_EDITOR')) define('ADVANCED_EDITOR', true);
if(ADVANCED_EDITOR && !defined('SPAW_PATH')) {
	$base = str_replace("/include", "", $baseDir);
	define('SPAW_PATH', $base.'/spaw/');
}

if(!defined("SPAW_TOOLBAR_STYLE")) define("SPAW_TOOLBAR_STYLE", "sqlitemanager");

if(DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(E_ALL ^ E_NOTICE);
}

if(!defined('LEFT_FRAME_WIDTH')) 		define('LEFT_FRAME_WIDTH', 200);
if(!defined('TEXTAREA_NB_COLS')) 		define('TEXTAREA_NB_COLS', 65);
if(!defined('TEXAREA_NB_ROWS'))			define('TEXAREA_NB_ROWS', 5);
if(!defined('PARTIAL_TEXT_SIZE'))		define('PARTIAL_TEXT_SIZE', 20);
if(!defined('DISPLAY_EMPTY_ITEM_LEFT'))	define('DISPLAY_EMPTY_ITEM_LEFT', true);
if(!defined('BROWSE_NB_RECORD_PAGE'))	define('BROWSE_NB_RECORD_PAGE', 20);
if(!defined('ALLOW_FULLSEARCH'))		define('ALLOW_FULLSEARCH', true);
if(!defined('JSCALENDAR_USE'))			define('JSCALENDAR_USE', true);
if(!defined('JSCALENDAR_PATH'))			define('JSCALENDAR_PATH', 'jscalendar/');

$SQLpunct = '.,;:=&()-+!<>';

$SQLoperator = array(
							'ABORT',
							'AFTER',
							'AND',
							'BEFORE',
							'BEGIN',
							'BETWEEN',
							'CASE',
							'CHECK',
							'COLLATE',
							'CONSTRAINT',
							'CASCADE',
							'CLUSTER',
							'CONFLICT',
							'DEFAULT',
							'DEFERRABLE',
							'DISTINCT',
							'DEFERRED',
							'DELIMITERS',
							'DESC',
							'EACH',
							'ELSE',
							'EXCEPT',
							'END',
							'FAIL',
							'FOR',
							'FOREIGN',
							'GLOB',
							'IN',
							'INTERSECT',
							'IS',
							'ISNULL',
							'IGNORE',
							'IMMEDIATE',
							'INITIALLY',
							'INSTEAD',
							'MATCH',
							'OF',
							'OFFSET',
							'RESTRICT',
							'ROW',
							'STATEMENT',
							'TEMP ALL',
							'LIKE',
							'NOT',
							'NOTNULL',
							'NULL',
							'OR',
							'PRIMARY',
							'REFERENCES',
							'THEN',
							'UNIQUE',
							'USING',
							'WHEN'
						);

$SQLKeyWordList = array(
							'AS',
							'ASC',
							'ATTACH',
							'BY',
							'COMMIT',
							'CREATE',
							'COPY',
							'CROSS',
							'DATABASE',
							'DELETE',
							'DROP',
							'DETACH',
							'EXPLAIN',
							'FROM',
							'FULL',
							'GROUP',
							'HAVING',
							'INDEX',
							'INSERT',
							'INTO',
							'INNER',
							'JOIN',
							'KEY',
							'LEFT',
							'LIMIT',
							'NATURAL',
							'OUTER',
							'PRAGMA',
							'RAISE',
							'REPLACE',
							'RIGHT',
							'ON',
							'ORDER',
							'ROLLBACK',
							'SELECT',
							'SET',
							'TABLE',
							'UNION',
							'UPDATE',
							'VALUES',
							'VACUUM',
							'VIEW',
							'WHERE',
							'TEMPORARY',
							'TRANSACTION',
							'TRIGGER'
						);

$SQLfunction	= array(
							'LENGTH',
							'LOWER',
							'UPPER',
							'SUBSTR',
							'SOUNDEX',
							'MD5',
							'NOW',
							'LAST_INSERT_ROWID',
							'RANDOM',
							'COUNT',
							'ABS',
							'AVG',
							'SUM',
							'MIN',
							'MAX',
							'ROUND'
						);
$SQLiteType		= array(
							''				=>	'',
							'VARCHAR'		=>	'',
							'TINYINT'		=>	'0',
							'INTEGER'		=>	'0',
							'INT'			=>	'0',
							'TEXT'			=>	'',
							'DATE'			=>	'0000-00-00',
							'SMALLINT'		=>	'0',
							'MEDIUMINT'		=>	'0',
							'BIGINT'		=>	'0',
							'FLOAT'			=>	'0.0',
							'DOUBLE'		=>	'0',
							'DECIMAL'		=>	'0.0',
							'DATETIME'		=>	'0000-00-00 00:00',
							'TIMESTAMP'		=>	'0',
							'TIME'			=>	'00:00',
							'YEAR'			=>	'0',
							'CHAR'			=>	'',
							'TINYBLOB'		=>	'',
							'TINYTEXT'		=>	'',
							'BLOB'			=>	'',
							'MEDIUMBLOB'	=>	'',
							'MEDIUMTEXT'	=>	'',
							'LONGBLOB'		=>	'',
							'LONGTEXT'		=>	'',
							'CLOB'			=>	'',
							'BOOLEAN'		=>	'',
						);

$SQLselect = array(
                            '<',
                            '<=',
                            '>',
                            '>=',
                            '=',
                            '!=',
                            'LIKE',
                            'NOT LIKE',
                            'GLOB',
                            'NOT GLOB',
                            'ISNULL',
                            'NOTNULL',
                            'fulltextsearch'
						);
$elementStartQuery = array(
							'ALTER',
							'ATTACH',
							'BEGIN',
							'COMMIT',
							'COPY',
							'CREATE',
							'DELETE',
							'DETACH',
							'DROP',
							'END',
							'EXPLAIN',
							'INSERT',
							'PRAGMA',
							'REINDEX',
							'REPLACE',
							'ROLLBACK',
							'SELECT',
							'UPDATE',
							'VACUUM'
						);

?>
