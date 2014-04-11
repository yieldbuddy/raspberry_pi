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
       HREF="/yieldbuddy/favicon.ico">
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
<p><img src="img/banner.png" width="383" height="77" />
</p>
<?php
$page = 'system.php';

$sec = "5";

header("Refresh: $sec; url=$page");
$allowedExts = array("hex", "cpp");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ( ($_FILES["file"]["type"] == "text/x-hex") || ($_FILES["file"]["type"] == "text/cpp") && ($_FILES["file"]["size"] < 300000) && in_array($extension, $allowedExts) )
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("upload/" . $_FILES["file"]["name"]))
      {
      echo "Overwriting File..." . $_FILES["file"]["name"];
      move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
      echo "<br/>";
      echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
      echo "<br/>";
      echo "To update firmware, type 'update' and click 'Send Command'";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
      echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "Invalid file";
  }
?>