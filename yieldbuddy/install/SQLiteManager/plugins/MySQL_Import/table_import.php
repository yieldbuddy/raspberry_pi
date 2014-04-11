<?php

function plugin_Reset() {
  unset($_SESSION['plugin_mysql_server']);
  unset($_SESSION['plugin_mysql_user']);
  unset($_SESSION['plugin_mysql_pass']);
  unset($_SESSION['plugin_mysql_tables']);
  unset($_SESSION['plugin_mysql_base']);
  session_write_close();
}

function plugin_ImportTableData() {
   
  echo '
  <center>
  <h4>Import MySQL data in table : '.$GLOBALS['table'].'</h4>
  ';
  
  $res = false;
  $link = CheckMySQLLink(); 
  if ($link) $res = CheckMySQLBase($link);
  if ($res)  $res = CheckMySQLTables($link);
  if ($res) $res = Do_ImportTableData($link);
  
  if ($res) {
    echo '<table class="Browse"><thead><tr><td class="Browse">Résumé: </td></tr></thead><tr><td style="padding:10px">';
    echo 'Transféré depuis la base <b>'.$_SESSION['plugin_mysql_base'].'</b> du serveur MySQL <b>'.$_SESSION['plugin_mysql_server'].'</b> :</br><ul>';

    echo '</ul>';
    echo 'Opération terminée. <a href="main.php?dbsel='.$GLOBALS['dbsel'].'&table='.$GLOBALS['table'].'&action=browseItem">Afficher le contenu...</a>'; 
    echo '</td><tr></table>';
    plugin_Reset();
    if ($link) mysql_close($link); 

  }

  echo '
  </center>
  </body>
  </html>
  ';
}

function CheckMySQLLink() {
  if (count($_POST) && isset($_POST['mysql_connect'])) {
    $_SESSION['plugin_mysql_server'] = $_POST['mysql_server'].($_POST['mysql_port']?':'.$_POST['mysql_port']:'');
    $_SESSION['plugin_mysql_user'] = $_POST['mysql_user'];
    $_SESSION['plugin_mysql_pass'] = $_POST['mysql_pass'];
  }
  if (isset($_SESSION['plugin_mysql_server'])) {
    $link = mysql_pconnect($_SESSION['plugin_mysql_server'],
                           $_SESSION['plugin_mysql_user'],
                           $_SESSION['plugin_mysql_pass']) or die(mysql_error()); 
    return $link;
  }
  echo '
  <form method="POST">
  <input type="hidden" name="mysql_connect" value="doIt">
  <table class="Insert" cellspacing="0" cellpadding="3" style="text-align:left">
  <thead><tr><td colspan="2" class="TitleHeader">Connexion MySQL</td></tr></thead>
  <tr>
   <td>Server :</td>
   <td><input type="text" class="text" name="mysql_server" value="localhost"></td>
  </tr>
  <tr>
   <td>Port :</td>
   <td><input type="text" class="text" name="mysql_port" value="3306" size="5"></td>
  </tr>
  <tr>
   <td>User :</td>
   <td><input type="text" class="text" name="mysql_user" value="root"></td>
  </tr>
  <tr>
   <td>Password :</td>
   <td><input type="password" class="text" name="mysql_pass"></td>
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
  return false;
}

function GetMySQLDatabases($link) {
  $data=array();
  if ($res=mysql_query('SHOW DATABASES;',$link)) {
    while ($line = mysql_fetch_array($res, MYSQL_ASSOC)) $data[] = $line['Database'];
  }
  return $data;
}

function CheckMySQLBase($link) {
  if (count($_POST) && isset($_POST['mysql_select_db'])) {
    $_SESSION['plugin_mysql_base'] = $_POST['mysql_base'];
  }
  if (isset($_SESSION['plugin_mysql_base'])) { 
    return mysql_select_db($_SESSION['plugin_mysql_base'],$link);
  }
  $dbs = GetMySQLDatabases($link);
  echo '
  <form method="POST">
  <input type="hidden" name="mysql_select_db" value="doIt">
  <table class="Insert" cellspacing="0" cellpadding="3" style="text-align:left">
  <thead><tr><td colspan="2" class="TitleHeader">Select MySQL Database...</td></tr></thead>
  <tr>
   <td colspan="2" align="center">
     <select name="mysql_base">
     ';
  foreach($dbs as $db) echo "<option>$db</option>";
  echo '
     </select>
   </td> 
  </tr>
  <tr> 
   <td colspan="2" align="center"><input type="submit" class="button"></td>
  </tr>
  </table>
  </form>
  ';
  return false;
}

function GetMySQLTables($link) {
  $data=array();
  $base = $_SESSION['plugin_mysql_base'];
  if ($res=mysql_list_tables($base,$link)) {
    for ($i=0; $i<mysql_num_rows($res); $i++) {
        $table=mysql_tablename($res, $i);
        if ($info=mysql_query("SELECT COUNT(*) as cnt FROM `$table`;",$link)) {
          if ($infos = mysql_fetch_array($info,MYSQL_ASSOC))
            $data['tables'][$table] = $infos['cnt'];
          mysql_free_result($info);
        } else die(mysql_error());
        if ($info=mysql_list_fields($base,$table,$link)) {
          for ($c=0; $c<mysql_num_fields($info); $c++) {
            $col = mysql_field_name($info,$c); 
            $flags = mysql_field_flags($info,$c);
            $flags = str_replace('primary_key','PK',$flags);
            $flags = str_replace('auto_increment','AUTO',$flags);
            $flags = str_replace('unsigned','UNSIGNED',$flags);
            $flags = str_replace('zerofill','ZERO',$flags);
            $flags = str_replace('enum','ENUM',$flags);
            $flags = str_replace('set','SET',$flags);
            $flags = preg_replace('#not_null[ ]?#','',$flags);
            $flags = preg_replace('#multiple_key[ ]?#','',$flags);
            $flags = preg_replace('#blob[ ]?#','',$flags);
            $data['columns'][$table][$col]=
              mysql_field_type($info,$c).' '.
              mysql_field_len($info,$c).'-'.
              $flags;
          }
          mysql_free_result($info);
        } else die(mysql_error());
    }
  } else die(mysql_error());
  
  return $data;
}
function clean_name($table) {
  //$table = ereg_replace(' ','_',$table);
  return $table;
}
function CheckMySQLTables($link) {
  if (count($_POST) && isset($_POST['mysql_select_tables'])) {
    $_SESSION['plugin_mysql_tables'] = $_POST; 
  }
  if (isset($_SESSION['plugin_mysql_tables']) && count($_SESSION['plugin_mysql_tables'])) return true;
  
  $tbs = GetMySQLTables($link);

  echo "
  <style type=\"text/css\">
  .SelectVisible { display: inline; }
  .SelectHidded { display: none; }
  </style>
  <script type=\"text/javascript\">
  var CurrentTable;
  var CurrentColumn;
  function ShowColumns(from) {
    for (var i=0; i<from.options.length; i++) {
		  var o = from.options[i];
      if (select = document.getElementById('tr_'+o.value)) {
  		  if (o.selected) {
          select.className = 'SelectVisible';
          CurrentTable = o.value;
        }
		    else 
		      select.className = 'SelectHidded'; 
		  }
    }
  }
  function SelectColumn(id) {
    CurrentColumn = document.getElementById('col_'+id);
  }
  function SetColValue(from) {
    for (var i=0; i<from.options.length; i++) {
		  var o = from.options[i];
		  if (o.selected && CurrentColumn)
        CurrentColumn.value = `'+o.value+'`';
    }
  }
  </script>
  <script type=\"text/javascript\" src=\"select.js\"></script>";
  echo '
  <form method="POST">
  <input type="hidden" name="mysql_select_tables" value="doIt">
  <table class="Insert" cellspacing="0" cellpadding="3" width="80%" style="text-align:left">
  <thead><tr><td colspan="2" class="TitleHeader">Columns Assignment</td></tr></thead>
  <tr>
   <td width="75%" align="left" nowrap="nowrap" valign="top">
   ';
   include_once('include/SQLiteTableProperties.class.php');
   $prop = new SQLiteTableProperties($GLOBALS['workDb']);
   $prop->getTableProperties($GLOBALS['table']);
   foreach ($prop->infoTable as $key=>$col_infos) {
     echo '<label for="'.$col_infos['name'].'"><input onClick="SelectColumn(this.id);" name="column" id="'.$col_infos['name'].'" '.(!$key?'checked="checked" ':'').'type="radio">';
     echo '<b>'.htmlentities($col_infos['name']).'</b> '.$col_infos['type'].' '.(($col_infos['pk'])?' [PK] ':' ').' '.($col_infos['dflt_value']?'default : '.htmlentities($col_infos['dflt_value']):'').'</label><br/>'."\n";
     echo '<input size="35" name="col_'.$col_infos['name'].'" id="col_'.$col_infos['name'].'" type="text" value="" class="text"><br/><br/>';
     if ($key==0) echo '<script type="text/javascript">SelectColumn("'.$col_infos['name'].'");</script>';
   }
   echo '
   </td>
   <td align="right" valign="top">
     <table><tr><td nowrap="nowrap" valign="top">Select MySQL Table to <br/>fill destination columns</td>
     <td>
     <select valign="bottom" onChange="ShowColumns(this);" name="mysql_tables[]" multiple="multiple" size="7">
     ';
  foreach($tbs['tables'] as $tb=>$rows) echo "<option value=\"$tb\">$tb ($rows)</option>";
  echo '
     </select>
     </td></tr></table><table>';
  if (isset($tbs['columns'])) foreach($tbs['columns'] as $table=>$columns) {
    echo '
  <tr id="tr_'.clean_name($table).'" class="SelectHidded">
   <td align="right">Base '.htmlentities($_SESSION['plugin_mysql_base']).', table <b>'.htmlentities($table).'</b><br/>
     <select style="width:300px" onChange="SetColValue(this);return false;" size="10" name="mysql_cols_'.clean_name($table).'[]" multiple="multiple">';
     foreach ($columns as $col=>$infos) echo "<option value=\"$col\">$col ($infos)</option>";
    echo '
     </select>
   </td>
  </tr>';     
  }
  echo '
  </td></tr></table></td>
  <tr>
   <td colspan="2" align="center"><input type="submit" class="button"></td>
  </tr>
  </table>
  </form>
  ';
  return false;
}

function Do_ImportTableData($link) {

  if (count($_POST) && isset($_POST['mysql_confirm'])) {  
    $mysqlQ = 'SELECT ';
    $sqlitQ = 'INSERT INTO '.brackets($GLOBALS['table']).'(';

  echo '<table class="Browse"><thead><tr><td class="Browse">Transfert... </td></tr></thead><tr><td style="padding:10px">';


    foreach ($_SESSION['plugin_mysql_tables'] as $key=>$value) {
      if (substr($key,0,4)=='col_') {
       $mysqlQ .= $value.', ';
       $sqlitQ .= brackets(substr($key,4)).', ';
      }
    }
    $mysqlQ = substr($mysqlQ,0,strlen($mysqlQ)-2);
    $sqlitQ = substr($sqlitQ,0,strlen($sqlitQ)-2);
    $mysqlQ .= ' FROM `'.$_SESSION['plugin_mysql_tables']['mysql_tables'][0].'`;';
    $sqlitQ .= ') VALUES ( %%VALUES%% );';
    
    echo '<b>MYSQL : '.htmlentities($mysqlQ).'<br/>';
    echo 'SQLITE : '.htmlentities($sqlitQ).'</b><br/><br/>';
        
    $cnt=0; $err=0;
    if (!$res = mysql_query($mysqlQ,$link)) { 
      die(mysql_error());
    } else {
      sqlitem_query($GLOBALS['workDb']->connId,'BEGIN;');
      while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
        foreach($row as $key=>$val) $row[$key]="'".$val."'";
        $query = str_replace('%%VALUES%%',implode(',',$row),$sqlitQ);
        if ($resInsert = sqlitem_query($GLOBALS['workDb']->connId,$query)) {
          $cnt++;
          echo implode(',',$row)."<br/>";
        } else {
          sqlitem_query($GLOBALS['workDb']->connId,'ROLLBACK;');
          echo "<b>REQUETE EN ERREUR : <br/>".$query."</b><br/>";
          $err=true;
          break;
        }
      }
      mysql_free_result($res);

      echo '</td></tr>';
      echo '</table>';

      if (!$err) {
        sqlitem_query($GLOBALS['workDb']->connId,'COMMIT;');
        echo "<b>$cnt lignes importées !</b>";
      } else return false;
      
    }
    
    return false; //$res;
  }
  
  echo '<form method="POST"><input type="hidden" name="mysql_confirm" value="doIt">';
  echo '<table class="Browse"><thead><tr><td class="Browse">Résumé: </td></tr></thead><tr><td style="padding:10px">';
  echo 'A transférer depuis la base <b>'.$_SESSION['plugin_mysql_base'].'</b> du serveur MySQL <b>'.$_SESSION['plugin_mysql_server'].'</b> :</br><ul>';
  foreach ($_POST as $key=>$value) {
    if (substr($key,0,4)=='col_') {      
     echo '<li>'.htmlentities('`'.$value.'` => '.substr($key,4)).'</li>'; 
    }
  } 
  echo '</ul>';
  echo '</td></tr>';
  echo '<tr><td style="padding-bottom:10px" align="center"><input type="submit" class="button" value="Importer"></td></tr>';
  echo '</table>';
  echo '</form>';
}

?>
