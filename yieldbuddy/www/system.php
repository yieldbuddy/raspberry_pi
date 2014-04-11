<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_arduino_firstrow.php';
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_email_firstrow.php';
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_camera_firstrow.php';
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
<table width="850" border="0" align="center" cellpadding="0" cellspacing="0">
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
    <td height="20" align="left" valign="top">
    
	<table width="850" border="0">
      <tr class="CenterPageTitles">
        <td width="104" height="34" align="left" valign="bottom"><a href="overview.php">Overview</a></td>
        <td width="150" valign="bottom"><a href="timers.php">Timers</a></td>
        <td width="155" valign="bottom"><a href="graphs.php">Graphs</a></td>
        <td width="193" valign="bottom"><a href="setpoints.php">Set Points</a></td>
        <td width="163" valign="bottom"><a href="alarms.php">Alarms</a></td>
        <td width="150" valign="bottom">System</td>
        <td width="99" align="right" valign="bottom"><a href="logout.php">Log Out</a></td>
      </tr>
	</table>
    
    </td>
  </tr>
  </table>
<table width="1090" align="center">
  <td width="1090"><div class="cssbox">
	  <table width="900" align="center">
  <tr>
    <td height="258" align="left">
    <form action="command.php" method="get">
    <br />
    <br />
    Send Command
      <p align="right">
        <input type="text" name="command" size="104" />
        <input type="submit"  value="Send Command" /></p>
      </form>
    </p>  
      <form action="upload.php" method="post" enctype="multipart/form-data">

    <br />
    Update Firmware
<p align="center">
      <input type="file" name="file" id="file" /> 
      <input type="submit" name="submit" value="Upload" />
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </form>
      <table width="775" border="0" align="center">
        <tr>
          <td height="24" align="center"><button onclick="Update()" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
            <div align="center">
              <script language="javascript" type="text/javascript">
        function Update() {
        var r=confirm("ARE YOU SURE YOU WANT TO UPDATE FIRMWARE??!!                                             Warning: You could potentially lose control!", "Confirm");
        if (r==true) {
          window.location.assign("/EnviroControl/command.php?submit=upgrade")
        }
      }
              </script>
          </div></td>
        </tr>
      </table>
<p align="center"><span class="description">Filename must be 'firmware.cpp.hex'  Click 'Upload' to upload. Click 'Update' to update.</span></p>
      </td>
  </tr>
  <tr>
	  
    <td height="77"><p>Time</p>
    <?php
		$RaspberryPiTime=date("M d Y h:i:s A");

		#echo "Raspberry Pi Time: ";
		#echo $ArduinoTime;
	
		$segments = explode(":",$RaspberryPiTime);
		$segments2 = explode(" ",$segments[0]);			  
		$RaspberryPi_hour = $segments2[3];
		#echo "    Hour: ";
		#echo $RaspberryPi_hour;
		
		#echo "    Minute: ";
		$RaspberryPi_min = $segments[1];
		#echo $RaspberryPi_min;
		
		#echo "    Second: ";
		$RaspberryPi_sec = $segments[2];
		$segments3 = explode(" ",$segments[2]);
		$RaspberryPi_sec = $segments3[0];
		#echo $RaspberryPi_sec;
		
		$ArduinoTime=$_SESSION['Arduino_Time'];
		$Arduino_month=$_SESSION['Arduino_Month'];
		$Arduino_day=$_SESSION['Arduino_Day'];
		$Arduino_year=$_SESSION['Arduino_Year'];
		$Arduino_hour = $_SESSION['Arduino_Hour'];
		$Arduino_min = $_SESSION['Arduino_Minute'];
		$Arduino_sec = $_SESSION['Arduino_Second'];


    ?>
    
     <table width="775" border="0" align="center">
        <tr>
        <form action="command.php" method="get">
          <td width="110">Raspberry Pi:</td>
          <td width="655">
		  Month
		  <select name="Rasp_month" id="Rasp_hour">
            <?php
				echo "<option selected=\"selected\">";
				echo date("m");
				echo "</option>";
			  	$i = 1;
				while ($i <= 12) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
		  Day
          <select name="Rasp_day" id="Rasp_day">
            <?php
				echo "<option selected=\"selected\">";
				echo date("d");
				echo "</option>";
			  	$i = 1;
				while ($i <= 31) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Year
          <select name="Rasp_year" id="Rasp_year">
            <?php
				echo "<option selected=\"selected\">";
				echo date("Y");
				echo "</option>";
			  	$i = 2012;
				while ($i <= 2112) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Hour
          <select name="Rasp_hour" id="Rasp_hour">
            <?php
				echo "<option selected=\"selected\">";
				echo date(H);
				echo "</option>";
			  	$i = 0;
				while ($i <= 23) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Min
          <select name="Rasp_min" id="Rasp_min">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo date(i);
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Sec
          <select name="Rasp_sec" id="Rasp_sec">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo date(s);
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          <input type="submit" name="submit" value="Set Raspberry Pi" /></td>
          </form>
        </tr>
        <tr>
        <form action="command.php" method="get">
          <td height="48" valign="bottom">Arduino:</td>
          <td valign="bottom">
          Month
          <select name="Arduino_month" id="Arduino_month">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_month;
			  echo "</option>";
			  	$i = 1;
				while ($i <= 12) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Day
          <select name="Arduino_day" id="Arduino_day">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_day;
			  echo "</option>";
			  	$i = 1;
				while ($i <= 31) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Year
          <select name="Arduino_year" id="Arduino_year">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_year;
			  echo "</option>";
			  	$i = 2012;
				while ($i <= 2112) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Hour
          <select name="Arduino_hour" id="Arduino_hour">
            <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_hour;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 23) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
          Min
          <select name="Arduino_min" id="Arduino_min">
              <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_min;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
            </select>
            Sec
            <select name="Arduino_sec" id="Arduino_sec">
              <?php
	  		  echo "<option selected=\"selected\">";
			  echo $Arduino_sec;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
				
			?>
            </select>
            <input type="submit" name="submit" value="Set Arduino" /></td>
          </form>
        </tr>
        <tr>
          <form action="command.php" method="get">
          <td height="60" colspan="2" align="center" valign="bottom">
          <input type="submit" name="submit" value="Set Arduino's Time to Raspberry Pi's Time" /></td>
          </form>
        </tr>
        <tr>
          <form action="command.php" method="get">
          <td height="55" colspan="2" align="center" valign="bottom">
          <input type="submit" name="submit" value="Set Raspberry Pi's Time to Arduino's Time" /></td>
          </form>
        </tr>
      </table>
     <p>Restore Defaults </p>
     <table width="775" border="0" align="center">
       <tr>
         <td height="40" colspan="2" align="center" nowrap="nowrap">
           <button onclick="RestoreDefaultsConfirm()">Restore Defaults</button>

			<script>
              function RestoreDefaultsConfirm() {
              var r=confirm("Restore Defaults?", "Confirm");
              if (r==true) {
                window.location.assign("/yieldbuddy/www/command.php?submit=Restore Defaults")
              }
            }
            </script>

       </td>
       </tr>
</table>
     <p><br />
       Email Alerts</p>
     <form action="command.php" method="get">
       <table width="705" border="0" align="center">
         <tr>
           <td width="677" align="center"><h5><em>Note: This uses TLS (Transport Layer Security, a protocol that encrypts and delivers mail securely.)</em> &nbsp;Make sure<br />
             your settings match the TLS
            settings for the server you are connecting to.</h5></td>
         </tr>
     </table>
       <table width="517" border="0" align="center">
         <tr>
           <td width="276">SMTP Server:            </td>
           <td width="216" align="center">
           <?php
		   session_start();
           echo "<input name=\"smtp_server\" type=\"text\" id=\"smtp_server\" value=" . $_SESSION['smtp_server']. " size=\"30\" />";
		   ?>
           </td>
          </tr>
         <tr>
           <td>SMTP Port:</td>
           <td align="center">
           <?php
		   session_start();
           echo "<input name=\"smtp_port\" type=\"text\" id=\"smtp_port\" value=" . $_SESSION['smtp_port']. " size=\"30\" />";
		   ?>
           </td>
         </tr>
         <tr>
           <td>&nbsp;</td>
           <td align="center">&nbsp;</td>
          </tr>
         <tr>
           <td height="25">Login Email Address:</td>
           <td align="center">
           <?php
		   session_start();
           echo "<input name=\"login_email\" type=\"text\" id=\"login_email\" value=" . $_SESSION['login_email_address']. " size=\"30\" />";
		   ?>
           </td>
          </tr>
         <tr>
           <td>Password:</td>
           <td align="center">
           <?php
		   session_start();
           echo "<input name=\"login_email_password\" type=\"password\" id=\"login_email_password\" size=\"30\" />";
		   ?>
		   </td>
          </tr>
         <tr>
           <td>&nbsp;</td>
           <td align="center">&nbsp;</td>
          </tr>
         <tr>
           <td>Email alerts to email address:            </td>
           <td align="center">
           <?php
		   session_start();
           echo "<input name=\"recipient_email\" type=\"text\" id=\"recipient_email\" value=" . $_SESSION['recipient']. " size=\"30\" />";
		   ?>
           </td>
          </tr>
         <tr>
           <td>&nbsp;</td>
           <td align="center">
           <input type="submit" name="submit" id="save_email_button" value="Save Email Settings" />
           </td>
          </tr>
   </table>
     </form>
     <p><br />
      <form action="command.php" method="get">
        <p><br />
        Camera Settings</p>
        <table width="800" border="0" align="center">
          <tr>
            <td width="299">Connect Back Address</td>
            <td width="380" align="center">
             <?php
	  	      session_start();
              echo "<input type=\"text\" name=\"camera_address\" size=\"50\" value=\"" .$_SESSION['camera_address']. "\"/>";
			 ?>
			 </td>
            <td width="107">
            
            <input type="submit" name="submit" value="Save Camera Settings" />
            </td>
          </tr>
        </table>
        <br />
        Log
        <br />
		<textarea name="comments" cols="115" rows="25">
<?php
	$myFile = $_SERVER['DOCUMENT_ROOT']. "/yieldbuddy/log.txt";
	$file_log=fopen($myFile, "r") or exit("Unable to open file! '" .$myFile."'");
	$contents = fread($file_log, filesize($myFile));
	echo $contents;
	fclose($file_log);
?>
		</textarea><br>

  </tr>
</td>
 </tr>
</table>
</div>
</td>
</table>
</body>
</html>
