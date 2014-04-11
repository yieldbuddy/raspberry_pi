<?php
/**
* Web based SQLite management
* Show and manage 'TABLE' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteTableProperties.class.php,v 1.115 2006/04/16 20:06:37 freddy78 Exp $ $Revision: 1.115 $
*/

include_once INCLUDE_LIB.'TableIndex.class.php';

class SQLiteTableProperties {

	/**
	* reference to the connection object
	*
	* @access public
	* @var object
	*/
	var $connId;

	/**
	* TABLE name
	*
	* @access private
	* @var string
	*/
	var $table;

	/**
	* this TABLE exist?
	*
	* @access private
	* @var bool
	*/
	var $isExist;

	/**
	* if this table have a PRIMARY KEY -> true
	*
	* @access private
	* @var bool
	*/
	var $tablePrimary;

	/**
	* TABLE properties
	*
	* @access private
	* @var array
	*/
	var $infoTable;

	/**
	* Class constructor
	*
	* @access public
	* @param string $conn reference to the connection object
	*/
	function __construct(&$conn, $table=""){
		// constructeur de la classe
		$this->connId = $conn;
		if(!empty($table)){
			$this->table = $table;
		} else if($GLOBALS['table']) {
			$this->table = $GLOBALS['table'];
		} elseif($GLOBALS['TableName']){
			$this->table = $GLOBALS['TableName'];
		} else return false;
		if($GLOBALS["action"] != "delete") {
			$this->isExist = $this->tableExist($this->table);
		} else {
			$this->isExist = true;
		}
		return $this->isExist;
	}


	/**
	* Get all the table properties
	*
	* @access public
	* @param string $table table name
	*/
	function getTableProperties($table=''){
		if(empty($table)) $table = $this->table;
		$this->connId->getResId('PRAGMA table_info('.brackets($table).');');
		$this->infoTable = $this->connId->getArray();
		$this->searchPrimaryKey($table);
		return $this->infoTable;
	}

	/**
	 * Get index SQL of table
	 *
	 * @access private
	 */
	function getIndexSQL() {
		$query = "SELECT sql FROM sqlite_master WHERE tbl_name LIKE '".$this->table."';";
		$this->connId->getResId($query);
		$listIndex = $this->connId->getArray();
		return $listIndex;
	}

	/**
	* Verify if this TABLE exist
	*
	* @access public
	* @param string $table Table name
	*/
	function tableExist($table){
		if(empty($table)) $table = $this->table;
		$this->connId->getResId("SELECT count(*) FROM sqlite_master WHERE type='table' AND name=".quotes($table).";");
		if($this->connId->connId->fetch_single()>0) return true;
		else return false;
	}

	/**
	* search of all the index
	*
	* @access public
	* @param string $table Table name
	*/
	function searchPrimaryKey($table){
		if(empty($table)) $table = $this->table;
		$this->tablePrimary = false;
		$this->connId->getResId("SELECT sql FROM sqlite_master WHERE type='table' and name=".quotes($table).";");
		$sql = $this->connId->connId->fetch_single();
		$firstPar 	= strpos($sql, '(');
		$endPar 	= strrpos($sql, ')')-1;
		$sql = substr($sql, ($firstPar+1), ($endPar - $firstPar));
		$sql = str_replace("\n", '', $sql);
		$ligne = explode(',', $sql);
		while(list($ligneNum, $cont) = each($ligne)){
			if(preg_match("#PRIMARY[[:space:]]KEY#i", $cont)){
				$tempCont = preg_replace('#PRIMARY[[:space:]]KEY#i', '', $cont);
				$tempCont = preg_replace('#\(|\)#', '', $tempCont);
				$tabColName = explode(',', $tempCont);
				if(is_array($tabColName)) {
					foreach($tabColName as $colName) {
						$primaryKey = $this->numCol(trim($colName));
						$this->infoTable[$primaryKey]['primary'] = true;
						$this->tablePrimary = true;
					}
					return $primaryKey;
				} else {
					return false;
				}
			}
		}
	}

	/**
	* save current TABLE properties, add or modify
	*
	* @access public
	*/
	function saveProp(){
		$query = 'CREATE TABLE '.brackets($this->table).' ('."\n";
		if(!$this->isExist) {
			$error = false;
			while(list($key, $value) = each($_POST['fieldName'])){
				if(!empty($_POST['fieldName'][$key])){
					$query .= brackets(cleanFieldName($value)).' '.$_POST['fieldType'][$key].(($_POST['fieldLength'][$key])? '('.SQLiteStripSlashes($_POST['fieldLength'][$key]).') ' : ' ' );
					$query .= $_POST['fieldNull'][$key];
					if(isset($_POST['primary']) && ($_POST['primary']==$key))
						$query .= ' PRIMARY KEY';
					$query .= (($_POST['fieldDefault'][$key] && ($_POST['fieldNull'][$key]=='NOT NULL'))? ' DEFAULT '.quotes($_POST['fieldDefault'][$key]) : '' ).",\n";
				}
			}
			$query = substr($query, 0, strlen($query)-2)."\n);";
			$res = $this->connId->getResId($query);
			if($res) {
				$this->isExist = true;
				$this->getTableProperties();
				displayQuery($query);
				$this->tablePropView();
				echo "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS['dbsel']."';</script>";
			}
			return;
		} else {
			$listIndexSQL = $this->getIndexSQL();
			$oldColumn = array();
			$newColumn = array();
			$nullToNotNull	= array();
			if(isset($_POST['after']) && ($_POST['after']=='START')){
				while(list($key, $trash) = each($_POST['fieldName'])) if(!empty($_POST['fieldName'][$key])) $query .= $this->getPostProp($key);
			}
			if(is_array($this->infoTable)) {
				reset($this->infoTable);
				while(list($cid, $champ)=each($this->infoTable)){
					if(isset($_POST['fieldName'][$cid]) && !isset($_POST['after'])) {
						$query .= $this->getPostProp($cid);
						$oldColumn[] = $this->infoTable[$cid]['name'];
						$newColumn[] = 	cleanFieldName($_POST['fieldName'][$cid]);
					} else{
						if(($GLOBALS['action'] == 'delete') && (isset($_POST['modify'][$cid]) && $_POST['modify'][$cid])) continue;
						$oldColumn[] = $this->infoTable[$cid]['name'];
						$query .= brackets($this->infoTable[$cid]['name']).' '.strtoupper($this->infoTable[$cid]['type']);
						$query .= (($this->infoTable[$cid]['notnull'])? ' NOT NULL' : '' );
						$noprimary 		= ( (isset($GLOBALS['action']) && ($GLOBALS['action'] == 'noprimary'))  && isset($_POST['modify'][$cid]) && $_POST['modify'][$cid] );
						$addprimary 	= ( (isset($GLOBALS['action']) && ($GLOBALS['action'] == 'addprimary')) && isset($_POST['modify'][$cid]) && $_POST['modify'][$cid] );
						if(!$noprimary && ( $addprimary || (isset($this->infoTable[$cid]['primary']) && $this->infoTable[$cid]['primary']) ) ) $query .= ' PRIMARY KEY';
						$query .= (($this->infoTable[$cid]['dflt_value'] && $this->infoTable[$cid]['notnull'])? ' DEFAULT '.quotes($this->infoTable[$cid]['dflt_value']) : '' ).",\n";
						$newColumn[] = $this->infoTable[$cid]['name'];
						if(isset($_POST['after']) && ($_POST['after']==(string)$cid)) {
							while(list($key, $trash) = each($_POST['fieldName'])){
								if(!empty($_POST['fieldName'][$key])) $query .= $this->getPostProp($key);
							}
						}
					}
					if(isset($_POST['fieldNull'][$cid]) && !$this->infoTable[$cid]['notnull'] && ($_POST['fieldNull'][$cid]=='NOT NULL')) {
						$nullToNotNull[] = $this->infoTable[$cid]['name'];
					}
				}
			}
			if(isset($_POST['after']) && ($_POST['after']=='END')){
				while(list($key, $trash) = each($_POST['fieldName'])) if(!empty($_POST['fieldName'][$key])) $query .= $this->getPostProp($key);
			}
			$query = substr($query, 0, strlen($query)-2)."\n);";
		}
		$condDrop = ( ($GLOBALS['action']=='delete') && !isset($_POST['modify']) );
		$displayError = false;
		if( !$condDrop && count($newColumn)>0) {
			$GLOBALS['phpSQLiteError'] = '';
			set_error_handler('phpSQLiteErrorHandling');
			$displayError = $this->connId->alterTable($this->table, $query, $oldColumn, $newColumn, $nullToNotNull);
			restore_error_handler();
			if(!$displayError) {
				// rebuild index
				if(is_array($listIndexSQL)) {
					foreach($listIndexSQL as $numIndex=>$indexSQL) {
						$res = @$this->connId->getResId($indexSQL['sql']);
					}
				}
			}
		} else {
			$this->connId->connId->query("BEGIN;", true, false);
			$query = 'DROP TABLE '.brackets($this->table).';';
			$res = $this->connId->connId->query($query, true, false);
			$this->connId->connId->query("COMMIT;", true, false);
		}

		$this->getTableProperties();
		if($displayError) displayError($this->connId->errorMessage);
		displayQuery($query);
		if($GLOBALS['action'] != 'delete')  {
			$this->tablePropView();
			echo "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS['dbsel']."';</script>";
		} else {
			echo "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS['dbsel']."'; parent.main.location='main.php?dbsel=".$GLOBALS['dbsel']."';</script>";
		}
	}

	/**
	* create column propertie from Form
	*
	* @access private
	* @param integer $index Number of column
	*/
	function getPostProp($index){
		$prop = brackets(cleanFieldName($_POST['fieldName'][$index])).' '.$_POST['fieldType'][$index].(($_POST['fieldLength'][$index])? '('.$_POST['fieldLength'][$index].') ' : ' ' );
		$prop .= $_POST['fieldNull'][$index].((isset($_POST['primary'][$index]) && $_POST['primary'][$index])? ' PRIMARY KEY' : '' );
		if($_POST['fieldDefault'][$index]!='') $prop .= ' DEFAULT '.quotes($_POST['fieldDefault'][$index]);
		elseif($_POST['fieldNull'][$index]=='NOT NULL') $prop .= ' DEFAULT "'.$GLOBALS["SQLiteType"][$_POST['fieldType'][$index]].'"';
		$prop .= ",\n";
		return $prop;
	}

	/**
	* Display TABLE form
	*
	* @access private
	*/
	function tableEditForm(){
		if(isset($_POST['modify']) && is_array($_POST['modify'])){
			$nbChamp = count($_POST['modify']);
			$tabIndex = array_keys($_POST['modify']);
		} elseif($this->isExist && empty($GLOBALS['action'])) $nbChamp = count($this->infoTable);
		else $nbChamp = $GLOBALS['nbChamps'];
		if($nbChamp){
		  echo '<!-- SQLiteTableProperties.class.php : tableEditForm() -->'."\n";
			echo '<br><center>';
			if(!$this->isExist) echo '<h4>'.$GLOBALS['traduct']->get(25).' : ';
			else echo '<h4>'.$GLOBALS['traduct']->get(26).' : ';
			echo $this->table.'</h4>';
			echo '<form name="tabprop" action="main.php?dbsel='.$GLOBALS['dbsel'].'" method="POST" target="main">';
			echo '	<table class="Browse" cellpadding=0 cellspacing=0 width=80%>
						<thead>
						<tr>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(27).'</td>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(28).'</td>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(29).'</td>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(30).'</td>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(31).'</td>
							<td align="center" class="Browse">'.$GLOBALS['traduct']->get(32).'</td>
						</tr>
						</thead>'."\n";
			for($i=0 ; $i<$nbChamp ; $i++){
				if(isset($tabIndex)) $index = $tabIndex[$i];
				else $index = $i;
				$this->editFormLine($index);
			}
			echo '</table>';
			echo '<input type="hidden" name="table" value="'.$this->table.'">'."\n".
			     '<input type="hidden" name="action" value="save">'."\n";
			if($GLOBALS['action'] == 'addChamp') echo '<input type="hidden" name="after" value="'.$_POST["after"].'">'."\n";
			echo '<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'">';
			echo '</form>';
			echo '</center>';
		} else $this->tablePropView();
	}

	/**
	* Display one column form
	*
	* @access private
	* @param integer $index Number of column
	*/
	function editFormLine($index){
		if($GLOBALS['action'] != 'addChamp'){
			if(isset($this->infoTable[$index]['name'])) 			$fieldName 		= $this->infoTable[$index]['name']; else $fieldName = '';
			if(isset($this->infoTable[$index]['notnull'])) 		$notnull 		= $this->infoTable[$index]['notnull']; else $notnull = '';
			if(isset($this->infoTable[$index]['dflt_value'])) 	$fieldDefault 	= $this->infoTable[$index]['dflt_value']; else $fieldDefault = '';
			if(isset($this->infoTable[$index]['primary'])) 		$fieldPrimary 	= $this->infoTable[$index]['primary']; else $fieldPrimary = '';
			if($pos=strpos($this->infoTable[$index]['type'], '(')){
				preg_match('/\((.*)\)/', $this->infoTable[$index]['type'], $lenType);
				$lenght = $lenType[1];
				$type = substr($this->infoTable[$index]['type'], 0, $pos);
			} else {
				$lenght = '';
				$type = $this->infoTable[$index]['type'];
			}
		} else {
			$fieldName = $notnull = $fieldDefault = $fieldPrimary = '';
			$type = '';
			$lenght = '';
		}

		$out = '<tr>
				<td align="center" class="Browse"><input type="text" class="text" name="fieldName['.$index.']" value="'.$fieldName.'"></td>
				<td align="center" class="Browse">
        <select name="fieldType['.$index.']">'."\n";
		$tabType = array_keys($GLOBALS['SQLiteType']);
		asort($tabType);
		foreach($tabType as $dispType) $out .= '     		<option value="'.$dispType.'"'.((strtoupper($type)==$dispType)? ' selected="selected"' : '' ).'>'.$dispType.'</option>'."\n";
		$out .= ' 	</select>'."\n".'</td>
				<td align="center" class="Browse"><input type="text" class="text" size="8" name="fieldLength['.$index.']" value="'.$lenght.'"></td>
				<td align="center" class="Browse"><select name="fieldNull['.$index.']">
						<option value="NOT NULL"'.(($notnull)? ' selected="selected"' : '' ).'>not null</option>
						<option value=""'.((!$notnull)? ' selected="selected"' : '' ).'>null</option>
					</select></td>
				<td align="center" class="Browse"><input type="text" class="text" name="fieldDefault['.$index.']" value="'.$fieldDefault.'"></td>
				<td align="center" class="Browse"><input type="radio" value="$index" name="primary" '.(($fieldPrimary)? ' checked="checked"' : '' ).'></td>
				</tr>';
		echo $out;
	}

	/**
	* Display TABLE properties
	*
	* @access public
	*/
	function tablePropView(){
	  echo '<!-- SQLiteTableProperties.class.php : tablePropView() -->'."\n";
		if( (($GLOBALS['index_action'] == 'save') && empty($_POST['addCols'])) || ($GLOBALS['index_action'] == 'delete') ) {
			$gestIndex = new TableIndex($this->table, $this->infoTable);
			displayQuery('');
			unset($GLOBALS['indexSeq'], $_GET['indexSeq'], $_POST['indexSeq']);
			unset($GLOBALS['index_action'], $_GET['index_action'], $_POST['index_action']);
			unset($gestIndex);
		}
		$out = '<br\>
				<div align="center">
				<table class="Main" cellpadding="0" cellspacing="0" width="80%">
					<tr>
						<td>
				<form name="tabprop" action="main.php?dbsel='.$GLOBALS['dbsel'].'" method="POST" target="main">
				<table class="Browse" cellpadding="0" cellspacing="0" width="100%">
					<thead>
					<tr>';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) $out .= '			<td class="Browse">&nbsp;</td>';
		$out .= '			<td align="center" class="Browse">'.$GLOBALS['traduct']->get(27).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(28).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(30).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(31).'</td>
						<td align="center" class="Browse" colspan="5">'.$GLOBALS['traduct']->get(33).'</td>
					</tr>
					</thead>';
		if(is_array($this->infoTable)){
			foreach($this->infoTable as $tableElement){
				if(isset($tableElement['cid'])) $out .= $this->linePropView($tableElement['cid']);
			}
		}
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) $out .= '	<tr><td colspan="10" class="BrowseSelect">&nbsp;&nbsp;&nbsp;'.displayPics("arrow_ltr.gif")."&nbsp;
				<a href=\"#\" onClick=\"javascript:setCheckBox('tabprop','modify',true);\" class=\"Browse\">".$GLOBALS["traduct"]->get(34)."</a>&nbsp;/&nbsp;
				<a href=\"#\" onClick=\"javascript:setCheckBox('tabprop', 'modify', false);\" class=\"Browse\">".$GLOBALS["traduct"]->get(35)."</a>&nbsp;-&nbsp;<i>".$GLOBALS["traduct"]->get(36)."</i>&nbsp;:&nbsp;
				<a href=\"#\" onClick=\"javascript: document.tabprop.action.value='modify'; document.tabprop.submit();\" class=\"Browse\">".displayPics("edit.png", $GLOBALS["traduct"]->get(14))."</a>&nbsp;-&nbsp;
				<a href=\"#\" onClick=\"javascript: if(confirm('".$GLOBALS["traduct"]->get(37)."')) { document.tabprop.action.value='delete'; document.tabprop.submit();}\" class=\"Browse\">".displayPics("deletecol.png", $GLOBALS["traduct"]->get(15))."</a></td></tr>";
		$out .= '</table>';
		$out .= '<input type="hidden" name="table" value="'.$GLOBALS['table'].'">'."\n";
		$out .= '<input type="hidden" name="action" value="">'."\n";
		$out .= '</form>';
		echo $out;
		echo '<hr width="80%">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) $this->formAddChamp();
		echo '<div class="TableOptions">'."\n";
		echo '<div class="Indexes">'."\n";
		echo '<h5>'.$GLOBALS['traduct']->get(38).'</h5>';
		$gestIndex = new TableIndex($this->table, $this->infoTable);
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '</td>
				</tr>
				</table>
				</div>';

	}

	/**
	* Display one column properties
	*
	* @access private
	* @param integer $index Number of column
	*/
	function linePropView($index){
		if($index % 2) $localBgColor = $GLOBALS['browseColor1'];
		else $localBgColor = $GLOBALS['browseColor2'];
		$textConfirm = $GLOBALS['traduct']->get(39).'\n'.$this->infoTable[$index]['name'].'?';
		if(!$this->infoTable[$index]['notnull']) $defltValue = '<i>NULL</i>';
		elseif($this->infoTable[$index]['dflt_value']=='') $defltValue = '&nbsp;';
		else $defltValue = $this->infoTable[$index]['dflt_value'];
		$out = '';
		$out .= "\t<tr bgcolor='".$localBgColor."' onMouseOver=\"setRowColor(this, $index, 'over', '".$localBgColor."', '".$GLOBALS['browseColorOver']."', '".$GLOBALS['browseColorClick']."')\"
									onMouseOut=\"setRowColor(this, $index, 'out', '".$localBgColor."', '".$GLOBALS['browseColorOver']."', '".$GLOBALS['browseColorClick']."')\">\n";
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= '<td align="center" class="Browse"><input type="checkbox" name="modify['.$index.']"></td>'."\n";
		$out .= '<td align="left" class="Browse">'.$this->infoTable[$index]['name'].'</td>'."\n";
		$out .= '<td align="left" class="Browse">'.strtoupper($this->infoTable[$index]['type']).'</td>'."\n";
		$out .= '<td align="center" class="Browse">'.((!$this->infoTable[$index]['notnull'])? $GLOBALS['traduct']->get(40) : $GLOBALS['traduct']->get(41) ).'</td>'."\n";
		$out .= '<td align="right" class="Browse">'.$defltValue.'</td>'."\n";

		$out .= '<td align="center" class="Browse" width="7%">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:setTableAction('tabprop', ".$index.", 'modify');\" class=\"Browse\">".displayPics("edit.png", $GLOBALS['traduct']->get(14))."</a>";
		else $out .= "<i>".displayPics("edit_off.png", $GLOBALS['traduct']->get(14))."</i>";
		$out .= '</td>'."\n";

		$out .= '<td align="center" class="Browse" width="7%">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:if(confirm('".$textConfirm."')) setTableAction('tabprop', ".$index.", 'delete');\" class=\"Browse\">".displayPics("deletecol.png", $GLOBALS['traduct']->get(15))."</a>";
		else $out .= "<i>".displayPics("deletecol_off.png", $GLOBALS['traduct']->get(15))."</i>";
		$out .= '</td>'."\n";

		$out .= '<td align="center" class="Browse" width="7%">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:setTableAction('tabprop', ".$index.", 'unique');\" class=\"Browse\">".displayPics("unique.png", $GLOBALS['traduct']->get(197))."</a>";
		else $out .= "<i>".displayPics("unique_off.png", $GLOBALS['traduct']->get(197))."</i>";
		$out .= '</td>'."\n";

		$out .= '<td align="center" class="Browse" width="7%">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:setTableAction('tabprop', ".$index.", 'index');\" class=\"Browse\">".displayPics("index.png", $GLOBALS['traduct']->get(198))."</a>";
		else $out .= "<i>".displayPics("index_off.png", $GLOBALS['traduct']->get(198))."</i>";
		$out .= '</td>'."\n";

		$out .= '<td align="center" class="Browse" width="7%">';
		if(($this->tablePrimary) && isset($this->infoTable[$index]['primary']) && ($this->infoTable[$index]['primary'])) {
			if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:setTableAction('tabprop', ".$index.", 'noprimary');\" class=\"Browse\">".displayPics("primaire.png", $GLOBALS['traduct']->get(42))."</a>";
			else $out .= "<i>".displayPics("primaire_off.png", $GLOBALS['traduct']->get(42))."</i>";
		} elseif(($this->tablePrimary) && (!isset($this->infoTable[$index]['primary']) || !($this->infoTable[$index]['primary']))) {
			$out .= '&nbsp;';
		} elseif(!$this->tablePrimary) {
			if(!$GLOBALS['workDb']->isReadOnly() && displayCondition("properties")) $out .= "<a href=\"#\" onClick=\"javascript:setTableAction('tabprop', ".$index.", 'addprimary');\" class=\"Browse\">".displayPics("primaire.png", $GLOBALS['traduct']->get(42))."</a>";
			else $out .= '<i>'.displayPics("primaire.png", $GLOBALS['traduct']->get(42)).'</i>';
		}
		$out .= '</td>';
		$out .= '	</tr>';
		return $out;
	}

	/**
	* Retreive the column number with his name
	*
	* @access public
	* @param string $name Column name
	*/
	function numCol($name){
		if(is_array($this->infoTable))
			while(list($cid, $champ)=each($this->infoTable))
				if($champ["name"]==$name)
					return $cid;
		return false;
	}

	/**
	* Display add column Form
	*
	* @access public
	*/
	function formAddChamp(){
		echo '<!-- SQLiteTableProperties.class.php : formAddChamp() -->'."\n";
		echo '<form name="addChamp" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">'."\n";
		echo '<span style="font-size: 12px;">'.$GLOBALS['traduct']->get(43).'
				<input type="text" name="nbChamps" value="1" size=2 class="small-input"> '.
				$GLOBALS['traduct']->get(44).' '."\n";
		echo '<select name="after" class="small-input">'."\n";
		echo '<option value="END">'.$GLOBALS['traduct']->get(45).'</option>'."\n";
		echo '<option value="START">'.$GLOBALS['traduct']->get(46).'</option>'."\n";
		foreach($this->infoTable as $champ) echo '<option value="'.$champ['cid'].'">'.$GLOBALS['traduct']->get(47).' '.$champ['name'].'</option>'."\n";
		echo '</select>
				<input type="submit" value="'.$GLOBALS['traduct']->get(69).'" class="button small-input">'."\n";
		echo '<input type="hidden" name="action" value="addChamp">'."\n";
		echo '</span>
				</form>'."\n";
	}

	/**
	* Display Form for add or modify table record
	*
	* @access public
	* @param string $req query where the record is
	* @param integer $numId Number of the query record
	* @param boolean $error if true, display POST value
	*/
	function formElement($req='', $numId='', $error=false){
		if(empty($req) && empty($numId))
		    $title = $GLOBALS['traduct']->get(48);
		else
		    $title = $GLOBALS['traduct']->get(49);

		if(!empty($req))
		    $tabData = $this->recupElement($req, $numId, $error);
		else
		    $tabData = array();
    echo '<!-- SQLiteTableProperties.class.php : formElement() -->'."\n";
		echo '<center><h4>'.$title.'</h4>'."\n";
		echo '<form name="editElement" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">'."\n";
		echo '	<table class="Insert" cellpadding="2" cellspacing="0" width="80%">
					<thead>
					<tr>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(27).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(28).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(10).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(30).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(50).'</td>
					</tr>
					</thead>';
		reset($this->infoTable);
		while(list($cid, $tabInfo) = each($this->infoTable)){
			$this->lineElement($tabInfo, $tabData);
		}
		echo '</table>';
		echo '<input type="hidden" name="action" value="saveElement">'."\n";
		if(isset($_REQUEST['currentPage'])) echo '<input type="hidden" name="currentPage" value="'.$_REQUEST['currentPage'].'">'."\n";
		if($req) {
			echo '<input type="hidden" name="numId" value="'.$numId.'">'."\n";
			echo '<input type="hidden" name="req" value="'.urlencode($req).'">'."\n";
		}
		echo '<table width=80% align="center"><tr><td>';
		$fontSize = "10px";
		if($GLOBALS["action"]!= "insertElement") {
			echo '<!--save type--><table><tr><td style="white-space: nowrap; font-size: '.$fontSize.';">'.$GLOBALS['traduct']->get(221).' : </td>
					<td><input type="radio" name="save_type" value="as_new_row"></td>
					<td style="font-size: '.$fontSize.';">: '.$GLOBALS['traduct']->get(219).'</td></tr>
					<tr><td>&nbsp;</td>
					<td><input type="radio" name="save_type" value="save" checked="checked"></td>
					<td style="font-size: '.$fontSize.';">: '.$GLOBALS['traduct']->get(220).'</td></tr></table><br/><!--end save type-->'."\n";
		} else {
			echo '<div align="center" style="font-size: '.$fontSize.';">'.$GLOBALS['traduct']->get(219).'</div>';
		}
		echo '</td><td align="right">';

		echo '<table><tr><td style="white-space: nowrap; font-size: '.$fontSize.';">'.$GLOBALS['traduct']->get(151).' : </td>
				<td><input type="radio" name="after_save" value="'.((isset($_REQUEST['currentPage']) && !empty($_REQUEST['currentPage']))? $_REQUEST['currentPage'] : ((isset($_REQUEST['after_save']) && !empty($_REQUEST['after_save']))? $_REQUEST['after_save'] : 'properties' ) ).'"'.((!isset($_POST['after_save']) || !empty($_POST['after_save']))? ' checked="checked"' : '' ).'></td>
				<td style="font-size: '.$fontSize.';">: '.$GLOBALS['traduct']->get(152).'</td></tr>
				<tr><td>&nbsp;</td>
				<td><input type="radio" name="after_save" value=""'.((isset($_REQUEST['after_save']) && ($_REQUEST['after_save']==''))? ' checked="checked"' : '' ).'></td>
				<td style="font-size: '.$fontSize.';">: '.$GLOBALS['traduct']->get(153).'</td></tr></table><br/>'."\n";

		echo '</td></tr></table>';

		echo '<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'" onclick="document.tabprop.submit();">';
		echo '</form>';
		echo '</center>'."\n";
		echo '<br/>'.str_repeat('&nbsp;', 10).'&raquo;&nbsp;<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'&amp;action=insertFromFile" class="base" target="main">'.$GLOBALS['traduct']->get(52).'</a>';
		return;
	}

	/**
	* Display on column for inert modify record
	*
	* @access private
	* @param array $infoTable table properties
	* @param array $tabValue current value for modify record
	*/
	function lineElement($infoTable, $tabValue=''){
		$simpleType = strtoupper(substr($infoTable['type'],0,4));
		if(($simpleType == 'TEXT') || ($simpleType == 'BLOB')) $BigInput = true; else $BigInput = false;
		if($BigInput && ADVANCED_EDITOR && (!isset($_COOKIE["SQLiteManager_HTMLon"]) || !$_COOKIE["SQLiteManager_HTMLon"])) $CheckreadOnly = ' DISABLED'; else $CheckreadOnly = '';
		if(!isset($tabValue[$infoTable['name']])) $tabValue[$infoTable['name']] = '';
		echo '	<tr>
					<td align="left" class="Insert">'.$infoTable['name'].'</td>
					<td align="center" class="Insert">'.StrToUpper($infoTable['type']).'</td>
					<td align="center" class="Insert">'.SQLiteFunctionList($infoTable['name']).'</td>
					<td align="center" class="Insert">';
		if(!$infoTable['notnull'] || isset($infoTable['primary'])){
			$CheckNull =  '<input type="checkbox" name="nullField['.$infoTable['name'].']" ';
			if($tabValue[$infoTable['name']]=='') $CheckNull .= 'checked="checked"';
			$CheckNull .= $CheckreadOnly;
			$CheckNull .= ' onChange="if(this.checked) cleanNullField(\'editElement\', \''.$infoTable['name'].'\');">';
		} else {
			 $CheckNull = '&nbsp;';
		}
		echo $CheckNull.'</td>
					<td align="left" class="Insert">';
		echo SQLiteInputType($infoTable, $tabValue[$infoTable['name']]);
		echo '</td>
				</tr>';
		return;
	}

	/**
	* save record
	*
	* @access private
	*/
	function saveElement(){
		if(isset($_GET['pos'])) $GLOBALS['numId'] = $_GET['pos'];
		if(isset($_REQUEST['numId'])) $GLOBALS['numId'] = $_REQUEST['numId'];
		if(isset($_GET['query'])) $GLOBALS['req'] = urldecode($_GET['query']);
		elseif(isset($_POST['req'])) $GLOBALS['req'] = urldecode($_POST['req']);
		if(isset($GLOBALS['req']) && isset($GLOBALS['numId'])){
			$oldValue = $this->recupElement($GLOBALS['req'], $GLOBALS['numId']);
		}

		if(isset($_POST['valField']) && is_array($_POST['valField'])){
			while(list($champ, $value) = each($_POST['valField'])){
				$value = SQLiteStripSlashes($value);
				$cid = $this->getCID($champ);
				$tempType = $this->infoTable[$cid]['type'];
				if(isset($_POST['funcs'][$champ]) && !empty($_POST['funcs'][$champ])){
					if(preg_match('#CHAR|TEXT|LOB|DATE#i', $tempType)) $funcVal = quotes($value);
					else $funcVal = $value;
					$value = applyFunction($_POST['funcs'][$champ], $funcVal);
				} elseif(!isset($_POST['nullField'][$champ]) || !$_POST['nullField'][$champ]) {
					if($tempType) {
						if(preg_match('#CHAR|TEXT|LOB|DATE#i', $tempType))
						    $value = quotes($value);
					} else
						$value = quotes($value);
				}

				if(isset($_POST['nullField'][$champ])) {
					$value = 'NULL';
				}
				if(!isset($_POST['numId']) || $_POST['save_type']=="as_new_row"){
					$listColumn[] 	= brackets($champ);
					$listValue[]	= $value;
				} else {
					if((isset($oldValue[$champ]) && ($value != quotes($oldValue[$champ])) || (!isset($oldValue[$champ])&& ($value != "NULL")))){
						$listColumn[]	= brackets($champ).'='.$value;
					}
				}

			}
		}

		$query = '';
		if($GLOBALS['action']=='deleteElement'){
			$query = 'DELETE FROM '.brackets($GLOBALS['table']).' WHERE ROWID='.$oldValue['ROWID'];
		} elseif(isset($_POST['numId']) && $_POST['save_type']!="as_new_row"){
			if(isset($listColumn) && !empty($listColumn)){
				$query = 'UPDATE '.brackets($GLOBALS['table']).' SET '.implode(', ', $listColumn).' WHERE ROWID='.$oldValue['ROWID'];
			}
		} else {
			if(isset($listColumn) && isset($listValue))
				$query = 'INSERT INTO '.brackets($GLOBALS['table']).' ('.implode(', ', $listColumn).') VALUES ('.implode(', ', $listValue).')';
		}
		displayQuery($query);
		$errorCode = false;
		if(isset($query) && !empty($query)){
			$this->connId->getResId('BEGIN;');
			if(!$this->connId->getResId($query)){
				echo '<center><span style="color: red;">'.$GLOBALS['traduct']->get(9).' : '.@$this->connId->connId->getError().'</span></center>';
				$this->formElement($GLOBALS['req'], $GLOBALS['numId'], true);
			}
			$this->connId->getResId('COMMIT;');
		}
		// return management
		if(!isset($_REQUEST['after_save']) && isset($_REQUEST['currentPage'])) $_REQUEST['after_save'] = $_REQUEST['currentPage'];
		if(!$errorCode && isset($_REQUEST['after_save'])){
			if($_REQUEST['after_save'] == '') $this->formElement(((isset($GLOBALS['req']))? $GLOBALS['req'] : '' ), ((isset($GLOBALS['numId']))? $GLOBALS['numId'] : '' ));
			else
				switch($_REQUEST['after_save']){
					case '':
					case 'properties':
						$this->tablePropView();
						break;
					case 'browseItem':
						if(isset($GLOBALS['numId'])){
							$GLOBALS['noDisplay'] = true;
							include_once INCLUDE_LIB.'ParsingQuery.class.php';
							$tabRes = ParsingQuery::noLimit($GLOBALS['req']);
							$GLOBALS['DisplayQuery'] = $tabRes['query'];
							$GLOBALS['pageBrowse'] = $_GET['pageBrowse'] = $tabRes['page'];
						}
						$GLOBALS['reBrowse'] = true;
						break;
				}
		}
	}

	/**
	* Form for insert data from text file formatted
	*
	* @access public
	*/
	function formFromFile(){
    echo '<!-- SQLiteTableProperties.class.php : formFromFile() -->'."\n";
		echo '<div align="center">'."\n";
		echo '<br/><h4>'.$GLOBALS['traduct']->get(140).'</h4><br/>';
		echo '<form name="fromfile" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$GLOBALS['table'].'" method="POST" ENCTYPE="multipart/form-data" target="main">'."\n";
		echo '<table border="1" width="70%">'."\n";
		echo '<tr><td>'.$GLOBALS['traduct']->get(137).'</td><td>&nbsp;<input type="file" class="file" name="fileInsert"></td></tr>';
		echo '<tr><td>'.$GLOBALS['traduct']->get(138).'</td><td><input type="checkbox" name="replaceAll"></td></tr>';
		echo '<tr><td>'.$GLOBALS['traduct']->get(139).'</td><td>&nbsp;<input type="text" class="text" name="separator" value="\t" size=5></td></tr>';
		echo '</table>'."\n";
		echo '<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'">'."\n";
		echo '<input type="hidden" name="action" value="saveFromFile">';
		echo '</form>';
		echo '</div>'."\n";
	}

	/**
	* Save data from text file formatted
	*
	* @access private
	*/
	function saveFromFile(){
		if($_POST['separator'] != '\\\t') $useDelim = ' USING DELIMITERS '.quotes($_POST['separator']);
		else $useDelim = '';
		$GLOBALS['DisplayQuery'] = $copyQuery = 'COPY '.brackets($this->table).' FROM '.quotes($_FILES['fileInsert']['tmp_name']).$useDelim.';';
		if($this->connId->connId->getVersion()!=3){
			$query[] = "BEGIN;";
			if(!empty($_FILES['fileInsert']['tmp_name'])){
				if(isset($_POST['replaceAll']) && $_POST['replaceAll']){
					$query[] = 'DELETE FROM '.brackets($this->table).';';
				}
				$query[] = $copyQuery;
				$query[] = "COMMIT;";
			}
		} else {
			// build save from file for SQLITE3
			$fileToLine = file($_FILES['fileInsert']['tmp_name']);
			if(is_array($fileToLine)){
				$query[] = "BEGIN;";
				if(isset($_POST['replaceAll']) && $_POST['replaceAll']){
					$query[] = 'DELETE FROM '.brackets($this->table).';';
				}
				if($_POST['separator'] == '\\t') $sep = "\t";
				else $sep = $_POST['separator'];
				foreach($fileToLine as $record){
					$recordElement = explode($sep, rtrim($record, "\n\r"));
					$query[] = "INSERT OR REPLACE INTO ".brackets($this->table)." VALUES ('".implode("', '", $recordElement)."');";
				}
				$query[] = "COMMIT;";
			}
		}
		$execError = false;
		$GLOBALS['phpSQLiteError'] = '';
		set_error_handler('phpSQLiteErrorHandling');
		foreach($query as $q){
			if(!$this->connId->getResId($q)){
				$execError = true;
				$this->connId->getResId("ROLLBACK TRANSACTION;");
				$errorMessage = '<table style="color: red;"><tr><td>'.$GLOBALS['traduct']->get(9).' :</td><td>'.$this->connId->connId->getError().'</td></tr>';
				if($GLOBALS['phpSQLiteError'] != '') $errorMessage .= '<tr><td>&nbsp;</td><td>'.$GLOBALS['phpSQLiteError'].'</td></tr>';
				$errorMessage .= '</table>';
				break;
			}
		}
		restore_error_handler();
		if($execError) {
			displayError($errorMessage);
			displayQuery($GLOBALS['DisplayQuery']);
			$this->formFromFile();
		} else {
			displayQuery($GLOBALS['DisplayQuery']);
		}
	}
	/**
	* Retrive Record from current query and numId
	*
	* @access public
	* @param string $req current query
	* @param integer $numId Number of record from current query
	* @param boolean $error if true return POST value
	*/
	function recupElement($req, $numId, $error=false){
		include_once INCLUDE_LIB.'ParsingQuery.class.php';
		$tabQueryElement = ParsingQuery::explodeSelect($req);

		$tabQueryElement['SELECT'] = 'ROWID, '.$tabQueryElement['SELECT'];

		if(preg_match('#FROM#i', $req)){
			$tabFrom = explode(',', $tabQueryElement['FROM']);
			foreach($tabFrom as $key=>$value)
			    $tabFrom[$key] = brackets(unquote($value));
			$tabQueryElement['FROM'] = implode(',', $tabFrom);
		}

		if(preg_match('#LIMIT#i', $req)){
			$tabLimit = explode(',', $tabQueryElement['LIMIT']);
			$tabQueryElement['LIMIT'] = ((int)$tabLimit[0]+$numId).',1';
		} else {
			$tabQueryElement['LIMIT'] = $numId.',1';
		}

		$querySearch = '';
		foreach($tabQueryElement as $clause=>$contentClause)
		    $querySearch .= $clause.' '.$contentClause.' ';

		$this->connId->connId->query($querySearch);
		$tabData = $this->connId->connId->fetch_array(null, (($this->connId->connId->getVersion()==3)? SQLITE_BOTH : SQLITE_ASSOC ));
		if($this->connId->connId->getVersion()==3) $tabData["ROWID"] = $tabData[0];
		if($error){
			foreach($tabData as $fieldname => $fieldvalue)
				if(isset($_POST[$fieldname])) $tabData[$fieldname] = $_POST[$fieldname];
		}
		return $tabData;
	}

	/**
	* Retrive 'cid' from champ name
	*
	* @access public
	* @param string $name
	*/
	function getCID($name){
		foreach($this->infoTable as $cid => $info)
			if($info['name']==$name) return $cid;
	}

	/**
	*
	*/
	function saveKey(){
		$cid = key($_POST['modify']);
		$columnName = $this->infoTable[$cid]['name'];
		if($_POST['action']=='unique') $type = 'UNIQUE ';
		else $type = '';
		$query = 'CREATE '.$type.'INDEX '.str_replace(' ','_',$this->table.'_'.$columnName).' ON '.brackets($this->table).'('.brackets($columnName).');';
		$GLOBALS['phpSQLiteError'] = '';
		set_error_handler('phpSQLiteErrorHandling');
		if(!$this->connId->getResId($query)){
				echo '<table align="center" style="color: red;"><tr><td>'.$GLOBALS['traduct']->get(9).' :</td><td>'.@$this->connId->connId->getError().'</td></tr>';
				if($GLOBALS['phpSQLiteError'] != '') echo '<tr><td>&nbsp;</td><td>'.$GLOBALS['phpSQLiteError'].'</td></tr>';
				echo '</table>';
		}
		restore_error_handler();
		displayQuery($query);
		$this->tablePropView();
	}


    /**
    * Generate SQL query for 'select'
    * @author Maurício M. Maia <mauricio.maia@gmail.com>
    *
    * @param string $table
    */
    function selectElement($table) {
        $showField = $_REQUEST['showField'];
        $valField = $_REQUEST['valField'];
        $operats = $_REQUEST['operats'];
		$error = false;
        $selectQuery = 'SELECT ';
        $condQuery = '';
		if(is_array($_REQUEST['showField']) && !empty($_REQUEST['showField'])){
			$selectQuery .= implode(", ", array_keys($_REQUEST['showField']));
	    } else $selectQuery .= '*';

        $fromQuery = ' FROM '.brackets($table).' ';
		if(is_array($_REQUEST['valField']) && !empty($_REQUEST['valField'])){
	        foreach($valField as $key => $value) {
	            if (	(isset($value) && !empty($value))
	            		|| (isset($operats[$key])
	            		&& !empty($operats[$key]))) {

					if($operats[$key] == 'ISNULL' || $operats[$key] == 'NOTNULL'){
	            		$condQuery .= $key.' '.$operats[$key];
	            	} else if($operats[$key]=="fulltextsearch"){
	            		if($selectQuery == "SELECT *"){
		            		$condQuery .= 'fulltextsearch('.$key.', '.quotes($value).', 0) > 0';
		            	} else {
		            		$selectQuery .= ', fulltextsearch('.$key.', '.quotes($value).', 0) AS '.$key.'Match';
		            		$condQuery .= $key.'Match > 0';
		            	}
	            	} else {
	            		$condQuery .= $key.' '.$operats[$key].' '.quotes($value);
	            	}
	            }
	        }
	    }
	    if(!empty($_REQUEST['CondSuppl'])){
	    	if($condQuery) $condQuery .= ' '.$_REQUEST['operSuppl'].' ';
	    	$condQuery .= $_REQUEST['CondSuppl'];
	    }
	    return $selectQuery.$fromQuery.(($condQuery)? 'WHERE '.$condQuery : '' );
    }

}

?>
