<?php

include 'includes/functions.inc.php';
include 'includes/includes.inc.php';

if (isset($_POST['submit'])) {

	$_POST = cleanArray($_POST);

	$table = $_POST['table'];
    $name = $_POST['name'];

    switch ($table) {
      case "carton":
        $date_added = $_POST['date_added'];
		mysql_query("insert into carton (name,date_added) values ('$name','$date_added')");
        break;
      case "backupset":
        $begin = $_POST['begin'];
        $end = $_POST['end'];
		mysql_query("insert into backupset (name,begin,end) values ('$name','$begin','$end')");
        break;
      case "location":
        $contact = $_POST['contact'];
		mysql_query("insert into location (name,contact) values ('$name','$contact')");
        break;
    }

	print "<script type=\"text/javascript\">parent.window.location.href='{$table}.php'</script>";

}

if (isset($_GET['table'])) {

	$table = $_GET['table'];

    print "<form id=\"addform\" method=\"post\" action=\"add.php\">";
    print "<table>";

	print "<input type='hidden' name='table' value='$table' />";

	$table = $_GET['table'];
	switch ($table) {
	  case "carton":
	    print "<h3>Add Cartons</h3>";
        print "<tr>";
        print "<td>Name</td>";
        print "<td>";
        createInput("text","name","");
        print "</td>";
        print "</tr>";
        print "<tr>";
        print "<td>Date Added</td>";
        print "<td>";
        createInput("date","date_added","");
        print "</td>";
        print "</tr>";
	    break;
	  case "backupset":
	    print "<h3>Add Backup Sets</h3>";
        print "<tr>";
        print "<td>Name</td>";
        print "<td>";
        createInput("text","name","");
        print "</td>";
        print "</tr>";
        print "<tr>";
        print "<td>Begin Date</td>";
        print "<td>";
        createInput("begin","begin","");
        print "</td>";
        print "</tr>";
        print "<tr>";
        print "<td>End Date</td>";
        print "<td>";
        createInput("end","end","");
        print "</td>";
        print "</tr>";
	    break;
	  case "location":
	    print "<h3>Locations</h3>";
        print "<tr>";
        print "<td>Name</td>";
        print "<td>";
        createInput("text","name","");
        print "</td>";
        print "</tr>";
        print "<tr>";
        print "<td>Contact</td>";
        print "<td>";
        createInput("text","contact","");
        print "</td>";
        print "</tr>";
	    break;
    }

    print "</table>";
    print "<p><input type=submit name=submit value=submit /></p>";
    print "</form>";

}

?>