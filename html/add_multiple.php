<?php

$page = "add_multiple";

include 'includes/header.inc.php';

print "<h2>Add Multiple</h2>";

if (isset($_POST['submit'])) {

	//$_POST = cleanArray($_POST);
	$type = $_POST['type'];
	if ($type == '')
		$type = ' ';
	$capacity = $_POST['capacity'];
	$tape_from = $_POST['tape_from'];
	$tape_to = $_POST['tape_to'];
	$container = $_POST['container'];
	$backup_set = $_POST['backup_set'];
	$carton = $_POST['carton'];
	$label = array();
	for ($i=$tape_from;$i<=$tape_to;$i++) {
            $label[$i] = $_POST['label'.$i];
            //echo("label[$i] = ".$label[$i]."<BR>");
	}

	if (is_numeric($tape_from) && is_numeric($tape_to) && $tape_from <= $tape_to) {
		for ($i = $tape_from; $i <= $tape_to; $i++) {
			//mysql_query("insert into tape (type,capacity,tape_number,container,backup_set,carton,label) values ('$type','$capacity','$i','$container','$backup_set','$carton','$label[$i]')");
                    add_tape($db, $i, $label[$i], $type, $capacity, $container, $backup_set, $carton );
		}
		//print "<script type=\"text/javascript\">parent.window.container.href='index.php'</script>";
                print("<BR>Tapes added<BR>");
	} else {
		print "<p><b>Something went wrong, please try again</b></p>";
            
	}
}

if ($login == 1) {

    //$backupset = mysqlToArray(mysql_query("select * from backupset"));
    //$carton = mysqlToArray(mysql_query("select * from carton"));
    //$container = mysqlToArray(mysql_query("select * from container"));
    
    $backupset = get_all_backups($db);
    $carton = get_all_cartons($db);
    $container = get_all_containers($db);
    
	print "<br />";
    print "<b>Add Multiple Tapes</b>";
    print "<form id=\"addform\" method=\"post\" action=\"add_multiple.php\">";
    print "<table>";
      print "<tr>";
        print "<td>type</td>";
        print "<td>";
        createInput("text","type","");
        print "</td>";
        print "<td rowspan=6>";
        print "<div id='add_multi_labels'>";
		print "</div>";
        print "</td>";
      print "</tr>";
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td>capacity (in GB)</td>";
        print "<td>";
        createInput("text","capacity","");
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td>tape_number(s)</td>";
        print "<td>from: ";
        createInput("text","tape_from","");
        print "<br />to: ";
        createInput("text","tape_to","");
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td>container</td>";
        print "<td>";
        createInput("select","container","",$container);
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td>backup_set</td>";
        print "<td>";
        createInput("select","backup_set","",$backupset);
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td>carton</td>";
        print "<td>";
        createInput("select","carton","",$carton);
        print "</td>";
      print "</tr>";
    print "</table>";
    print "<p>Adding <label id=number_of_tapes>0</label> tapes</p>";
    print "<p><input type=submit name=submit value=submit /></p>";
    print "</form>";

} else {
	include 'logout.php';
}

include "includes/footer.inc.php"; ?>