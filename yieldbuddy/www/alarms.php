<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_alarms.php';
if(!file_exists('users/' . $_SESSION['username'] . '.xml')){
	header('Location: index.php');
	die;
}

?>
<script type="text/javascript">
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function alarm_sql(sensorname, alarmname, alarmvalue) {
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	  }
	alarm_php_string = new String;
	alarm_php_string = "sql/sql_alarm_set.php?sensorname=" + sensorname + "&alarmname=" + alarmname + "&alarmvalue=" + alarmvalue;
	xmlhttp.open("GET",alarm_php_string,true);
	xmlhttp.send();
	sleep(500);
	document.location.reload(true);
}

</script>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="SHORTCUT ICON"
       HREF="/yieldbuddy/www/img/favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>yieldbuddy</title>
<style type="text/css">
body {
	background-image: url(img/background.png);
	margin-top: 0px;
	background-color: #000;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #000;
	font-weight: bold;
	position: relative;
}
.description {
	font-size: 9px;
}
color.white {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFF;
	font-weight: bold;
	position: relative;
	font-size: 10px;
}
a:link {
	color: #999;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #999;
}
a:hover {
	text-decoration: underline;
	color: #999;
}
a:active {
	text-decoration: none;
	color: #999;
}
.CenterPageTitles {
	text-align: center;
}
.CenterPageTitles td {
	color: #FFF;
}
</style>
<style type="text/css">
	div.cssbox {
		font-family: Verdana, Geneva, sans-serif;
		border: 2px solid #000000 ;
		border-radius: 40px ;
		padding: 20px ;
		background-color: #FFFFFF ;
		color: #000 ;
		width: 90% ;
		margin-left: auto ;
		margin-right: auto ;
	}
</style>
</head>

<body>
<table width="775" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br />
    <img src="img/banner.png" width="280" height="52" />
    <color class="white">
    <?php
    include $_SERVER['DOCUMENT_ROOT']. '/yieldbuddy/www/version.php';
    ?>
    </color>
    </td>
  </tr>
  <tr>
    <td height="20" align="left" valign="top">
    
    <table width="850" border="0">
      <tr class="CenterPageTitles">
        <td width="104" height="34" align="left" valign="bottom"><a href="overview.php">Overview</a></td>
        <td width="150" valign="bottom"><a href="timers.php">Timers</a></td>
        <td width="155" valign="bottom"><a href="graphs.php">Graphs</a></td>
        <td width="193" valign="bottom"><a href="setpoints.php">Set Points</a></td>
        <td width="163" valign="bottom">Alarms</td>
        <td width="150" valign="bottom"><a href="system.php">System</a></td>
        <td width="99" align="right" valign="bottom"><a href="logout.php">Log Out</a></td>
      </tr>
    </table>
    
    </td>
  </tr>
</table>
<table width="1060" align="center">
    <td height="258" align="left" valign="top"><div class="cssbox"><p>
		<br />
      Alarms</p>
      <table width="850" border="0" align="center">
        <tr>
          <td width="240">Time</td>
          <td width="134">Alarm</td>
          <td width="145">Status</td>
          <td align = "center" width="100">Emailed</td>
          
        </tr>
        <tr>
        <?php
		session_start();
		if ($_SESSION['pH1_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['pH1_Low_Time'];
				echo "</td>";
				echo "<td>pH1 Low</td>";
				if ($_SESSION['pH1_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH1','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['pH1_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH1','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['pH1_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['pH1_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('pH1','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['pH1_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['pH1_High_Time'];
				echo "</td>";
				echo "<td>pH1 High</td>";
				if ($_SESSION['pH1_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH1','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['pH1_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH1','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['pH1_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['pH1_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('pH1','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['pH2_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['pH2_Low_Time'];
				echo "</td>";
				echo "<td>pH2 Low</td>";
				if ($_SESSION['pH2_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH2','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['pH2_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH2','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['pH2_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['pH2_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('pH2','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['pH2_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['pH2_High_Time'];
				echo "</td>";
				echo "<td>pH2 High</td>";
				if ($_SESSION['pH2_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH2','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['pH2_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('pH2','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['pH2_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['pH2_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('pH2','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['Temp_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['Temp_Low_Time'];
				echo "</td>";
				echo "<td>Temp Low</td>";
				if ($_SESSION['Temp_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Temp','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['Temp_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Temp','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['Temp_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['Temp_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('Temp','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['Temp_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['Temp_High_Time'];
				echo "</td>";
				echo "<td>Temp High</td>";
				if ($_SESSION['Temp_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Temp','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['Temp_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Temp','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['Temp_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['Temp_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('Temp','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['RH_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['RH_Low_Time'];
				echo "</td>";
				echo "<td>RH Low</td>";
				if ($_SESSION['RH_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('RH','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['RH_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('RH','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['RH_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['RH_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('RH','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['RH_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['RH_High_Time'];
				echo "</td>";
				echo "<td>RH High</td>";
				if ($_SESSION['RH_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('RH','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['RH_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('RH','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['RH_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['RH_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('RH','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['TDS1_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['TDS1_Low_Time'];
				echo "</td>";
				echo "<td>TDS1 Low</td>";
				if ($_SESSION['TDS1_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS1','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['TDS1_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS1','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['TDS1_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['TDS1_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('TDS1','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['TDS1_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['TDS1_High_Time'];
				echo "</td>";
				echo "<td>TDS1 High</td>";
				if ($_SESSION['TDS1_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS1','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['TDS1_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS1','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['TDS1_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['TDS1_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('TDS1','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['TDS2_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['TDS2_Low_Time'];
				echo "</td>";
				echo "<td>TDS2 Low</td>";
				if ($_SESSION['TDS2_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS2','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['TDS2_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS2','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['TDS2_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['TDS2_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('TDS2','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['TDS2_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['TDS2_High_Time'];
				echo "</td>";
				echo "<td>TDS2 High</td>";
				if ($_SESSION['TDS2_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS2','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['TDS2_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('TDS2','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['TDS2_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['TDS2_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('TDS2','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['CO2_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['CO2_Low_Time'];
				echo "</td>";
				echo "<td>CO2 Low</td>";
				if ($_SESSION['CO2_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('CO2','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['CO2_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('CO2','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['CO2_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['CO2_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('CO2','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['CO2_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['CO2_High_Time'];
				echo "</td>";
				echo "<td>CO2 High</td>";
				if ($_SESSION['CO2_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('CO2','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['CO2_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('CO2','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['CO2_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['CO2_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('CO2','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		
		
				if ($_SESSION['Light_Low_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['Light_Low_Time'];
				echo "</td>";
				echo "<td>Light Low</td>";
				if ($_SESSION['Light_Low_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Light','Low_Alarm',3)\" /></td>";
				}
				if ($_SESSION['Light_Low_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Light','Low_Alarm',4)\" /></td>";
				}
				if ($_SESSION['Light_Low_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['Light_Low_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('Light','Low_Alarm',0)\" /></td>";
			echo "</tr>";
		}
		
		if ($_SESSION['Light_High_Alarm'] > 0) {
			echo "<tr>";
				echo "<td>";
				echo $_SESSION['Light_High_Time'];
				echo "</td>";
				echo "<td>Light High</td>";
				if ($_SESSION['Light_High_Alarm'] == 1) {
					echo "<td>Unacknowledged</td>";
					echo "<td></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Light','High_Alarm',3)\" /></td>";
				}
				if ($_SESSION['Light_High_Alarm'] == 2) {
					echo "<td>Unacknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Acknowledge\" onclick=\"alarm_sql('Light','High_Alarm',4)\" /></td>";
				}
				if ($_SESSION['Light_High_Alarm'] == 3) {
					echo "<td>Acknowledged</td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				if ($_SESSION['Light_High_Alarm'] == 4) {
					echo "<td>Acknowledged</td>";
					echo "<td align=\"center\"><img src=\"img/email.png\" /></td>";
					echo "<td></td>";
				}
				echo "<td><input type=\"submit\" name=\"alarmname_ack_id\" id=\"alarmname_ack_id\" value=\"Delete\" onclick=\"alarm_sql('Light','High_Alarm',0)\" /></td>";
			echo "</tr>";
		}
  ?>
  </tr>
	  </table>
	</td>
  </div>
</table>
</body>
</html>
