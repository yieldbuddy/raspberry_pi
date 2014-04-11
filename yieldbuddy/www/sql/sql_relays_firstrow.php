<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(4000);
$results = $db->query('SELECT *	FROM Relays');

$column_to_session_value = array(
    "0" => "Relay1",
    "1" => "Relay1_isAuto",
    "2" => "Relay2",
    "3" => "Relay2_isAuto",
    "4" => "Relay3",
    "5" => "Relay3_isAuto",
    "6" => "Relay4",
    "7" => "Relay4_isAuto",
    "8" => "Relay5",
    "9" => "Relay5_isAuto",
    "10" => "Relay6",
    "11" => "Relay6_isAuto",);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 12){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

$db->close();
?>
