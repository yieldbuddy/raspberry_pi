<?php
/**
* Web based SQLite management
* Show result query with paginate, sort, modify/delete links
* @package SQLiteManager
* @author Frédéric HENNINOT
* @version $Id: browse.php,v 1.45 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.45 $
*/

include_once INCLUDE_LIB.'ParsingQuery.class.php';
include_once INCLUDE_LIB.'sql.class.php';
if(!isset($withForm)) $withForm = true;

if(!isset($DisplayQuery) || empty($DisplayQuery)){
	if($action == 'sql') {
		$displayResult = false;
	}
	if(!empty($table) || !empty($view)) $DisplayQuery = 'SELECT * FROM '.quotes(brackets($table, false).brackets($view, false));
	else $DisplayQuery = '';
} else if(!isset($_FILES)) {
	$DisplayQuery = urldecode($GLOBALS['DisplayQuery']);
} elseif( !empty($_POST['DisplayQuery']) || !empty($_GET['DisplayQuery']) ) {
  $DisplayQuery = SQLiteStripSlashes($DisplayQuery);
}
if(!isset($displayResult)) $displayResult = true;
if(!isset($sql_action)) $sql_action = '';
if( ($sql_action=='explain') && !preg_match('#EXPLAIN#i', $DisplayQuery) ) $DisplayQuery = 'EXPLAIN '.$DisplayQuery;
$SQLiteQuery = new sql($workDb, $DisplayQuery);
if( $sql_action != 'modify'){
	$error = $SQLiteQuery->verify(false);
} else {
	$error = false;
}
if($SQLiteQuery->withReturn && !$error && $displayResult){
	include_once INCLUDE_LIB.'SQLiteToGrid.class.php';
	if(!empty($GLOBALS["table"])) $linkItem = 'table='.$GLOBALS["table"];
	else $linkItem = 'view='.$GLOBALS["view"];

	$accessResult = $SQLiteQuery->checkAccessResult($DisplayQuery);

	$DbGrid = new SQLiteToGrid($workDb->connId, $SQLiteQuery->query, 'Browse', true, BROWSE_NB_RECORD_PAGE, '70%');
	$DbGrid->enableSortStyle(false);
	$DbGrid->setGetVars('?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$table.'&amp;action=browseItem&amp;DisplayQuery='.urlencode($DisplayQuery));
	if($DbGrid->getNbRecord()<=BROWSE_NB_RECORD_PAGE) $DbGrid->disableNavBarre();
	if($accessResult && (!$workDb->isReadOnly() && displayCondition('data'))){
		if(displayCondition('del')) $deleteLink = "<a href=\"main.php?dbsel=".$GLOBALS["dbsel"]."&amp;table=".$accessResult."&amp;action=deleteElement&amp;query=#%QUERY%#&amp;pos=#%POS%#&amp;currentPage=browseItem\" class=\"Browse\" target=\"main\">".displayPics("deleterow.png", $GLOBALS["traduct"]->get(15))."</a>";
		else $deleteLink = displayPics("deleterow_off.png", $GLOBALS["traduct"]->get(15));
		if(displayCondition('data')) $modifyLink = "<a href=\"main.php?dbsel=".$GLOBALS["dbsel"]."&amp;table=".$accessResult."&amp;action=modifyElement&amp;query=#%QUERY%#&amp;pos=#%POS%#&amp;currentPage=browseItem\" class=\"Browse\" target=\"main\">".displayPics("edit.png", $GLOBALS["traduct"]->get(14))."</a>";
		else $modifyLink = displayPics("edit_off.png", $GLOBALS["traduct"]->get(14));
		$DbGrid->addCalcColumn($GLOBALS["traduct"]->get(33),
				"<div class=\"BrowseImages\">".$modifyLink."&nbsp;".$deleteLink."</div>", "center", 0);
	}

	$showTime = '<div class="time" align="center">'.$GLOBALS['traduct']->get(213).' '.$SQLiteQuery->queryTime.' '.$GLOBALS['traduct']->get(214).'</div>';
	if($allFullText) $caption = '<a href="main.php?dbsel='.$GLOBALS["dbsel"].'&amp;'.$linkItem.'&amp;action=browseItem&amp;fullText=0" target="main">'.displayPics("nofulltext.png", $GLOBALS['traduct']->get(225)).'</a>';
	else $caption = '<a href="?dbsel='.$GLOBALS["dbsel"].'&amp;'.$linkItem.'&amp;action=browseItem&amp;fullText=1">'.displayPics("fulltext.png", $GLOBALS['traduct']->get(226)).'</a>';
	if($allHTML) $capHTML = '<a href="main.php?dbsel='.$GLOBALS["dbsel"].'&amp;'.$linkItem.'&amp;action=browseItem&amp;HTMLon=0" target="main">'.displayPics("HTML_on.png", "HTML").'</a>';
	else $capHTML = '<a href="main.php?dbsel='.$GLOBALS["dbsel"].'&amp;'.$linkItem.'&amp;action=browseItem&amp;HTMLon=1" target="main">'.displayPics("HTML_off.png", "Texte").'</a>';

//    $DbGrid->addCaption("top", '<div><div style="float: left">'.$caption.str_repeat('&nbsp;', 3).$capHTML.'</div>'.$showTime.'</div>');
	$capTable = '<div><div style="float: left">'.$caption.str_repeat('&nbsp;', 3).$capHTML.'</div>'.$showTime.'</div>';

    $DbGrid->build();

	if(!isset($noDisplay) || !$noDisplay) displayQuery($DbGrid->getRealQuery());
	if($DbGrid->getNbRecord()) {

    	echo '<table align="center"><tr><td>'.$capTable.'</td></tr><tr><td>';
    	$DbGrid->show();
    	echo '<!-- browse.php -->'."\n";
		echo '<div class="BrowseOptions">';
		if(empty($view) && (!$workDb->isReadOnly() && displayCondition("properties"))){
			echo '<hr width="60%">
					<form name="addView" action="main.php?dbsel='.$GLOBALS['dbsel'].'" method="POST" target="main">
					<table class="BrowseOption"><tr><td>
					&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(97).'
					<input type="text" class="text" name="ViewName" value="" /> '.$GLOBALS['traduct']->get(98).'
					<input class="button" type="submit" value="'.$GLOBALS['traduct']->get(69).'" />
					</td></tr></table>
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="ViewProp" value="'.urlencode($DisplayQuery).'" />
					</form>';
		}
		if($accessResult && (displayCondition('export'))){
			echo '<hr width="60%">';
			echo '	<table class="BrowseOption"><tr><td>
					<a href="main.php?dbsel='.$GLOBALS['dbsel'].'&amp;table='.$GLOBALS['table'].'&amp;queryExport='.urlencode($DisplayQuery).'&amp;action=export" class="Browse">&nbsp;&raquo;&nbsp;'.$GLOBALS['traduct']->get(76).'</a>
					</td></tr></table>';
		}
		echo '</div>';
    	echo '</td></tr></table>';

	}
	if(!$DbGrid->getNbRecord()) $SQLiteQuery->getForm($DbGrid->getRealQuery());
} else {
	if(!$SQLiteQuery->multipleQuery && (!isset($noDisplay) || !$noDisplay)) displayQuery($DisplayQuery, true, $SQLiteQuery->changesLine);
	else $SQLiteQuery->DisplayMultipleResult();
	if(!empty($DisplayQuery) && $error) {
		$withForm = true;
		$errorMessage = "";
		if(is_array($SQLiteQuery->lineError)) $errorMessage = $GLOBALS["traduct"]->get(99)." : ".implode(", ", $SQLiteQuery->lineError)."<br>";
		$errorMessage .= $SQLiteQuery->errorMessage;
		displayError($errorMessage);
	}
	if($withForm && WITH_AUTH && isset($SQLiteManagerAuth) &&  !$SQLiteManagerAuth->getAccess("execSQL")) $withForm = false;
	if($withForm) $SQLiteQuery->getForm($DisplayQuery);
}
?>

</body>
</html>
