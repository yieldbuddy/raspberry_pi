<?php
/**
* Web based SQLite management
* Class for manage database operation
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteDbOperation.class.php,v 1.9 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.9 $
*/

/**
* Web based SQLite management
* Class for manage database operation
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteDbOperation.class.php,v 1.9 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.9 $
*/

include_once INCLUDE_LIB."SQLiteDbConnect.class.php";

class SQLiteDbOperation {

	/**
	* reference to the connection object
	*
	* @access public
	* @var object
	*/
	var $connId;

	/**
	 *
	 * @access private
	 * @var array
	 */
	var $tabDb;

	/**
	* Class constructor
	*
	* @access public
	* @param string $conn reference to the connection object
	*/
	function __construct(&$connId){
		$this->connId = $connId;
		$error = false;
		$tabInfoDb = $this->getTabDb();
		foreach($tabInfoDb as $trash=>$locDb) $locTabInfoDb[$locDb["id"]] = $locDb["name"];
		if(!isset($GLOBALS['operation_action'])) $GLOBALS['operation_action'] = '';
		switch($GLOBALS['operation_action']){
			case '':
				$this->operationView();
				break;
			case 'renameTable':
				$error = $GLOBALS['workDb']->copyTable($_REQUEST["table"], brackets($_REQUEST["newName"], false), false);
				echo '<script type="text/javascript">parent.left.location=\'left.php?dbsel='.$GLOBALS["dbsel"].'\'; parent.main.location=\'main.php?dbsel='.$GLOBALS["dbsel"].'&table='.$_REQUEST["newName"].'&action='.$GLOBALS["action"].'\'</script>';
				$this->operationView();
				break;
			case 'moveTable':
				if($_REQUEST["dbDest"] == $GLOBALS["tabInfoDb"]["id"])
				    $destTableInfo = brackets($_REQUEST["moveName"]);
				else
				    $destTableInfo = brackets($locTabInfoDb[$_REQUEST["dbDest"]], false).".".brackets($_REQUEST["moveName"], false);
				$error = $GLOBALS['workDb']->copyTable($_REQUEST["table"], $destTableInfo, false);
				$this->operationView();
				break;
			case 'copyTable':
				if($_REQUEST["dbDest"] == $GLOBALS["tabInfoDb"]["id"])
				    $destTableInfo = brackets($_REQUEST["copyName"], false);
				else
				    $destTableInfo = brackets($locTabInfoDb[$_REQUEST["dbDest"]], false).".".brackets($_REQUEST["copyName"], false);
				$error = $GLOBALS['workDb']->copyTable($_REQUEST["table"], $destTableInfo, true);
				$this->operationView();
				break;
		}
		if(!empty($GLOBALS['operation_action']) && !$error){
			$GLOBALS["redirect"] = "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."'; parent.main.location='main.php?dbsel=".$GLOBALS["dbsel"]."';</script>";
		}
	}

	/**
	* Display Available Operation
	*
	* @access public
	*/
	function operationView(){
		$ModifPropOk = (!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties'));
		$localTableProp = new SQLiteTableProperties($this->connId);
		$fieldInfo = $localTableProp->getTableProperties($GLOBALS["table"]);

		// build select field
		$fieldList = '<select name="field">';
		foreach($fieldInfo as $info) $fieldList .= '<option value="'.$info["cid"].'">'.$info["name"].'</option>';
		$fieldList .= '</select>';



		echo '<center>
				<table cellspacing="0" width="80%">
					<tr>
						<td>';

		echo '				<form name="Rename" action="main.php" method="POST" target="main">
							<table class="Browse" cellspacing="0" width="100%">
								<thead>
									<tr class="Browse">
										<td colspan=2 align="left" class="tapPropTitle">&nbsp;'.$GLOBALS["traduct"]->get(215).'</td>
									</tr>
								</thead>'."\n";
		echo '					<tr>
										<td>&nbsp;<input type="text" name="newName" size=15>&nbsp;</td><td align="right"><input type="submit" value="'.$GLOBALS["traduct"]->get(69).'" class="button"></td>
									</tr>
							</table>
							<input type="hidden" name="dbsel" value="'.$GLOBALS["dbsel"].'">
							<input type="hidden" name="table" value="'.$GLOBALS["table"].'">
							<input type="hidden" name="action" value="'.$GLOBALS["action"].'">
							<input type="hidden" name="operation_action" value="renameTable">
							</form>';
		echo '			</td>
					</tr>';
		echo '		<tr>
						<td>';
		echo '				<form name="Move" action="main.php" method="POST" target="main">
							<table class="Browse" cellspacing="0" width="100%">
								<thead>
									<tr class="Browse">
										<td colspan=2 align="left" class="tapPropTitle">&nbsp;'.$GLOBALS["traduct"]->get(216).'</td>
									</tr>
								</thead>'."\n";
		echo '					<tr>
										<td>&nbsp;'.$this->getDbList().'.<input type="text" name="moveName" size=15>&nbsp;</td>
										<td align="right"><input type="submit" value="'.$GLOBALS["traduct"]->get(69).'" class="button"></td>
								</tr>
							</table>
							<input type="hidden" name="dbsel" value="'.$GLOBALS["dbsel"].'">
							<input type="hidden" name="table" value="'.$GLOBALS["table"].'">
							<input type="hidden" name="action" value="'.$GLOBALS["action"].'">
							<input type="hidden" name="operation_action" value="moveTable">
							</form>';
		echo '			</td>
					</tr>';
		echo '		<tr>
						<td>';
		echo '				<form name="Copy" action="main.php" method="POST" target="main">
							<table class="Browse" cellspacing="0" width="100%">
								<thead>
									<tr class="Browse">
										<td colspan=3 align="left" class="tapPropTitle">&nbsp;'.$GLOBALS["traduct"]->get(217).'</td>
									</tr>
								</thead>'."\n";
		echo '					<tr>
										<td>&nbsp;'.$this->getDbList($GLOBALS["dbsel"]).'.<input type="text" name="copyName" size=15>&nbsp;</td>
										<td style="white-space: nowrap">
											<input name="whatToDo" value="structure" style="vertical-align: middle;" type="radio">
											<label>'.$GLOBALS["traduct"]->get(124).'</label>&nbsp;&nbsp;<br>
											<input name="whatToDo" value="data" checked="checked" style="vertical-align: middle;" type="radio">
											<label>'.$GLOBALS["traduct"]->get(125).'</label>&nbsp;&nbsp;<br>
											<input name="whatToDo" value="dataonly" style="vertical-align: middle;" type="radio">
											<label>'.$GLOBALS["traduct"]->get(126).'</label>&nbsp;&nbsp;<br>
											<input name="dropTable" value="true" style="vertical-align: middle;" type="checkbox">
											<label>'.$GLOBALS["traduct"]->get(218).'</label>&nbsp;&nbsp;<br>
										</td>

										<td align="right"><input type="submit" value="'.$GLOBALS["traduct"]->get(69).'" class="button"></td>
								</tr>
							</table>
							<input type="hidden" name="dbsel" value="'.$GLOBALS["dbsel"].'">
							<input type="hidden" name="table" value="'.$GLOBALS["table"].'">
							<input type="hidden" name="action" value="'.$GLOBALS["action"].'">
							<input type="hidden" name="operation_action" value="copyTable">
							</form>';

		echo '			</td>
					</tr>
				</table>
				</center>';
	}

	/**
	* Display SQlite error
	*
	* @access public
	* @param string $queryDisplay Query to display
	* @param resource $res Database connection resource
	*/
	function _getError($queryDisplay, $res){
		if(!$res){
			$errorMessage .= $GLOBALS['traduct']->get(9).' : '.@$this->connId->connId->getError()."\n";
		}
		displayQuery($queryDisplay);
		if(!empty($errorMessage)) displayError($errorMessage);

	}

	function getDbList($selected=""){
		$tabInfoDb = $this->getTabDb();
		$dbList = '<select name="dbDest">';
		foreach($tabInfoDb as $dbInfo) $dbList .= '<option value="'.$dbInfo["id"].'"'.(($dbInfo["id"]==$selected)? ' selected' : '' ).'>'.$dbInfo["name"].'</option>';
		$dbList .= '</select>';
		return $dbList;
	}

	function getTabDb(){
		$this->tabDb = array();
		$tempTabDb = $GLOBALS["db"]->array_query("SELECT id, name, location FROM database", SQLITE_ASSOC);
		foreach($tempTabDb as $tabDbInfo) {
			if(sqlite::getDbVersion($tabDbInfo['location']) == $this->connId->connId->getVersion()) {
				$this->tabDb[] = $tabDbInfo;
			}
		}
		return $this->tabDb;
	}
}
?>