<?php
function plugin_ExportXML() {

  if (isset($_POST['export_dir'])) {
    include_once('DB/Sqlite/Tools.php');
    
    $dbinfo = pathinfo($GLOBALS['workDb']->baseName);
    $olddir = getcwd();
    chdir($dbinfo['dirname']);
    $DBTools = new DB_Sqlite_Tools($dbinfo['basename']);
    try {
    $DBTools->copySafe('./');
    $DBTools->createXMLDumps($_POST['export_dir']);
    } catch (DB_Sqlite_Tools_Exception $e) { 
      chdir($olddir);
      return false;
    }
    chdir($olddir);
    return true;
  }
  echo '
  <center><form method="POST">
  <input type="hidden" name="ExportXML" value="doIt">
  <table class="Insert" cellspacing="0" cellpadding="3" style="text-align:left">
  <thead><tr><td colspan="2" class="TitleHeader">Export Database to XML</td></tr></thead>
  <tr>
   <td>Output Directory :</td>
   <td><input type="text" size="70" class="text" name="export_dir" value="'.getcwd().'"></td>
  </tr>
  <tr> 
   <td colspan="2" align="center"><input type="submit" class="button"></td>
  </tr>
  </table>
  </form>
  </center>
  </body>
  </html>  
  ';
  
}

function plugin_ImportXML() {

  if (isset($_POST['import_xmlfile'])) {
    include_once('DB/Sqlite/Tools.php');
  
    $dbinfo = pathinfo($GLOBALS['workDb']->baseName);
    $olddir = getcwd();
    chdir($dbinfo['dirname']);
    $DBTools = new DB_Sqlite_Tools($dbinfo['basename']);

    $DBTools->createDBFromXML($xml_file,$_POST['import_database']);

  }
  echo '
  <center><form method="POST">
  <input type="hidden" name="ImportXML" value="doIt">
  <table class="Insert" cellspacing="0" cellpadding="3" style="text-align:left">
  <thead><tr><td colspan="2" class="TitleHeader">Import Database from XML</td></tr></thead>
  <tr>
   <td>New database name :</td>
   <td><input type="text" size="30" class="text" name="import_database"></td>
  </tr>
  <tr>
   <td>XML File :</td>
   <td><input type="file" size="70" class="text" name="import_xmlfile"></td>
  </tr>
  <tr> 
   <td colspan="2" align="center"><input type="submit" class="button"></td>
  </tr>
  </table>
  </form>
  </center>
  </body>
  </html>  
  '; 
}
?>
