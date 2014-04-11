<?php
/**
* Web based SQLite management
* Manage manual query and file query
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: sql.php,v 1.5 2004/01/04 13:44:55 freddy78 Exp $ $Revision: 1.5 $
*/

if(!empty($_FILES["sqlFile"]["tmp_name"])){
	$fp = fopen($_FILES["sqlFile"]["tmp_name"], "r");
	$DisplayQuery = fread($fp, $_FILES["sqlFile"]["size"]);
}
include INCLUDE_LIB."browse.php";

?>
