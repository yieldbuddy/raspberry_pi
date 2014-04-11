<?php
/**
* Web based SQLite management
* Index management class
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: TableIndex.class.php,v 1.28 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.28 $
*/

/**
* Web based SQLite management
* Index management class
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: TableIndex.class.php,v 1.28 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.28 $
*/
class TableIndex {

	/**
	* Table name
	* @access public
	* @var string
	*/
	var $table; 
	
	/**
	* reference with table properties
	* @access private
	* @var array
	*/
	var $tableInfo;
	
	/**
	* Index properties
	* @access public
	* @var array
	*/
	var $indexInfo;

	/**
	* Class constructor
	*
	* @param string $table Table name
	* @param array &$propTable reference of the table properties
	*/
	function __construct($table, &$propTable){
		$this->table = $table;
		if(is_array($propTable)) $this->tableInfo = $propTable;
		$this->getIndexList();
		if(!isset($GLOBALS['index_action'])) $GLOBALS['index_action'] = '';
		switch($GLOBALS['index_action']){
			case '':
				$this->indexPropView();
				break;
			case 'addIndex':
			case 'modify':
				$this->indexPropForm();
				break;
			case 'save':
			case 'delete':
				if(!empty($_POST['addCols'])) $this->indexPropForm();
				else $this->save();
				break;
			default:
				break;				
		}
	}		
	
	/**
	* Save the index properties
	* manage new or update
	*
	*/
	function save(){
		if($GLOBALS['index_action'] != 'delete'){
			foreach($this->indexInfo as $listIndex) {
				if(isset($listIndex['name']) && ($listIndex['name'] == $_REQUEST['name'])){
					$query[] = 'DROP INDEX '.$_POST['name'].';';
				}
			}
		} else {
			$query[] = $GLOBALS['DisplayQuery'] = 'DROP INDEX '.$this->indexInfo[$_GET['indexSeq']]['name'].';';
		}
		if($GLOBALS['index_action'] == 'save') {
			if(is_array($_POST['columnName'])){
				while(list($id, $cont) = each($_POST['columnName'])) {
				  if($cont == '') unset($_POST['columnName'][$id]);
				  $_POST['columnName'][$id] = brackets($_POST['columnName'][$id]);				  
				}
			}
			$query[] = $GLOBALS['DisplayQuery'] = 'CREATE '.(($_POST['indexType'])? 'UNIQUE ' : '' ).'INDEX '.str_replace(' ','_',$_POST['name']).' ON '.brackets($this->table).'('.implode(',', $_POST['columnName']).');'; 
		}

		foreach($query as $req) {

			$GLOBALS['workDb']->connId->query($req, false, false);

		}
		return;
	}
	
	/**
	* Display all index properties
	*/
	function indexPropView(){
		echo '<form name="indexprop" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">';
		echo '<table class="Index" cellpadding="0" cellspacing="0" width="90%">
					<thead>
					<tr>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(91).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(28).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(27).'</td>
						<td align="center" class="Browse" colspan="2">'.$GLOBALS['traduct']->get(33).'</td>
					</tr>
					</thead>';
		if(is_array($this->indexInfo)) while(list($i, $info) = each($this->indexInfo)){
			$this->linePropView($i, $info);
		}
		echo '</table>';
		echo '<input type="hidden" name="action" value="">'."\n";
		echo '</form>'."\n";
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) $this->formAddIndex();
		return;
	}
	
	/**
	* Display one index properties
	*/
	function linePropView($i, $info){
		static $indexI;
		if($indexI == '') $indexI = 0;
		if($indexI % 2) $localBgColor = $GLOBALS['browseColor1'];
		else $localBgColor = $GLOBALS['browseColor2'];

		echo '	<tr bgcolor="'.$localBgColor.'" '.
		     'onMouseOver="'."setRowColor(this, $indexI, 'over', '$localBgColor', '".$GLOBALS['browseColorOver']."', '".$GLOBALS["browseColorClick"]."')\" ".
			 'onMouseOut="'."setRowColor(this, $indexI, 'out', '$localBgColor', '".$GLOBALS["browseColorOver"]."', '".$GLOBALS["browseColorClick"]."')\">\n".'
					<td align="left" class="Index">'.$info['name'].'</td>
					<td align="left" class="Index">'.$info['type'].'</td>
					<td align="right" class="Index">'.implode('<br>', $info['champ']).'</td>';					
		if(strtoupper($i) != 'PRIMARY'){		
			echo '		<td align="center" class="Index">';
			if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'&amp;index_action=modify&amp;indexSeq='.$i.'" class="Browse" target="main">'.displayPics('edit.png', $GLOBALS['traduct']->get(14)).'</a>';
			else echo '<i>'.displayPics('edit_off.png', $GLOBALS['traduct']->get(14)).'</i>';
			echo '</td>';
			echo '		<td align="center" class="Index">';
			if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) 
				echo "<a href=\"#\" onClick=\"javascript:if(confirm('".addslashes($GLOBALS['traduct']->get(92))." \'".$info['name']."\'')) parent.main.location='main.php?dbsel=".$GLOBALS['dbsel']."&amp;table=".$this->table."&amp;index_action=delete&amp;indexSeq=".$i."';".'" class="Browse">'.
				displayPics('edittrash.png', $GLOBALS['traduct']->get(15)).'</a>';
			else echo '<i>'.displayPics('edittrash_off.png', $GLOBALS['traduct']->get(15)).'</i>';
			echo '</td>';
		} else {
			echo '		<td align="center" class="Index">&nbsp;</td>';
			echo '		<td align="center" class="Index">&nbsp;</td>';
		}
		echo '	</tr>';
		$indexI++;
	}
	
	/**
	* Display form to add index
	*/
	function formAddIndex(){
		echo '<form name="addIndex" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">'."\n";
		echo '	<span style="font-size: 12px;">'.$GLOBALS['traduct']->get(93).' 
				<input type="text" name="nbCols" value=1 size="2" class="small-input" /> '.
				$GLOBALS['traduct']->get(94).' 
				<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'" />'."\n";
		echo '<input type="hidden" name="index_action" value="addIndex">'."\n";
		echo '</span></form>'."\n";
	}
	
	/**
	* Display index Form
	* Add or modif
	*/
	function indexPropform(){
		if( isset($GLOBALS['indexSeq']) && ($GLOBALS['indexSeq'] != '') ){
			$seq = $GLOBALS['indexSeq'];
			$nbCols = count($this->indexInfo[$seq]['champ']);
		} else {
			$GLOBALS['indexSeq'] = '';
			$nbCols = $GLOBALS['nbCols'];
			$seq = '';
		}
		if(isset($_POST['addCols']) && $_POST['addCols']) $nbCols += $GLOBALS['addCols']; 
		echo '<form name="addIndex" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">'."\n";
		echo '<span style="font-size: 12px;"><b>'.$GLOBALS['traduct']->get(19).' : </b>
				<input type="text" class="text" name="name" value="'.((isset($_POST['name']))? $_POST['name'] : ((isset($this->indexInfo[$seq]['name']))? $this->indexInfo[$seq]['name'] : '' ) ).'" class="small-input">'.str_repeat('&nbsp;', 5).'<b>'.$GLOBALS['traduct']->get(20).' : </b>';
		if(isset($_POST['indexType']) && !empty($_POST['indexType'])) $currentType = $_POST['indexType'];
		elseif(isset($this->indexInfo[$seq]['type'])) $currentType = $this->indexInfo[$seq]['type'];
		else $currentType = '';
		echo '<select name="indexType" class="small-input"><option value="UNIQUE"'.(($currentType == 'UNIQUE')? ' selected="selected"' : '' ).'>UNIQUE</option><option value=""'.(($currentType != 'UNIQUE')? ' selected="selected"' : '' ).'>KEY</option></select><br/>';
		echo '<center><table width="98%"><tr><td align="right" valign="top"><b>'.$GLOBALS['traduct']->get(27).' :&nbsp;</b></td><td align="center">';
		for($i=0 ; $i<$nbCols ; $i++){
			echo '<select name="columnName[]" class="small-input"><option value="">-- '.$GLOBALS['traduct']->get(95).' --</option>';
			foreach($this->tableInfo as $champInfo) {		
				echo '<option value="'.$champInfo['name'].'"'.((($this->indexInfo[$seq]['champ'][$i] == $champInfo['name']) || ($_POST['columnName'][$i] == $champInfo['name']))? ' selected="selected"' : '' ).'>';
				echo $champInfo['name'].'</option>';
			}
			echo '</select><br/>';
		}
		echo '</td></tr>';
		echo '<tr><td align="center" colspan="2"><input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'" class="small-input"></td></tr>';
		echo '</table></center>';
		echo '</span>';
		echo '<hr width="90%">'."\n";
		echo '<span style="font-size: 12px;">'.$GLOBALS['traduct']->get(96).' <input type="text" class="text" name="addCols" value="" class="small-input" size="2"> '.$GLOBALS['traduct']->get(94).'.<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'" class="small-input"></span>'."\n";
		echo '<input type="hidden" name="nbCols" value="'.$nbCols.'"><input type="hidden" name="index_action" value="save"><input type="hidden" name="indexSeq" value="'.$GLOBALS['indexSeq'].'"></form>'."\n";
		
	}
	
	/**
	* Return the index list
	*/
	function getIndexList(){
		if(is_array($this->tableInfo)){
			foreach($this->tableInfo as $propTable) {
				if(isset($propTable['primary'])){
					$temp['name'] = $temp['type'] = 'PRIMARY';
					$temp['champ'][] = $propTable['name'];
					$this->indexInfo['primary'] = $temp;				
				}
			}
		}
		$tabIndexList = $GLOBALS['workDb']->connId->array_query('PRAGMA index_list('.brackets($this->table).');', SQLITE_ASSOC);
		if(is_array($tabIndexList)) {
			foreach($tabIndexList as $indexInfo){
				if(preg_match('#autoindex#i', $indexInfo['name'])) continue;
				if(!empty($indexInfo)){
					$tabInfoIndex = $GLOBALS['workDb']->connId->array_query('PRAGMA index_info('.brackets($indexInfo['name']).');', SQLITE_ASSOC);
					if(is_array($tabInfoIndex)){						
						$indexNum = $indexInfo['seq'];
						foreach($tabInfoIndex as $indexChamp) $this->indexInfo[$indexNum]['champ'][] = $indexChamp['name'];
						$this->indexInfo[$indexNum]['name'] = $indexInfo['name'];
						$this->indexInfo[$indexNum]['type'] = (($indexInfo['unique'])? 'UNIQUE' : 'KEY' );
					}
				}
			}
		}
		return $this->indexInfo;
	}
}
?>
