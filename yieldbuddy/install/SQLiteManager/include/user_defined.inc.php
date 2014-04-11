<?php
/**
* Web based SQLite management
* User defined
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: user_defined.inc.php,v 1.7 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.7 $
*/


/**
 * General include path
 */
// define("INCLUDE_LIB", "./include/");

/**
 * By default the DEBUG mode depend of SQLiteManagerVersion, if it's finish by 'CVS' debug mode turn on
 * You can force debug mode to on by setting 'DEBUG' constante
 */
// define("DEBUG", false);

/**
 * You can move configuration database, it's best for security reason.
 * for this you can set 'SQLiteDb' constante with the full path!
 * The configuration database version must be the same of 'SQLITE3' constante.
 * for windows : define('SQLiteDb', 'c:\path\to\sqlite\config.db');
 */
// define ("SQLiteDb", "/usr/local/apache2/htdocs/SQLiteManager-1.2.0RC1/include/config.db");
/**
 * You can define a default dir where you place all sqlite databases!!
 * All database create without path will be save here, and the uploaded database so
 */
// define("DEFAULT_DB_PATH", "/var/www/sqliteDb/"); 
/**
 * Some plugins has been in development
 * To activate the use of plugin set the 'ALLOW_EXEC_PLUGIN' to true
 */ 
// define("ALLOW_EXEC_PLUGIN", false);

/**
 * You activate authenticate by setting the constante 'WITH_AUTH' to true
 * In admin mode you can manage all users and groups
 * The default passwd of 'admin' user is 'admin'
 */
//define("WITH_AUTH", false);

/**
 * In authenticate mode, you can autorize the user to change password
 */
// define("ALLOW_CHANGE_PASSWD", true);

/**
 * The 'NAV_NBLINK' constante, define the default number of link into navigation bar
 */
// define("NAV_NBLINK", 10);

/**
 * Left Frame width
 */
//define("LEFT_FRAME_WIDTH", 200);

/**
 * display empty item into left frame
 */
// define("DISPLAY_EMPTY_ITEM_LEFT", true);

/**
 * Number of columns for textarea
 */
// define("TEXTAREA_NB_COLS", 60);

/**
 * Number of rows for textarea
 */
// define("TEXAREA_NB_ROWS", 5);

/**
 * Number of char display on partial text view
 */
// define("PARTIAL_TEXT_SIZE", 20);

/**
 * Number of record per page display on browse mode
 */
// define("BROWSE_NB_RECORD_PAGE", 20);

/**
 * Replace TextArea with advanced editor SPAW Editor
 * Only where 'HTML' is selected in browse mode
 */
// define("ADVANCED_EDITOR", false);

/**
 * Define the full path to SPAW Editor
 */
// define("SPAW_PATH", "/usr/local/apache2/htdocs/sqlitemanager/spaw/");

/**
 * Define Toolbar style for the SPAW Editor
 */
// define("SPAW_TOOLBAR_STYLE", "sqlitemanager");

/**
 * Use jscalendar for date entrie
 */
// define('JSCALENDAR_USE', true);

/**
 * jscalendar path
 */
// define('JSCALENDAR_PATH', '/jscalendar/');

/**
 *Allow use of 'Full search' UDF
 */
 // define("ALLOW_FULLSEARCH", true);
?>
