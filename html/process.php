<?php

include 'includes/functions.inc.php';
include 'includes/includes.inc.php';

if (isset($_POST['submit'])) {

  $id = $_POST['id'];
  $table = $_POST['table'];
  $name = $_POST['name'];
  $begin = $_POST['begin'];
  $end = $_POST['end'];
  $date_added = $_POST['date_added'];
  $contact = $_POST['contact'];
  $type = $_POST['type'];
  $capacity = $_POST['capacity'];
  $tape_number = $_POST['tape_number'];
  $location = $_POST['location'];
  $backup_set = $_POST['backup_set'];
  $carton = $_POST['carton'];
  $label = $_POST['label'];

  $columns = "";

  switch ($table) {
    case "backupset":
      if (!($name==""))
        $columns .= "name='{$name}', ";
      if (!($begin==""))
        $columns .= "begin='{$begin}', ";
      if (!($end==""))
        $columns .= "end='{$end}', ";
      break;
    case "carton":
      if (!($name==""))
        $columns .= "name='{$name}', ";
      if (!($date_added==""))
        $columns .= "date_added='{$date_added}', ";
      break;
    case "location":
      if (!($name==""))
        $columns .= "name='{$name}', ";
      if (!($contact==""))
        $columns .= "contact='{$contact}', ";
      break;
    default:
      if (!($type==""))
        $columns .= "type='{$type}', ";
      if (!($capacity==""))
        $columns .= "capacity='{$capacity}', ";
      if (!($tape_number==""))
        $columns .= "tape_number='{$tape_number}', ";
      if (!($location==""))
        $columns .= "location='{$location}', ";
      if (!($backup_set==""))
        $columns .= "backup_set='{$backup_set}', ";
      if (!($carton==""))
        $columns .= "carton='{$carton}', ";
      if (!($label==""))
        $columns .= "label='{$label}', ";
  }

  $columns = substr($columns, 0, -2);

  if (!($columns=="")) {

    $query = "UPDATE $table SET $columns WHERE ";

    for($i=0;$i<count($id);$i++) {
      $query .= "id=$id[$i]";
      if ($i<(count($id)-1))
        $query .= " OR ";
    }

    mysql_query($query);

  }

  print "<script type=\"text/javascript\">parent.window.location.href='{$table}.php'</script>";

}

?>