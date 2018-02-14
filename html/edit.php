<div id=editpage>

<?php

include 'includes/functions.inc.php';
include 'includes/includes.inc.php';

if (isset($_POST['submit'])) {

  $submit = $_POST['submit'];
  $multiedit = $_POST['multiedit'];
  $table = $_POST['table']; 

  if (empty($multiedit)) {
    print "<script type=\"text/javascript\">parent.window.location.href='{$table}.php'</script>";
  }

  if ($submit == "delete") {
    $query = "DELETE FROM $table WHERE ";
    for($i=0;$i<count($multiedit);$i++){
	    $query .= "id=$multiedit[$i]";
	    if ($i<(count($multiedit)-1))
	    	$query .= " OR ";
    }
    mysql_query($query);
    print "<script type=\"text/javascript\">parent.window.location.href='{$table}.php'</script>";
  }

  if ($submit == "edit") {

    $backupset = mysqlToArray(mysql_query("select * from backupset"));
    $carton = mysqlToArray(mysql_query("select * from carton"));
    $location = mysqlToArray(mysql_query("select * from location"));

    print "<form method=\"post\" action=\"process.php\">";

    print "<h2>Editing the following records</h2>";

    print "<input type=submit name=submit value=save>";
    print "<input type=button value=cancel onclick=\"parent.window.location.href='{$table}.php'\" />";

    print "<fieldset>";
    print "<table>";

    //$query = "select * FROM $table WHERE ";
    $query = "select tape.id, tape.type, tape.capacity, tape.tape_number, location.name as location, backupset.name as backup_set, carton.name as carton, tape.label from tape left join location on tape.location=location.id left join backupset on tape.backup_set=backupset.id left join carton on tape.carton=carton.id WHERE ";
    for($i=0;$i<count($multiedit);$i++){
	    $query .= "tape.id=$multiedit[$i]";
	    if ($i<(count($multiedit)-1))
	    	$query .= " OR ";
    }
    $result = mysql_query($query);

    $headresult=mysql_query("select * from $table limit 1");
    $row=mysql_fetch_assoc($headresult);

    print "<tr>";
    foreach(array_keys($row) as $piece) {
      print "<th>";
      print $piece;
      switch ($piece) {
        case "id":
          print "<br><br>change to:";
          print "<input type=hidden name=table value=$table>";
          break;
        case "type":
          print "<br><br>";
          createInput("text","type","");
          break;
        case "capacity":
          print "<br><br>";
          createInput("text","capacity","");
          break;
        case "tape_number":
          print "<br><br>";
          createInput("text","tape_number","");
          break;
        case "location":
          print "<br><br>";
          createInput("select","location","",$location);
          break;
        case "backup_set":
          print "<br><br>";
          createInput("select","backup_set","",$backupset);
          break;
        case "carton":
          print "<br><br>";
          createInput("select","carton","",$carton);
          break;
        case "label":
          print "<br><br>";
          createInput("text","label","");
          break;
        case "name":
          print "<br><br>";
          createInput("text","name","");
          break;
        case "begin":
          print "<br><br>";
          createInput("begin","begin","");
          break;
        case "end":
          print "<br><br>";
          createInput("end","end","");
          break;
        case "date_added":
          print "<br><br>";
          createInput("date","date_added","");
          break;
        case "contact":
          print "<br><br>";
          createInput("text","contact","");
          break;
        default:
      }
      print "</th>";
    }
    print "</tr>";

    while($row=mysql_fetch_assoc($result)) {
      print "<tr>";
      foreach(array_keys($row) as $piece) {
		print "<td align=center style='background-color:#333333;'>";
        if (!isset($row[$piece]) or $row[$piece]=='') {
          print '&nbsp';
        } else {
          if ($piece == "id") {
            print "<input type=hidden name=id[] value={$row[$piece]} checked>";
            print $row[$piece];
          } else {
            print $row[$piece];
          }
        }
        print "</td>";
      }
      print "</tr>\n";
    }
    
    print "</table>";
    print "</fieldset>";
    print "</form>";

  }

}

?>

</div>