<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);
//pH1
$results = $db->query('SELECT *	FROM pH1');

$column_to_session_value = array(
    "3" => "pH1_Low_Alarm",
    "4" => "pH1_Low_Time",
    "5" => "pH1_High_Alarm",
    "6" => "pH1_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=3;
	while($i < 7){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//pH2
$results = $db->query('SELECT *	FROM pH2');

$column_to_session_value = array(
    "3" => "pH2_Low_Alarm",
    "4" => "pH2_Low_Time",
    "5" => "pH2_High_Alarm",
    "6" => "pH2_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=3;
	while($i < 7){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//Temp
$results = $db->query('SELECT *	FROM Temp');

$column_to_session_value = array(
    "7" => "Temp_Low_Alarm",
    "8" => "Temp_Low_Time",
    "9" => "Temp_High_Alarm",
    "10" => "Temp_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=7;
	while($i < 11){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//RH
$results = $db->query('SELECT *	FROM RH');

$column_to_session_value = array(
    "7" => "RH_Low_Alarm",
    "8" => "RH_Low_Time",
    "9" => "RH_High_Alarm",
    "10" => "RH_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=7;
	while($i < 11){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//TDS1
$results = $db->query('SELECT *	FROM TDS1');

$column_to_session_value = array(
    "6" => "TDS1_Low_Alarm",
    "7" => "TDS1_Low_Time",
    "8" => "TDS1_High_Alarm",
    "9" => "TDS1_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=6;
	while($i < 10){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//TDS2
$results = $db->query('SELECT *	FROM TDS2');

$column_to_session_value = array(
    "6" => "TDS2_Low_Alarm",
    "7" => "TDS2_Low_Time",
    "8" => "TDS2_High_Alarm",
    "9" => "TDS2_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=6;
	while($i < 10){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//TDS2
$results = $db->query('SELECT *	FROM TDS2');

$column_to_session_value = array(
    "6" => "TDS2_Low_Alarm",
    "7" => "TDS2_Low_Time",
    "8" => "TDS2_High_Alarm",
    "9" => "TDS2_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=6;
	while($i < 10){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//CO2
$results = $db->query('SELECT *	FROM CO2');

$column_to_session_value = array(
    "6" => "CO2_Low_Alarm",
    "7" => "CO2_Low_Time",
    "8" => "CO2_High_Alarm",
    "9" => "CO2_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=6;
	while($i < 10){
//	echo "<p></p>";
//	echo $column_to_session_value[$i];
//	echo ": ";
//	echo $row[$i];
	$_SESSION[$column_to_session_value[$i]] = $row[$i];
	$i=$i+1;
	}
}

//Light
$results = $db->query('SELECT *	FROM Light');

$column_to_session_value = array(
    "3" => "Light_Low_Alarm",
    "4" => "Light_Low_Time",
    "5" => "Light_High_Alarm",
    "6" => "Light_High_Time",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=3;
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
