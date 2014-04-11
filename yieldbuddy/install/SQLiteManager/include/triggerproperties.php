<?php
/**
* Web based SQLite management
* Show and manage 'TRIGGER' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: triggerproperties.php,v 1.4 2004/01/04 13:44:55 freddy78 Exp $ $Revision: 1.4 $
*/

include_once INCLUDE_LIB."SQLiteTriggerProperties.class.php";
$triggerProp = new SQLiteTriggerProperties($workDb);
switch($action){
	case "":
	default:			
		$triggerProp->PropView();
		break;
	case "modify":
	case "add":
		$triggerProp->triggerEditForm();
		break;
	case "save":
	case "delete":
		$triggerProp->saveProp();
		break;
}	 

?>
