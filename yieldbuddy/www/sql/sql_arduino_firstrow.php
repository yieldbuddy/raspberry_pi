<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);
$results = $db->query('SELECT *	FROM Arduino');

$column_to_session_value = array(
    "0" => "Arduino_Time",
    "1" => "Arduino_Month",
    "2" => "Arduino_Day",
    "3" => "Arduino_Year",
    "4" => "Arduino_Hour",
    "5" => "Arduino_Minute",
    "6" => "Arduino_Second",
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
