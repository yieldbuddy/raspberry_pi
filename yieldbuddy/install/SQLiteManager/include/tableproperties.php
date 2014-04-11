<?php
/**
* Web based SQLite management
* Show and manage 'TABLE' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: tableproperties.php,v 1.21 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.21 $
*/

include_once INCLUDE_LIB.'SQLiteTableProperties.class.php';
$tableProp = new SQLiteTableProperties($workDb);
if(!empty($table)){
	$tableProp->getTableProperties();
	switch($action){
		case '':
		default:
			$tableProp->tablePropView();
			break;
		case 'modify':
		case 'addChamp':
			$tableProp->tableEditForm();
			break;
		case 'save':
		case 'delete':
		case 'addprimary':
		case 'noprimary':
			$tableProp->saveProp();
			break;
		case 'unique':
		case 'index':
			$tableProp->saveKey();
			break;
		case 'insertElement':
			$tableProp->formElement();
			break;
		case 'insertFromFile':
			$tableProp->formFromFile();
			break;
		case 'saveFromFile':
			$tableProp->saveFromFile();
			break;
		case 'modifyElement':
			$tableProp->formElement(urldecode(SQLiteStripSlashes($_GET['query'])), $_GET['pos']);
			break;
		case 'saveElement':
		case 'deleteElement':
			$tableProp->saveElement();
			if(isset($GLOBALS['reBrowse']) && $GLOBALS['reBrowse']) include_once INCLUDE_LIB.'browse.php';
			break;
		case 'empty':
			$query = 'DELETE FROM '.brackets($table).';';
			if($dbsel) $workDb->getResId($query);
			displayQuery($query, false);
			$redirect = "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$dbsel."'; parent.main.location='main.php?dbsel=$dbsel&table=$table';</script>";
			break;
		case 'export':
			include_once INCLUDE_LIB.'SQLiteExport.class.php';
			$export = new SQLiteExport($workDb);
			break;
		case 'select':
			include_once INCLUDE_LIB.'SQLiteSelect.class.php';
			$select = new SQLiteSelect($workDb, $table);
			break;
		case 'selectElement':
			$DisplayQuery = $tableProp->selectElement($table);
			include INCLUDE_LIB.'browse.php';
			break;
		case 'operation':
			include_once INCLUDE_LIB.'SQLiteDbOperation.class.php';
			$export = new SQLiteDbOperation($workDb);
			break;
		}
} else {
	switch($action){
		case '':
		default:
			$tableProp->tableEditForm();
			break;
		case 'add_table':
			$tableProp->tableEditForm();
			break;
	}
}
?>

</body>
</html>
