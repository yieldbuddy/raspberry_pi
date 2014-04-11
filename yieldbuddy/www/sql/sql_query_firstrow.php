<?php
session_start();
//SET MAXIMUM NUMBER OF ROWS TO QUERY HERE:
$MaximumRows = 1;
echo "Maximum Number of Rows: " . $MaximumRows . "<br></br>";

//Load SQL settings
$sql_address=trim($_SESSION['sql_address']);
$sql_username=trim($_SESSION['sql_username']);
$sql_password=trim($_SESSION['sql_password']);
$sql_database=trim($_SESSION['sql_database']);

//echo "|" . $sql_address . "| |" . $sql_username . "| |" . $sql_password . "| |" . $sql_database . "|<br></br>";

echo "Querying SQL Database...<br></br>";

//record the starting time 
$start=microtime(); 
$start=explode(" ",$start); 
$start=$start[1]+$start[0];

$mysqli = new mysqli($sql_address, $sql_username, $sql_password, $sql_database, 3306);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}


//Time
$Time_query = "SELECT Time FROM Sensors ORDER BY `Time` DESC ";
$Time_result = $mysqli->query($Time_query);     
if (!$Time_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$Time_row = $Time_result->fetch_row();
$Time_row = implode(" ", $Time_row);
echo $Time_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_Time'] = $Time_row;
$Time_result->close();


//pH1
$pH1_query = "SELECT pH1 FROM Sensors ORDER BY `Time` DESC ";
$pH1_result = $mysqli->query($pH1_query);     
if (!$pH1_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$pH1_row = $pH1_result->fetch_row();
$pH1_row = implode(" ", $pH1_row);
echo $pH1_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_pH1'] = $pH1_row;
$pH1_result->close();

//pH2
$pH2_query = "SELECT pH2 FROM Sensors ORDER BY `Time` DESC ";
$pH2_result = $mysqli->query($pH2_query);     
if (!$pH2_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$pH2_row = $pH2_result->fetch_row();
$pH2_row = implode(" ", $pH2_row);
echo $pH2_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_pH2'] = $pH2_row;
$pH2_result->close();

//Temp
$Temp_query = "SELECT Temp FROM Sensors ORDER BY `Time` DESC ";
$Temp_result = $mysqli->query($Temp_query);     
if (!$Temp_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$Temp_row = $Temp_result->fetch_row();
$Temp_row = implode(" ", $Temp_row);
echo $Temp_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_Temp'] = $Temp_row;
$Temp_result->close();

//RH
$RH_query = "SELECT RH FROM Sensors ORDER BY `Time` DESC ";
$RH_result = $mysqli->query($RH_query);     
if (!$RH_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$RH_row = $RH_result->fetch_row();
$RH_row = implode(" ", $RH_row);
echo $RH_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_RH'] = $RH_row;
$RH_result->close();

//TDS1
$TDS1_query = "SELECT TDS1 FROM Sensors ORDER BY `Time` DESC ";
$TDS1_result = $mysqli->query($TDS1_query);     
if (!$TDS1_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$TDS1_row = $TDS1_result->fetch_row();
$TDS1_row = implode(" ", $TDS1_row);
echo $TDS1_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_TDS1'] = $TDS1_row;
$TDS1_result->close();

//TDS2
$TDS2_query = "SELECT TDS2 FROM Sensors ORDER BY `Time` DESC ";
$TDS2_result = $mysqli->query($TDS2_query);     
if (!$TDS2_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$TDS2_row = $TDS2_result->fetch_row();
$TDS2_row = implode(" ", $TDS2_row);
echo $TDS2_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_TDS2'] = $TDS2_row;
$TDS2_result->close();

//CO2
$CO2_query = "SELECT CO2 FROM Sensors ORDER BY `Time` DESC ";
$CO2_result = $mysqli->query($CO2_query);     
if (!$CO2_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$CO2_row = $CO2_result->fetch_row();
$CO2_row = implode(" ", $CO2_row);
echo $CO2_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_CO2'] = $CO2_row;
$CO2_result->close();

//Light
$Light_query = "SELECT Light FROM Sensors ORDER BY `Time` DESC ";
$Light_result = $mysqli->query($Light_query);     
if (!$Light_result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}

$Light_row = $Light_result->fetch_row();
$Light_row = implode(" ", $Light_row);
echo $Light_row;
echo "&nbsp;&nbsp;&nbsp;";
$_SESSION['Sensors_Light'] = $Light_row;
$Light_result->close();



/*
$i=0;
while($i < sizeof($Time_rows) and $i < 100) {
  echo $i . ") ";
  echo implode(" ", $Time_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $pH1_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $pH2_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $Temp_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $RH_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $TDS1_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $TDS2_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $CO2_rows[$i]) . " &nbsp;&nbsp;";  
  echo implode(" ", $Light_rows[$i]) . " " . "<br></br>";
  $i=$i+1;
}
*/

//echo $mysqli->host_info . "<br></br>";

$mysqli->close();

//record the ending time 
$end=microtime(); 
$end=explode(" ",$end); 
$end=$end[1]+$end[0]; 

printf("<br></br>Query took %f seconds.",$end-$start);

?>
