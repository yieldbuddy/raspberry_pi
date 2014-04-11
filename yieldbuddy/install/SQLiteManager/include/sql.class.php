<?php
/**
* Web based SQLite management
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: sql.class.php,v 1.62 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.62 $
*/

class sql {

	/**
	* @access public
	* @var objet
	* Class instance of database connection
	*/
	var $connId;

	/**
	* @access private
	* @var string
	* current query
	*/
	var $query;

	/**
	* @access private
	* @var bool
	* if true, all query are journalised into file
	*/
	var $journalised;

	/**
	* @access private
	* @var string
	* filename where query are store when journalised
	*/
	var $journalFile;

	/**
	* @access public
	* @var bool
	* true if query return data
	*/
	var $withReturn;

	/**
	* @access public
	* @var bool
	* true if the current query content are multiple
	*/
	var $multipleQuery;

	/**
	* Last query Execution Time (msec)
	*
	* @access public
	* @var integer
	*/
	var $queryTime;

	/**
	* @access private
	* @var integer
	* Number lines has been changes by the current query
	*/
	var $changesLine;

	/**
	* @access private
	* @var integer
	* Number of query into the curent multiple query
	*/
	var $nbQuery;

	/**
	* @access private
	* @var bool
	* true if has an error in the current query
	*/
	var $error;

	/**
	* @access private
	* @var integer
	* Error line number for multiple query
	*/
	var $lineError;

	/**
	* @access private
	* @var string
	* SQLite error message
	*/
	var $errorMessage;

	/**
	* @access private
	* @var string
	* SQLite query executed when error
	*/
	var $errorQuery;

	/**
	* Class constructor
	*
	* @access public
	* @param object $handle instance of connection class
	* @param string $query
	* @param string $journal filename of the journalise file
	*/
	function __construct(&$handle, $query, $journal=''){
		$this->connId = $handle;
		$this->query = trim($query);
		if(preg_match('#^(select|explain|pragma)[[:space:]]#i', $this->query)) $this->withReturn = true;
		else $this->withReturn = false;
		if($journal != ''){
			$this->journalised = true;
			$this->journalFile = $journal;
		} else $this->journalised = false;
		return;
	}

	/**
	* Verify and exec query
	*
	* @access public
	*/
	function verify($autocommit=true){
		if(is_resource($this->connId->connId) || is_object($this->connId->connId)){
			if(!empty($GLOBALS['attachDbList'])){
				// attachment of all database
				foreach($GLOBALS['attachDbList'] as $attachDbId) {
					$attachQuery = 'ATTACH DATABASE '.quotes($GLOBALS['attachInfo'][$attachDbId]['location']).' AS '.quotes($GLOBALS['attachInfo'][$attachDbId]['name']).';';
					$this->execQuery($attachQuery);
				}
			}
			if($this->query != ''){
				$parsing = new ParsingQuery($this->query, ((isset($_POST['sqltype']))? $_POST['sqltype'] : 1 ));
				$tabQuery = $parsing -> convertQuery();
				if(!is_array($tabQuery)){
					$this->multipleQuery = false;
					$this->query = $tabQuery;
					if(!$result = $this->execQuery($tabQuery)) {
						$this->addChanges();
					}
					return $result;
				} else {
					$time=0;
					$this->multipleQuery = true;
					$this->connId->connId->query('BEGIN TRANSACTION;');
					$error = false;
					$lineNum = 1;
					$this->changesLine = $queryNum = 0;
					foreach($tabQuery as $query){
						if(!empty($query) && substr(trim($query), 0, 1)!='#'){
							if($this->_checkBeginQuery($query)){
								$queryNum++;
								if(isset($commitafter) && $commitafter){
									$this->connId->connId->query('COMMIT TRANSACTION;');
									$this->connId->connId->query('BEGIN TRANSACTION;');
									$commitafter=false;
								}
							}
							if($this->_checkBeginQuery($query, 'CREATE|DROP') && !preg_match('#^create[[:space:]]database#i', $query)) {
								if ($autocommit) $commitafter = true;
							}
							if($this->execQuery($query)){
								$error = true;
								$this->lineError[] = $lineNum;
							}
							$time += $this->queryTime;
							$this->addChanges();
							$lineNum++;
						}
						if($error) break;
					}
					if($error) {
						$this->connId->connId->query('ROLLBACK TRANSACTION;');
					} else {
						$this->connId->connId->query('COMMIT TRANSACTION;');
					}
					$this->error = $error;
					$this->withReturn = false;
					$this->nbQuery = $queryNum;
				}
			} else {
				$this->error = true;
				$this->errorMessage = $GLOBALS['traduct']->get(64);
			}
		} else {
			$this->error = true;
			$this->errorMessage = $GLOBALS['traduct']->get(65);
		}
		$this->queryTime = (isset($time)?$time:0);
		return $this->error;
	}

	function addChanges() {
		$tempChanges = $this->connId->connId->changes();
		$this->changesLine += $tempChanges;
		return $tempChanges;
	}

	/**
	* Exec, manage error and journalised
	*
	* @access public
	* @param string $string query
	*/
	function execQuery($query){
		$queryExec = $this->cleanup($query);
		$this->errorQuery = '';
		$this->queryLog($queryExec);
		if(!preg_match('#^create[[:space:]]database#i', $queryExec)){
			$GLOBALS['phpSQLiteError'] = '';
			set_error_handler('phpSQLiteErrorHandling');
			if($this->connId->getResId($queryExec)){
				$this->error = false;
			} else {
				$this->error = true;
				$this->errorQuery = $queryExec;
				$this->errorMessage = '<table style="color: red;"><tr><td>'.$GLOBALS['traduct']->get(9).' :</td><td>'.$this->connId->connId->getError().'</td></tr>';
				if($GLOBALS['phpSQLiteError'] != '') $this->errorMessage .= '<tr><td>&nbsp;</td><td>'.$GLOBALS['phpSQLiteError'].'</td></tr>';
				if(strstr($GLOBALS['phpSQLiteError'],'syntax error') && $this->multipleQuery)
					$this->errorMessage .= '<tr><td valign="top"><pre class="error_query">Query :</pre></td><td><pre class="error_query">'.htmlentities($this->errorQuery, ENT_NOQUOTES, $GLOBALS['charset']).'</pre></td></tr>';
				$this->errorMessage .= '</table>';
			}
			$this->queryTime = $this->connId->queryTime;
			restore_error_handler();
		} else {
			// emulating 'CREATE DATABASE'
			preg_match('#CREATE[[:space:]]DATABASE(.*)#i', $queryExec, $result);
			$newDatabase = $result[1];
			if(strrpos($newDatabase, ';')) $newDatabase = substr($newDatabase, 0, strrpos($newDatabase, ';'));
			if(preg_match('#[[:space:]]as[[:space:]]#i', $newDatabase)){
				preg_match('#(.*)[[:space:]]AS(.*)#i', $newDatabase, $result);
				$newDatabaseName = trim($result[1]);
				$newDatabaseFilename = trim($result[2]);
			} else {
				$newDatabaseName = $newDatabaseFilename = trim($newDatabase);
			}
			unset($GLOBALS['workDb']);
			include_once INCLUDE_LIB.'SQLiteDbConnect.class.php';
			$tempdir = dirname($newDatabaseFilename);
			if($tempdir == '.') $newDatabaseFilename = DEFAULT_DB_PATH . $newDatabaseFilename;
			$GLOBALS['workDb'] = new SQLiteDbConnect($newDatabaseFilename);
			$GLOBALS['workDb']->includeUDF();
			$this->connId = $GLOBALS['workDb'];

			$query = 'INSERT INTO database (name, location) VALUES ('.quotes($newDatabaseName).', '.quotes(DEFAULT_DB_PATH.$newDatabaseFilename).')';
			if(!$GLOBALS['db']->query($query)) {
				$error = true;
				$this->errorQuery = $query;
				$message .= '<li><span style="color: red; font-size: 11px">'.$GLOBALS['traduct']->get(100).'</span></li>';
			} else {
				if(DEBUG) $GLOBALS['dbsel'] = $GLOBALS['db']->last_insert_id();
				else $GLOBALS['dbsel'] = @$GLOBALS['db']->last_insert_id();
				echo "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS['dbsel']."';</script>";
			}
		}
		return $this->error;
	}


	/**
	* Cleanup POST query
	* and convert MySQL type into SQLite type
	*
	* @access public
	* @param array $data data table
	* @param string $width width of the end table (px or %)
	*/
	function cleanup($query){
		$query = SQLiteStripSlashes($query);
		if(!isset($_POST['sqltype'])) $_POST['sqltype']=1;
		if($_POST['sqltype']==2){
			$query = str_replace("\'", "''", $query);
			$query = str_replace("\\\"", "\"\"", $query);
		}
		return $query;
	}

	/**
	* Manual query Form
	*
	* @access public
	* @param string $query
	*/
	function getForm($query){
		//Mozilla textarea bigger than IE one
		$mozIE = (!strstr($_SERVER["HTTP_USER_AGENT"],'IE'));
	  echo '<!-- sql.class.php : getForm() -->'."\n";
		echo '<div align="center">';
		echo '	<form name="sql" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$GLOBALS['table'].'" method="POST" ENCTYPE="multipart/form-data" target="main">
				<table class="Insert" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td class="Browse" colspan="2">'.$GLOBALS['traduct']->get(66).'</b></td>
					</tr>
				</thead>
				<tr>
					<td class="DisplayQuery">
						<div>
        					<textarea wrap="hard" name="DisplayQuery" cols="80" rows="'.(TEXAREA_NB_ROWS-$mozIE).'" onfocus="document.sql.DisplayQuery.select();">'.$this->cleanup($query).'</textarea>
        				</div>
        				<div align="left">'.$GLOBALS['traduct']->get(67).' :
							<input type="file" size="35" class="file" name="sqlFile">
							<input type="hidden" name="action" value="sql">
						</div>
					</td>';
		if(!empty($GLOBALS['table'])) {
			echo '<td align="center" style="padding-top: 1px;" valign="top">'.$this->GetColumnSelect().'</td>';
		}
		echo '	</tr>
				</table>'."\n";
		echo '	<div style="padding:5px;" align="center">'.$GLOBALS['traduct']->get(68).'
					<input type="radio" name="sqltype" value=1 '.(((!isset($_POST['sqltype'])) || ($_POST['sqltype']==1))? 'checked="checked"' : '' ).'> - MySQL
    				<input type="radio" name="sqltype" value="2"'.(((isset($_POST['sqltype'])) && ($_POST['sqltype']==2))? ' checked="checked"' : '' ).'><br/>
					<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'">
				</div>'."\n";
		echo '	</form>'."\n".'</div>';
	if($this->connId->connId->getVersion()!=3) {
	    echo '<table class="Tip"><tr><th class="TipTitle">';
	    echo $GLOBALS['traduct']->get(224);
	    echo '</th></tr><tr><td class="TipBody">';
	    echo "SELECT php('sprinf','%02d',MyColumn) FROM TABLE;</br>"."\n";
	    echo '</td></tr></table>';
	}
    echo '</body>'."\n";
    echo '</html>';
	}

	/**
	* Verify if the result can be modify or deleted
	* if true, return the table name else return false
	*
	* @access public
	* @param string $query
	*/
	function checkAccessResult($query){
		if(preg_match('#EXPLAIN|JOIN|GROUP[[:space:]]#i', $query))
		    return false;

		$match = 'WHERE|ORDER|LIMIT';
		if(preg_match("#$match#i", $query))
		    preg_match('#FROM(.*)('.$match.')#i', $query, $result);
		else
		    preg_match('#FROM(.*)#i', $query, $result);

		if(isset($result[1])) {
			$listTable = trim($result[1]);
			$posEnd = strrpos($listTable, ';');
			if($posEnd)
			    $listTable = substr($listTable, 0, $posEnd);
		} else $listTable = '';
		$GLOBALS['TableListImpact'] = $listTable;
		if(strpos($listTable, ','))
		    return false;
	    $tableNAme = unquote(trim($listTable));

		if($res = $this->connId->getResId('SELECT type FROM sqlite_master WHERE name LIKE '.quotes($tableNAme)));
		if(@$this->connId->connId->fetch_single() != 'table')
			return false;
		else
		    return $tableNAme;
	}

	/**
	* Log string into journal file
	*
	* @access public
	* @param string $string
	*/
	function queryLog($string){
		if($this->journalised){
			$fp = fopen($this->journalFile, 'a+');
			fwrite($fp, $string."\n");
			fclose($fp);
		}
	}

	/**
	* Verify if the string param is a start of string
	* and if param motif is set, check if the start query content this
	*
	* @access public
	* @param string $req query
	* @param string $motif
	*/
	function _checkBeginQuery($req, $motif=NULL){
		if(preg_match('/^\s*(select|insert|update|delete|create|drop|replace|pragma)\s/i', $req)) {
			if(strlen($motif) != '') {
				return preg_match('/^\s*('.$motif.')\s/i', $req) == 1;
			}
		return true;
		}
		return false;
	}

	/**
	* Display result when the query is multiple
	*
	* @access public
	*/
	function DisplayMultipleResult(){
		echo '
			<table width="60%" align="center">
				<tr><td bgcolor="lightblue" >'.$this->nbQuery.' '.$GLOBALS['traduct']->get(70).' '.$this->queryTime.' '.$GLOBALS['traduct']->get(214).'</td></tr>
				<tr><td bgcolor="#CCCCCC"><span class="sqlsyntaxe">&nbsp;'.$this->changesLine.' '.$GLOBALS['traduct']->get(71).'</span></td></tr>
			</table>';
		return;
	}

	/**
	* Get column of table select for SQL
	*
	* @access private
	*/
	function GetColumnSelect(){
		$query = 'PRAGMA table_info('.brackets($GLOBALS['table']).');';
		$tableInfoTable = array();
		$out = '';
		if($this->connId->getResId($query)){
			$tableInfoTable = $this->connId->getArray();
		}
		if(!empty($tableInfoTable)){
			$optionList = array();
			foreach($tableInfoTable as $columnInfo){
				$optionList[] = '<option value="'.brackets($GLOBALS['table']).'.'.brackets($columnInfo['name']).'">'.brackets($GLOBALS['table']).'.'.brackets($columnInfo['name']).'</option>';
			}
			$out = '<div><select name="columnTable" size="'.TEXAREA_NB_ROWS.'" multiple="multiple">'."\n".implode("\n", $optionList).'</select></div>'."\n";
			$out .= '<div><input name="insertButton" class="button" type="button" value="'.$GLOBALS['traduct']->get(75).'" onClick="insertColumn();"></div>';
		}
		return $out;
	}
}

?>
