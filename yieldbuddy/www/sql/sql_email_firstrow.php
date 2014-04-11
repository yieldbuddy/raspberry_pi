<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);
$results = $db->query('SELECT *	FROM Email');

$column_to_session_value = array(
    "0" => "smtp_server",
    "1" => "smtp_port",
    "2" => "login_email_address",
    "3" => "email_password_hash",
    "4" => "recipient",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 5){
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
