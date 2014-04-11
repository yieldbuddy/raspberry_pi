<?php
session_start();

$sensorname=$_GET["sensorname"];
$alarmname=$_GET["alarmname"];
$alarmvalue=$_GET["alarmvalue"];

alarm_sql($sensorname,$alarmname,$alarmvalue);

function alarm_sql($sensorname,$alarmname,$alarmvalue) {
	include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
	$db->busyTimeout(5000);

	$alarmsql_query = "UPDATE `" . $sensorname . "` SET `" . $alarmname . "` = " . $alarmvalue;
	$alarmsql_result = $db->query($alarmsql_query);     
	if (!$alarmsql_result) {
	  printf("Query: " + $alarmsql_query + " ");
	  printf("Query failed: %s\n", $db->error);
	  exit;
	}

}
?>
