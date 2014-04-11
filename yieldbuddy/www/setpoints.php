<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_setpoints_firstrow.php';
if(!file_exists('users/' . $_SESSION['username'] . '.xml')){
	header('Location: index.php');
	die;
}

#$page = $_SERVER['PHP_SELF'];
#$sec = "2";
#header("Refresh: $sec; url=$page");

?>
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
<table width="850" border="0" align="center">
  <tr>
    <td align="center"><br />
    <img src="img/banner.png" width="280" height="52" />
    <color class="white">
    <?php
    include $_SERVER['DOCUMENT_ROOT']. '/yieldbuddy/www/version.php';
    ?>
    </color>
  </tr>
  <tr>
    <td valign="middle">
    
    <table width="850" border="0">
      <tr class="CenterPageTitles">
        <td width="104" height="34" align="left" valign="bottom"><a href="overview.php">Overview</a></td>
        <td width="150" valign="bottom"><a href="timers.php">Timers</a></td>
        <td width="155" valign="bottom"><a href="graphs.php">Graphs</a></td>
        <td width="193" valign="bottom">Set Points</td>
        <td width="163" valign="bottom"><a href="alarms.php">Alarms</a></td>
        <td width="150" valign="bottom"><a href="system.php">System</a></td>
        <td width="99" align="right" valign="bottom"><a href="logout.php">Log Out</a></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
	<table width="900" align = "center">
    <td width ="900" align="center"><div class="cssbox">
      <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="77" colspan="2">
          
            <div align="center">
            <h6>Note: Low / High Values are used for alerts and for logging purposes.<br>
            </h6>
            </div>
            <h4>
              pH1
            </h4>
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$pH1Value_Low=$_SESSION['pH1Value_Low'];
		$pH1Value_High=$_SESSION['pH1Value_High'];
		echo "<td width=\"300\"><div align=\"right\">";
        echo "Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"pH1Value_Low\" size=\"6\" value=\"" . $pH1Value_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">";
		echo "High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"pH1Value_High\" size=\"6\" value=\"" . $pH1Value_High . "\" /></div></td>";
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\">";
		echo "<input type=\"submit\"  name=\"submit\" value=\"Save pH1 Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            pH2
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();		
		$pH2Value_Low=$_SESSION['pH2Value_Low'];
		$pH2Value_High=$_SESSION['pH2Value_High'];
		echo "<td width=\"300\"><div align=\"right\">";
        echo "Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"pH2Value_Low\" size=\"6\" value=\"" . $pH2Value_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">";
		echo "High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"pH2Value_High\" size=\"6\" value=\"" . $pH2Value_High . "\" /></div></td>";
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\">";
		echo "<input type=\"submit\"  name=\"submit\" value=\"Save pH2 Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            Temperature
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();		
		$TempValue_Low=$_SESSION['TempValue_Low'];
		$TempValue_High=$_SESSION['TempValue_High'];
		echo "<td width=\"300\"><div align=\"right\">";
        echo "Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TempValue_Low\" size=\"6\" value=\"" . $TempValue_Low . "\" />";
		echo "</td>";
     	echo "<td><div align=\"right\">";
		echo "High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TempValue_High\" size=\"6\" value=\"" . $TempValue_High . "\" /></div></td>";
		echo "</tr>";
		
		$Heater_ON=$_SESSION['Heater_ON'];
		$Heater_OFF=$_SESSION['Heater_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">Heater On: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Heater_ON\" size=\"6\" value=\"" . $Heater_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">Heater Off: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Heater_OFF\" size=\"6\" value=\"" . $Heater_OFF . "\" /></div></td>";
		echo "</tr>";
		$AC_ON=$_SESSION['AC_ON'];
		$AC_OFF=$_SESSION['AC_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">AC On: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"AC_ON\" size=\"6\" value=\"" . $AC_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">AC Off: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"AC_OFF\" size=\"6\" value=\"" . $AC_OFF . "\" /></div></td>";
		
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save Temp Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            RH
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$RHValue_Low=$_SESSION['RHValue_Low'];
		$RHValue_High=$_SESSION['RHValue_High'];
        echo "<td width=\"300\"><div align=\"right\">Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"RHValue_Low\" size=\"6\" value=\"" . $RHValue_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"RHValue_High\" size=\"6\" value=\"" . $RHValue_High . "\" /></div></td>";
		echo "</tr>";
		
		$Humidifier_ON=$_SESSION['Humidifier_ON'];
		$Humidifier_OFF=$_SESSION['Humidifier_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">Humidifier ON: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Humidifier_ON\" size=\"6\" value=\"" . $Humidifier_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">Humidifier OFF: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Humidifier_OFF\" size=\"6\" value=\"" . $Humidifier_OFF . "\" /></div<</td>";
		echo "</tr>";
		
		$Dehumidifier_ON=$_SESSION['Dehumidifier_ON'];
		$Dehumidifier_OFF=$_SESSION['Dehumidifier_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">Dehumidifier ON: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Dehumidifier_ON\" size=\"6\" value=\"" . $Dehumidifier_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">Dehumidifier OFF: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"Dehumidifier_OFF\" size=\"6\" value=\"" . $Dehumidifier_OFF . "\" /></div></td>";
		
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save RH Settings\" /></div></td>";
		echo "</tr>";

		?>
                </form>
              </tr>
            </table>
            <br />
            TDS 1
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$TDS1Value_Low=$_SESSION['TDS1Value_Low'];
		$TDS1Value_High=$_SESSION['TDS1Value_High'];
        echo "<td width=\"300\"><div align=\"right\">Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TDS1Value_Low\" size=\"6\" value=\"" . $TDS1Value_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TDS1Value_High\" size=\"6\" value=\"" . $TDS1Value_High . "\" /></div></td>";
		
		$NutePump1_ON=$_SESSION['NutePump1_ON'];
		$NutePump1_OFF=$_SESSION['NutePump1_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">Nutrient Pump On: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"NutePump1_ON\" size=\"6\" value=\"" . $NutePump1_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">Nutrient Pump Off: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"NutePump1_OFF\" size=\"6\" value=\"" . $NutePump1_OFF . "\" /></div></td>";
		echo "</tr>";
		
		$MixPump1_Enabled=$_SESSION['MixPump1_Enabled'];
		echo "<tr>";
		echo "<td><div align=\"right\">MixPump1 Enabled: &nbsp;&nbsp;&nbsp;";
		if ($MixPump1_Enabled == "1"){
	    echo "<label for=\"MixPump1 Enabled\"></label>";
		echo "<select name=\"MixPump1 Enabled\" id=\"MixPump1 Enabled\">";
		echo "<option selected=\"selected\">True</option>";
     	echo "<option>False</option>";
		echo "</select>";
		} else if ($MixPump1_Enabled == "0") {
	    echo "<label for=\"MixPump1 Enabled\"></label>";
		echo "<select name=\"MixPump1 Enabled\" id=\"MixPump1 Enabled\">";
		echo "<option selected=\"selected\">False</option>";
     	echo "<option>True</option>";
		echo "</select>";
		}
		
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save TDS1 Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            TDS 2
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$TDS2Value_Low=$_SESSION['TDS2Value_Low'];
		$TDS2Value_High=$_SESSION['TDS2Value_High'];
        echo "<td width=\"300\"><div align=\"right\">Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TDS2Value_Low\" size=\"6\" value=\"" . $TDS2Value_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"TDS2Value_High\" size=\"6\" value=\"" . $TDS2Value_High . "\" /></div></td>";
		
		$NutePump2_ON=$_SESSION['NutePump2_ON'];
		$NutePump2_OFF=$_SESSION['NutePump2_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">Nutrient Pump On: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"NutePump2_ON\" size=\"6\" value=\"" . $NutePump2_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">Nutrient Pump Off: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"NutePump2_OFF\" size=\"6\" value=\"" . $NutePump2_OFF . "\" /></div></td>";
		echo "</tr>";
		
		$MixPump2_Enabled=$_SESSION['MixPump2_Enabled'];
		echo "<tr>";
		echo "<td><div align=\"right\">MixPump2 Enabled: &nbsp;&nbsp;&nbsp;";
		if ($MixPump2_Enabled == "1"){
	    echo "<label for=\"MixPump2 Enabled\"></label>";
		echo "<select name=\"MixPump2 Enabled\" id=\"MixPump2 Enabled\">";
		echo "<option selected=\"selected\">True</option>";
     	echo "<option>False</option>";
		echo "</select>";
		} else if ($MixPump2_Enabled == "0") {
	    echo "<label for=\"MixPump2 Enabled\"></label>";
		echo "<select name=\"MixPump2 Enabled\" id=\"MixPump2 Enabled\">";
		echo "<option selected=\"selected\">False</option>";
     	echo "<option>True</option>";
		echo "</select>";
		}
		
		echo "</tr>";
		echo "<tr><td></td>";
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save TDS2 Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            CO2
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$CO2Value_Low=$_SESSION['CO2Value_Low'];
		$CO2Value_High=$_SESSION['CO2Value_High'];
        echo "<td width=\"300\"><div align=\"right\">Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"CO2Value_Low\" size=\"6\" value=\"" . $CO2Value_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"CO2Value_High\" size=\"6\" value=\"" . $CO2Value_High . "\" /></div></td>";
		echo "</tr>";
		
		$CO2_ON=$_SESSION['CO2_ON'];
		$CO2_OFF=$_SESSION['CO2_OFF'];
		echo "<tr>";
        echo "<td><div align=\"right\">CO2 On: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"CO2_ON\" size=\"6\" value=\"" . $CO2_ON . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">CO2 Off: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"CO2_OFF\" size=\"6\" value=\"" . $CO2_OFF . "\" /></div></td>";
		echo "</tr>";
		
		$CO2_Enabled=$_SESSION['CO2_Enabled'];
		echo "<tr>";
		echo "<td><div align=\"right\">CO2 Enabled: &nbsp;&nbsp;&nbsp;";
		if ($CO2_Enabled == "1"){
	    echo "<label for=\"CO2 Enabled\"></label>";
		echo "<select name=\"CO2 Enabled\" id=\"CO2 Enabled\">";
		echo "<option selected=\"selected\">True</option>";
     	echo "<option>False</option>";
		echo "</select>";
		} else if ($CO2_Enabled == "0") {
	    echo "<label for=\"CO2 Enabled\"></label>";
		echo "<select name=\"CO2 Enabled\" id=\"CO2 Enabled\">";
		echo "<option selected=\"selected\">False</option>";
     	echo "<option>True</option>";
		echo "</select>";
		}
		//echo "<input type=\"text\" name=\"CO2_Enabled\" size=\"6\" value=\"" . $CO2_Enabled . "\" /></div></td>";
		echo "</tr>";
		echo "<tr><td></td>";			
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save CO2 Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
            <br />
            Light
            <table width="600" border="0">
              <tr>
                <form action="command.php" method="get">
                  <?php
		session_start();
		$LightValue_Low=$_SESSION['LightValue_Low'];
		$LightValue_High=$_SESSION['LightValue_High'];
        echo "<td width=\"300\"><div align=\"right\">Low: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"LightValue_Low\" size=\"6\" value=\"" . $LightValue_Low . "\" /></div>";
		echo "</td>";
		echo "<td><div align=\"right\">High: &nbsp;&nbsp;&nbsp;";
		echo "<input type=\"text\" name=\"LightValue_High\" size=\"6\" value=\"" . $LightValue_High . "\" /></div></td>";
		echo "</tr>";
		
		echo "<tr><td></td>";				
		echo "<td><div align=\"right\"><input type=\"submit\"  name=\"submit\" value=\"Save Light Settings\" /></div></td>";
		echo "</tr>";
		?>
                </form>
              </tr>
            </table>
        </td>
        </tr>
      </table>
  </td>
  </tr>
</table>

</body>
</html>
