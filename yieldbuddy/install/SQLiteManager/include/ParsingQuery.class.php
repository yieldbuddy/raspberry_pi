<?php
/**
* Web based SQLite management
*
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: ParsingQuery.class.php,v 1.41 2006/04/16 12:23:37 freddy78 Exp $ $Revision: 1.41 $
*/

class ParsingQuery {
	/**
	* Current Query
	*
	* @var string
	* @access private
	*/
	var $query;

	/**
	* query formated type
	* 1 = SQLite , 2 = MySQL
	*
	* @access private
	* @var bool
	*/
	var $type;

	/**
	* All user string in an array
	*
	* @access private
	* @var array
	*/
	var $tabString;

	/**
	* Query with format string included
	*
	* @access private
	* @var string
	*/
	var $formattedQuery;

	/**
	* array of all query component
	*
	* @access private
	* @var array
	*/
	var $explodedQuery;

	/**
	* Constructor of the class
	*
	* @param string $query Query string command
	* @param int $type type export selector
	*/
	function __construct($query, $type){
		$this->query = $query;
		$this->type = $type;
	}

	/**
	* Convert MySQL query to SQLite query
	*
	* @access public
	*/
	function tabletoSQLite($query){
		$query = preg_replace('#auto_increment=(.*)[[:space:]];#i', ';', $query);
		$query = preg_replace('#[[:space:]]UNSIGNED[[:space:]]#i', ' ', $query);
		$query = preg_replace('#TYPE=(.*)[[:space:]];#i', ';', $query);
		$query = str_replace("\n", ' ', $query);
		$startPar = strpos($query, '(');
		$endPar = strrpos($query, ')');
		preg_match('/TABLE[[:space:]](.*)[[:space:]]\(/i', substr($query, 0, ($startPar+1)), $result);
		$tableName = preg_replace('#\[|\]#','',$result[1]);
		$tableElement = explode(',', substr($query, $startPar+1, ($endPar - $startPar)-1));
		$numElement = 0;
		$primaryExist = false;
		while(list($key, $value)=each($tableElement)){
			$numElement++;
			if(preg_match('#not[[:space:]]null#i', $value)) {
				$matches = 'not[[:space:]]null';
				$defineElement[$numElement]['null'] = false;
			}
			if($matches) $value = preg_replace("#$matches#i", '', $value);
			if(!preg_match('#[[:space:]]key[[:space:]]#i', $value)){
				$value = $this->bracketsReplaceSpaces($value,'@¤&');
				$tabValue = explode(' ', trim($value));
				$tabValue[0] = str_replace('@¤&',' ',$tabValue[0]);
				$defineElement[$numElement]['name'] = trim($tabValue[0]);
				if(preg_match('#auto_increment#i', trim($value))){
					$defineElement[$numElement]['type'] = 'INTEGER';
					$defineElement[$numElement]['sup'] = 'PRIMARY KEY';
					$primaryExist = true;
					continue;
				}
				$defineElement[$numElement]['type'] = str_replace(' int(', ' INTEGER(', $tabValue[1]);
				for($i = 2 ; $i<count($tabValue) ; $i++) {
					if (preg_match('#zerofill[ ]?#i',$tabValue[$i])) {
						$tabValue[$i] = preg_replace('#zerofill[ ]?#i','',$tabValue[$i]);
						//$defineElement[$numElement]['type'] .= ' zerofill';
					}
					if(!isset($defineElement[$numElement]['sup'])) $defineElement[$numElement]['sup']='';
					$defineElement[$numElement]['sup'] .= $tabValue[$i].' ';
				}

				//if(eregi('set|enum', $tabValue[1])){
				if(preg_match('#set|enum#i', $tabValue[1])){
					//if(!ereg('\)', $tabValue[1])){
					if(!preg_match('#\)#i', $tabValue[1])){
						for($i=($key+1) ; $i<= (count($tableElement)+1) ; $i++) {
							$tabValue[1] .= ','.$tableElement[$i];
							unset($tableElement[$i]);
							//if(ereg('\)', $tabValue[1])) break;
							if(preg_match('#\)#', $tabValue[1])) break;
						}
					}
					preg_match('/\((.*)\)/i', $tabValue[1], $enumRes);
					$tabPropEnum = explode(',', $enumRes[1]);
					$maxlen = 0;
					foreach($tabPropEnum as $propEnum) if(strlen($propEnum)>$maxlen) $maxlen = strlen($propEnum);
					$defineElement[$numElement]['type'] = 'varchar('.($maxlen-2).')';

					preg_match('/\)(.*)/', $tabValue[1], $supRes);
					$defineElement[$numElement]['type'] .= $supRes[1];
				}
			} else {
				//if(!ereg('\)', $value)){
				if(!preg_match('#\)#', $value)){
					for($i=($key+1) ; $i<= (count($tableElement)+1) ; $i++) {
						$value .= ','.$tableElement[$i];
						unset($tableElement[$i]);
						if(preg_match('#\)#', $value)) break;
					}
				}
				if(isset($tabIndex)) $numIndex = count($tabIndex)+1;
				else $numIndex = 1;
				preg_match('#key(.*)\(#i', $value, $indexSearch);
				$indexName = preg_replace('#\[|\]#','',trim($indexSearch[1]));
				if(preg_match('#PRIMARY#i', $value) && !$primaryExist) {
					$listChamp = $this->recupFields($value);
					if(is_array($listChamp)) {
						$tabIndex[$numIndex]['type'] = 'UNIQUE';
						$tabIndex[$numIndex]['champ'] = $listChamp;
						$tabIndex[$numIndex]['name'] = $indexName;
					} else {
						$listElem = $defineElement;
						while(list($key, $value) = each($listElem)){
							if($value['name']==$listChamp) $defineElement[$key]['sup'] .=' PRIMARY KEY';
						}
					}
				} elseif(!preg_match('#PRIMARY#i', $value)) {
					if(preg_match('#UNIQUE#i', $value)) $tabIndex[$numIndex]['type'] = 'UNIQUE';
					$listChamp = $this->recupFields($value);
					$tabIndex[$numIndex]['champ'] = $listChamp;
					$tabIndex[$numIndex]['name'] = $indexName;
				}
			}
		}

		$finaleQuery = 'CREATE TABLE '.brackets($tableName).' ('."\n";
		foreach($defineElement as $elem) {
			$column[] = brackets($elem['name']).' '.$elem['type'].((isset($elem['null']) && !$elem['null'])? ' NOT NULL ' : ' ' ).$elem['sup'];
		}
		$finaleQuery .= "\t".implode(",\n\t", $column)."\n);";
		$tabQ[] = $finaleQuery;
		if(isset($tabIndex) && is_array($tabIndex)){
			foreach($tabIndex as $ind){
				$query = 'CREATE';
				if($ind['type']) $query .= ' '.$ind['type'];
				if (is_array($ind['champ'])) {
				    foreach ($ind['champ'] as $key=>$champ) $ind['champ'][$key] = brackets($champ);
					$columns = implode(',', $ind['champ']);
				} else
				    $columns = brackets($ind['champ']);
				$query .= ' INDEX '. str_replace(' ','_',$tableName.'_'.$ind['name']).' ON '.brackets($tableName).' ('.$columns.');';
				$tabQ[] = $query;
			}
		}
		return $tabQ;
	}

	/**
	*  Convert MySQL brackets in query when spaces in objects
	*
	* @access public
	*/
	function convertBrackets($query){
		$query = str_replace('\`','@¤&',$query);

	    //Force brackets conversion even if no spaces in object name (for bracket tests)
	    $force = false;

		$d = $p = 0; $in = false;
		while ($p = strpos("-$query",'`',$p)) {
			$in = (!$in);

			if (!$in) {
				$object = substr($query,$d,$p-$d-1);
				if (strpos("-$object",' ') || $force)
					$query = substr($query,0,$d-1)."[$object]".substr($query,$p);
			    else {
    				$query = substr($query,0,$d-1).$object.substr($query,$p);
	    			$p-=2;
		    	}
			}
			$d = $p;
			$p++;
		}

		return str_replace('@¤&','`',$query);
	}

	/**
	*  Replace spaces in brackets, usefull before split(' ',$query)
	*
	* @access public
	*/
	function bracketsReplaceSpaces($query,$replaceBy) {
		if (strstr($query,'[')) {
		    //regex : objects between brackets
			if (preg_match_all('#\[([^\]]*)?\]#',$query,$matches,PREG_SET_ORDER))
				foreach ($matches as $matche)
					if (strstr($matche[1],' ')) {
						$object = str_replace(' ',$replaceBy,$matche[0]);
						$query = str_replace($matche[0],$object,$query);
					}
		}
		return $query;
	}

	/**
	*  Split, Clean and Convert query!!
	*
	* @access public
	*/
	function convertQuery(){
		$localQuery = $this->query;
		$localQuery = str_replace("\r\n", "\n", $localQuery);
		$localQuery = preg_replace("#/;?\n/#", ";\n", $localQuery);
		if($this->type == 2){
			$localQuery = str_replace("\\'", "''", $localQuery);
			$localQuery = preg_replace("/^use.*\n/i", '', $localQuery);
			$localQuery = $this->convertBrackets($localQuery);
		}

		$localQuery = $this->purgeComment($localQuery);
		if(strpos($localQuery, ";\n")){
			$tabQuery = explode(";\n", $localQuery);
			$tabOut = array();
			$startTrigger = false;
			$tempQueryString = '';
			while(list($key, $req) = each($tabQuery)) {
				if(empty($req)) continue;
				if(!$startTrigger && preg_match('#^'.implode('|', $GLOBALS['elementStartQuery']).'#i', $req)) {
					// starting new query
					if(!empty($tempQueryString)) {
						if(substr($tempQueryString, -1)!=';') $tempQueryString .= ';';
							$tabOut[] = $tempQueryString;
							$tempQueryString = '';
					}
				}
				if($this->type == 1){
					if(preg_match('#begin[[:space:]]transaction#i', $req)) continue;
					if(preg_match('#commit|transaction#i', $req)) continue;
					if(preg_match('#create[[:space:]]trigger#i', $req)) {
						$startTrigger = true;
						$queryTrigger = '';
					}
					if(!$startTrigger) {
						$tempQueryString .= $req;
					} else {
						$queryTrigger .= ' '.$req;
						if(substr($req, -1)!=';') $queryTrigger .= ';';
						if(preg_match('#end$#i', trim($req))) {
							$startTrigger = false;
							$tabOut[] = str_replace("\n", ' ', $queryTrigger);
						}
					}
				} elseif($this->type == 2) {
					$req = str_replace("\\r\\n", "\n", $req);
					if(preg_match('#^--#', $req)) continue;
					if(preg_match('#[[:space:]]IF EXISTS[[:space:]]#i', $req)) continue;
					if(preg_match('#create[[:space:]]table#i', $req)) {
						$tabTable = $this->tabletoSQLite(str_replace("\n", ' ', $req));
						$tabOut = array_merge($tabOut, $tabTable);
						$tempQueryString = '';
					} else {
						$tempQueryString .= $req;
					}
				}
			}
			if(!empty($tempQueryString)) {
				if(substr($tempQueryString, -1)!=';') $tempQueryString .= ';';
				$tabOut[] = $tempQueryString;
			}
			return $tabOut;
		} else {
			if($this->type==2){
				if(preg_match('#CREATE[[:space:]]TABLE#i', $localQuery)) $localQuery = $this->tabletoSQLite($localQuery);
				$localQuery = str_replace("\\r\\n", "\n", $localQuery);
			}
			return $localQuery;
		}
	}

	/**
	* Retreive champ name from sql list
	*
	* @access private
	* @param string $string is the string into SELECT and FROM
	*/
	function recupFields($string){
		$string = preg_replace('#\[|\]#','',$string);
		preg_match('/\((.*)\)/', $string, $parChamp);
		if(strpos($parChamp[1], ',')) $listChamp = explode(',', $parChamp[1]);
		else $listChamp = trim($parChamp[1]);
		return $listChamp;
	}

	/**
	* Clean query's comment
	*
	* @access public
	* @param string $query query
	*/
	function purgeComment($query){
		$tabQ = explode("\n", $query);
		$commentBlock = false;
		$outQ = array();
		if(is_array($tabQ)){
			foreach($tabQ as $lineQ){
				if(preg_match('#\/\*#', $lineQ)) $commentBlock = true;
				if( !$commentBlock && (substr(trim($lineQ), 0, 1)!= '#') && (substr(trim($lineQ), 0, 2)!= '--') && !empty($lineQ)) {
					$outQ[] = $lineQ;
				}
				if(preg_match('#\*\/#', $lineQ)) $commentBlock = false;
			}
			$query = implode("\n", $outQ);
		}
		return $query;
	}

	/**
	* recup query without limit
	*
	* @access public
	* @param string $query query
	*/
	function noLimit($query){
		if(preg_match('#LIMIT[[:space:]]#i', $query)){
			preg_match('/LIMIT(.*),/i', $query, $limitRes);
			if(isset($limitRes[1])) {
				$startRecLimit = (int)(trim($limitRes[1]));
				$out['page'] = ($startRecLimit / BROWSE_NB_RECORD_PAGE) +1;
				$out['query'] = preg_replace('#LIMIT.*#i', '', $query);
			}
		} else {
			$out['query'] = $query;
			$out['page'] = '';
		}
		return $out;
	}

	/**
	* extract all query properties
	*
	* @access private
	* @ param string $query query
	*/
	function explodeQuery($query=''){
		if($query == '') $query = $this->query;
		$query = $this->formattedQuery = preg_replace("#''#", '%£Q£%', $query);
		$tabQuote = strpos_all($query, "'");
		$inString = false;
		$this->tabString = array();
		$stringNumber = 0;
		if(is_array($tabQuote)){
			while(list($key, $posQuote) = each($tabQuote)){
				if(!$inString){
					$start = $posQuote;
					$stringNumber++;
					$inString = true;
				} else {
					$end = $posQuote;
					$subQuery = substr($query, $start, ($end-$start)+1);
					$this->tabString[$stringNumber] = preg_replace('#%£Q£%#', "''",  $subQuery);
					$this->formattedQuery = str_replace($subQuery, '%£'.$stringNumber.'£%', $this->formattedQuery);
					$inString = false;
				}
			}
		}
		$this->formattedQuery = str_replace("\t", '', $this->formattedQuery);
		$tabExplodedQuery = preg_split('#[[:space:]]+#', $this->formattedQuery);
		$tabOut = array();
		foreach($tabExplodedQuery as $once){
			if(preg_match('#['.preg_quote($GLOBALS['SQLpunct']).']#', $once)){
				$once = preg_replace('/['.preg_quote($GLOBALS['SQLpunct']).']/', ' $0 ', $once);
				$tempTab = explode(" ",$once);
				$tabOut = array_merge($tabOut, $tempTab);
			} else {
				$tabOut[] = $once;
			}
		}
		$this->explodedQuery = $tabOut;
		return;
	}

	/**
	* Colorize SQL
	*
	* @access private
	*/
	function colorWordList(){
		$indent = $braketLevel = 0;
		foreach($this->explodedQuery as $key=>$value){
			if(($value == '') || (preg_match('#%£(.*)£%#', $value))) continue;
			$currentWord = strtoupper(trim($value));
			$tabWord = array_merge($GLOBALS['SQLKeyWordList'], $GLOBALS['SQLoperator']);
			if(preg_match('#['.preg_quote($GLOBALS['SQLpunct']).']#i', $currentWord)){
				$value = $this->explodedQuery[$key] = preg_replace('/['.preg_quote($GLOBALS['SQLpunct']).']/', "<span class=\"syntaxe_punct\">$0</span>", $value);
			}
			if(in_array($currentWord, $tabWord)){
				$this->explodedQuery[$key] = $this->colorizeWord($value, $currentWord, 'syntaxe_keyword');
			} elseif(in_array($currentWord, $GLOBALS['SQLfunction'])){
				$this->explodedQuery[$key] = $this->colorizeWord($value, $currentWord, 'syntaxe_function');
			} elseif(($currentWord != '0') && in_array($currentWord, $GLOBALS['SQLiteType'])){
				$this->explodedQuery[$key] = $this->colorizeWord($value, $currentWord, 'syntaxe_type');
			} elseif(preg_match('#[0-9]+#', trim($value))){
				$this->explodedQuery[$key] = $this->colorizeWord($value, trim($value), 'syntaxe_digit');
			} elseif(preg_match('#[0-9a-z]+#i', trim($value))){
				$this->explodedQuery[$key] = $this->colorizeWord($value, trim($value), 'syntaxe_variable');
			} else {
				$this->explodedQuery[$key] = $this->colorizeWord($value, trim($value), 'syntaxe_variable');
			}
			$braketOk = false;
			$tabBraket = array();
			if(preg_match('#\(#', $currentWord)) {
				$braketStart = true;
				$braketLevel++;
				if($braketOk) {
					$indent++;
					$tabBraket[] = $braketLevel;
					$braketOk = false;
					$this->explodedQuery[$key] = $this->explodedQuery[$key].'<div style="margin-left: '.$indent.'em;">';
				}
			}
			if(preg_match('#\)#', $currentWord)) {
				$braketEnd = true;
				if(is_array($tabBraket) && in_array($braketLevel, $tabBraket)){
					$this->explodedQuery[$key] = '</div>'.$this->explodedQuery[$key];
					$indent--;
				}
				$braketLevel--;
			}
			/*
			if(preg_match('#,#', $currentWord)){
				$this->explodedQuery[$key] = $this->explodedQuery[$key].'<br/>';
			}
			*/
			$DownIndent = $UpIndent = false;
			$outString = "";
			switch ($currentWord) {
				case 'CREATE':
					$DownIndent = true;
					$outString = '<br/>'.$this->explodedQuery[$key];
					$braketOk = true;
					break;
				case 'EXPLAIN':
				case 'DESCRIBE':
				case 'SET':
				case 'DELETE':
				case 'SHOW':
				case 'DROP':
				case 'UPDATE':
				case 'ANALYZE':
				case 'ANALYSE':
				case 'LIMIT':
				case 'SELECT':
				case 'FROM':
				case 'WHERE':
				case 'LEFT':
				case 'RIGHT':
				case 'INNER':
				case 'GROUP':
				case 'ORDER':
				case 'INSERT':
				case 'REPLACE':
				case 'VALUES':
				case 'END':
					$DownIndent = true;
					$outString = '<br/>'.$this->explodedQuery[$key];
					break;
				default:
					break;
			}
			if($DownIndent){
					if($indent){
						$indent--;
						$this->explodedQuery[$key] = '</div>'.$this->explodedQuery[$key];
					}
			}
			if($outString){
				$this->explodedQuery[$key] = $outString;
			}
			if($UpIndent){
					$indent++;
			}
		}
		if($indent){
			for($i=$indent; $i>0 ; $i--) $this->explodedQuery[] = '</div>';
		}
	}

	/**
	* Colorize aword
	* replace word into a string with the word colorized
	*
	* @access public
	* @param string $string The start string
	* @param string $word The word who must be colorized
	* @param string $className The style classname for colorize word
	* @return string
	*/
	function colorizeWord($string, $word, $className){
		$newWord = '<span class="'.$className.'">'.$word.'</span>';
		return preg_replace('#'.preg_quote($word).'#i', $newWord, $string);
	}

	/**
	* Displaying Highlighted Query
	*
	* @access private
	*/
	function highlightQuery(){
		$query = implode(' ', $this->explodedQuery);
		$query = preg_replace('/%£Q£%/i', '<span class="syntaxe_string">'."''".'</span>', $query);
		foreach($this->tabString as $key=>$value)
		    $query = preg_replace('#%£'.$key.'£%#i', '<span class="syntaxe_string">'.htmlentities($value, ENT_NOQUOTES, $GLOBALS['charset']).'</span>', $query);
		if(strpos($query, '<br/>') === 0)
		    $query = substr($query, 5, strlen($query)-4);
		return $query;
	}

	/**
	 *
	 *
	 */
	function explodeSelect($query){
		$tabClause = array('SELECT', 'FROM', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY', 'LIMIT');
		$tabElement = array();
		$i = 0;
		foreach($tabClause as $selectElem) {
			if(preg_match("#$selectElem#i", $query)){
				$tabElement[$i++] = $selectElem;
			}
		}
		$tabResult = preg_split('/'.implode('|', array_values($tabElement)).'/', $query);
		$out = array();
		foreach($tabElement as $key=>$clause) if($key>=0) $out[$clause] = trim($tabResult[$key+1]);
		return $out;
	}
}
?>
