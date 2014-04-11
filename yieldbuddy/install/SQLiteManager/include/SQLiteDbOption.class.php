<?php
/**
* Web based SQLite management
* Class for manage database options
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteDbOption.class.php,v 1.31 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.31 $
*/

/**
* Web based SQLite management
* Class for manage database options
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLiteDbOption.class.php,v 1.31 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.31 $
*/
class SQLiteDbOption {
	
	/**
	* reference to the connection object
	*
	* @access public
	* @var object
	*/
	var $connId;
	
	/**
	* Class constructor
	*
	* @access public
	* @param string $conn reference to the connection object
	*/
	function __construct(&$connId){
		$this->connId = $connId;
		switch($GLOBALS['option_action']){
			case '':
				$this->optionView();
				break;
			case 'check':
				$query = 'PRAGMA integrity_check;';
				$res = $this->connId->getResId($query);
				if($res) $tabRes = $this->connId->getArray();
				$this->_getError($query, $res);
				echo '<center><br>Result : '.$tabRes[0]['integrity_check'].'<br></center>';
				$this->optionView();
				break;
			case 'vacuum':
				$query = 'VACUUM;';
				$res = $this->connId->getResId($query);
				displayQuery($query);
				$this->optionView();
				break;
			case 'save':
				$query[] = 'PRAGMA '.(($this->connId->connId->getVersion()==3)? '' : 'default_' ).'synchronous='.$_POST['synchro'].';';
				$query[] = 'PRAGMA default_cache_size='.$_POST['cache_size'].';';
				$query[] = 'PRAGMA '.(($this->connId->connId->getVersion()==3)? '' : 'default_' ).'temp_store='.$_POST['temp_store'].';';
				foreach($query as $req) $res = $this->connId->getResId($req);
				$this->optionView();				
				break;
		}
	}
	
	/**
	* Display Available Option
	*
	* @access public	
	*/
	function optionView(){
		if($tabInfoDb = @stat($this->connId->baseName)){
			if($tabInfoDb['size']<1024) {
				$size = $tabInfoDb['size'].'o';
			} elseif($tabInfoDb['size']< (1024*1024)){
				$size = number_format(($tabInfoDb['size']/1024), 2, ',', '').'Ko';
			} elseif($tabInfoDb['size']< (1024*1024*1024)){
				$size = number_format(($tabInfoDb['size']/(1024*1024)), 2, ',', '').'Mo';			
			}
			$perm = substr(sprintf("%o", fileperms($this->connId->baseName)), 3);
			for($i=0 ; $i<3 ; $i++){
				$currPerm = substr($perm, $i, 1);
				$strPerm = '';
				if($currPerm & 4) $strPerm .= 'r'; else $strPerm .= '-';
				if($currPerm & 2) $strPerm .= 'w'; else $strPerm .= '-';
				if($currPerm & 1) $strPerm .= 'x'; else $strPerm .= '-';
				$tabPerm[$i] = $strPerm;
			}
			$perms = $tabPerm[2].$tabPerm[1].$tabPerm[0];
			$dateModif = date('d-m-Y H:i:s', $tabInfoDb['mtime']);
		}
		$res = $this->connId->getResId('PRAGMA '.(($this->connId->connId->getVersion()==3)? '' : 'default_' ).'synchronous;');
		$tabSynchro = $this->connId->getArray();
		if(isset($tabSynchro[0])) $valSynchro = $tabSynchro[0]['synchronous']; else $valSynchro = "";
		$res = $this->connId->getResId('PRAGMA cache_size;');
		$tabCache = $this->connId->getArray();
		if(isset($tabCache[0])) $valCache = $tabCache[0]['cache_size']; else $valCache = "";
		$res = $this->connId->getResId('PRAGMA '.(($this->connId->connId->getVersion()==3)? '' : 'default_' ).'temp_store;');
		$tabTempStore = $this->connId->getArray();
		if(isset($tabTempStore[0])) $valTempStore = $tabTempStore[0]['temp_store']; else $valTempStore = "";
		if(DEMO_MODE) $dbLocation = '/***/***/'.basename($this->connId->baseName);
		else $dbLocation = $this->connId->baseName;
		$ModifPropOk = (!$GLOBALS['workDb']->isReadOnly() && displayCondition('properties'));
		echo '	<center><table width="80%"><tr><td align="center">';
		echo '	<fieldset><legend>'.$GLOBALS['traduct']->get(178).'</legend>';
		echo '
					<table cellspacing="0" cellpadding="4">
						<tr><td>&nbsp;</td><td>
							<table>
								<tr bgcolor="#e7dfce"><td class="Browse"><span class="infosTitle">'.$GLOBALS['traduct']->get(179).' </span></td><td class="Browse"><span class="infos">'.$dbLocation.'</span></td></tr>
								<tr bgcolor="#f7f3ef"><td class="Browse"><span class="infosTitle">'.$GLOBALS['traduct']->get(180).' </span></td><td class="Browse"><span class="infos">'.$size.'</span></td></tr>
								<tr bgcolor="#e7dfce"><td class="Browse"><span class="infosTitle">'.$GLOBALS['traduct']->get(181).' </span></td><td class="Browse"><span class="infos">'.$perms.'</span></td></tr>
								<tr bgcolor="#f7f3ef"><td class="Browse"><span class="infosTitle">'.$GLOBALS['traduct']->get(182).' </span></td><td class="Browse"><span class="infos">'.$dateModif.'</span></td></tr>
							</table>
						</td></tr>
					</table>';
		echo "\n\t".'</fieldset>';
		echo "\n\t".'<fieldset><legend>'.$GLOBALS['traduct']->get(212,'Maintenance').'</legend>';
		echo '
					<table cellspacing="0" cellpadding="4">
						<tr><td>&nbsp;</td><td>'."\n";

		if($ModifPropOk) echo '		<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=options&amp;option_action=check" class="base" target="main">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(183).'</a><br/><br/>';
		else echo '		&nbsp;&raquo;&nbsp;<span class="tabprop"><i>'.$GLOBALS['traduct']->get(183).'</i></span><br/><br/>';
		if($ModifPropOk) echo '		<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=options&amp;option_action=vacuum" class="base" target="main">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(184).'</a>';
		else echo '		&nbsp;&raquo;&nbsp;<span class="tabprop"><i>'.$GLOBALS['traduct']->get(184).'</i></span>';
		echo '
						</td></tr>
					</table>';
		echo "\n\t".'</fieldset>';

		echo '	<fieldset><legend>'.$GLOBALS['traduct']->get(84).'</legend>';
		echo '  <table align="center" cellpadding="10" cellspacing="5">
				<tr><td colspan=2 align="center">
					<form name="formOption" action="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;action=options&amp;option_action=save" method="POST" target="main">
						<table style="font-size: 11px">
							<tr>
								<td align="right">'.$GLOBALS['traduct']->get(185).' </td>
								<td align="right">'.$GLOBALS['traduct']->get(187).'<input type="radio" name="synchro" value="0"'. (($valSynchro==0)? ' checked="checked"' : '' ).(($ModifPropOk)? '' : ' disabled="disabled"'). '></td>
								<td align="right">'.$GLOBALS['traduct']->get(188).'<input type="radio" name="synchro" value="1"'. (($valSynchro==1)? ' checked="checked"' : '' ).(($ModifPropOk)? "" : ' disabled="disabled"'). '></td>
								<td align="right">'.$GLOBALS['traduct']->get(189).'<input type="radio" name="synchro" value="2"'. (($valSynchro==2)? ' checked="checked"' : '' ).(($ModifPropOk)? '' : ' disabled="disabled"'). '></td>
							</tr>
							<tr>
								<td align="right">'.$GLOBALS['traduct']->get(186).' </td>
								<td colspan="3"><input type="text" class="text" name="cache_size" value="'.$valCache.'" size="5"'.(($ModifPropOk)? '' : ' disabled="disabled"' ).'></td>
							</tr>
							<tr>
								<td align="right">'.$GLOBALS['traduct']->get(193).' </td>
								<td align="right">'.$GLOBALS['traduct']->get(194).'<input type="radio" name="temp_store" value="0"'.(($valTempStore==0)? ' checked="checked"' : '' ).(($ModifPropOk)? '' : " disabled" ).'></td>
								<td align="right">'.$GLOBALS['traduct']->get(195).'<input type="radio" name="temp_store" value="1"'.(($valTempStore==1)? ' checked="checked"' : '' ).(($ModifPropOk)? '' : " disabled" ).'></td>
								<td align="right">'.$GLOBALS['traduct']->get(196).'<input type="radio" name="temp_store" value="2"'.(($valTempStore==2)? ' checked="checked"' : '' ).(($ModifPropOk)? '' : " disabled" ).'></td>
							</tr>
						</table>
						<br>';
		if($ModifPropOk) echo '			<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(200,'Update').'">';
		echo '		</form>
				</td></tr>
				</table>';
		echo '</fieldset></td></tr></table>';
		echo $this->attachDb().'</center>'; 
		
	}
	
	/**
	* Display SQlite error
	*
	* @access public	
	* @param string $queryDisplay Query to display
	* @param resource $res Database connection resource
	*/	
	function _getError($queryDisplay, $res){
		if(!$res){
			$errorMessage .= $GLOBALS['traduct']->get(9).' : '.@$this->connId->connId->getError."\n";
		}
		displayQuery($queryDisplay);
		if(!empty($errorMessage)) displayError($errorMessage);

	}
	
	/**
	* Database attachment management and display
	*
	* @access public	
	*/	
	function attachDb(){
		// save form result Or Del attach database	
		if(isset($_REQUEST['attach_action']) && $_REQUEST['attachId']){
			switch($_REQUEST['attach_action']){
				case 'add':
					$query = 'INSERT INTO attachment (base_id, attach_id) VALUES ('.quotes($GLOBALS['dbsel']).', '.quotes($_REQUEST['attachId']).');';
					break;
				case 'del':
					$query = 'DELETE FROM attachment WHERE id='.quotes($_REQUEST['attachId']).';';
					break;
			}
			$GLOBALS['db']->query($query);
		}
		//echo "<script type=\"text/javascript\">parent.left.location.reload();</script>";
		// display attach database attach and form to add it
		$out = '<table width="80%"><tr><td align="center">';
		$out .= '<fieldset><legend>'.$GLOBALS['traduct']->get(146).'</legend>
					<table class="Browse" cellspacing="0" width="200">
						<thead>
							<tr class="Browse">
								<td colspan=2 align="center" class="tapPropTitle">'.$GLOBALS['traduct']->get(131).'</td>
							</tr>
						</thead>'."\n";
		
		$tabAttach = SQLiteDbConnect::getAttachDb();
		$tabAttachId = array();
		foreach($tabAttach as $attach_id=>$attachInfo){
			$tabAttachId[] = $attach_id;
			$color = ($attach_id%2)? '#f7f3ef':'#e7dfce';
			$out.= '		<tr class="Browse" bgcolor="'.$color.'">
							<td class="Browse">'.$attachInfo['name'].'</td><td class="Browse" align="center"><a href="?dbsel='.$GLOBALS['dbsel'].'&amp;action=options&amp;options_action=attach&amp;attach_action=del&amp;attachId='.$attachInfo['id'].'">'.displayPics("supprime.gif", $GLOBALS['traduct']->get(86)).'</a></td>
						</tr>'."\n";
		}		
		$query = 'SELECT * FROM database';
		$tabDb = $GLOBALS['db']->array_query($query, SQLITE_ASSOC);
		$out.= '			<tr class="Browse">
							<form name="attachDb" method="POST">
							<td style="padding:5px" colspan="2" align="center">
								<select name="attachId">
								<option value="">'.$GLOBALS['traduct']->get(227).'</option>'."\n";
		foreach($tabDb as $dbInfo){
			if(!in_array($dbInfo['id'], $tabAttachId) && ($dbInfo['id']!=$GLOBALS['dbsel'])) {
				$tempDb = $GLOBALS["SQLiteFactory"]->sqliteGetInstance($dbInfo["location"]);	
				if(is_object($tempDb)) {
					$versionNum = $tempDb->getVersion();				
					if($versionNum == $this->connId->connId->getVersion()) {
						$out.= '<option value="'.$dbInfo['id'].'">'.$dbInfo['name'].'</option>';
					}
				}
			}
		}		
		$out.= '						</select>
									<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(43).'">
							</td>
							<input type="hidden" name="dbsel" value="'.$GLOBALS['dbsel'].'">
							<input type="hidden" name="action" value="options">
							<input type="hidden" name="options_action" value="attach">
							<input type="hidden" name="attach_action" value="add">
							</form>
						</tr>';
		$out .= '			</table>
				</fieldset>
			</td></tr></table>';
		return $out;
	}
	
}
?>
