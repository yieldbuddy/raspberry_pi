<?php
/**
* Web based SQLite management
* View management Class
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteViewProperties.class.php,v 1.31 2006/04/16 18:56:57 freddy78 Exp $ $Revision: 1.31 $
*/

class SQLiteViewProperties {

	/**
	* reference of connection object
	*
	* @access public
	* @var object
	*/
	var $connId;

	/**
	* VIEW name
	*
	* @access private
	* @var string
	*/
	var $view;

	/**
	* this VIEW is exist?
	*
	* @access private
	* @var bool
	*/
	var $isExist;

	/**
	* Properties of current VIEW
	*
	* @access private
	* @var array
	*/
	var $viewProperties;

	/**
	* Class constructor
	*
	* @access public
	* @param object $conn reference to the connection object
	*/
	function __construct($conn){
		// constructeur de la classe
		$this->connId = $conn;
		if($GLOBALS["view"] && ($GLOBALS["action"]!="add")) {
			$this->view = $GLOBALS["view"];
		} elseif($GLOBALS["ViewName"]){
			$this->view = $GLOBALS["ViewName"];
		} else return false;
		$this->isExist = $this->viewExist($this->view);
		return $this->isExist;
	}

	/**
	* Verify if VIEW exist
	*
	* @access public
	* @param string
	*/
	function viewExist($view){
		if(empty($view)) $view = $this->view;
		$query = "SELECT sql FROM sqlite_master WHERE type='view' AND name=".quotes($view).";";
		if($this->connId->getResId($query)){
			$viewSQL = $this->connId->connId->fetch_single();
			if(!empty($viewSQL)){
				$viewSQL = str_replace('select', 'SELECT', $viewSQL);
				preg_match('/SELECT(.*)/', str_replace("\n", ' ', $viewSQL), $propTab);
				$this->viewProperties = trim($propTab[0]);
				return true;
			} else return false;
		} else return false;
	}

	/**
	* Save current view properties
	*
	* @access public
	*/
	function saveProp(){
		if(($GLOBALS['action']=='delete') || ($this->isExist)){
			$queryDisplay = $query[] = 'DROP VIEW '.brackets($this->view).';';
		}
		if($GLOBALS['action']!='delete'){
			if($this->isExist) $viewname = $this->view;
			else $viewname = $_POST['ViewName'];
			if(!empty($_POST['ViewName']) && !empty($_POST['ViewProp'])) {
				$queryDisplay = $query[] = 'CREATE VIEW '.brackets($viewname).' AS '.urldecode(SQLiteStripSlashes($_POST['ViewProp'])).';';
			}
		}
		$errorMessage = '';
		if($query){
			foreach($query as $req){
				$this->connId->getResId("BEGIN;");
				$res = $this->connId->getResId($req);
				$this->connId->getResId("COMMIT;");
				if(!$res){
					$errorCode = @sqlitem_last_error($this->connId->connId);
					$errorMessage .= $GLOBALS['traduct']->get(9).' '.$errorCode.' : '.@$this->connId->connId->getError().'\n';
				}
			}
		} else if(empty($_POST['ViewName']) || empty($_POST['ViewProp'])){
			$errorMessage .= $GLOBALS['traduct']->get(18);
		}
		if($GLOBALS['action']!='delete') {
			if($queryDisplay) displayQuery($queryDisplay);
			if(!empty($errorMessage)){
				displayError($errorMessage);
				$this->viewEditForm();
			}
		} else {
				$GLOBALS["redirect"] = "<script type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."'; parent.main.location='main.php?dbsel=".$GLOBALS["dbsel"]."';</script>";
		}
	}

	/**
	* Display current VIEW properties
	*
	* @access public
	*/
	function propView(){
	  echo '<!-- SQLiteViewProperties.class.php : propView() -->'."\n";
		echo '<br>';
		echo '	<table cellpadding="2" cellspacing="0" width="90%" class="viewProp">
					<tr class="viewPropTitle"><td align="right" width="20%" class="viewPropTitle">'.$GLOBALS['traduct']->get(19).' :&nbsp;</td><td align="center" class="viewPropTitle">'.$this->view.'</td></tr>
					<tr><td align="right" class="viewProp">'.$GLOBALS['traduct']->get(61).' :&nbsp;</td><td class="viewProp">'.highlight_query($this->viewProperties).'</td></tr>
				</table>';
		echo '<div align="center">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;view='.$this->view.'&amp;action=modify" class="base" target="main">'.$GLOBALS['traduct']->get(14).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(14).'</i></span>';
		echo str_repeat('&nbsp;', 10);
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('del')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;view='.$this->view.'&amp;action=delete" class="base" target="main">'.$GLOBALS['traduct']->get(15).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(15).'</i></span>';
		echo '</div>';
	}

	/**
	* Display VIEW form to add or modify
	*
	* @access public
	*/
	function viewEditForm(){
	  echo '<!-- SQLiteViewProperties.class.php : viewEditForm() -->'."\n";
		echo '<br><center>';
		if($GLOBALS['action']=='add') echo '<h4>'.$GLOBALS['traduct']->get(62).'</h4>';
		else echo '<h4>'.$GLOBALS['traduct']->get(63).' : '.$this->view.'</h4>';
		if($this->isExist){
			$ViewName = $this->view;
			$ViewProp = $this->viewProperties;
		} else {
			$ViewName = '';
			$ViewProp = '';
		}
		echo '<form name="viewprop" action="main.php?dbsel='.$GLOBALS['dbsel'].'" method="POST" target="main">';
		echo '	<table cellpadding="2" cellspacing="0" width="70%">
					<tr><td align="right" class="viewPropTitle" style="white-space: nowrap">'.$GLOBALS['traduct']->get(19).' :</td><td class="viewProp"><input type="text" class="text" name="ViewName" value="'.$ViewName.'"></td>
					<tr><td align="right" class="viewPropTitle" style="white-space: nowrap">'.$GLOBALS['traduct']->get(61).' :</td><td class="viewProp"><textarea name="ViewProp" cols="60" rows="4">'.htmlentities($ViewProp, ENT_NOQUOTES, $GLOBALS['charset']).'</textarea></td>
				</table>';
		echo '<input type="hidden" name="view" value="'.$this->view.'">'."\n";
		echo '<input type="hidden" name="action" value="save">'."\n";
		echo '<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'">';
		echo '</form>' . "\n";
		echo '</center>';

	}

    /**
    * Generate SQL query for 'select'
    * @author Maurício M. Maia <mauricio.maia@gmail.com>
    *
    * @param string $table
    */
    function selectElement($view) {
        $showField = $_REQUEST['showField'];
        $valField = $_REQUEST['valField'];
        $operats = $_REQUEST['operats'];
		$error = false;
        $selectQuery = 'SELECT ';
        $condQuery = '';
		if(is_array($_REQUEST['showField']) && !empty($_REQUEST['showField'])){
			$selectQuery .= implode(", ", array_keys($_REQUEST['showField']));
	    } else $selectQuery .= '*';

        $selectQuery .= ' FROM '.brackets($view).' ';
		if(is_array($_REQUEST['valField']) && !empty($_REQUEST['valField'])){
	        foreach($valField as $key => $value) {
	            if (	(isset($value) && !empty($value))
	            		|| (isset($operats[$key])
	            		&& !empty($operats[$key]))) {

					if($operats[$key] == 'ISNULL' || $operats[$key] == 'NOTNULL'){
	            		$condQuery .= $key.' '.$operats[$key];
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
	    return $selectQuery.(($condQuery)? 'WHERE '.$condQuery : '' );
    }

}

?>

