<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript">
function TurnRelay(number, on_off) {
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
	xmlhttp.open("GET","command.php?command=relay" + number + " " + on_off,true);
	xmlhttp.send();
}

function TurnAuto(number, isAuto) {
alert("ok!");
	var xmlhttp;
	if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	} else {
	  // code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
	}
	xmlhttp.open("GET","command.php?command=Relay" + number + " isAuto " + isAuto,true);
	xmlhttp.send();


}
</script>
<?php>
session_start();
include ($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_relays_firstrow.php');
if ($_SESSION['Relay1'] > 1) {
	exit();
}
?>
<table width="390" border="0">
<tr>
<td></td>
<td align="left">Relay Status</td>
<td align="center">Mode</td>
<tr><td>&nbsp;</td><td></td><td></td></tr>
<?php
//session_start();
//include ($_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_relays_firstrow.php');

	  
	  //Relay 1
	  try
	  {
      echo "<tr>";
	  echo "<td>Relay 1 (Water Pump 1): </td>";
	  $Relay1=$_SESSION['Relay1'];
	  //echo $Relay1. "<br />";

  	  $Relay1_isAuto = $_SESSION['Relay1_isAuto'];
  		if ($Relay1 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 1 OFF\" alt=\"Relay 1 OFF\" onclick=\"TurnRelay(1, 'on')\"/></td>";
	    	} elseif ($Relay1 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 1 ON\" alt=\"Relay 1 ON\" onclick=\"TurnRelay(1, 'off')\"/></td>";
		}
		if ($Relay1_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 1 Manual\" alt=\"Relay 1 Manual\" onclick=\"TurnAuto(1, 1)\"/></td>";
		} elseif ($Relay1_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 1 Auto\" alt=\"Relay 1 Auto\" onclick=\"TurnAuto(1, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
      echo "</tr>";
	  
	  
	  //Relay 2
	  try
	  {
	  echo "<tr>";
	  echo "<td>Relay 2 (Water Pump 2): </td>";
	  $Relay2=$_SESSION['Relay2'];
	  //echo $Relay2. "<br />";

  	  $Relay2_isAuto = $_SESSION['Relay2_isAuto'];
  		if ($Relay2 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 2 OFF\" alt=\"Relay 2 OFF\" onclick=\"TurnRelay(2, 'on')\"/></td>";
	    	} elseif ($Relay2 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 2 ON\" alt=\"Relay 2 ON\" onclick=\"TurnRelay(2, 'off')\"/></td>";
		}
		if ($Relay2_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 2 Manual\" alt=\"Relay 2 Manual\" onclick=\"TurnAuto(2, 1)\"/></td>";
		} elseif ($Relay2_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 2 Auto\" alt=\"Relay 2 Auto\" onclick=\"TurnAuto(2, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
	  
	  echo "</tr>";

	  //Relay 3
	  try
	  {
      echo "<tr>";
	  echo "<td>Relay 3 (Free): </td>";
	  $Relay3=$_SESSION['Relay3'];
	  //echo $Relay3. "<br />";

  	  $Relay3_isAuto = $_SESSION['Relay3_isAuto'];
  		if ($Relay3 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 3 OFF\" alt=\"Relay 3 OFF\" onclick=\"TurnRelay(3, 'on')\"/></td>";
	    	} elseif ($Relay3 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 3 ON\" alt=\"Relay 3 ON\" onclick=\"TurnRelay(3, 'off')\"/></td>";
		}
		if ($Relay3_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 3 Manual\" alt=\"Relay 3 Manual\" onclick=\"TurnAuto(3, 1)\"/></td>";
		} elseif ($Relay3_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 3 Auto\" alt=\"Relay 3 Auto\" onclick=\"TurnAuto(3, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
	
      echo "</tr>";

	  //Relay 4
	  try
	  {
      echo "<tr>";
	  echo "<td>Relay 4 (Free): </td>";
	  $Relay4=$_SESSION['Relay4'];
	  //echo $Relay4. "<br />";

  	  $Relay4_isAuto = $_SESSION['Relay4_isAuto'];
  		if ($Relay4 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 4 OFF\" alt=\"Relay 4 OFF\" onclick=\"TurnRelay(4, 'on')\"/></td>";
	    	} elseif ($Relay4 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 4 ON\" alt=\"Relay 4 ON\" onclick=\"TurnRelay(4, 'off')\"/></td>";
		}
		if ($Relay4_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 4 Manual\" alt=\"Relay 4 Manual\" onclick=\"TurnAuto(4, 1)\"/></td>";
		} elseif ($Relay4_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 4 Auto\" alt=\"Relay 4 Auto\" onclick=\"TurnAuto(4, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
	  echo "</tr>";

	  //Relay 5
	  try
	  {
      echo "<tr>";
	  echo "<td>Relay 5 (Free): </td>";
	  $Relay5=$_SESSION['Relay5'];
	  //echo $Relay5. "<br />";

  	  $Relay5_isAuto = $_SESSION['Relay5_isAuto'];
  		if ($Relay5 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 5 OFF\" alt=\"Relay 5 OFF\" onclick=\"TurnRelay(5, 'on')\"/></td>";
	    	} elseif ($Relay5 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 5 ON\" alt=\"Relay 5 ON\" onclick=\"TurnRelay(5, 'off')\"/></td>";
		}
		if ($Relay5_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 5 Manual\" alt=\"Relay 5 Manual\" onclick=\"TurnAuto(5, 1)\"/></td>";
		} elseif ($Relay5_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 5 Auto\" alt=\"Relay 5 Auto\" onclick=\"TurnAuto(5, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
	  echo "</tr>";

	  //Relay 6
	  try
	  {
	  echo "<tr>";
	  echo "<td>Relay 6 (Light/Ballast): </td>";
	  $Relay6=$_SESSION['Relay6'];
	  //echo $Relay6. "<br />";

  	  $Relay6_isAuto = $_SESSION['Relay6_isAuto'];
  		if ($Relay6 == "0") {
	 		echo "<td align=\"center\"><img src=\"img/relay_off.jpg\" title=\"Relay 6 OFF\" alt=\"Relay 6 OFF\" onclick=\"TurnRelay(6, 'on')\"/></td>";
	    	} elseif ($Relay6 == "1") {
	 		echo "<td align=\"center\"><img src=\"img/relay_on.jpg\" title=\"Relay 6 ON\" alt=\"Relay 6 ON\" onclick=\"TurnRelay(6, 'off')\"/></td>";
		}
		if ($Relay6_isAuto == "0") {
			echo "<td align=\"center\"><img src=\"img/manual.jpg\" title=\"Relay 6 Manual\" alt=\"Relay 6 Manual\" onclick=\"TurnAuto(6, 1)\"/></td>";
		} elseif ($Relay6_isAuto == "1") {
			echo "<td align=\"center\"><img src=\"img/auto.jpg\" title=\"Relay 6 Auto\" alt=\"Relay 6 Auto\" onclick=\"TurnAuto(6, 0)\"/></td>";
		}
	  
	  } catch (Exception $e) {
	  echo "Error!  " .$e->getMessage(). "<br />";
	  }
	  echo "</tr>";
	?>
    
</table>
