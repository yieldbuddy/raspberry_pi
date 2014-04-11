<?php
/**
* Web based SQLite management
* Show and manage 'FUNCTION' properties
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteTriggerProperties.class.php,v 1.34 2006/04/16 18:56:57 freddy78 Exp $ $Revision: 1.34 $
*/

class SQLiteTriggerProperties {

	/**
	* reference of the connection object
	*
	* @access public
	* @var resource
	*/
	var $connId;

	/**
	* TRIGGER name
	*
	* @access private
	* @var string
	*/
	var $trigger;

	/**
	* this TRIGGER exist?
	*
	* @access private
	* @var bool
	*/
	var $isExist;

	/**
	* TRIGGER properties
	* @access private
	* @var array
	*/
	var $triggerProperties;

	/**
	* Class constructor
	*
	* @access public
	* @param object $conn reference to the connection object
	*/
	function __construct($conn){
		$this->connId = $conn;
		if($GLOBALS['trigger'] && ($GLOBALS['action']!='add')) {
			$this->trigger = $GLOBALS['trigger'];
		} elseif($GLOBALS['TriggerName']){
			$this->trigger = $GLOBALS['TriggerName'];
		} else return false;
		$this->isExist = $this->triggerExist($this->trigger);
		return $this->isExist;
	}

	/**
	* Verify if the TRIGGER exist
	*
	* @access public
	* @param string $trigger
	*/
	function triggerExist($trigger){
		if(empty($trigger)) $trigger = $this->trigger;
		$query = "SELECT sql FROM sqlite_master WHERE type='trigger' AND name=".quotes($trigger).";";
		if($this->connId->getResId($query)){
			$triggerSQL = $this->connId->connId->fetch_single();
			if(!$triggerSQL) return false;
			$this->triggerProperties = $triggerSQL;
			return true;
		} else return false;
	}

	/**
	* save current TRIGGER properties
	*
	* @access public
	*/
	function saveProp(){
		if(($GLOBALS['action']=='delete') || ($this->isExist)){
			$queryDisplay = $query[] = 'DROP TRIGGER '.brackets($this->trigger).';';
		}
		if($GLOBALS['action']!='delete'){
			if($this->isExist) $triggername = $this->trigger;
			else $triggername = $_POST['TriggerName'];
			$queryCreate = 'CREATE TRIGGER '.brackets($triggername).' '.$_POST['TriggerMoment'];
			if($_POST['TriggerMoment']!='') $queryCreate .= ' ';
			$queryCreate .= $_POST['TriggerEvent'].' ';
			if($_POST['TriggerEvent']=='UPDATE OF') $queryCreate .= $_POST['ColumnList'].' ';
			$queryCreate .= 'ON '.brackets($_POST['TriggerOn']).' '.$_POST['TriggerAction'];
			if($_POST['TriggerAction']!='') $queryCreate .= ' ';
			if($_POST['TriggerCondition']=='WHEN') $queryCreate .= 'WHEN '.$_POST['ConditionList'].' ';
			$queryCreate .= "\n".'BEGIN '."\n".SQLiteStripSlashes($_POST['TriggerStep'])."\n".' END;'."\n";
			$queryDisplay = $query[] = $queryCreate;
			$this->triggerProperties = $queryCreate;
		}
		$errorMessage = '';
		foreach($query as $req){
			$this->connId->getResId("BEGIN;");
			$res = $this->connId->getResId($req);
			$this->connId->getResId("COMMIT;");
			if(!$res){
				$errorMessage .= $GLOBALS['traduct']->get(9).' '.$errorCode.' : '.@$this->connId->connId->getError()."\n";
			}
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
	* Display the current TRIGGER properties
	*
	* @access public
	*/
	function propView(){
		$tabTrigInfo = $this->extractProperties($this->triggerProperties);
		$triggView = 'CREATE TRIGGER '.brackets($this->trigger).' '.$tabTrigInfo['TriggerMoment'];
		if($tabTrigInfo['TriggerMoment']!='') $triggView .= ' ';
		$triggView .= $tabTrigInfo['TriggerEvent'].$tabTrigInfo['ColumnList'].' ';
		$triggView .= 'ON '.brackets($tabTrigInfo['TriggerOn']).' '.$tabTrigInfo['TriggerAction'];
		if($tabTrigInfo['TriggerAction']!='') $triggView .= ' ';
		if($tabTrigInfo['TriggerCondition']=='WHEN ') $triggView .= 'WHEN '.$tabTrigInfo['ConditionList'].' ';
		$triggView .= "\n".'BEGIN '."\n".$tabTrigInfo["TriggerStep"]."\n".' END;'."\n";
		echo '<br>';
		echo '	<table cellpadding="2" cellspacing="0" width="90%" class="viewProp">
					<tr class="viewPropTitle"><td align="right" width="20%" class="viewPropTitle">'.$GLOBALS['traduct']->get(19)." :&nbsp;</td><td align='center' class='viewPropTitle'>".$this->trigger."</td></tr>
					<tr><td align='right' class='viewProp'>".$GLOBALS['traduct']->get(53)." :&nbsp;</td><td class='viewProp'>".nl2br($triggView)."</td></tr>";
		echo '		</table>';
		echo '<div align="center">';
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties')) echo "<a href=\"main.php?dbsel=".$GLOBALS["dbsel"]."&amp;trigger=".$this->trigger."&amp;action=modify\" class='base' target=\"main\">".$GLOBALS['traduct']->get(14).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(14).'</i></span>';
		echo str_repeat('&nbsp;', 10);
		if(!$GLOBALS['workDb']->isReadOnly() && displayCondition('del')) echo '<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;trigger='.$this->trigger.'&amp;action=delete" class="base" target="main">'.$GLOBALS['traduct']->get(15).'</a>';
		else echo '<span class="base"><i>'.$GLOBALS['traduct']->get(15).'</i></span>';
		echo '</div>';
}

	/**
	* Display the current TRIGGER form for add or modify
	*/
	function triggerEditForm(){
		if($this->isExist) $tabTrigInfo = $this->extractProperties($this->triggerProperties);
		else $tabTrigInfo = array('TriggerMoment'=>'', 'TriggerEvent'=>'', 'TriggerOn'=>'', 'TriggerCondition'=>'', 'TriggerStep'=>'');
		echo '<br><center>';
		if($GLOBALS['action']=='add') echo '<h4>'.$GLOBALS['traduct']->get(54).'</h4>';
		else echo '<h4>'.$GLOBALS['traduct']->get(55).' : '.$this->trigger.'</h4>';
		echo "	<script  type=\"text/javascript\">
				function checkColumn(){
					base=document.forms['triggerprop'];
					if(base.TriggerEvent.selectedIndex==3) afficheCalque('column');
					else cacheCalque('column');
					return;
				}
				function checkCondition(){
					base=document.forms['triggerprop'];
					if(base.TriggerCondition.selectedIndex==1) afficheCalque('condition');
					else cacheCalque('condition');
					return;
				}
				</script>";
		echo "<form name=\"triggerprop\" action=\"main.php?dbsel=".$GLOBALS["dbsel"]."\" method=\"POST\" target=\"main\">";
		echo "	<table cellpadding=2 cellspacing=0>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(19)." :</td><td class='viewProp'><input type=\"text\" name=\"TriggerName\" value=\"".$this->trigger."\"></td>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(56)." :</td><td class='viewProp'>
						<select name='TriggerMoment'>
							<option value=\"\"></option>
							<option value='BEFORE'".(($tabTrigInfo["TriggerMoment"]=="BEFORE")? " selected" : "" ).">BEFORE</option>
							<option value='AFTER'".(($tabTrigInfo["TriggerMoment"]=="AFTER")? " selected" : "" ).">AFTER</option>
							<option value='INSTEAD OF'".(($tabTrigInfo["TriggerMoment"]=="INSTEAD OF")? " selected" : "" ).">INSTEAD OF</option>
						</select></td>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(57)." :</td><td class='viewProp'>
							<div>
								<div style='float: left'>
									<select name='TriggerEvent' onChange='checkColumn()'>
										<option value='DELETE'".(($tabTrigInfo["TriggerEvent"]=="DELETE")? " selected" : "" ).">DELETE</option>
										<option value='INSERT'".(($tabTrigInfo["TriggerEvent"]=="INSERT")? " selected" : "" ).">INSERT</option>
										<option value='UPDATE'".(($tabTrigInfo["TriggerEvent"]=="UPDATE")? " selected" : "" ).">UPDATE</option>
										<option value='UPDATE OF'".(($tabTrigInfo["TriggerEvent"]=="UPDATE OF")? " selected" : "" ).">UPDATE OF</option>
									</select>
								</div>
								<div id='column' style='float: right'>
									<input type=\"text\" name=\"ColumnList\" value=\"".(($tabTrigInfo["TriggerEvent"]=="UPDATE OF ")? $tabTrigInfo["ColumnList"] : "" )."\" size=40>
								</div>
							</div>
						</td>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(58)." :</td><td class='viewProp'>
						<select name='TriggerOn'>".$this->getOn($tabTrigInfo["TriggerOn"])."</select></td>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(33)." :</td><td class='viewProp'>
						<select name='TriggerAction'><option value=''></option>
						<option value='FOR EACH ROW'>FOR EACH ROW</option>
						<option value='FOR EACH STATEMENT'>FOR EACH STATEMENT</option></select></td>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(59)." :</td><td class='viewProp'>
						<div>
						<div style='float: left'>
							<select name='TriggerCondition' onChange='checkCondition();'>
								<option value=''></option>
								<option value='WHEN'".(($tabTrigInfo["TriggerCondition"]=="WHEN ")? " selected" : "" ).">WHEN</option>
							</select>
						</div>
						<div id='condition' style='float: right'>
							<input type=\"text\" name=\"ConditionList\" value=\"".(($tabTrigInfo["TriggerCondition"]=="WHEN ")? $tabTrigInfo["ConditionList"] : "" )."\" size=40>
						</div>
						</div>
						</td>
					</tr>
					<tr><td align='right' class='viewPropTitle'>".$GLOBALS['traduct']->get(60)." :</td><td class='viewProp'><textarea name='TriggerStep' cols=60 rows=4>".htmlentities(trim($tabTrigInfo["TriggerStep"]), ENT_NOQUOTES, $GLOBALS['charset'])."</textarea></td>
				</table>";
		echo "<input type=\"hidden\" name=\"trigger\" value=\"".$this->trigger."\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
		echo "<input class=\"button\" type=\"submit\" value=\"".$GLOBALS['traduct']->get(51)."\">";
		echo "</form></center>";
		echo "<script  type=\"text/javascript\">checkColumn();checkCondition();</script>";

	}

	/**
	* Get all table and view for 'ON' TRIGGER properties
	*
	* @access private
	* @param string $selected current use
	*/
	function getOn($selected){
		$query = "SELECT name, type FROM sqlite_master WHERE (type='table' OR type='view') ORDER BY name;";
		$listOn = '';
		if($this->connId->connId->query($query)){
			while($ligne = $this->connId->connId->fetch_array(null, SQLITE_ASSOC)){
				$listOn .= '<option value="'.$ligne['name'].'"'.(($ligne['name']==$selected)? ' selected="selected"' : '' ).'>'.$ligne['name'].'('.$ligne['type'].')</option>';
			}
		}
		return $listOn;
	}

	/**
	* Extract All properties in the SQL TRIGGER Properties
	*
	* @access private
	* @param string $sql SQL TRIGGER properties
	*/
	function extractProperties($sql){
		$out = array('TriggerMoment'=>'', 'TriggerEvent'=>'', 'ColumnList'=>'', 'TriggerAction'=>'', 'TriggerCondition'=>'', 'TriggerOn'=>'', 'TriggerStep'=>'');
		$sql = str_replace("\n", ' ', $sql);
		preg_match('/CREATE[[:space:]](.*)[[:space:]]ON/i', $sql, $StartTrig);
		if($StartTrig[1]) $firstSubString = $StartTrig[1];
		if(preg_match('#BEFORE#i', $firstSubString)) 	$out['TriggerMoment'] = 'BEFORE';
		if(preg_match('#AFTER#i', $firstSubString)) 	$out['TriggerMoment'] = 'AFTER';
		if(preg_match('#DELETE#i', $firstSubString)) 	$out['TriggerEvent'] = 'DELETE';
		elseif(preg_match('#INSERT#i', $firstSubString)) 	$out['TriggerEvent'] = 'INSERT';
		elseif(preg_match('#UPDATE OF#i', $firstSubString)) 	{
			preg_match('#OF[[:space:]](.*)[[:space:]]ON#i', $sql, $colList);
			$out['TriggerEvent'] 	= 'UPDATE OF ';
			if($colList[1]) $out['ColumnList'] = trim($colList[1]);
		} elseif(preg_match('#UPDATE#i', $sql)) 	$out['TriggerEvent'] = 'UPDATE';

		if(preg_match('#FOR EACH ROW#i', $sql)) {
			$out['TriggerAction'] = 'FOR EACH ROW';
			$searchTable = 'FOR';
		}
		if(preg_match('#FOR EACH STATEMENT#i', $sql)){
			$out['TriggerAction'] = 'FOR EACH STATEMENT';
			$searchTable = 'FOR';
		}
		if(preg_match('#WHEN#i', $sql)) 	{
			$searchTable = 'WHEN';
			preg_match('/WHEN[[:space:]](.*)[[:space:]]|BEGIN/i', $sql, $condList);
			$out['TriggerCondition'] = 'WHEN ';
			if($condList[1]) $out['ConditionList'] = trim($condList[1]);
		}
		if(!isset($searchTable)) $searchTable = 'BEGIN';
		preg_match('/ON[[:space:]](.*)[[:space:]]'.$searchTable.'/i', $sql, $tabList);
		if($tabList[1]) $out['TriggerOn'] = trim($tabList[1]);
		$Begin = stristr($sql, 'BEGIN');
		preg_match('/BEGIN(.*)END/i', $sql, $stepList);
		if($stepList[1]) $out['TriggerStep'] = $stepList[1];
		return $out;
	}
}

?>
