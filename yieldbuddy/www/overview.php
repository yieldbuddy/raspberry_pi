<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
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
td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #000;
	font-weight: bold;
	position: relative;
}
color.white {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFF;
	font-weight: bold;
	position: relative;
	font-size: 10px;
}
.description {
	font-size: 9px;
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
 <script language = "JavaScript">
 function preloader() 
 {
 RelayONImage = new Image(); 
 RelayONImage.src = "img/relay_on.jpg";
 RelayOFFImage = new Image(); 
 RelayOFFImage.src = "img/relay_off.jpg";
 }
 </script>
<body onLoad="javascript:preloader()">

<script language="javascript" type="text/javascript">
<!-- 
//Browser Support Code
var graphloopcount = 20;
var PastResponseText_Relays;

var int=self.setInterval(function(){loop()},2000);
function loop() {
  updateRaspberryPiTime();
  updateArduinoTime();
  updateSensorInfo();
  updateRelayInfo();
}

function updateRaspberryPiTime() {
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			if (ajaxRequest.responseText != "") {
				document.getElementById("RaspberryPiTime").innerHTML = ajaxRequest.responseText;
			}
		}
	}
	ajaxRequest.open("GET", "updateRaspberryPiTime.php", true);
	ajaxRequest.send(null); 

}

function updateArduinoTime() {
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			if (ajaxRequest.responseText != "") {
				document.getElementById("ArduinoTime").innerHTML = ajaxRequest.responseText;
			}
		}
	}
	ajaxRequest.open("GET", "updateArduinoTime.php", true);
	ajaxRequest.send(null); 

}

function updateSensorInfo() {
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			if (ajaxRequest.responseText != "") {
			document.getElementById("sensorInfo").innerHTML = ajaxRequest.responseText;
			}
		}
	}
	ajaxRequest.open("GET", "updateSensors.php", true);
	ajaxRequest.send(null); 
}

function updateRelayInfo() {
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			if(ajaxRequest.responseText != document.getElementById("relayInfo").innerHTML){
				document.getElementById("relayInfo").innerHTML = ajaxRequest.responseText;
			}
		}
	}
	ajaxRequest.open("GET", "updateRelays.php", true);
	ajaxRequest.send(null); 
}

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
	xmlhttp.open("GET","command.php?command=Relay" + number + " " + on_off,true);
	xmlhttp.send();
}

function TurnAuto(number, isAuto) {
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
	xmlhttp.open("GET","command.php?command=Relay" + number + " isAuto " + isAuto,true);
	xmlhttp.send();
}
//-->
</script>

<table width="850" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="100" colspan="2" align="center" valign="bottom"><br />
    <img src="img/banner.png" width="280" height="52" />
    <color class="white">
    <?php
    include $_SERVER['DOCUMENT_ROOT']. '/yieldbuddy/www/version.php';
    ?>
    </color>
    </td>
  </tr>
  <tr>
    <td height="20" colspan="2" align="left" valign="top">
    
    <table width="850" border="0">
      <tr class="CenterPageTitles">
        <td width="104" height="34" align="left" valign="bottom">Overview</td>
        <td width="150" valign="bottom"><a href="timers.php">Timers</a></td>
        <td width="155" valign="bottom"><a href="graphs.php">Graphs</a></td>
        <td width="193" valign="bottom"><a href="setpoints.php">Set Points</a></td>
        <td width="163" valign="bottom"><a href="alarms.php">Alarms</a></td>
        <td width="150" valign="bottom"><a href="system.php">System</a></td>
        <td width="99" align="right" valign="bottom"><a href="logout.php">Log Out</a></td>
      </tr>
    </table>
    
    </td>
  </tr>
</table>
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
<td width="1000" align="center"><div class="cssbox">
  <table>
  <tr>
    <td width="456" height="20" align="left" valign="top">
    <p id="RaspberryPiTime">  </p>
    </td>
    <td width="456" height="20" align="right" valign="middle">
	<p id="ArduinoTime">  </p>
    </td>
  </tr>
  <tr>
    <td height="38" colspan="2"><table width="850" height="44" border="0">
	 <tr>
        <td height="40" valign="top"><p><strong>Sensor Information</strong></p>
          <p id="sensorInfo"></p>
        </td>
        <td align="right" valign="top">        
          <p align="right" id="relayInfo"></p>
       </td>
     </tr>
   </td>
  </tr>
  <tr>
    <td height="2" colspan="2" valign="top"></td>
  </tr>
  <tr>
    <td height="19" colspan="2" align="left" valign="top"><p>&nbsp;Camera</p></td>
  </tr>
  <tr>
    <td height="77" colspan="2" align="center" valign="top"><p id="camera_applet">
    <?php 
	include $_SERVER['DOCUMENT_ROOT'].'/yieldbuddy/www/sql/sql_camera_firstrow.php';
	session_start();
	$camera_address = trim($_SESSION['camera_address']);
    ?>
     <applet code=com.charliemouse.cambozola.Viewer
    archive=java/cambozola.jar width="640" height="480" style="border-width:1; border-color:gray; border-style:solid;"> <param name=url value="<?php echo $camera_address; ?>"> </applet>  </p>
    <p><button onclick="RestartCam()">Restart Camera</button> <button onclick="StopCam()">Stop Camera</button>
         <script language="javascript" type="text/javascript">
			function RestartCam() {
			  window.location.assign("/yieldbuddy/www/command.php?command=restart cam")
			}
			function StopCam() {
			  window.location.assign("/yieldbuddy/www/command.php?command=stop cam")
			}
         </script></p>
    </td>
  </tr>
  </table>
</div>
</td>
</table>
</body>
</html>
