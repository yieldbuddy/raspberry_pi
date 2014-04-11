<?php
/**
* Web based SQLite management
* Class for manage user authentification
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id
*/

class SQliteAuth {

	/**
	* user identification
	*
	* @access private
	* @var integer
	*/
	var $user;

	/**
	* user information
	*
	*/
	var $userInformation;

	/**
	* Class constructor
	*
	* @access public
	*/
	function __construct(){
		if($GLOBALS['action'] == 'logout') {
			$_SESSION['SQLiteManagerConnected'] = false;
			unset($_SESSION['SQLiteManagerUserId']);
			$_SESSION['oldUser'] = $_SERVER['PHP_AUTH_USER'];
			session_write_close();
			echo "<script type=\"text/javascript\">parent.location='index.php';</script>";
			exit;
		}
		if(!isset($_SESSION['SQLiteManagerConnected']) || !$_SESSION['SQLiteManagerConnected']){
			if((isset($_SESSION['oldUser']) && ($_SESSION['oldUser'] == $_SERVER['PHP_AUTH_USER'])) || !isset($_SERVER['PHP_AUTH_USER'])) {
				unset($_SESSION['oldUser']);
				$this->authenticate();
			} else {
				$this->checkExistTable();
				$this->userInformation = $this->getAuthParam();
				$this->user = $_SESSION['SQLiteManagerUserId'] = $this->userInformation['user_id'];
				$_SESSION['SQLiteManagerConnected'] = true;
			}
		} else {
			$this->userInformation = $this->getAuthParam();
			$this->user = $_SESSION['SQLiteManagerUserId'] = $this->userInformation['user_id'];
		}
	}


	/**
	* get user connected information
	*
	* @access public
	*/
	function getAuthParam(){
		if(isset($_SERVER['PHP_AUTH_USER'])) $login = $_SERVER['PHP_AUTH_USER'];
		else $login = '';
		if(isset($_SERVER['PHP_AUTH_PW'])) $passwd = $_SERVER['PHP_AUTH_PW'];
		else $passwd = '';
		$query = '	SELECT user_id, user_name, user_passwd, del, empty, export, data, execSQL, properties, groupe_name, groupe_id
					FROM users , groupes
					WHERE user_groupe_id = groupe_id
						AND user_login='.quotes($login);
		$infoUser = $GLOBALS["db"]->array_query($query);
		if(empty($infoUser)) {
			$_SESSION['SQLiteManagerConnected'] = false;
			unset($_SESSION['SQLiteManagerUserId']);
			$_SESSION['oldUser'] = $_SERVER['PHP_AUTH_USER'];
			displayError($GLOBALS['traduct']->get(148));
			exit;
		} else {
			$passwdOk = false;
			if(count($infoUser)>1) {
				foreach($infoUser as $infoNum=>$infoOneUser){
					if($infoOneUser['user_passwd'] == md5($passwd)){
						$numUser = $infoNum;
						$passwdOk = true;
					}
				}
			} elseif($infoUser[0]['user_passwd'] == md5($passwd)) $passwdOk = true;
			if(!$passwdOk) {
				$_SESSION['oldUser'] = $_SERVER['PHP_AUTH_USER'];
				displayError($GLOBALS['traduct']->get(149));
				exit;
			}
		}
		if(!isset($numUser)) $numUser = 0;
		return $infoUser[$numUser];
	}

	/**
	* Send HTTP authentification FORM
	*
	* @access public
	*/
	function authenticate(){
		header('WWW-Authenticate: Basic realm="SQLiteManager"');
    	header('HTTP/1.0 401 Unauthorized');
		displayError($GLOBALS['traduct']->get(147));
		exit;
	}

	/**
	* upgrade config database if not exist table 'users' and 'groupes'
	*
	* @access private
	*/
	function checkExistTable(){
		$existTables = $GLOBALS['db']->array_query("SELECT name FROM sqlite_master WHERE type='table' AND (name='users' OR name='groupes');", SQLITE_ASSOC);
		if(empty($existTables) || (count($existTables)!=2)) {
			// create table for attachment management
			$query[] = "CREATE TABLE users ( user_id INTEGER PRIMARY KEY, user_groupe_id INTEGER, user_name VARCHAR(50), user_login VARCHAR(50) , user_passwd VARCHAR(32) );";
			$query[] = "INSERT INTO users VALUES ('1', '1', 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3');";
			$query[] = "INSERT INTO users VALUES ('2', '2', 'data', 'data', '8d777f385d3dfec8815d20f7496026dc');";
			$query[] = "INSERT INTO users VALUES ('3', '3', 'guest', 'guest', '084e0343a0486ff05530df6c705c8bb4');";
			$query[] = "CREATE TABLE groupes ( groupe_id INTEGER PRIMARY KEY, groupe_name VARCHAR(50), properties TINYINT , execSQL TINYINT , data TINYINT , export TINYINT , empty TINYINT , del TINYINT );";
			$query[] = "INSERT INTO groupes VALUES ('1', 'Admin', '1', '1', '1', '1', '1', '1');";
			$query[] = "INSERT INTO groupes VALUES ('2', 'datamanager', '0', '0', '1', '1', '0', '0');";
			$query[] = "INSERT INTO groupes VALUES ('3', 'user', '0', '0', '0', '0', '0', '0');";
			foreach($query as $req) $GLOBALS["db"]->query($req);
		}
		return;
	}

	/**
	* get groupe_id
	*
	* @access public
	*/
	function getGroupeId(){
		if(is_array($this->userInformation) && !empty($this->userInformation))
			return $this->userInformation['groupe_id'];
	}

	/**
	* return true if 'Admin'
	*
	* @access public
	*/
	function isAdmin(){
		if(is_array($this->userInformation) && !empty($this->userInformation)) {
			if($this->userInformation['groupe_id']==1) return true;
			else return false;
		}
	}

	/**
	* Return acces controle for module
	*
	* @access public
	* @param string $module module name
	*/
	function getAccess($module){
		if(is_array($this->userInformation) && !empty($this->userInformation))
			if(isset($this->userInformation[$module]))
			    return $this->userInformation[$module];
			else
			    return false;
	}

	/**
	* Manage Groupe and user
	*
	* @access public
	*/
	function manageAuth(){
		if(!isset($GLOBALS['auth_action'])) $GLOBALS['auth_action'] = '';
		echo '<h2>'.$GLOBALS['traduct']->get(190).'</h2>';
		switch($GLOBALS['auth_action']){
			case '':
			default:
			case 'passwdUser':
				$this->viewPrivileges();
				break;
			case 'modifyUser':
			case 'addUser':
				$this->viewPrivileges(true);
				break;
			case 'deleteUser':
				if($_REQUEST['user']!=1) $GLOBALS['db']->query('DELETE FROM users WHERE user_id='.$_REQUEST['user']);
				$this->viewPrivileges();
				break;
			case 'savePasswd':
				break;
			case 'modifyGroupe':
			case 'addGroupe':
				$this->viewPrivileges(false, true);
				break;
			case 'deleteGroupe':
				if($_REQUEST['groupe']!=1) $GLOBALS['db']->query('DELETE FROM groupes WHERE groupe_id='.$_REQUEST['groupe']);
				$this->viewPrivileges();
				break;
			case 'saveUser';
				if(!empty($_POST['name']) && !empty($_POST['login']) && !empty($_POST['groupe_id'])){
					if(isset($_REQUEST['user']) && !empty($_REQUEST['user'])){
						$query = 'UPDATE users SET user_groupe_id='.$_POST['groupe_id'].', user_name='.quotes($_POST['name']).', user_login='.quotes($_POST['login']).' WHERE user_id='.$_POST['user'];
					} else {
						$query = 'INSERT INTO users (user_name, user_login, user_groupe_id, user_passwd) VALUES ('.quotes($_POST['name']).', '.quotes($_POST['login']).', '.$_POST["groupe_id"].', '.quotes(md5('')).');';
					}
					if(!empty($query)) $GLOBALS['db']->query($query);
				}
				$this->viewPrivileges();
				break;
			case 'saveGroupe':
				if(!empty($_POST['groupe_name'])){
					if(isset($_REQUEST['groupe']) && !empty($_REQUEST['groupe'])){
						$query = '	UPDATE groupes ' .
								'	SET 	groupe_name='.quotes($_POST['groupe_name']).',' .
								' 			properties='.$_POST['properties'].', ' .
								'			execSQL='.$_POST['execSQL'].', ' .
								'			data='.$_POST['data'].', ' .
								'			export='.$_POST['export'].', ' .
								'			empty='.$_POST['empty'].', ' .
								'			del='.$_POST['del'].
								' 	WHERE groupe_id='.$_REQUEST['groupe'];
					} else {
						$query = 'INSERT INTO groupes (groupe_name, properties, execSQL, data, export, empty, del) '.
                     'VALUES ('.quotes($_POST['groupe_name']).', '.quotes($_POST['properties']).', '.quotes($_POST['execSQL']).', '.quotes($_POST['data']).', '.quotes($_POST['export']).', '.quotes($_POST['empty']).', '.quotes($_POST['del']).')';
					}
					if(!empty($query)) {
						$GLOBALS['db']->query($query);
					}
				}
				$this->viewPrivileges();
				break;
		}
	}

	/**
	* View all privileges information
	*
	* @access public
	*/
	function viewPrivileges($withFormUser=false, $withFormGroupe=false){
		$query = '	SELECT user_id, user_name AS '.quotes($GLOBALS['traduct']->get(163)).',
						user_login AS '.quotes($GLOBALS['traduct']->get(164)).',
						groupe_name AS '.quotes($GLOBALS['traduct']->get(165)).'
					FROM users, groupes WHERE user_groupe_id=groupe_id;';
		include_once INCLUDE_LIB.'SQLiteToGrid.class.php';
		$tabUser = new SQLiteToGrid($GLOBALS['db'], $query, 'PrivUser', true, 10, '95%');
		$tabUser->enableSortStyle(false);
		$tabUser->hideColumn(0);
		$tabUser->setGetVars('?action=auth');
		if($tabUser->getNbRecord()<=10) $tabUser->disableNavBarre();
		$tabUser->addCalcColumn($GLOBALS['traduct']->get(33), '	<a href="?action=auth&amp;auth_action=modifyUser&amp;user=#%0%#" class="Browse">'.displayPics('edit.png', $GLOBALS['traduct']->get(14)).'</a>&nbsp;
											<a href="?action=auth&amp;auth_action=deleteUser&amp;user=#%0%#" class="Browse">'.displayPics('edittrash.png', $GLOBALS['traduct']->get(15)).'</a>&nbsp;
											<a href="?action=auth&amp;auth_action=passwdUser&amp;user=#%0%#" class="Browse">'.displayPics('encrypted.png', $GLOBALS['traduct']->get(157)).'</a>&nbsp;', 'center', 999);
		$tabUser->addCaption('bottom', '<a href="?action=auth&amp;auth_action=addUser" class="Browse">'.$GLOBALS['traduct']->get(159).'</a>');
		$tabUser->disableOnClick();
		$tabUser->build();

		// ------------------------------------------------------------------------
		$query = 'SELECT groupe_id, groupe_name AS '.quotes($GLOBALS['traduct']->get(163)).',
						CASE properties WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(61)).',
						CASE execSQL WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(166)).',
						CASE data WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(167)).',
						CASE export WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(168)).',
						CASE empty WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(169)).',
						CASE del WHEN 1 THEN '.quotes($GLOBALS['traduct']->get(191)).' ELSE '.quotes($GLOBALS['traduct']->get(192)).' END AS '.quotes($GLOBALS['traduct']->get(170)).'
					FROM groupes;';
		include_once INCLUDE_LIB.'SQLiteToGrid.class.php';
		$tabGroupe = new SQLiteToGrid($GLOBALS['db'], $query, 'PrivGroupe', true, 10, '95%');
		$tabGroupe->enableSortStyle(false);
		$tabGroupe->hideColumn(0);
		$tabGroupe->setGetVars('?action=auth');
		if($tabGroupe->getNbRecord()<=10) $tabGroupe->disableNavBarre();
		$tabGroupe->addCalcColumn($GLOBALS['traduct']->get(33), '	<a href="?action=auth&amp;auth_action=modifyGroupe&amp;groupe=#%0%#" class="Browse">'.displayPics('edit.png', $GLOBALS['traduct']->get(14)).'</a>&nbsp;
											<a href="?action=auth&amp;auth_action=deleteGroupe&amp;groupe=#%0%#" class="Browse">'.displayPics('edittrash.png', $GLOBALS['traduct']->get(15)).'</a>&nbsp;', 'center', 999);
		$tabGroupe->addCaption('bottom', '<a href="?action=auth&amp;auth_action=addGroupe" class="Browse">'.$GLOBALS['traduct']->get(160).'</a>');
		$tabGroupe->disableOnClick();
		$tabGroupe->build();

		echo '<table align="center" class="Browse"><tr><td align="center" valign="top">';
		echo '<div class="Rights"><div style="text-align: center;">'.$GLOBALS['traduct']->get(161).'</div>';
		$tabUser->show();
		if($withFormUser) {
			echo '<hr style="border: 1px dashed black; width: 90%;">';
			$this->formUser();
		}
		if(isset($_REQUEST['auth_action']) && ($_REQUEST['auth_action'] == 'passwdUser')) {
			echo '<hr style="border: 1px dashed black; width: 90%;">';
			$this->changePasswd();
		}
		echo '</div></td>';
		echo '<td align="center" valign="top">';
		echo '<div class="Rights"><div align="center">'.$GLOBALS['traduct']->get(162).'</div>';
		$tabGroupe->show();
		if($withFormGroupe){
			echo '<hr style="border: 1px dashed black; width: 90%;">';
			$this->formGroupe();
		}
		echo '</div></td></tr></table>';

	}

	/**
	* Get user's information
	*
	* @access public
	* @param int $user user ID
	* @return array
	*/
	function getUserInfo($user){
		if(isset($_POST) && isset($_POST["user"])){
			$out[0]["user_name"] 		= $_POST["user_name"];
			$out[0]["user_login"] 		= $_POST["user_login"];
			$out[0]["user_groupe_id"] 	= $_POST["user_groupe_id"];
			return $out;
		} else {
			$query = "SELECT user_name, user_login, user_groupe_id FROM users WHERE user_id=".$user;
			$out = $GLOBALS["db"]->array_query($query);
			return $out[0];
		}
	}

	/**
	* Get groupe's information
	*
	* @access public
	* @param int $group groupe_id
	* @return array
	*/
	function getGroupeInfo($group){
		$query = "SELECT * FROM groupes WHERE groupe_id=".$group;
		$out = $GLOBALS["db"]->array_query($query);
		return $out[0];
	}

	/**
	* Display user form
	*
	* @access private
	*/
	function formUser(){
		if(isset($_REQUEST["user"])) $dataUser = $this->getUserInfo($_REQUEST["user"]);
		$groupeList = $GLOBALS["db"]->array_query("SELECT groupe_id, groupe_name FROM groupes");
		foreach($groupeList as $groupe) $dataGroupe[$groupe["groupe_id"]] = $groupe["groupe_name"];
		echo "<form name='user' method='POST' action='main.php' target='main'>
				<table style='font-size: 10px'>
					<tr><td>".$GLOBALS["traduct"]->get(163)."</td><td><input type='text' class='text' name='name' value='".((!empty($dataUser))? $dataUser["user_name"] : "" )."'></td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(164)."</td><td><input type='text' class='text' name='login' value='".((!empty($dataUser))? $dataUser["user_login"] : "" )."'></td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(165)."</td><td>".createSelect($dataGroupe, "groupe_id", ((!empty($dataUser))? $dataUser["user_groupe_id"] : "" ))."</td></tr>
					<tr><td colspan=2 align='center'><input class='button' type='submit' value='".$GLOBALS["traduct"]->get(51)."'></td>
					</table>
				<input type='hidden' name='action' value='".$GLOBALS["action"]."'>
				<input type='hidden' name='user' value='".((isset($GLOBALS["user"]))? $GLOBALS["user"] : "" )."'>
				<input type='hidden' name='auth_action' value='saveUser'>
				</form>";

	}

	/**
	* Display Groupe formGroupe
	*
	* @access public
	*/
	function formGroupe(){
		if(isset($_REQUEST["groupe"])) $dataGroupe = $this->getGroupeInfo($_REQUEST["groupe"]);
		else $dataGroupe = array();
		if(isset($dataGroupe["groupe_name"])) $groupeName = $dataGroupe["groupe_name"];
		else $groupeName = "";
		if(!isset($dataGroupe["properties"])){
			$dataGroupe["properties"] = $dataGroupe["execSQL"] = $dataGroupe["data"] = $dataGroupe["export"] = $dataGroupe["empty"] = $dataGroupe["del"] = 0;
		}
		echo "<form name='groupe' method='POST' action='main.php' target='main'>
				<table style='font-size: 10px'>
					<tr><td>".$GLOBALS["traduct"]->get(163)."</td><td><input type='text' class='text' name='groupe_name' value='".$groupeName."'></td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(61)."</td><td><input type='radio' name='properties' value=1".(($dataGroupe["properties"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='properties' value=0".((!$dataGroupe["properties"])? " checked" : "" )."> Non</td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(166)."</td><td><input type='radio' name='execSQL' value=1".(($dataGroupe["execSQL"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='execSQL' value=0".((!$dataGroupe["execSQL"])? " checked" : "" )."> Non</td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(167)."</td><td><input type='radio' name='data' value=1".(($dataGroupe["data"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='data' value=0".((!$dataGroupe["data"])? " checked" : "" )."> Non</td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(168)."</td><td><input type='radio' name='export' value=1".(($dataGroupe["export"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='export' value=0".((!$dataGroupe["export"])? " checked" : "" )."> Non</td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(169)."</td><td><input type='radio' name='empty' value=1".(($dataGroupe["empty"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='empty' value=0".((!$dataGroupe["empty"])? " checked" : "" )."> Non</td></tr>
					<tr><td>".$GLOBALS["traduct"]->get(170)."</td><td><input type='radio' name='del' value=1".(($dataGroupe["del"])? " checked" : "" )."> Oui".str_repeat("&nbsp;", 5)."<input type='radio' name='del' value=0".((!$dataGroupe["del"])? " checked" : "" )."> Non</td></tr>
					<tr><td colspan=2 align='center'><input class='button' type='submit' value='".$GLOBALS["traduct"]->get(51)."'></td>
					</table>
				<input type='hidden' name='action' value='".$GLOBALS["action"]."'>
				<input type='hidden' name='groupe_id' value='".((isset($GLOBALS["groupe"]))? $GLOBALS["groupe"] : "" )."'>
				<input type='hidden' name='auth_action' value='saveGroupe'>
				</form>";

	}

	/**
	* change password form
	*/
	function changePasswd(){
		$error = false;
		$err_message = "";
		if(isset($GLOBALS["passwd_action"]) && ($GLOBALS["passwd_action"] == "save")){
			$query = "SELECT user_passwd FROM users WHERE user_id=".$_REQUEST["user"].";";
			$GLOBALS['db']->query($query);
			$passCurrent = $GLOBALS['db']->fetch_single();
			if($passCurrent != md5($_POST["old"])){
				$error = true;
				$err_message = $GLOBALS["traduct"]->get(171);
			} else if($_POST["pass"] != $_POST["confirm"]){
				$error = true;
				$err_message = $GLOBALS["traduct"]->get(172);
			}
			if(!$error){
				$query = "UPDATE users SET user_passwd='".md5($_POST["pass"])."' WHERE user_id=".$_REQUEST["user"].";";
				$GLOBALS['db']->query($query);
				echo '<div class="Rights" style="margin: 5px; text-align: center">'.$GLOBALS["traduct"]->get(173);
				if(!isset($_REQUEST["auth_action"])) echo "<br><a href=\"index.php?action=logout\" target='_parent' class='Browse'>".$GLOBALS["traduct"]->get(174)."</a>";
				echo "</div>";
			}
		}

		if($error || !isset($GLOBALS["passwd_action"]) || ($GLOBALS["passwd_action"]=="")){
			echo "<form name='passwd' method=POST action='main.php' target='main'>";
			echo "<table class='tabProp' style='border: 1px solid blue; margin: 2px'>";
			echo "<tr><td colspan=2 align='center'>".$GLOBALS["traduct"]->get(157)."</td></tr>";
			if($error){
				echo "<tr><td colspan=2 align='center'><div width=80% style='border: 1px solid red'>".$err_message."</div></td></tr>";
			}
			echo "<tr><td align='right' style='white-space: nowrap'>".$GLOBALS["traduct"]->get(175)."</td><td>&nbsp;<input type='password' class='text' name='old' size=10></td></tr>";
			echo "<tr><td align='right' style='white-space: nowrap'>".$GLOBALS["traduct"]->get(176)."</td><td>&nbsp;<input type='password' class='text' name='pass' size=10></td></tr>";
			echo "<tr><td align='right' style='white-space: nowrap'>".$GLOBALS["traduct"]->get(177)."</td><td>&nbsp;<input type='password' class='text' name='confirm' size=10></td></tr>";
			echo "<tr><td colspan=2 align='center'><input class='button' type='submit' value='".$GLOBALS["traduct"]->get(51)."'></td></tr>";
			echo "</table>";
			echo "<input type='hidden' name='action' value='".$GLOBALS["action"]."'>";
			echo "<input type='hidden' name='user' value='".((isset($_REQUEST["user"]))? $_REQUEST["user"] : $_SESSION["SQLiteManagerUserId"] )."'>";
			echo "<input type='hidden' name='passwd_action' value='save'>";
			if(isset($GLOBALS["auth_action"])) echo "<input type='hidden' name='auth_action' value='".$GLOBALS["auth_action"]."'>";
			echo "</form>";
		}
	}
}
?>
