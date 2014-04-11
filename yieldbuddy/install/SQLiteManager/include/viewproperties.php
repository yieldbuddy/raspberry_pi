<?php
/**
* Web based SQLite management
* Show and manage 'VIEW' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: viewproperties.php,v 1.7 2004/11/25 14:26:07 tpruvot Exp $ $Revision: 1.7 $
*/

include_once INCLUDE_LIB."SQLiteViewProperties.class.php";
$viewProp = new SQLiteViewProperties($workDb);
switch($action){
	case "":
	default:			
		$viewProp->PropView();
		break;
	case "modify":
	case "add":
		$viewProp->viewEditForm();
		break;
	case "save":
	case "delete":
		$viewProp->saveProp();
		break;
	case "export":
		include_once INCLUDE_LIB."SQLiteExport.class.php";
		$export = new SQLiteExport($workDb);			
		break;
	case 'select':
		include_once INCLUDE_LIB.'SQLiteSelect.class.php';
		$select = new SQLiteSelect($workDb, $view);
		break;
	case 'selectElement':
		$DisplayQuery = $viewProp->selectElement($table);
		include INCLUDE_LIB.'browse.php';
		break;
}	 
?>

</body>
</html>
