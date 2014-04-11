<?php
// +------------------------------------------------------------------------+
// | SQLiteToGrid                                                           |
// +------------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Frédéric HENNINOT                              |
// | Email         fhenninot@freesurf.fr                                    |
// | Licence       This code is released under GPL                          |
// +------------------------------------------------------------------------+

/**
 * PHP4 Grid Presentation Data with more feature.
 * @package SQLiteManager
 * @author Frédéric HENNINOT <fhenninot@freesurf.fr>
 * @version $Id: SQLiteToGrid.class.php,v 1.41 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.41 $
 */

class SQLiteToGrid {

	/**
	* Resource connection
	*
	* @access private
	* @var resource
	*/
	var $SQLiteConnId;

	/**
	* Original SQL Query
	*
    * @access private
	* @var string
	*/
	var $query;

	/**
	* Count SQL Query
	*
    * @access private
	* @var string
	*/
	var $queryCount;

	/**
	* SQL expression extract : between 'SELECT' and 'FROM'
	*
    * @access private
	* @var string
	*/
	var $listChamp;

	/**
	* Default sort order
	*
    * @access private
	* @var string
	*/
	var $queryOrderDefault;

	/**
	* Nombre d'enregistrement
	*
	* @access private
	* @var int
	*/
	var $nbRecordQuery;

	/**
	* SQL expression sort var
	*
    * @access private
	* @var string
	*/
	var $order;

	/**
	* SQL Init Sort column
	* @access private
	* @var int
	*/
	var $orderInit;

	/**
	* Number of record per page
	*
    * @access private
	* @var string
	*/
	var $recordPerPage;

	/**
	* Veritable requête executée
	*
	* @access private
	* @var string
	*/
	var $realQuery;

	/**
    * @access private
	* @var array
	* Column title label
	*/
	var $title;

	/**
	* Identification table, to view many table in one page
	*
	* @access private
	* @var string
	*/
	var $tabId;

	/**
	* boolean array for Enable/Disable sort column
	*
    * @access private
	* @var array
	*/
	var $sort;

	/**
 	* Column title style.
	* true : button for sort column
	* false : hyperlink for sort column
	*
	* @access private
	* @var bool
	*/
	var $buttonStyle=true;

	/**
	* Add var to hyperlink.
	* Allow full domain
	* example :
	* index.php?action=module&module=user
	* ?action=module
	*
    * @access private
	* @var string
	*/
	var $getVar;

	/**
	* data to print
	*
	* @access private
	* @var array
	*/
	var $data;

	/**
	* tableau des styles 'CSS'.
	* if empty, automatic value for TD, TABLE and BUTTON
	*
    * @access private
	* @var array
	*/
	var $style;

	/**
	* Alignement of the column result, without title
	*
    * @access private
	* @var array
	*/
	var $align;

	/**
	* Formatage d'affichage de la colonne. Le resultat des autres colonne peuvent être utilisé dans une même colonne mais sur la même ligne
	* a l'aide du masque #%num_colonne%#
	*
	* @access private
	* @var array
	*/
	var $format;
	/**
	* List the column to be hide
	*
    * @access private
	* @var array
	*/
	var $hide;

	/**
	* List of the calculated column
	*
	* @access private
	* @var array
	*/
	var $calcColumn;

	/**
	* Number of column in the result table and Title table
	*
	* @access private
	* @var int
	*/
	var $nbColonne;

	/**
	* table result width. also in px or %
	*
    * @access private
	* @var int
	*/
	var $width;

	/**
	* view the navigation bar.
	* Default : false
	*
    * @access private
	* @var bool
	*/
	var $navigate=false;

	/**
	* theorical number of page
	*
    * @access private
	* @var int
	*/
	var $nbPage;

	/**
	* Current page to view
	*
    * @access private
	* @var int
	*/
	var $pageStart;

	/**
	* Information on navigate page
	* @access public
	* @var array
	*/
	var $infoNav;
	/**
	* older column to sort.
	* to know the sens
	*
    * @access private
	* @var int
	*/
	var $oldOrder;

	/**
	* Determine the sort order
	* 'ASC' or 'DESC'
	*
    * @access private
	* @var string
	*/
	var $orderSens;

	/**
	* Buffer to send to the brother
	*
    * @access private
	* @var string
	*/
	var $out;

	/**
	* Caption for table
	*
	* @access private
	* @var array $tabCaption  array("align"=>, "content"=>)
	*/
	var $tabCaption;

	/**
	* Disable 'OnClick' javascript function on table
	*
	* @var boolean $onClick
	*/
	var $onClick;

	/**
	* Class constructor
	*
    * @access public
	* @param resource $connId resource SQLite Connection
	* @param string $query SQL query to display
	* @param string $tabId table name for display many table with independante navigate
	* @param bool $autoTitle if true the class determineless the column title
	* @param int $nbRecord nb line view per page
	* @param string $width width of the end table (px or %)
	*/
	function __construct(&$connId, $query, $tabId='', $autoTitle=true, $nbRecord=10, $width=500){
		if(is_resource($connId) || is_object($connId)) $this->SQLiteConnId = $connId;
		if($tabId) $this->tabId = $tabId;
		else $this->setTabId();
		$this->_fromSession();
		$posEnd = strrpos(trim($query), ';');
		if($posEnd) $query = substr(trim($query), 0, $posEnd);
		$this->query = $query;
		$this->onClick = true;
		$this->recordPerPage = $nbRecord;
		$this->navigate = true;
		$this->_parseQuery($autoTitle);
		$this->_definePage();
		if($width) $this->width = $width;
		if(empty($this->tabId)) $this->setTabId();
		$data = $this->_getRecord();
		if(is_array($data)){
			foreach($data as $ligne){
				if(empty($this->nbColonne)) {
					$this->nbColonne = count($ligne);
				} elseif(count($ligne)!=$this->nbColonne) {
					$this->_sendError($GLOBALS['traduct']->get(105));
					return;
				}
			}
			$this->data = $data;
		} else {
			$this->_sendError($GLOBALS['traduct']->get(106));
		}
		$this->_toSession();
		return true;
	}

	/**
	* Affectation of an identification table
	*
	* @access public
	* @param string $ident identification string
	*/
	function setTabId($ident=''){
		if(!empty($ident)) {
			$this->tabId = $GLOBALS['GridTabId'][] = $ident;
		} else {
			// Recherche du plus grand indice
			$tabIndex = $GLOBALS['GridTabId'];
			if(!is_array($tabIndex)) {
				$this->tabId = $GLOBALS['GridTabId'][]='tab1';
			} else {
				$max = 0;
				foreach($tabIndex as $value){
					if((substr($value, 0, 3) == 'tab') && (($num = substr($value, 3, strlen($value)-3))>$max)) $max = $num;
				}
				$this->tabId = $GLOBALS['GridTabId'][] = 'tab'.($max+1);
			}
		}
		return;
	}

	/**
	* Set Table title
	*
	* @access public
	* @param array $title tableau des titres du tableau
	*/
	function setTitle($title){
		if(is_array($title)) $this->title = $title;
		if(empty($this->nbColonne)) $this->nbColonne = count($title);
	}

	/**
	* Set Alignement for the data table. no the title
	*
	* @access public
	* @param array $tabAlign String table to set teh column alignement
	*/
	function setAlign($tabAlign){
		if(is_array($tabAlign)){
			if(count($tabAlign)!=$this->nbColonne) {
				$this->_sendError($GLOBALS['traduct']->get(107));
			} else {
				foreach($tabAlign as $align) {
					if( ($align != 'left') && ($align != 'center') && ($align != 'right') ) {
						$this->_sendError($GLOBALS['traduct']->get(108));
						return;
					}
				}
				$this->align = $tabAlign;
			}
		} else {
			$this->_sendError($GLOBALS['traduct']->get(109));
		}
	}

	/**
	* Affecte un format d'affichage pour chaque colonne
	*
	* @access public
	* @param array $tabFormat tableau de chaine par colonne
	*/
	function setFormat($tabFormat){
		if(is_array($tabFormat)){
			$this->format = $tabFormat;
		} else {
			$this->_sendError($GLOBALS['traduct']->get(110));
		}
	}
	/**
	* Set the GET var to add into the links
	*
    * @access public
	* @param string $string example : "?action=module&module=user"
	*/
	function setGetVars($string){
		$this->getVar = $string;
		return;
	}

	/**
	* Allow sortable column. default all column are sortable
	*
	* @access public
	* @param array $tabSort : bool array true->column sortable ; false->column not sortable
	*/
	function setSort($tabSort){
		if(is_array($tabSort)){
			if(count($tabSort)!=$this->nbColonne) {
				$this->_sendError($GLOBALS['traduct']->get(111));
			} else {
				foreach($tabSort as $sort) {
					if( ($sort != 0) && ($sort != 1) ) {
						$this->_sendError($GLOBALS['traduct']->get(112));
						return;
					}
				}
				$this->sort = $tabSort;
			}
		} else {
			$this->_sendError($GLOBALS['traduct']->get(113));
		}
	}

	/**
	* Default sort action is with button style. If false -> sort action is hyperlinks
	*
	* @access public
	* @param bool $button
	*/
	function enableSortStyle($button = true){
		if($button) $this->buttonStyle = true;
		else $this->buttonStyle = false;
		return;
	}

	/**
	* Disable Navigation Barre when is automatically set
	*
	* @access public
	*/
	function disableNavBarre(){
		$this->navigate = false;
	}

	/**
	* Allow result column to be hide
	*
	* @access public
	* @param int $num column number. can it a bool array with true->column is hide ; false->column is show
	*/
	function hideColumn($num){
		if(is_array($num)) $this->hide = $num;
		else $this->hide[$num] = true;
	}

	/**
	* Same as hideColumn() but to show column
	*
	* @access public
	* @param int $num
	* @see hideColumn($num)
	*/
	function showColumn($num){
		if(is_array($num)) $this->hide = $num;
		else $this->hide[$num] = false;
	}

	/**
	* This method is to add calculate column. default the position is at end.
	* The format string, is a template string where you can use all the column result.
	*
	* @access public
	* @param string $title The column title
	* @param string $format The string to be parse to set the value. format is #%ColNum%#, replace by the column value
	* @param string $align Set the data alignement
	* @param int $pos position de la colonne calculée, si 999 alors à la fin par ordre de création
	*/
	function addCalcColumn($title, $format, $align, $pos=999){
		if(!empty($title)){
			if(!empty($format)){
				if(!is_array($this->calcColumn)) $numCalc = 0;
				else $numCalc = count($this->calcColumn-1);
				$this->calcColumn[$numCalc]['title'] 	= $title;
				$this->calcColumn[$numCalc]['format'] 	= $format;
				$this->calcColumn[$numCalc]['align'] 	= $align;
				$this->calcColumn[$numCalc]['position'] = $pos;
			} else {
				$this->_sendError($GLOBALS['traduct']->get(114));
			}
		} else {
			$this->_sendError($GLOBALS['traduct']->get(115));
		}
	}

	/**
	* Build the table result and buffer behind show
	*
	* @access public
	* @return string
	*/
	function build(){
		$out = '';
		$out .= $this->_showHeader();
		$out .= $this->_showTable();
		if($this->navigate) $out .= $this->_showNavigate();
		$out .= $this->_showFooter();
		$this->out = $out;
		return $out;
	}

	/**
	* Show the result
	*
	* @access public
	*/
	function show(){
		echo $this->out;
		return;
	}

	/**
	* Send error message to brother
	*
	* @access public
	* @param string $message Error message
	*/
	function _sendError($message){
		echo '<table width="300" style="border: 2px solid red;">'."\n";
		echo '<tr><td align="center"><span style="font-size: 16px; color: red;"></span><b>'.$GLOBALS['traduct']->get(9).'</b></span></td></tr>'."\n";
		echo '<tr><td align="center"><span style="font-size: 14px, color: blue;"><b>'.$message.'</b></span></td></tr>'."\n";
		echo '</table>';
		return;
	}


	/**
	* Build the header, use Table and Thead for the title row
	*
	* @access private
	* @return string
	*/
	function _showHeader(){
	  $out = '<!-- SQLiteToGrid.class.php : _showHeader() -->'."\n";
		$out .= 	"<div align=\"".$GLOBALS["QueryResultAlign"]."\">\n<table width=".$this->width." cellspacing=0 cellpadding=0 class=\"".$this->tabId."\">\n";

		if(isset($this->tabCaption) && !empty($this->tabCaption)) {
			$out .= "<caption style=\"white-space: nowrap\" align=\"".$this->tabCaption["align"]."\">".$this->tabCaption["content"]."</caption>";
		}
		$out .= "\t<thead>\n\t\t<tr>\n";
		if(empty($this->getVar)) $this->getVar = '?';
		else $this->getVar .= '&amp;';
		if(!is_array($this->sort)) $sortDefault = true;
		if (count($this->title))
		while(list($index, $titleColonne) = each($this->title)) {
			$linkCond = ((!isset($this->sort[$index]) && $sortDefault) || $this->sort[$index]);
			$sort = '';
			if(is_array($this->calcColumn)){
				foreach($this->calcColumn as $calcCol){
					if($calcCol['position'] == $index){
						if($this->buttonStyle) $out .= "\t\t\t<td class=\"".$this->tabId."\"><button class=\"button\" type=\"button\">".$calcCol["title"]."</button></td>\n";
						else $out .= "\t\t\t<td align=\"center\" class=\"".$this->tabId."\">".$calcCol["title"]."</td>\n";
					}
				}
			}
			if((isset($_GET["sort".$this->tabId]) && ($_GET["sort".$this->tabId] == $index)) || (isset($this->orderInit) && ($this->orderInit==$index))){
				if($this->orderSens == "ASC") $infoSort = '&nbsp;<img src="'.IMG_ASC.'" border="0">';
				else $infoSort = '&nbsp;<img src="'.IMG_DESC.'" border="0">';
			} else {
				$infoSort = "";
			}
			if(!is_array($this->hide) || (!isset($this->hide[$index]) || !$this->hide[$index])){
				$align=($infoSort)?'right':'center';
				if($this->buttonStyle){
					if($linkCond) $sort = " onClick=\"document.location='".$this->getVar."sort".$this->tabId."=".$index."'\"";
					$out .= "\t\t\t<td align=\"center\" class=\"".$this->tabId."\"><button class=\"button\" type=\"button\"".$sort." class=\"".$this->tabId."\">".$titleColonne.$infoSort."</button></td>\n";
				} else {
					if($linkCond) {
						$out .= "\t\t\t".'<td align="'.$align.'" class="'.$this->tabId.'" style="white-space: nowrap"><a href="'.$this->getVar.'sort'.$this->tabId."=".$index.'" class="'.$this->tabId.'" style="border: 0px">'.$titleColonne.$infoSort.'</a></td>'."\n";
					} else {
						$out .= "\t\t\t<td align=\"center\" class=\"".$this->tabId."\" style=\"white-space: nowrap\">".$titleColonne."</td>\n";
					}
				}
			}
		}
		if(is_array($this->calcColumn)){
			foreach($this->calcColumn as $calcCol){
					if(	isset($index) && ($calcCol["position"] > $index)){
						if($this->buttonStyle) $out .= "\t\t\t<td class=\"".$this->tabId."\"><button class=\"button\" type=\"button\">".$calcCol["title"]."</button></td>\n";
						else $out .= "\t\t\t<td align=\"center\" class=\"".$this->tabId."\">".$calcCol["title"]."</td>\n";
					}
			}
		}
		$out .= "\t\t</tr>\n";
		$out .= "\t</thead>\n";
		return $out;
	}

	/**
	* Build the data table result
	*
	* @access private
	* @return string
	*/
	function _showTable(){
	  $out = '<!-- SQLiteToGrid.class.php : _showTable() -->'."\n";
		$pos = 0;
		if(is_array($this->data))
		foreach($this->data as $ligne){
			if($pos % 2) $localBgColor = $GLOBALS['browseColor1'];
			else $localBgColor = $GLOBALS['browseColor2'];
			$out .=  "\t<tr 	onMouseOver=\"setRowColor(this, $pos, 'over', '".$localBgColor."', '".$GLOBALS["browseColorOver"]."', '".$GLOBALS["browseColorClick"]."')\"
								onMouseOut=\"setRowColor(this, $pos, 'out', '".$localBgColor."', '".$GLOBALS["browseColorOver"]."', '".$GLOBALS["browseColorClick"]."')\"";
			if($this->onClick) $out .= "						onMouseDown=\"setRowColor(this, $pos, 'click', '".$localBgColor."', '".$GLOBALS["browseColorOver"]."', '".$GLOBALS["browseColorClick"]."')\"";
			$out .= ">\n";
			while(list($index, $value) = each($ligne)){
				if($GLOBALS["allHTML"]) $value = htmlentities($value, ENT_NOQUOTES, $GLOBALS['charset']);
				if(!$GLOBALS["allFullText"]){
					if(strlen($value)>PARTIAL_TEXT_SIZE) $value = substr($value, 0, PARTIAL_TEXT_SIZE).'...';
				}
				if($value=="") {
					if(isset($this->NullInfo[$this->title[$index]]) && ($this->NullInfo[$this->title[$index]]==0)) $value="<i>NULL</i>";
					else $value="&nbsp;";
				}
				if(is_array($this->calcColumn)) {
					foreach($this->calcColumn as $calcCol){
						if($calcCol['position'] == $index) $out .= "\t\t".'<td bgcolor="'.$localBgColor.'" '.((!empty($calcCol['align']))? 'align="'.$calcCol['align'].'"' : '' ).'style="white-space: nowrap" width="10%" class="'.$this->tabId.'">'.$this->_formatCalc($ligne, $calcCol['format'], $pos ).'</td>'."\n";
					}
				}
				if(!is_array($this->hide) || !isset($this->hide[$index]) || !$this->hide[$index]) $out .= "\t\t".'<td bgcolor="'.$localBgColor.'" '.(($this->align[$index])? 'align="'.$this->align[$index].'"' : '' ).'style="white-space: nowrap" class="'.$this->tabId.'">'.((!empty($this->format[$index]))? $this->_formatCalc($ligne, $this->format[$index]) : $value ).'</td>'."\n";
			}
			if(is_array($this->calcColumn)) {
				foreach($this->calcColumn as $calcCol){
					if($calcCol["position"] > $this->nbColonne) $out .= "\t\t".'<td bgcolor="'.$localBgColor.'" '.((!empty($calcCol['align']))? 'align="'.$calcCol['align'].'"' : '' ).'style="white-space: nowrap" width="10%" class="'.$this->tabId.'">'.$this->_formatCalc($ligne, $calcCol['format'], $pos ).'</td>'."\n";
				}
			}
			$out .= "\t</tr>\n";
			$pos++;
		}
		return $out;
	}

	/**
	* Build the table footer
	*
	* @access private
	* @return string
	*/
	function _showFooter(){
		return "</table>\n</div>\n";
	}

	/**
	* Build the navigation bar, it use the Tfoot
	*
	* @access private
	* @return string
	*/
	function _showNavigate(){
	  $out = '<!-- SQLiteToGrid.class.php : _showNavigate() -->'."\n";
		$out .= "\t<tr class=\"navbarre\"><td colspan=\"".$this->_countVisibleColumn()."\" align=\"center\" class=\"".$this->tabId."\" nowrap=\"nowrap\" style=\"white-space: nowrap\">\n";
		if(NAV_TOP) $top = '<img src="'.NAV_TOP.'" border=0>';
		else $top = '<<';
		if(NAV_PREC) $prec = '<img src="'.NAV_PREC.'" border=0>';
		else $prec = '<';
		if(NAV_SUIV) $suiv = '<img src="'.NAV_SUIV.'" border=0>';
		else $suiv = '>';
		if(NAV_END) $end = '<img src="'.NAV_END.'" border=0>';
		else $end = '>>';
		if(isset($_GET['sort'.$this->tabId])) $linkSort = 'sort'.$this->tabId.'='.$_GET['sort'.$this->tabId].'&amp;';
		else $linkSort = '';
		if($this->pageStart>1) {
			$top = "<a href=\"".$this->getVar.$linkSort."page".$this->tabId."=1\">".$top."</a>";
			$prec = "<a href=\"".$this->getVar.$linkSort."page".$this->tabId."=".($this->pageStart - 1)."\">".$prec."</a>";
		}
		if($this->pageStart<$this->nbPage){
			$suiv = "<a href=\"".$this->getVar.$linkSort."page".$this->tabId."=".($this->pageStart + 1)."\">".$suiv."</a>";
			$end = "<a href=\"".$this->getVar.$linkSort."page".$this->tabId."=".($this->nbPage)."\">".$end."</a>";
		}
		if($this->nbPage<NAV_NBLINK){
			$startLink = 1;
			$endLink = $this->nbPage;
		} else {
			if(($this->pageStart<($this->nbPage - (int)(NAV_NBLINK/2))) && ($this->pageStart>(int)(NAV_NBLINK/2))) $startLink = $this->pageStart - ((int)(NAV_NBLINK/2));
			elseif($this->pageStart>=($this->nbPage - (int)(NAV_NBLINK/2))) $startLink = $this->nbPage - (NAV_NBLINK-1);
			else $startLink = 1;
			if( ($startLink+NAV_NBLINK-1) > $this->nbPage) {
				$startLink = $this->nbPage - NAV_NBLINK + 1;
				$endLink = $this->nbPage;
			} else {
				$endLink = $startLink + (NAV_NBLINK-1);
			}
		}
		$link = '';
		for($i=$startLink ; $i<=$endLink ; $i++){
			if($i == $this->pageStart) $link .= '<span style="font-size: 12px;">'.$i.'</span>';
			else $link .= "<a href=\"".$this->getVar.$linkSort."page".$this->tabId."=".$i."\"><span style='font-size: 12px'>".$i."</span></a>";
			if($i < $endLink) $link .= NAV_SEP;
		}
		$infoNav = "&nbsp;&nbsp;".$GLOBALS["traduct"]->get(136)." ".$this->infoNav["start"]."-".$this->infoNav["end"]."/".$this->infoNav["all"];
		$out .= "<div style=\"text-align: left;\">".$infoNav.NAV_SEP.$top.NAV_SEP.$prec.NAV_SEP.$link.NAV_SEP.$suiv.NAV_SEP.$end."</div><div style=\"clear: both;\"></div></div>";
		$out .= "\t</td></tr>\n";
		return $out;
	}

	/**
	* Simple template methode to replace var value in the string format for the calc Column
	*
	* @access private
	* @param array &$ligne reference on the ligne result table
	* @param string $format template to work with
	* @return string
	*/
	function _formatCalc(&$ligne, $format, $pos=""){
		preg_match('/#%(.*)%#/', $format, $var);
		while(isset($var[1])){
			if((substr($var[1],0,3)!='POS') && (substr($var[1],0,5)!='QUERY')){
				$format = str_replace('#%'.$var[1].'%#', $ligne[$var[1]], $format);
			} elseif(substr($var[1],0,3)=='POS'){
				$format = str_replace('#%POS%#', $pos, $format);
			} elseif(substr($var[1],0,5)=='QUERY'){
				$format = str_replace('#%QUERY%#', urlencode($this->getRealQuery()), $format);
			}
			preg_match('/#%(.*)%#/', $format, $var);
		}
		return $format;
	}

	/**
	* Method for calc paginate
	*
	* @access private
	*/
	function _definePage(){
		$nbRecord = $this->_countRecord();
		$this->nbPage = ceil($nbRecord / $this->recordPerPage);
		if(!isset($_GET['page'.$this->tabId])) $this->pageStart = 1;
		else $this->pageStart = $_GET['page'.$this->tabId];
		$this->indexStart = (($this->pageStart - 1) * $this->recordPerPage);
		$this->infoNav['start'] = $this->indexStart;
		$this->infoNav['end']	= $this->indexStart + $this->recordPerPage;
		$this->infoNav['all']	= $nbRecord;
		if($this->infoNav['end']>$nbRecord) $this->infoNav['end']=$nbRecord;
	}

	/**
	* Methode to set the order sens
	*
	* @access private
	*/
	function _checkOrder(){
		if(isset($_GET['sort'.$this->tabId]) && ($_GET['sort'.$this->tabId]==$this->oldOrder) && (!isset($_GET['page'.$this->tabId]))){
			if($this->orderSens == 'ASC') $this->orderSens = 'DESC';
			else $this->orderSens = 'ASC';
		} elseif(!isset($_GET['page'.$this->tabId])){
			$this->orderSens = 'ASC';
		}
	}

	/**
	* retreive session data
	*
	* @access private
	*/
	function _fromSession(){
		if( array_key_exists('old_order'.$this->tabId, $_SESSION) ) {
			$this->oldOrder = $_SESSION['old_order'.$this->tabId];
		}
		if( array_key_exists('order_sens'.$this->tabId, $_SESSION) ) {
			$this->orderSens = $_SESSION['order_sens'.$this->tabId];
		}
		return;
	}

	/**
	* save session data
	*
	* @access private
	*/

	function _toSession(){
		if(!isset($oldOrder)) $oldOrder = '';
		if(isset($_GET['sort'.$this->tabId])) $oldOrder = $_GET['sort'.$this->tabId];
		elseif(isset($this->orderInit)) $oldOrder = $this->orderInit;
		$_SESSION['old_order'.$this->tabId] = $oldOrder;
		$_SESSION['order_sens'.$this->tabId] = $this->orderSens;
		return;
	}

	/**
	* Return the visible column number
	*
	* @access private
	* @return int
	*/
	function _countVisibleColumn(){
		if(is_array($this->hide)) $nbHide = array_sum($this->hide); else $nbHide = 0;
		if(is_array($this->calcColumn)) $nbCalc = count($this->calcColumn); else $nbCalc = 0;
		return ($this->nbColonne - $nbHide + $nbCalc);
	}

	/**
	* Parsing Original Query for extract information
	*
 	* @access private
	* @param boolean $autoTitle false: extract title from query, true: extract with sql command
	*/
	function _parseQuery($autoTitle){
		$this->query = preg_replace('#^select[[:space:]]#', 'SELECT ', $this->query);
		$this->query = preg_replace('#[[:space:]]distinct[[:space:]]#', ' DISTINCT ', $this->query);
		$this->query = preg_replace('#[[:space:]]as[[:space:]]#', ' AS ', $this->query);
		$this->query = preg_replace('#[[:space:]]from[[:space:]]#', ' FROM ', $this->query);
		$this->query = preg_replace('#[[:space:]]where[[:space:]]#', ' WHERE ', $this->query);
		$this->query = preg_replace('#[[:space:]]group[[:space:]]+by[[:space:]]#', ' GROUP BY ', $this->query);
		$this->query = preg_replace('#[[:space:]]having[[:space:]]#', ' HAVING ', $this->query);
		$this->query = preg_replace('#[[:space:]]order[[:space:]]+by[[:space:]]#', ' ORDER BY ', $this->query);
		$this->query = preg_replace('#[[:space:]]limit[[:space:]]#', ' LIMIT ', $this->query);
		if($autoTitle){
			$queryCalc = $this->query;
			$queryCalc = str_replace('[[:space:]]DISTINCT[[:space:]]', ' ', $queryCalc);
			$queryCalc = preg_replace("#\t|\n#", ' ', $queryCalc);
			preg_match('/SELECT[[:space:]](.*)[[:space:]]FROM/', $queryCalc, $listChamp);
			if(isset($listChamp[1])) $this->listChamp = $listChamp[1];
			else $this->listChamp = '';
			preg_match('/ORDER[[:space:]]+BY[[:space:]]+(.*)/', $this->query, $order);
			if(isset($order[0])) $this->query = str_replace($order[0], '', $this->query);
			if(isset($order[1]) && (preg_match('#asc#i', $order[1]) || preg_match('#desc#i', $order[1]))){
				preg_match('/[[:space:]]+(.*)/', trim($order[1]), $sens);
				$order[1] = trim(str_replace($sens, '', $order[1]));
				$this->queryOrderSensDefault = trim($sens[1]);
			}
			if(isset($order[1])) $this->queryOrderDefault = str_replace('"', '', $order[1]);
			/*
			if((!eregi("\*", $this->listChamp)) && !eregi("PRAGMA|EXPLAIN", $this->query)){
				$stringChamp = $this->listChamp;
				while($startPar = strpos($stringChamp, "(")){
					$endPar = strpos($stringChamp, ")");
					$chainePar = substr($stringChamp, $startPar, ($endPar-$startPar)+1);
					$stringChamp = str_replace($chainePar, "", $stringChamp);
				}
				$this->listChamp = $stringChamp;
				$listChamp = explode(",", $this->listChamp);
				foreach($listChamp as $champ){
					preg_match("/[[:space:]]AS[[:space:]](.*)/i", $champ, $surname);
					if(isset($surname[1]) && !empty($surname[1])) {
						$surname[1] = ereg_replace("\"|'", "", $surname[1]);
						$tabTitle[] = trim($surname[1]);
					} else {
						preg_match("/\.(.*)/", $champ, $table);
						if(isset($table[1]) && !empty($table[1])) $tabTitle[] = $table[1];
						else $tabTitle[] = trim($champ);
					}
				}
			} else {
				$tabTitle = $this->_fetchField();
			}
			*/
			$tabTitle = $this->_fetchField();

			$this->setTitle($tabTitle);
		}
		preg_match('/FROM[[:space:]]+(.*)/', $this->query, $from);
		if(isset($from[1])) $this->queryCount = 'SELECT count(*) FROM '.$from[1];
		else $this->queryCount = $this->query;
		if(isset($_GET['sort'.$this->tabId])){
			while(list($index, $name) = each($tabTitle)){
				if($index == $_GET['sort'.$this->tabId]) $this->order = $name;
			}
		} elseif(!empty($this->queryOrderDefault)) {
			$this->order = $this->queryOrderDefault;
			$this->orderInit = array_search(trim($this->order), $tabTitle);
		}
		$this->_checkOrder();
		return;
	}

	/**
	* retourne le nombre d'enregistrement
	*
	* @access public
	* @return int
	*/
	function getNbRecord(){
		return $this->nbRecordQuery;
	}

	/**
	* retourne la requête executée
	*
	* @access public
	* @return string
	*/
	function getRealQuery(){
		return $this->realQuery;
	}

	/**
	* Return the column name, when parsing query can't determine it
	*
	* @access private
	* @return array
	*/
	function _fetchField(){
		if(preg_match('#^select#i', $this->query) && !preg_match('#limit#i', $this->query)) $queryLoc = $this->query.' LIMIT 0,1';
		else $queryLoc = $this->query;
		if($res = $this->SQLiteConnId->query($queryLoc)){
			for($i=0 ; $i < $this->SQLiteConnId->num_fields() ; $i++){
				$title[] = $this->SQLiteConnId->field_name(null, $i);
			}
			if(isset($title)) return $title;
			else return false;
		}
		return false;
	}

	/**
	* Return the original number of record
	*
 	* @access private
	* @return int
	*/
    function _countRecord(){
        if(!isset($this->nbRecordQuery)){
            if($this->SQLiteConnId->getVersion()==2) {
                $qCount =
                preg_match('/^\s*(UPDATE|DELETE|INSERT|ALTER|JOIN|GROUP|LIMIT|PRAGMA)\s/i',
                            $this->query);
            } else {
                $qCount = false;
            }
            if($qCount){
                if($this->SQLiteConnId->query($this->queryCount)){
                    $this->nbRecordQuery = $this->SQLiteConnId->fetch_single();
                } else $this->_sendError($GLOBALS['traduct']->get(117));
            } else {
                if (preg_match('#^SELECT \* FROM#i', $this->query)) {
                    $q = preg_replace('#^SELECT \* FROM#i','SELECT COUNT(*) as count FROM', $this->query);
                    if ($this->SQLiteConnId->query($q)) {
                        $this->nbRecordQuery = $this->SQLiteConnId->fetch_single();
                    }
                } else {
                    $tabResult = $this->SQLiteConnId->array_query($this->query);
                    $this->nbRecordQuery = count($tabResult);
                }
            }
        }

        return $this->nbRecordQuery;
    }

	/**
	* Return an array with the data to send
	*
 	* @access private
	* @return array
	*/
	function _getRecord(){
		if(isset($GLOBALS['TableListImpact'])){
			$tableList = explode(',', $GLOBALS['TableListImpact']);
			if(count($tableList)>1) $withTableName = true;
			else $withTableName = false;
			foreach($tableList as $tableImpact){
				if(!empty($tableImpact) && !preg_match('#\.#', $tableImpact)){
					$tempInfoTable = $this->SQLiteConnId->array_query('PRAGMA table_info('.brackets(trim($tableImpact)).');');
					if(is_array($tempInfoTable)){
						foreach($tempInfoTable as $infoTable) {
							if($withTableName) $this->NullInfo[trim($tableImpact).'.'.$infoTable['name']] = $infoTable['notnull'];
							else $this->NullInfo[$infoTable['name']] = $infoTable['notnull'];
						}
					}
				}
			}
		}
		if(strpos(trim($this->order), ' ')) $order = '"'.$this->order.'"';
		else $order = $this->order;

		$query = $this->query.(($this->order)? ' ORDER BY '.$order.' '.$this->orderSens : '' );
		if(!preg_match('#pragma#i', $this->query) && !preg_match('#limit#i', $this->query)) $query .= ' LIMIT '.$this->indexStart.', '.$this->recordPerPage;

		if($this->SQLiteConnId->query($query)){
			unset($tabRecord);
			$tabRecord = array();
			while($ligne = $this->SQLiteConnId->fetch_array(null, SQLITE_NUM)){
				$tabRecord[] = $ligne;
			}
		}
		$this->realQuery = $query;
		return $tabRecord;
	}

	/**
	* Add caption to table
	*
	* @access public
	* @param string $alignement
	* @param string $content
	*/
	function addCaption($align, $content){
		$this->tabCaption['align'] = $align;
		$this->tabCaption['content'] = $content;
	}

	/**
	* Disable 'onclick' javascript function on table
	*
	* @access public
	*/
	function disableOnClick(){
		$this->onClick = false;
		return;
	}
}
?>
