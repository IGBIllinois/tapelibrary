<?php

require_once '../libs/db.class.inc.php';


function cleanArray($array) {
    global $db;
  foreach ($array as $key => $value){
    $array[$key] = mysql_real_escape_string($value);
  }
  return $array;
}

function mysqlToArray($mysqlResult) {
  $dataArray = array();
  $i = 1;
  while($row = mysql_fetch_array($mysqlResult,MYSQL_ASSOC)) {	
    foreach($row as $key=>$data) {
       
      $dataArray[$i][$key] = $data;
    }
    $i++;
  }
  return $dataArray;
}

function createInput($type, $name, $default, $array=array(), $id="", $onChange="", $id_name="id") {
  $formName = $name;
    if($id != "") {
        $formName = $name . "_" . $id;
    }
      
    switch ($type) {
      
    case "select":
      print "<select id='{$formName}' name='{$formName}' ". ($onChange != "" ? " onChange='$onChange' " : "") . (($id != "") ? " id='{$name}_{$id}' ": "") .">";
      print "<option value=''>None</option>";
      $i=0;
      foreach ($array as $value) {
        print "<option value={$value[$id_name]}";
        if ($value['id'] == $default)
          print " selected";
        
        print ">{$value['name']}</option>";
      }
      print "</select>";
      break;
    case "date":
      print "<input type=text id=datepicker class={$name} name={$formName} value={$default}>";
      break;
    case "begin":
      print "<input type=text id=from class={$name} name={$formName} value={$default}>";
      break;
    case "end":
      print "<input type=text id=to class={$name} name={$formName} value={$default}>";
      break;
    default:
      //print "<input class={$name} name={$formName} ".(($id != "") ? " id={$formName} " : "" ) . " value=\"{$default}\" placeholder=\"Enter {$name}\">";
        print "<input class='{$name}' name='{$formName}' ".(($id != "") ? " id='{$formName}' " : "" ) . " value=\"{$default}\">";
  }
}

function printSQL($query, $tableid) {
  if ($tableid == 'tape') {
    $backupset = mysqlToArray(mysql_query("select * from backupset"));
    $carton = mysqlToArray(mysql_query("select * from carton"));
    $container = mysqlToArray(mysql_query("select * from container"));
  }
  $result=mysql_query($query);
  if (mysql_num_rows($result) == 0)
    print "<p>No Results</p>";
  print "<div id=log>0 records checked</div>";
  print "<fieldset>";
  print "<br />";
  print "<form action=edit.php method=post>";
  if ($tableid == 'tape') {
    print "<input type=button value=add onclick=\"location.href='add_multiple.php'\" />&nbsp;&nbsp;&nbsp;";
  } else {
    print "<input type=button value=add class=iframe_add href='add.php?table={$tableid}' />&nbsp;&nbsp;&nbsp;";
  }
  print "<input type=submit name=submit value=edit class=icon_submit id=edit_submit>&nbsp;&nbsp;&nbsp;";
  print "<input type=submit name=submit value=delete class=icon_submit id=delete_submit>";
  print "<br /><br />";
  print "<table id={$tableid} class='sortable'>";
  $headresult=mysql_query($query . " limit 1");
  $row=mysql_fetch_assoc($headresult);
  print "<thead><tr>";
  foreach(array_keys($row) as $piece) {
    switch ($piece) {
	  case "id":
	    print "<th><input type=checkbox id=checkall /></th>";
	    break;
	  case "capacity":
	    print "<th>capacity<br />(in GB)</th>";
	    break;
	  case "tape_number":
	    print "<th>$piece</th>";
	    break;
	  default:
	    print "<th>$piece</th>";
    }
  }
  print "</tr></thead>";
  print "<tfoot><tr>";
  print "<td colspan=2 align=center>";
  if ($tableid == 'tape') {
    print "<input type=button value=add onclick=\"location.href='add_multiple.php'\" />&nbsp;&nbsp;&nbsp;";
  } else {
    print "<input type=button value=add class=iframe_add href='add.php?table={$tableid}' />&nbsp;&nbsp;&nbsp;";
  }
  print "<input type=submit name=submit value=edit class=icon_submit id=edit_submit>&nbsp;&nbsp;&nbsp;";
  print "<input type=submit name=submit value=delete class=icon_submit id=delete_submit>";
  print "</td>";
  print "</tr></tfoot>";
  print "</table>";
  print "<input type=hidden name=table value={$tableid}>";
  print "</form>";
  print "</fieldset>";

}


    function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
