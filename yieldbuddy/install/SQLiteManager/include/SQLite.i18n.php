<?php
/**
* Web based SQLite management
* Multilingual management
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: SQLite.i18n.php,v 1.25 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.25 $
*/

class GestionLangue {
	
	/**
	* Array of all text
	* @var array
	* @access private
	*/
	var $tabText;

	/**
	* Character encoding
	* @var string
	* @access private
	*/
	var $encoding;
	
	/**
	* Class constructor
	*
	* @access public
	* @param $tableau array 
	*/
	function __construct($tableau){
		global $currentLangue;
		if(is_array($tableau)){
			$this->tabText = $tableau;
		}

		$this->encoding = $GLOBALS["charset"];
		return;		
	}
	
	/**
	* Get the good text to display
	*
	* @access public
	* @param $index int numero du message
	* @param $default string message par défaut
	*/
	function get($index,$default='No translate'){
		if($index){
			if(isset($this->tabText[$index]) && $this->tabText[$index]){
			  $res = @htmlentities($this->tabText[$index],ENT_NOQUOTES,$this->encoding);
			  $res = str_replace('&lt;','<',$res);
			  $res = str_replace('&gt;','>',$res);
			  $res = str_replace('&amp;','&',$res);
				return $res;
			} else {
				return $default;
			}					
		}
	}	

	/**
	* Get the good text to display without html entities
	*
	* @access public
	* @param $index int numero du message
	* @param $default string message par défaut
	*/
	function getdirect($index,$default='No translate'){
		if($index){
			if(isset($this->tabText[$index]) && $this->tabText[$index]){
			  $res = @html_entity_decode($this->tabText[$index], ENT_NOQUOTES, $this->encoding);
				return $res;
			} else {
				return $default;
			}					
		}
	}	

}
if(isset($_POST['Langue'])) {
	$currentLangue = $_POST['Langue'];	
	setcookie('SQLiteManager_currentLangue',$_POST['Langue'],1719241200,'/');
	$_COOKIE['SQLiteManager_currentLangue'] = $currentLangue = $_POST['Langue'];
	echo "<script type=\"text/javascript\">parent.location='index.php';</script>";
} elseif(isset($_COOKIE['SQLiteManager_currentLangue'])) {
	$currentLangue = $_COOKIE['SQLiteManager_currentLangue'];
} else {
	$lang = '';
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$lang=strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
	}
	if($lang=='fr') $currentLangue = 1;
	else $currentLangue = 2;
}

if(file_exists('./lang/'.$availableLangue[$currentLangue].'.inc.php')){
	include_once './lang/'.$availableLangue[$currentLangue].'.inc.php';
} else {
	include_once './lang/english.inc.php';
}

$traduct = new GestionLangue($TEXT);
?>
