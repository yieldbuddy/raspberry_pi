<?php
/**
* Web based SQLite management
* Show and manage 'FUNCTION' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: functproperties.php,v 1.6 2004/11/25 12:56:18 tpruvot Exp $ $Revision: 1.6 $
*/

include_once INCLUDE_LIB.'SQLiteFunctionProperties.class.php';
$functProp = new SQLiteFunctionProperties($workDb);
switch($action){
	case '':
	default:			
		$functProp->PropView();
		break;
	case 'modify':
	case 'add':
		$functProp->functEditForm();
		break;
	case 'save':
	case 'delete':
		$functProp->saveProp();
		break;
	case 'export':
		include_once INCLUDE_LIB.'SQLiteExport.class.php';
		$export = new SQLiteExport($workDb);			
		break;
}	 

?>

</body>
</html>
