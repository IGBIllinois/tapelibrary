

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>Add Backup Set</H3>");
if(isset($_POST['backupset_name'])) {
    //echo("Adding backupset : ".$_POST['backupset_name']."<BR>");

    $name = $_POST['backupset_name'];
    $container_type=null;
    $container_id=null;
    $main_location = null;

    //if(isset($_POST['container_type'])) {
    //    $container_type = $_POST['container_type'];
    //}
    $error= "";
    if(isset($_POST['backupset_name']) && $_POST['backupset_name']!="") {
        $backupset_name = $_POST['backupset_name'];
    } else {
        $error .= "<div class='alert alert-danger'>Please enter a name for the backup set.</div>";
    }
    if(isset($_POST['start_date']) && $_POST['start_date'] != "") {
        $start_date = $_POST['start_date'];
    } else {
        $error .= "<div class='alert alert-danger'>Please enter a start date for the backup set. (YYYY-MM-DD)</div>";
    }
    if(isset($_POST['end_date']) && $_POST['end_date']!="") {
        $end_date = $_POST['end_date'];
    } else {
        $error .= "<div class='alert alert-danger'>Please enter an end date for the backup set. (YYYY-MM-DD)</div>";
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
        $error .= "<div class='alert alert-danger'>Please make sure the start date is before the end date. (YYYY-MM-DD)</div>";
    }
    if(strlen($error) == 0) {
        //echo("name = $backupset_name, start = $start_date, end = $end_date, program = $program, notes = $notes <BR>");
        $backupset = new backupset($db);
        try{ 
        $result = $backupset->add_backupset($backupset_name, $start_date, $end_date, $program, $main_location, $notes);
        } catch(Exception $e) {
            echo($e->getTraceAsString());
        }
        //echo("result = $result<BR>");
        if($result['RESULT']) {
            echo("<div class='alert alert-success'>".$result['MESSAGE']." </div>");
        } else {
            echo("<div class='alert alert-danger'>".$result['MESSAGE']." </div>");
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
//echo("<tr><td>Program (Crashplan, Bacula, etc.):</td><td><input type='text' name='program' id='program' ".(isset($program) ? ("value='$program'") : "")."></td></tr>");
//createInput("select", "program",(isset($program) ? ("$program") : ""), $db->get_programs());
echo("<tr><td>Program: <a href='add_program.php'>(Add a new program?)</a></td><td>");

createInput("select", "program",(isset($program) ? ("$program") : ""), program::get_programs($db));
echo("</td></tr>");

echo("<tr><td>Main Location <a href='add_location.php'>(Add a new location?)</a></td><td>");
createInput("select", "main_location",(isset($main_location) ? ("$main_location") : ""), tape_library_object::get_locations($db));
echo("</td></tr>");

echo("<tr><td>Notes:</td><td><textarea rows='2' name='notes' id='notes'>".(isset($notes) ? $notes : "")."</textarea></td></tr>");


echo("</table>");
echo("<input type='submit' name='submit' value='Add Backup Set'>");
echo("</form>");

include 'includes/footer.inc.php';
