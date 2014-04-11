<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_lighting_firstrow.php';
include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_watering_firstrow.php';
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
    <td height="100" valign="middle">
    
    <table width="850" border="0" align="center">
      <tr class="CenterPageTitles">
        <td width="104" height="34" align="left" valign="bottom"><a href="overview.php">Overview</a></td>
        <td width="150" valign="bottom">Timers</td>
        <td width="155" valign="bottom"><a href="graphs.php">Graphs</a></td>
        <td width="193" valign="bottom"><a href="setpoints.php">Set Points</a></td>
        <td width="163" valign="bottom"><a href="alarms.php">Alarms</a></td>
        <td width="150" valign="bottom"><a href="system.php">System</a></td>
        <td width="99" align="right" valign="bottom"><a href="logout.php">Log Out</a></td>
      </tr>
    </table>
  
<table width="1000" border="0" cellpadding="0" cellspacing="0">
<td width="1000"><div class="cssbox">
    <p><br>Lighting Schedule</br></p>
    <form action="command.php" method="get">
      <table width="365" border="0">
        <td>On Time:</td>
        <td colspan="2"><select name="Light_ON_hour" id="Light_ON_hour">
          <?php
		  session_start();
		  $Light_ON_hour=$_SESSION['Light_ON_hour'];
		  echo "<option selected=\"selected\">";
		  echo $Light_ON_hour;
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
          <select name="Light_ON_min" id="Light_ON_min">
            <?php
			  session_start();
			  $Light_ON_min=$_SESSION['Light_ON_min'];
	  		  echo "<option selected=\"selected\">";
			  echo $Light_ON_min;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select></td>
        </tr>
      <tr>
        <td>Off Time:</td>
        <td colspan="2"><select name="Light_OFF_hour" id="Light_OFF_hour">
          <?php
		  $Light_OFF_hour=$_SESSION['Light_OFF_hour'];
		  echo "<option selected=\"selected\">";
		  echo $Light_OFF_hour;
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
          <select name="Light_OFF_min" id="Light_OFF_min">
            <?php
			  $Light_OFF_min=$_SESSION['Light_OFF_min'];
	  		  echo "<option selected=\"selected\">";
			  echo $Light_OFF_min;
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
          </td>
      </tr>
      <tr>
        <td height="62" colspan="3" align="center"><input type="submit" name="submit" value="Save Light Schedule" /></td>
        </tr>
    </table>
  </form>
    <p>Watering Schedule    </p>
    <table width="365" border="0">
      <form action="command.php" method="get">
        <tr>
          <td width="125">Start Pump At:</td>
          <td width="235"><select name="Pump_start_hour" id="Pump_start_hour">
            <?php
			session_start();
			  $Pump_start_hour=$_SESSION['Pump_start_hour'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_start_hour;
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
            <select name="Pump_start_min" id="Pump_start_min">
              <?php
			session_start();
			  $Pump_start_min=$_SESSION['Pump_start_min'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_start_min;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 59) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
            </select></td>
        </tr>
        <tr>
          <td width="125">And Every:</td>
          <td><select name="Pump_every_hours" id="Pump_every_hours">
            <?php
			session_start();
			  $Pump_every_hours=$_SESSION['Pump_every_hours'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_every_hours;
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
            hours
            <select name="Pump_every_mins" id="Pump_every_mins">
              <?php
			session_start();
			  $Pump_every_mins=$_SESSION['Pump_every_mins'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_every_mins;
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
            minutes</td>
        </tr>
        <tr>
          <td width="125">For:</td>
          <td><select name="Pump_for" id="Pump_for">
            <?php
			session_start();
			  $Pump_for=$_SESSION['Pump_for'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_for;
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
            minutes each time</td>
        </tr>
        <tr>
          <td>For a Total of:</td>
          <td><select name="Pump_times" id="Pump_times">
            <?php
			session_start();
			  $Pump_times=$_SESSION['Pump_times'];
	  		  echo "<option selected=\"selected\">";
			  echo $Pump_times;
			  echo "</option>";
			  	$i = 0;
				while ($i <= 20) {
					echo "<option>";
					echo $i;
					echo "</option>";
					$i++;
				}
			?>
          </select>
            times per day </td>
        </tr>
        <tr>
          <td height="57" colspan="2" align="center"><input type="submit"  name="submit" value="Save Watering Schedule" /></td>
        </tr>
      </form>
  </table>
    <p>Committed Watering Schedule</p>
    <p>
      <?php
	
	if ($Pump_start_hour < 12) {
		$Pump_start_isAM = 1;
	} else {
		$Pump_start_isAM = 0;
	}
	
     $Pump_hour_array[0] = $Pump_start_hour;
     $Pump_min_array[0] = $Pump_start_min;
     $Pump_isAM_array[0] = $Pump_start_isAM;


    $i = 0;
    while ($i < $Pump_times){
     
       $Pump_hour_array[$i] = $Pump_start_hour + (($i) * $Pump_every_hours);
       $Pump_min_array[$i] = $Pump_start_min + (($i) * $Pump_every_mins);
       
                    
      while ($Pump_min_array[$i] > 59) {
        $Pump_min_array[$i] = $Pump_min_array[$i] - 60;
        $Pump_hour_array[$i]++;
      }

      $AMPM_int = ($Pump_hour_array[$i] / 12);
	  if ($AMPM_int > 0 && $AMPM_int < 1){
		  $AMPM_int = 0;
	  }elseif ($AMPM_int > 1 && $AMPM_int < 2){
		  $AMPM_int = 1;
	  } elseif ($AMPM_int > 2 && $AMPM_int < 3){
	 	  $AMPM_int = 2;
	  } elseif ($AMPM_int > 3 && $AMPM_int < 4){
 		  $AMPM_int = 3;
	  } elseif ($AMPM_int > 4 && $AMPM_int < 5){
 		  $AMPM_int = 4;
	  }
	  
	  
	  while ($Pump_hour_array[$i] > 24) {
         $Pump_hour_array[$i] = $Pump_hour_array[$i] - 24;
      }

/*
Use For Debugging
	  echo "Pump_start_isAM: ";
	  echo $Pump_start_isAM;
   	  echo " AMPMINT: ";
	  echo $AMPM_int;
	  echo " case_zero: ";
	  echo $case_zero;
	  echo " Pump_hour_array[$i]: ";
	  echo $Pump_hour_array[$i];
  	  echo " Pump_isAM_array[$i]: ";
	  echo $Pump_isAM_array[$i];
	  echo  "<br />";
*/
	   $i=$i+1;
   }


   $i = 0;
	
    while ($i<$Pump_times){
	  $Pump_hour_on = $Pump_hour_array[$i];
	  $Pump_min_on  = $Pump_min_array[$i]; 
	  $Pump_min_off = $Pump_min_on + $Pump_for;
	  
/*	  if ($Pump_isAM_array[$i] == 0){
		$Pump_hour_on = $Pump_hour_on + 12;
	  }
	  */
	  
	  $Pump_hour_off = $Pump_hour_on;
	
	  if ($Pump_min_on > 59) {
		$Pump_min_on = 60 - $Pump_min_on;
		$Pump_hour_on++; 
	  }
	  if ($Pump_hour_on > 23) {
	   $Pump_hour_on = $Pump_hour_on - 24; 
	  }
	  
	  if ($Pump_min_off > 59) {
		$Pump_min_off = $Pump_min_off - 60;
		$Pump_hour_off++; 
	  }
	  if ($Pump_hour_off > 23) {
	   $Pump_hour_off = $Pump_hour_off - 24; 
	  }
	
	  echo "<p>";
	  echo $i+1;
	  echo ") ";
      echo " On Time: ";
	  echo $Pump_hour_on;
	  echo ":";
	  if ($Pump_min_on < 10){
		  echo "0";
	  }
	  echo $Pump_min_on;
	  echo " (";
	  if ($Pump_hour_on > 12){
		  echo ($Pump_hour_on - 12);
	  } else { 
	  	if ($Pump_hour_on == 0) {
			echo "12";
		} else {
		  echo $Pump_hour_on;
		}
	  }
	  echo":";
	  if ($Pump_min_on < 10){
	  echo "0";
	  }
	  echo $Pump_min_on;
	  echo " ";
	  if ($Pump_hour_on > 11){
		  echo " PM";
	  } else {
		  echo " AM";
	  }
  	  echo ")";
	  

/*	  if ($Pump_isAM_array[$i] == 1) {
		  echo "AM";
	  }else{
		  echo "PM";
	  }
	  echo ")"; */
	  echo "&nbsp;";
 	  echo "&nbsp;";
   	  echo "&nbsp;";
      echo " Off Time: ";
	  echo $Pump_hour_off;
	  echo ":";
	  if ($Pump_min_off < 10){
		  echo "0";
	  }
  	  echo $Pump_min_off;
	  echo " (";
	  if ($Pump_hour_off > 12){
		  echo ($Pump_hour_off - 12);
	  } else { 
	  	if ($Pump_hour_off == 0) {
			echo "12";
		} else {
		  echo $Pump_hour_off;
		}
	  }
	  echo":";
	  if ($Pump_min_off < 10){
	  echo "0";
	  }
	  echo $Pump_min_off;
	  echo " ";
	  if ($Pump_hour_off > 11){
		  echo " PM";
	  } else {
		  echo " AM";
	  }
  	  echo ")";
 	  echo "</p>";

	  $i=$i+1;
	}
	?>
    </p>
  </tr>
</table>
</td>
</table>
</body>
</html>
