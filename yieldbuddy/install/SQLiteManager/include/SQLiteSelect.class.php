<?php
/**
* Web based SQLite management
* Class for generate 'Select' querys
* @package SQLiteManager
* @author Maurício M. Maia <mauricio.maia@gmail.com>
* @version $Id: SQLiteSelect.class.php,v 1.18 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.18 $
*/

class SQLiteSelect {

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
	* Class constructor
	*
	* @access public
	* @param string $conn reference to the connection object
	*/
	function __construct(&$conn){
		// constructeur de la classe
		$this->connId = $conn;
		if($GLOBALS['table']) {
			$this->table = $GLOBALS['table'];
		} elseif($GLOBALS['TableName']){
			$this->table = $GLOBALS['TableName'];
		} else if($GLOBALS['view']) {
			$this->table = $GLOBALS['view'];
		} elseif($GLOBALS['ViewName']){
			$this->table = $GLOBALS['ViewName'];
		} else return false;
		switch(isset($GLOBALS['select_action'])? $GLOBALS['select_action'] : '' ){
			case '':
				$this->getTableInfo($this->table);
				$this->form();
				break;
		}
	}

	/**
	* Get some table properties
	*
	* @access public
	* @param string $table table name
	*/
	function getTableInfo($table=''){
		if(empty($table)) $table = $this->table;
		$this->connId->getResId('PRAGMA table_info('.brackets($table).');');
		$this->infoTable = $this->connId->getArray();
		return $this->infoTable;
	}

	/**
	* Display Form for select table records
	*
	* @access public
	* @param string $req query where the record is
	* @param integer $numId Number of the query record
	* @param boolean $error if true, display POST value
	*/
	function form(){
	  echo '<!-- SQLiteSelect.class.php : form() -->'."\n";
		echo '<center><h4>'.$GLOBALS["traduct"]->get(201).'</h4>'."\n";
		echo '<form name="select" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$this->table.'" method="POST" target="main">'."\n";
		echo '	<table class="Insert" cellpadding="2" cellspacing="0" width="80%">
					<thead>
					<tr>
						<td align="center" class="Browse">'.$GLOBALS["traduct"]->get(73).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(27).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(28).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(202).'</td>
						<td align="center" class="Browse">'.$GLOBALS['traduct']->get(50).'</td>
					</tr>
					</thead>';
		reset($this->infoTable);
	  if (!isset($tabData)) $tabData = array();
		while(list($cid, $tabInfo) = each($this->infoTable)){
			$this->lineElement($tabInfo, $tabData);
		}
		echo '	<tr>
					<td style="text-align:left" colspan="10">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.displayPics('arrow_ltr.gif').'&nbsp;
					<a href="#" onClick="javascript:setCheckBox(\'select\',\'showField\',true);" class="Browse">'.$GLOBALS['traduct']->get(34).'</a>&nbsp;/&nbsp;
					<a href="#" onClick="javascript:setCheckBox(\'select\', \'showField\', false);" class="Browse">'.$GLOBALS['traduct']->get(35).'</a>';
		echo '	<tr>
					<td colspan="4">&nbsp;</td><td class="TitleHeader" style="text-align:left">'.$GLOBALS["traduct"]->get(203).'</td>
				</tr>'."\n";
		echo '	<tr>
					<td colspan="3">&nbsp;</td>'."\n".'
					<td style="text-align:right; white-space: nowrap">
						<label for="AND">'.$GLOBALS["traduct"]->get(204).'</label><input type="radio" name="operSuppl" id="AND" value="AND" checked="checked"><br/>
						<label for="OR">'.$GLOBALS["traduct"]->get(205).'</label><input type="radio" name="operSuppl" id="OR" value="OR">
					</td>
					<td style="text-align:left">
						<textarea name="CondSuppl" cols="'.TEXTAREA_NB_COLS.'" rows="4"></textarea>
					</td>
				</tr>';
		echo '</table>';
		if(isset($req)) {
			echo '<input type="hidden" name="numId" value="'.$numId.'">'."\n";
			echo '<input type="hidden" name="req" value="'.urlencode($req).'">'."\n";
		}
		echo '<input type="hidden" name="action" value="selectElement">'."\n";
		if(isset($_REQUEST['currentPage'])) echo '<input type="hidden" name="currentPage" value="'.$_REQUEST['currentPage'].'">'."\n";

		echo '<input class="button" type="submit" value="'.$GLOBALS["traduct"]->get(201).'" onclick="document.tabprop.submit();">';
		echo '</form>';
		echo '</center>';
		return;
	}

	/**
	* Display on column for select records
	*
	* @access private
	* @param array $infoTable table properties
	* @param array $tabValue current value for modify record
	*/
	function lineElement($infoTable, $tabValue=''){
		if(!isset($tabValue[$infoTable['name']])) $tabValue[$infoTable['name']] = '';
		echo '	<tr>
					<td align="center" class="Insert"><input type="checkbox" name="showField['.$infoTable['name'].']"'.(($tabValue[$infoTable['name']]=='')? ' checked="checked"' : '' ).'></td>
					<td align="left" class="Insert">'.$infoTable['name'].'</td>
					<td align="center" class="Insert">'.StrToUpper($infoTable['type']).'</td>
					<td align="center" class="Insert">'.SQLiteSelectList($infoTable['name']).'</td>
					<td align="left" class="Insert">'.SQLiteInputType($infoTable, $tabValue[$infoTable['name']], false, false).'</td>
				</tr>';
		return;
	}

}

?>


