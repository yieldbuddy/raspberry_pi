<?php
/**
* Web based SQLite management
* FUNCTION management Class
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteFunctionProperties.class.php,v 1.34 2006/04/16 18:56:57 freddy78 Exp $ $Revision: 1.34 $
*/

class SQLiteFunctionProperties {

	/**
	* reference to the connection object
	*
	* @access public
	* @var resource
	*/
	var $connId;

	/**
	* function name
	*
	* @access private
	* @var string
	*/
	var $function;

	/**
	* this function exist?
	*
	* @access private
	* @var bool
	*/
	var $isExist;

	/**
	* Properties of the current FUNCTION
	*
	* @access private
	* @var array
	*/
	var $functionProperties;

	/**
	* Class constructor
	*
	* @access public
	* @param object $conn reference to the connection object
	*/
	function __construct(&$conn){
		// constructeur de la classe
		$this->connId = $conn;
		if($GLOBALS['function'] && ($GLOBALS['action']!='add')) {
			$this->function = $GLOBALS['function'];
		} elseif(isset($GLOBALS['FunctionName']) && $GLOBALS['FunctionName']){
			$this->function = $GLOBALS['FunctionName'];
		} else return false;
		$this->isExist = $this->functionExist($this->function);
		return $this->isExist;
	}

	/**
	* Verify if this FUNCITION exist
	*
	* @access public
	* @param string
	*/
	function functionExist($function){
		if(empty($function)) $function = $this->function;
		$query = 'SELECT * FROM user_function WHERE funct_name='.quotes($function).' AND (base_id='.$GLOBALS['dbsel'].' OR base_id IS NULL);';
		$tempTabFunction = $GLOBALS['db']->array_query($query);		
		if(count($tempTabFunction)==1){
			$exist = false;
			foreach($tempTabFunction as $tempFunctProp) {
				$this->functionProperties = $tempFunctProp;
				$exist = true;
			}
			return $exist;
		} else return false;
	}

	/**
	* save properties of the current FUNCTION
	*
	* @access private
	*/
	function saveProp(){
		if($GLOBALS['action']=='delete'){
			$queryDisplay = 'DELETE FROM user_function WHERE funct_name='.quotes($this->function).' AND (base_id='.$GLOBALS['dbsel'].' OR base_id IS NULL);';
		}
		if($GLOBALS['action']!='delete'){
			$base_id = (($_POST['FunctAttribAll']==1)? 'NULL' : $GLOBALS['dbsel'] );
			if($_POST['FunctName']		!= $this->functionProperties['funct_name']) 		$tabSQL['funct_name'] 		= "'".$this->connId->formatString($_POST['FunctName'])."'";
			if($_POST['FunctType']		!= $this->functionProperties['funct_type']) 		$tabSQL['funct_type'] 		= $this->connId->formatString($_POST['FunctType']);
			if($_POST['FunctCode']		!= $this->functionProperties['funct_code']) 		$tabSQL['funct_code'] 		= "'".$this->connId->formatString($_POST['FunctCode'])."'";
			if($_POST['FunctFinalCode']	!= $this->functionProperties['funct_final_code']) 	$tabSQL['funct_final_code'] = "'".$this->connId->formatString($_POST['FunctFinalCode'])."'";
			if($_POST['FunctNumArgs']	!= $this->functionProperties['funct_num_args']) 	$tabSQL['funct_num_args'] 	= $this->connId->formatString($_POST['FunctNumArgs']);
			if($base_id	!= $this->functionProperties['base_id']) $tabSQL['base_id'] = $base_id;
			if(is_array($tabSQL)){
				if($this->isExist) {
					while(list($key, $value) = each($tabSQL)) $tabUpdate[] = $key.'='.$value;
					$queryDisplay = 'UPDATE user_function SET '.implode(',', $tabUpdate).' WHERE id='.$_POST['id'].';';
				} else {
					$tabCol = array_keys($tabSQL);
					$tabVal = array_values($tabSQL);
					$nbVal = count($tabSQL);
					$queryDisplay = 'INSERT INTO user_function ('.implode(',', $tabCol).') VALUES ('.implode(',', $tabVal).');';
				}
			}
		}
		$errorMessage = '';
		$res = $GLOBALS['db']->query($queryDisplay);
		if(!$res){
			$errorCode = @sqlitem_last_error($this->connId->connId);
			$errorMessage .= $GLOBALS['traduct']->get(9).' '.$errorCode.' : '.@$this->connId->connId->getError()."\n";
		}
		displayQuery($queryDisplay);
		if(!empty($errorMessage)) displayError($errorMessage);
		if($GLOBALS['action']!='delete') {
			$this->propView();
			echo "<script  type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."';</script>";
		} else {
			echo "<script  type=\"text/javascript\">parent.left.location='left.php?dbsel=".$GLOBALS["dbsel"]."'; parent.main.location='main.php?dbsel=".$GLOBALS["dbsel"]."';</script>";
		}
	}

	/**
	* Display current FUNCITION properties
	*
	* @access public
	*/
	function propView(){
	  echo '<!-- SQLiteFunctionProperties.class.php : propView() -->'."\n";
		echo '<br><center>';
		$funct_code = highlight_string("<?php\n".$this->functionProperties['funct_code']."\n?>", true);
		$funct_finale_code = highlight_string($this->functionProperties['funct_final_code'], true);
		echo '	<table cellpadding="2" cellspacing="0" width="80%" class="viewProp">
					<tr class="viewPropTitle"><td align="right" width="20%" class="viewPropTitle">'.$GLOBALS['traduct']->get(19).' :&nbsp;</td><td align="center" class="viewPropTitle">'.htmlentities($this->function, ENT_NOQUOTES, $GLOBALS['charset']).'</td></tr>
					<tr><td align="right" class="viewProp">Type :&nbsp;</td><td align="center" class="viewProp">'.(($this->functionProperties['funct_type']==1)? $GLOBALS['traduct']->get(10) : $GLOBALS['traduct']->get(11) ).'</td></tr>
					<tr><td align="right" class="viewProp">'.$GLOBALS['traduct']->get(10).' :&nbsp;</td><td class="viewProp">'.$funct_code.'</td></tr>';
		if($this->functionProperties['funct_type']==2) echo '		<tr><td align="right" class="viewProp">'.$GLOBALS['traduct']->get(12).' :&nbsp;</td><td class="viewProp">'.$funct_final_code.'</td></tr>';
		echo '			<tr><td align="right" class="viewProp">'.$GLOBALS['traduct']->get(13).' :&nbsp;</td><td class="viewProp">'.$this->functionProperties['funct_num_args'].'</td></tr>';
		echo '		</table>';
		echo '<div align="center">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;function='.$this->function.'&amp;action=modify" class="base" target="main">'.$GLOBALS['traduct']->get(14).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(14).'</i></span>';
		echo str_repeat('&nbsp;', 10);
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('del')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;function='.$this->function.'&amp;action=delete" class="base" target="main">'.$GLOBALS['traduct']->get(15).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(15).'</i></span>';
		echo '</div>';
		echo '</center>';
}

	/**
	* Display FUNCTION add or modify Form
	*
	* @access public
	*/
	function functEditForm(){
	  echo '<!-- SQLiteFunctionProperties.class.php : functEditForm() -->'."\n";
		echo '<br><center>';
		if($GLOBALS['action']=='add') echo '<h4>'.$GLOBALS['traduct']->get(16).'</h4>';
		else echo '<h4>'.$GLOBALS['traduct']->get(17).' : '.$this->function.'</h4>';
		if($this->isExist){
			$FunctName = $this->function;
			$FunctProp = $this->functionProperties;
			$attribAll = (($FunctProp['base_id']=='')? 1 : 0 );
		} else {
			$FunctName = '';
			$FunctProp = array('id'=>false, 'funct_type'=>1, 'funct_code'=>'', 'funct_final_code'=>'', 'funct_num_args'=>0);
			$attribAll = 0;
		}
		echo "	<script type=\"text/javascript\">
				function subform(){
					base=document.forms['functprop'];
					error=false;
					if(base.elements['FunctName'].value=='') error=true;
					if(base.elements['FunctCode'].value=='') error=true;
					if(base.elements['FunctNumArgs'].value=='') error=true;
					if( (base.elements['FunctType'].selectedIndex==1) && (base.elements['FunctFinalCode'].value=='') ) error=true;
					if(!error){
						if(base.elements['function'].value=='') base.elements['function'].value=base.FunctName.value;
						return true;
					} else {
						alert('".html_entity_decode($GLOBALS['traduct']->get(18), ENT_NOQUOTES, $GLOBALS['charset'])."');
						return false;
					}
				}
				</script>";
		echo '<form name="functprop" action="main.php?dbsel='.$GLOBALS['dbsel'].'" method="POST" onSubmit="return subform();" target="main">';
		echo "\t".'<table cellpadding="2" cellspacing="0" width="80%">';
		echo "\t".'<tr><td align="right" class="viewPropTitle">'.$GLOBALS['traduct']->get(19).' :</td><td class="viewProp"><input type="text" class="text" name="FunctName" value="'.$FunctName.'"></td>';
		echo "\t".'<tr><td align="right" class="viewPropTitle">'.$GLOBALS['traduct']->get(20).' :</td><td class="viewProp"><select name="FunctType" onChange="ftype();"><option value="1"'.(($FunctProp['funct_type']==1)? ' selected="selected"' : '' ).'>'.$GLOBALS['traduct']->get(10).'</option><option value="2"'.(($FunctProp['funct_type']==2)? ' selected="selected"' : '' ).'>Aggregation</option></select></td>';
		echo "\t".'<tr><td align="right" class="viewPropTitle">'.$GLOBALS['traduct']->get(21).' :</td><td class="viewProp"><textarea name="FunctCode" cols="'.TEXTAREA_NB_COLS.'" rows="'.TEXAREA_NB_ROWS.'">'.htmlentities($FunctProp['funct_code'], ENT_NOQUOTES, $GLOBALS['charset']).'</textarea></td>';
		echo "\t".'<tr><td align="right" class="viewPropTitle"><div id="Pfinal1">'.$GLOBALS['traduct']->get(22).' :</div></td><td class="viewProp"><div id="Pfinal2"><textarea name="FunctFinalCode" cols="'.TEXTAREA_NB_COLS.'" rows="4">'.htmlentities($FunctProp['funct_final_code'], ENT_NOQUOTES, $GLOBALS['charset']).'</textarea></div></td>';
		echo "\t".'<tr><td align="right" class="viewPropTitle">'.$GLOBALS['traduct']->get(23).' :</td><td class="viewProp"><input type="text" class="text" name="FunctNumArgs" value="'.$FunctProp['funct_num_args'].'"></td>';
		echo "\t".'<tr><td align="right" class="viewPropTitle">&nbsp;</td><td class="viewProp"><input type="checkbox" name="FunctAttribAll" value="1"'.(($attribAll)? ' checked="checked"' : '' ).'>&nbsp;'.$GLOBALS['traduct']->get(24).'</td>';
		echo "\t".'</table>';
		echo '<input type="hidden" name="function" value="'.$this->function.'">'."\n";
		if($FunctProp['id']) echo '<input type="hidden" name="id" value="'.$FunctProp['id'].'">'."\n";
		echo '<input type="hidden" name="action" value="save">'."\n";
		echo '<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(51).'">';
		echo '</form></center>';
		echo '<script type="text/javascript">ftype();</script>';

	}
}

?>


