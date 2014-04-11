<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);
$results = $db->query('SELECT *	FROM Camera');

$column_to_session_value = array(
    "0" => "camera_address",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 1){
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
