<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);
$results = $db->query('SELECT *	FROM Watering_Schedule');

$column_to_session_value = array(
    "0" => "Pump_start_hour",
    "1" => "Pump_start_min",
    "2" => "Pump_start_isAM",
    "3" => "Pump_every_hours",
    "4" => "Pump_every_mins",
    "5" => "Pump_for",
    "6" => "Pump_times",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 7){
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
