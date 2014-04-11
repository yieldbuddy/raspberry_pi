<?php
session_start();
if(!file_exists('users/' . $_SESSION['username'] . '.xml')){
	header('Location: index.php');
	die;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="SHORTCUT ICON"
       HREF="/yieldbuddy/www/img/favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sending Command</title>
<style type="text/css">
body {
	background-image: url(img/background.png);
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #CCC;
	font-weight: bold;
	position: relative;
}
</style>
</head>

<body>
<p><img src="img/banner.png" width="383" height="77" /></p>
<p>
      <?php
 	$sec = "2";  //Default Refresh Time
	session_start();
	$command=$_GET["command"]; 	
	$submit=$_GET["submit"]; 	

	echo "Submit:  " .$submit. "<br>";
	if ($submit == "Set Arduino's Time to Raspberry Pi's Time"){
	$command="setdate," .date("m"). "," .date("d"). "," .date("Y"). "," .date("H"). "," .date("i"). "," .date("s");
	}
	if ($submit == "Set Raspberry Pi's Time to Arduino's Time"){
	$command = "Set Raspberry Pi's Time to Arduino's Time";
	}
		
	if ($submit == "Set Arduino"){
	$Arduino_month = $_GET["Arduino_month"];
	$Arduino_day = $_GET["Arduino_day"];
	$Arduino_year = $_GET["Arduino_year"];
	$Arduino_hour = $_GET["Arduino_hour"];
	$Arduino_min = $_GET["Arduino_min"];
	$Arduino_sec = $_GET["Arduino_sec"];
	$command="setdate," .$Arduino_month. "," .$Arduino_day. "," .$Arduino_year. "," .$Arduino_hour. "," .$Arduino_min. "," .$Arduino_sec;
	}
	if ($submit == "Set Raspberry Pi"){
	$Rasp_month = $_GET["Rasp_month"];
	$Rasp_day = $_GET["Rasp_day"];
	$Rasp_year = $_GET["Rasp_year"];
	$Rasp_hour = $_GET["Rasp_hour"];
	$Rasp_min = $_GET["Rasp_min"];
	$Rasp_sec = $_GET["Rasp_sec"];
	$command="setraspberrypi," .$Rasp_month. "," .$Rasp_day. "," .$Rasp_year. "," .$Rasp_hour. "," .$Rasp_min. "," .$Rasp_sec;
	}
	if ($submit == "Save Light Schedule"){
	$Light_Mode_INT = $_GET["Light_Mode_INT"];
	$Light_ON_hour = $_GET["Light_ON_hour"];
	$Light_ON_min = $_GET["Light_ON_min"];
	$Light_OFF_hour = $_GET["Light_OFF_hour"];
	$Light_OFF_min = $_GET["Light_OFF_min"];
	$command="setlightschedule," .$Light_ON_hour. "," .$Light_ON_min. "," .$Light_OFF_hour. "," .$Light_OFF_min;
	$sec = "4";  //Increase Refresh Time to allow changes
	}
	if ($submit == "Save Watering Schedule"){
	$Pump_start_hour = $_GET["Pump_start_hour"];
	$Pump_start_min = $_GET["Pump_start_min"];
	$Pump_every_hours = $_GET["Pump_every_hours"];
	$Pump_every_mins= $_GET["Pump_every_mins"];
	$Pump_for = $_GET["Pump_for"];
	$Pump_times = $_GET["Pump_times"];
	$command="setwateringschedule," .$Pump_start_hour. "," .$Pump_start_min. "," .$Pump_every_hours. "," .$Pump_every_mins. "," .$Pump_for. "," .$Pump_times;
	$sec = "5";  //Increase Refresh Time to allow changes
	}
	
	if ($submit == "Restore Defaults"){
		$command="restoredefaults";
	}
	
	if ($submit == "Save Email Settings"){
		$smtp_server = $_GET["smtp_server"];
		$smtp_port = $_GET["smtp_port"];
		$login_email = $_GET["login_email"];
		$login_email_password = $_GET["login_email_password"];
		$recipient_email = $_GET["recipient_email"]; 
		$command="saveemailsettings," .$login_email. "," .$login_email_password. "," . $recipient_email. "," . $smtp_server. "," .$smtp_port;
	}
	
	if ($submit == "Save Camera Settings"){
		$camera_address = $_GET["camera_address"];
		
		session_start();

		$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/yieldbuddy.sqlite3');
		$db->busyTimeout(4000);
		$alarmsql_query = "UPDATE Camera SET connectback_address='" . $camera_address ."'";
		$query = $db->exec($alarmsql_query);
		if (!$query) {
			echo("Error saving changes: '$error'");
		}
	}
	
	if ($submit == "Save pH1 Settings"){
	$pH1Value_Low = $_GET["pH1Value_Low"];
	$pH1Value_High = $_GET["pH1Value_High"];
	$command="setpH1," .$pH1Value_Low. "," .$pH1Value_High;
	}
	
	if ($submit == "Save pH2 Settings"){
	$pH2Value_Low = $_GET["pH2Value_Low"];
	$pH2Value_High = $_GET["pH2Value_High"];
	$command="setpH2," .$pH2Value_Low. "," .$pH2Value_High;
	}
	
	if ($submit == "Save Temp Settings"){
	$TempValue_Low = $_GET["TempValue_Low"];
	$TempValue_High = $_GET["TempValue_High"];
	$Heater_ON = $_GET["Heater_ON"];
	$Heater_OFF = $_GET["Heater_OFF"];
	$AC_ON= $_GET["AC_ON"];
	$AC_OFF = $_GET["AC_OFF"];		
	$command="setTemp," .$TempValue_Low. "," .$TempValue_High. "," .$Heater_ON. "," .$Heater_OFF. "," .$AC_ON. "," .$AC_OFF;
	}
	
	if ($submit == "Save RH Settings"){
	$RHValue_Low = $_GET["RHValue_Low"];
	$RHValue_High = $_GET["RHValue_High"];
	$Humidifier_ON = $_GET["Humidifier_ON"];
	$Humidifier_OFF = $_GET["Humidifier_OFF"];
	$Dehumidifier_ON= $_GET["Dehumidifier_ON"];
	$Dehumidifier_OFF = $_GET["Dehumidifier_OFF"];		
	$command="setRH," .$RHValue_Low. "," .$RHValue_High. "," .$Humidifier_ON. "," .$Humidifier_OFF. "," .$Dehumidifier_ON. "," .$Dehumidifier_OFF;
	}
	
	if ($submit == "Save TDS1 Settings"){
	$TDS1Value_Low = $_GET["TDS1Value_Low"];
	$TDS1Value_High = $_GET["TDS1Value_High"];
	$NutePump1_ON = $_GET["NutePump1_ON"];
	$NutePump1_OFF = $_GET["NutePump1_OFF"];
	$MixPump1_Enabled= $_GET["MixPump1_Enabled"];
	if ($MixPump1_Enabled == "True"){
		$MixPump1_Enabled = "1";
	} else if ($MixPump1_Enabled == "False") {
		$MixPump1_Enabled = "0";
	}
	$command="setTDS1Value," .$TDS1Value_Low. "," .$TDS1Value_High. "," .$NutePump1_ON. "," .$NutePump1_OFF. "," .$MixPump1_Enabled;
	}
	
	if ($submit == "Save TDS2 Settings"){
	$TDS2Value_Low = $_GET["TDS2Value_Low"];
	$TDS2Value_High = $_GET["TDS2Value_High"];
	$NutePump2_ON = $_GET["NutePump2_ON"];
	$NutePump2_OFF = $_GET["NutePump2_OFF"];
	$MixPump2_Enabled= $_GET["MixPump2_Enabled"];	
	if ($MixPump2_Enabled == "True"){
		$MixPump2_Enabled = "1";
	} else if ($MixPump2_Enabled == "False") {
		$MixPump2_Enabled = "0";
	}	
	$command="setTDS2Value," .$TDS2Value_Low. "," .$TDS2Value_High. "," .$NutePump2_ON. "," .$NutePump2_OFF. "," .$MixPump2_Enabled;
	}
	
	if ($submit == "Save CO2 Settings"){
	$CO2Value_Low = $_GET["CO2Value_Low"];
	$CO2Value_High = $_GET["CO2Value_High"];
	$CO2_ON = $_GET["CO2_ON"];
	$CO2_OFF = $_GET["CO2_OFF"];	
	$CO2_Enabled = $_GET["CO2_Enabled"];
	if ($CO2_Enabled == "True"){
		$CO2_Enabled = "1";
	} else if ($CO2_Enabled == "False") {
		$CO2_Enabled = "0";
	}
	$command="setCO2," .$CO2Value_Low. "," .$CO2Value_High. "," .$CO2_ON. "," .$CO2_OFF. "," .$CO2_Enabled;
	}
	
	if ($submit == "Save Light Settings"){
	$LightValue_Low = $_GET["LightValue_Low"];
	$LightValue_High = $_GET["LightValue_High"];
	$command="setLight," .$LightValue_Low. "," .$LightValue_High;
	}
	

	//SEND COMMAND
	if ($submit == "Save Email Settings"){
    echo "Sending Command:  Save Email Settings<br>";
	} else {
	echo "Sending Command:  " .$command. "<br>";
	}
	$myFile = $_SERVER['DOCUMENT_ROOT'] . "/yieldbuddy/Command";
	$file_command=fopen($myFile, "w") or exit("Unable to open file! '" .$myFile."'");
        fwrite($file_command, $command);
	echo "Command Sent.";
	fclose($file_command);
	
	
	//PAGES THAT REQUIRE MORE REFRESH TIME
	if ($command == "restart cam"){
		$sec = "5";
	}
	header("Refresh: $sec; url={$_SERVER['HTTP_REFERER']}");
     ?>
</p>

</body>
</html>
