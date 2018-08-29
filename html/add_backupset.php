<?php
/* 
 * Add a new Backup Set to the database
 */

include 'includes/header.inc.php';

echo("<H3>Add Backup Set</H3>");

if(isset($_POST['backupset_name'])) {
    // Submit the form...
    
    $name = $_POST['backupset_name'];
    $container_type=null;
    $container_id=null;
    $main_location = null;

    $error= "";
    if(isset($_POST['backupset_name']) && $_POST['backupset_name']!="") {
        $backupset_name = $_POST['backupset_name'];
    } else {
        $error .= html::error_message("Please enter a name for the backup set.");
    }
    if(isset($_POST['start_date']) && $_POST['start_date'] != "") {
        $start_date = $_POST['start_date'];
    } else {
        $error .= html::error_message("Please enter a start date for the backup set. (YYYY-MM-DD)");
    }
    if(isset($_POST['end_date']) && $_POST['end_date']!="") {
        $end_date = $_POST['end_date'];
    } else {
        $error .= html::error_message("Please enter an end date for the backup set. (YYYY-MM-DD)");
    }

    if(isset($_POST['program'])) {
        $program = $_POST['program'];
    }
    
    if(isset($_POST['main_location'])) {
        $main_location = $_POST['main_location'];
    }
    
    if(isset($_POST['notes'])) {
        $notes = $_POST['notes'];
    }
    if(isset($start_date) && isset($end_date) && $start_date > $end_date) {
        $error .= html::error_message("Please make sure the start date is before the end date. (YYYY-MM-DD)");
    }
    if(strlen($error) == 0) {
        $backupset = new backupset($db);
        try{ 
        $result = $backupset->add_backupset($backupset_name, $start_date, $end_date, $program, $main_location, $notes);
        } catch(Exception $e) {
            echo($e->getTraceAsString());
        }

        if($result['RESULT']) {
            echo(html::success_message($result['MESSAGE']));
        } else {
            echo(html::error_message($result['MESSAGE']));
        }
     } else {
         echo($error."<BR>");
     }
}

echo("<BR>");
echo("<form name='add_backupset' action='add_backupset.php' method='POST'>");

echo("<table class='table table-bordered'>");
echo("<tr><td width=30%>Backup Set Name:</td><td><input type='text' name='backupset_name' id='backupset_name' ".(isset($backupset_name) ? ("value='$backupset_name'") : "")."></td></tr>");
echo("<tr><td>Start Date (YYYY-MM-DD):</td><td><input type='text' name='start_date' pattern='[0-9]{4}-[0-9]{2}-[0-9]{2}' id='start_date' ".(isset($start_date) ? ("value='$start_date'") : "")."></td></tr>");
echo("<tr><td>End Date (YYYY-MM-DD):</td><td><input type='text' name='end_date' id='end_date' ".(isset($end_date) ? ("value='$end_date'") : "")."></td></tr>");
echo("<tr><td>Program: <a href='add_program.php'>(Add a new program?)</a></td><td>");

$all_programs = program::get_programs($db);
      echo "<select id='program' name='program'>";
      echo "<option value=''>None</option>";
      $i=0;
      foreach ($all_programs as $curr_program) {
        echo "<option value='".$curr_program->get_id()."'";
        if (isset($program) && $program == $curr_program->get_id())
          echo " selected";
        
        echo ">".$curr_program->get_name(). 
                (($curr_program->get_version() != null && 
                $curr_program->get_version() != "") ? 
                " (Version ".$curr_program->get_version().")" : "")."</option>";
      }
      echo "</select>";
      
echo("</td></tr>");

echo("<tr><td>Notes:</td><td><textarea rows='2' name='notes' id='notes'>".(isset($notes) ? $notes : "")."</textarea></td></tr>");

echo("</table>");
echo("<input type='submit' name='submit' value='Add Backup Set'>");
echo("</form>");

include 'includes/footer.inc.php';
