<?php
/**
* Web based SQLite management
* Class for database structure export
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteExport.class.php,v 1.27 2006/04/18 09:19:20 freddy78 Exp $ $Revision: 1.27 $
*/

/**
* Web based SQLite management
* Class for database structure export
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteExport.class.php,v 1.27 2006/04/18 09:19:20 freddy78 Exp $ $Revision: 1.27 $
*/
class SQLiteExport {

	/**
	* reference to the connection object
	*
	* @access public
	* @var object
	*/
	var $connId;

	/**
	* Export content
	*
	* @access private
	* @var array
	*/
	var $exportContent;

	/**
	* User Defined Function properties
	*
	* @access private
	* @param array $UDFprop
	*/
	var $UDFprop;

	/**
	 *
	 * Full table's information
	 *
	 * @access private
	 * @param array $tableProperties
	 */
	var $tableProperties;

	/**
	 *
	 * Trigger information
	 *
	 * @access private
	 * @param array $triggerProperties
	 */
	var $triggerProperties;

	/**
	* Class constructor
	*
	* @access public
	* @param string $conn reference to the connection object
	*/
	function __construct(&$connId){
		$this->connId = $connId;
		switch($GLOBALS['export_action']){
			case '':
				$this->form();
				break;
			case 'go':
			$this->exportContent = '';
				$this->exportHeader();
				if(isset($_POST['table']) && !empty($_POST['table'])){
					$this->exportTableSelector($_POST['table'], $_POST['type']);
				} elseif(isset($_POST['view']) && !empty($_POST['view'])) {
					$this->viewProperties($_POST['view']);
				} elseif(isset($_POST['function']) && !empty($_POST['function'])) {
					$this->functionProperties($_POST['function']);
				} else {
					$this->dbProperties();
				}
				$this->send();
				break;
		}
	}

	/**
	* Display form for option choose
	*
	* @access public
	*/
	function form(){
		echo '<center><br>';
		if(isset($_REQUEST['queryExport']) && $_REQUEST['queryExport']) {
			$query = urldecode(SQLiteStripSlashes($_REQUEST['queryExport']));
			displayQuery($query, false);
		}
		echo '<form name="export" action="main.php" method="POST" target="main">
				<table class="Insert" cellspacing="0" cellpadding="0" border="1" width="50%">
					<tr>
						<td valign="top">
						  <table width="100%" cellspacing="0" cellpadding="0">
              <thead><tr><td class="tabproptitle">
              '.$GLOBALS['traduct']->get(76).'
							</td></tr>
							</thead>
              <tr><td style="padding-left:15px;padding-top:15px;">
							<input type="radio" name="type" value="1" checked>'.$GLOBALS['traduct']->get(124).'<br/>
							<input type="radio" name="type" value="2">'.$GLOBALS['traduct']->get(125).'<br/>
							<input type="radio" name="type" value="3">'.$GLOBALS['traduct']->get(126).'<br/>
							</td></tr>
							</table>
						</td>
						<td>
						  <table width="100%" cellspacing="0" cellpadding="0">
              <thead>
              <tr><td class="tabproptitle">
							'.$GLOBALS["traduct"]->get(72).' :
							</td></tr>
							</thead>
              <tr><td>
							&nbsp;<input type="checkbox" name="drop" value="1">'.$GLOBALS['traduct']->get(43).' "DROP"<br/><br/>
							</td></tr>
              <thead>
              <tr><td class="tabproptitle">
							'.$GLOBALS["traduct"]->get(199).' :
							</td></tr>
							</thead>
              <tr><td>
							&nbsp;<input type="checkbox" name="fullInsert" value="1">'.$GLOBALS['traduct']->get(127).'<br/><br/>
							</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">&nbsp;<input type="checkbox" name="trans" value="1">'.$GLOBALS['traduct']->get(128).'
						<input type="checkbox" name="win" value="1">CRLF</td>
					</tr>
					<tr>
						<td align="center" style="padding: 3px;" colspan="2"><input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'"></td>
					</tr>
				</table>
				<input type="hidden" name="dbsel" value="'.$GLOBALS['dbsel'].'">';
		if(isset($GLOBALS['table'])) echo '	<input type="hidden" name="table" value="'.$GLOBALS['table'].'">';
		if(isset($GLOBALS['queryExport'])) echo '	<input type="hidden" name="queryExport" value="'.SQLiteStripSlashes($GLOBALS['queryExport']).'">';
		if(isset($GLOBALS['view'])) echo '	<input type="hidden" name="view" value="'.$GLOBALS['view'].'">';
		if(isset($GLOBALS['function'])) echo '	<input type="hidden" name="function" value="'.$GLOBALS['function'].'">';
		echo '	<input type="hidden" name="action" value="'.$GLOBALS['action'].'">
				<input type="hidden" name="export_action" value="go">
				</form>
				</center>';
	}

	/**
	* return formated header
	*
	* @access public
	*/
	function exportHeader(){
		$out =  '# SQLiteManager Dump#%BREAK%#';
		$out .= '# Version: '.SQLiteManagerVersion.'#%BREAK%#';
		$out .= '# http://www.sqlitemanager.org/#%BREAK%#';
		$out .= '# #%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(129).': '.$_SERVER['HTTP_HOST'].'#%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(130).': '. date('l dS of F Y h:i a').'#%BREAK%#';
		$out .= '# SQLite Version: '.@$this->connId->connId->sqlite_version().'#%BREAK%#';
		$out .= '# PHP Version: '.@phpversion().'#%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(131).': '.basename($this->connId->baseName).'#%BREAK%#';
		$out .= '# --------------------------------------------------------#%BREAK%#';
		$this->exportContent .= $out;
		return $out;
	}

	/**
	* send content with correct header
	*
	* @access public
	*/
	function send(){
		if(!isset($_POST['trans']) || empty($_POST['trans'])){
			$tabOut = explode('#%BREAK%#', $this->exportContent);
			echo '<table class="export"><tr><td style="white-space: nowrap">';
			foreach($tabOut as $ligneOut) echo htmlentities($ligneOut, ENT_NOQUOTES, $GLOBALS['charset']).'<br>';
			echo '</td></tr></table>';
		} else {
			// envoi du fichier
			ob_end_clean();
			if(isset($_POST['table']) && !empty($_POST['table'])) $filename = $_POST['table'];
			else $filename = basename($this->connId->baseName);
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename='.$filename.'.sql');
			if(!isset($_POST['win']) || empty($_POST['win']))
				echo str_replace('#%BREAK%#', "\n", $this->exportContent);
			else
				echo str_replace('#%BREAK%#', "\r\n", $this->exportContent);
		}
	}

	/**
	* return table properties
	*
	* @access public
	* @param string $table table name
	*/
	function tableProperties($table){
		$this->getTableProperties($table);
		$out =  '#%BREAK%###%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(132).': '.$table.'#%BREAK%#';
		$out .= '##%BREAK%#';
		$query = 'SELECT sql, type FROM sqlite_master WHERE sql IS NOT NULL AND tbl_name='.quotes($table).';';
		$this->connId->getResId($query);
		if(isset($_POST['drop']) && !empty($_POST['drop'])) $out .= 'DROP TABLE '.brackets($table).';#%BREAK%#';
		$tabResult = $this->connId->getArray();
		foreach($tabResult as $sqlProp) {
			if($sqlProp['type'] != 'trigger') {
				$out .= $sqlProp['sql'].';#%BREAK%#';
			} else {
				$this->triggerProperties[] = $sqlProp['sql'];
			}
		}
		$this->exportContent .= $out;
		return;
	}

	/**
	* return table data
	*
	* @access public
	* @param string $table table name
	*/
	function tableContent($table){
		$out =  '#%BREAK%###%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(133).': '.$table.'#%BREAK%#';
		$out .= '##%BREAK%#';
		// build nullByName array
		if(!is_array($this->tableProperties)) $this->getTableProperties($table);
		if(is_array($this->tableProperties)){
			foreach($this->tableProperties as $trash=>$tabInfoTable){
				$nullByName[$tabInfoTable["name"]] = $tabInfoTable["notnull"];
			}
		} else {
			$nullByName = array();
		}

		if(isset($_REQUEST['queryExport']) && $_REQUEST['queryExport']) $query = urldecode(SQLiteStripSlashes($_REQUEST['queryExport']));
		else $query = 'SELECT * FROM '.brackets($table);
		$this->connId->connId->query($query);
		while($ligne = $this->connId->connId->fetch_array(null, SQLITE_ASSOC)) {
			if(isset($_POST['fullInsert']) && !empty($_POST['fullInsert']) && !isset($columnList)){
				for($i=0 ; $i<$this->connId->connId->num_fields() ; $i++) {
					$currentNameField = $this->connId->connId->field_name(null, $i);
					$columnList[$i] = brackets($currentNameField);
				}
			}
			$columnValue = array();
			$out .= 'INSERT INTO '.brackets($table);
			if(isset($_POST['fullInsert']) && !empty($_POST['fullInsert'])) {
				$out .= ' ('.implode(', ', $columnList).')';
			}
			while(list($key, $val) = each($ligne)) {
				$columnValue[$key] = "'".$this->connId->formatString($val)."'";
				if(isset($nullByName[$key]) && !$nullByName[$key] && ($columnValue[$key] == "''")) $columnValue[$key] = "NULL";
			}
			$out .= " VALUES (".implode(", ", $columnValue).");#%BREAK%#";
		}
		$out = str_replace(";\r\n", "; ", $out);
		$this->exportContent .= $out;
		return;
	}

	/**
	* Iterator for export all database
	*
	* @access private
	*/
	function dbProperties(){
		$tableList = $this->connId->getPropList('Table');
		if(is_array($tableList)) foreach($tableList as $tableName) $this->exportTableSelector($tableName, $_POST['type']);
		if(count($this->triggerProperties)) {
			// Export Trigger after Table Properties
			$this->exportContent .=  '#%BREAK%###%BREAK%#';
			$this->exportContent .= '# '.$GLOBALS['traduct']->getdirect(233).'#%BREAK%#';
			$this->exportContent .= '##%BREAK%#';
			$this->exportContent .= implode(';#%BREAK%#', $this->triggerProperties);
			$this->exportContent .= ';#%BREAK%#';
			$this->exportContent .= '# --------------------------------------------------------#%BREAK%##%BREAK%#';
		}
		$viewList = $this->connId->getPropList('View');
		if(is_array($viewList)) foreach($viewList as $viewName) $this->viewProperties($viewName);
		$functionList = $this->connId->getPropList('Function');
		if(is_array($functionList)) foreach($functionList as $functionName) $this->functionProperties($functionName);
		return;
	}

	/**
	* Selector for export table
	*
	* @access private
	* @param string $table table name
	* @param int $type type for controle the output
	*/
	function exportTableSelector($table, $type){
		switch($type){
			case 1:
				$this->tableProperties($table);
				break;
			case 2:
				$this->tableProperties($table);
				$this->tableContent($table);
				break;
			case 3:
				$this->tableContent($table);
				break;
		}
		$this->exportContent .= '# --------------------------------------------------------#%BREAK%##%BREAK%#';
		return;
	}

	/**
	* return View properties
	*
	* @access public
	* @param string $table table name
	*/
	function viewProperties($view){
		$out =  '#%BREAK%###%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(134).': '.$view.'#%BREAK%#';
		$out .= '##%BREAK%#';
		$query = "SELECT sql FROM sqlite_master WHERE type='view' AND tbl_name=".quotes($view)." ORDER BY rootpage";
		$this->connId->getResId($query);
		if(isset($_POST['drop']) && $_POST['drop']) $out .= 'DROP VIEW '.brackets($table).';#%BREAK%#';
		foreach($this->connId->getArray() as $sqlProp) {
			$out .= $sqlProp['sql'].';#%BREAK%#';
		}
		$this->exportContent .= $out;
		return;
	}

	/**
	* return function properties
	*
	* @access public
	* @param string $table table name
	*/
	function functionProperties($function){
		$out =  '#%BREAK%###%BREAK%#';
		$out .= '# '.$GLOBALS['traduct']->getdirect(135).': '.$function.'#%BREAK%#';
		$out .= '##%BREAK%#';
		if(!is_array($this->UDFprop)) $this->UDFprop = $this->connId->getUDF();
		foreach($this->UDFprop as $UDF) {
			if($UDF['funct_name'] == $function){
				$propFunct = $UDF;
				break;
			}
		}
		if(is_array($propFunct)){
			$out .= '/*#%BREAK%#';
			$out .= $propFunct['funct_code'];
			if($propFunct['funct_type']==2) $out .= $propFunct['funct_final_code'].'#%BREAK%#';
			$out .= '#%BREAK%#*/#%BREAK%#';
		}
		$this->exportContent .= $out;
		return;
	}

	function getTableProperties($table){
		include_once INCLUDE_LIB.'SQLiteTableProperties.class.php';
		$O_TableProperties = new SQLiteTableProperties($this->connId, $table);
		$this->tableProperties = $O_TableProperties->getTableProperties();
		return $this->tableProperties;
	}

}
?>
