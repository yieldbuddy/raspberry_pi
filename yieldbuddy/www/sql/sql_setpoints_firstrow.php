<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/new_SQLite3.php');
$db->busyTimeout(2000);

//pH1
$results = $db->query('SELECT *	FROM pH1');

$column_to_session_value = array(
    "0" => "pH1Value_Low",
    "1" => "pH1Value_High",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 2){
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
    "0" => "pH2Value_Low",
    "1" => "pH2Value_High",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 2){
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
    "0" => "TempValue_Low",
    "1" => "TempValue_High",
    "2" => "Heater_ON",
    "3" => "Heater_OFF",
    "4" => "AC_ON",
    "5" => "AC_OFF",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 6){
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
    "0" => "RHValue_Low",
    "1" => "RHValue_High",
    "2" => "Humidifier_ON",
    "3" => "Humidifier_OFF",
    "4" => "Dehumidifier_ON",
    "5" => "Dehumidifier_OFF",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 6){
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
    "0" => "TDS1Value_Low",
    "1" => "TDS1Value_High",
    "2" => "NutePump1_ON",
    "3" => "NutePump1_OFF",
    "4" => "MixPump1_Enabled",
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

//TDS2
$results = $db->query('SELECT *	FROM TDS2');

$column_to_session_value = array(
    "0" => "TDS2Value_Low",
    "1" => "TDS2Value_High",
    "2" => "NutePump2_ON",
    "3" => "NutePump2_OFF",
    "4" => "MixPump2_Enabled",
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

//CO2
$results = $db->query('SELECT *	FROM CO2');

$column_to_session_value = array(
    "0" => "CO2Value_Low",
    "1" => "CO2Value_High",
    "2" => "CO2_ON",
    "3" => "CO2_OFF",
    "4" => "CO2_Enabled",
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

//Light
$results = $db->query('SELECT *	FROM CO2');

$column_to_session_value = array(
    "0" => "LightValue_Low",
    "1" => "LightValue_High",
);

while ($row = $results->fetchArray()) {
//	var_dump($row);
	
	$i=0;
	while($i < 2){
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
