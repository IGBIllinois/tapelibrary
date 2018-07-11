

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<h3>Edit Backup Set</H3>");
if(isset($_POST['id'])) {
    //echo("Adding backupset : ".$_POST['backupset_name']."<BR>");

    $backupset_id = $_POST['id'];
    if(!isset($backupset_id)) {
        echo(html::error_message("The backupset you selected does not exist. Please try again."));;
        return;
    }
    $backupset = new backupset($db, $backupset_id);
    if($backupset == null) {
        echo(html::error_message("The backupset you selected does not exist. Please try again."));

    } else {
    $name = $backupset->get_name();
    $begin = $backupset->get_begin_date();
    $end = $backupset->get_end_date();
    $program = $backupset->get_program();
    $notes = $backupset->get_notes();
    $main_location = $backupset->get_main_location();
    $active = $backupset->is_active();

}


if(isset($_POST['submit_edit'])) {
    
$backupset_id = $_POST['backupset_id'];

    //if(isset($_POST['container_type'])) {
    //    $container_type = $_POST['container_type'];
    //}
    $error= "";
    if(isset($_POST['name']) && $_POST['name']!="") {
        $name = $_POST['name'];
    } else {
        $error .= html::error_message("Please enter a name for the backup set.");
    }
    if(isset($_POST['begin']) && $_POST['begin'] != "") {
        $begin = $_POST['begin'];
    } else {
        $error .= html::error_message("Please enter a start date for the backup set. (YYYY-MM-DD)");
    }
    if(isset($_POST['end']) && $_POST['end']!="") {
        $end = $_POST['end'];
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
    if($begin > $end) {
        $error .= html::error_message("Please make sure the start date is before the end date. (YYYY-MM-DD)");
            
    }
    if(strlen($error) == 0) {
        //echo("name = $name, begin = $begin, end = $end, program = $program, notes = $notes <BR>");
        $backupset = new backupset($db, $backupset_id);
        $result = $backupset->edit_backupset($name, $begin, $end, $program, $main_location, $notes);
        //echo("result = $result<BR>");
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
echo("<form name='edit_backupset' action='edit_backupset.php' method='POST'>");
echo("<input type='hidden' name='backupset_id' value='".$backupset_id."'>");
echo("<input type='hidden' name='id' value='".$backupset_id."'>");
echo("<table class='table table-bordered'>");
echo("<tr><td width=30%>Backup Set Name:</td><td><input type='text' name='name' id='name' ".(isset($name) ? ("value='$name'") : "")."></td></tr>");
echo("<tr><td>Start Date (YYYY-MM-DD):</td><td><input type='text' name='begin' pattern='[0-9]{4}-[0-9]{2}-[0-9]{2}' id='begin' ".(isset($begin) ? ("value='$begin'") : "")."></td></tr>");
echo("<tr><td>End Date (YYYY-MM-DD):</td><td><input type='text' name='end' id='end' ".(isset($end) ? ("value='$end'") : "")."></td></tr>");
echo("<tr><td>Program: </td><td>");
//createInput("select", "program",(isset($program) ? ("$program") : ""), program::get_programs($db));

$all_programs = program::get_programs($db);
      echo "<select id='program' name='program'>";
      echo "<option value=''>None</option>";
      $i=0;
      foreach ($all_programs as $curr_program) {
        echo "<option value='".$curr_program->get_id()."'";
        if (isset($program) && $program == $curr_program->get_id())
          echo " selected";
        
        echo ">".$curr_program->get_name(). " : ". $curr_program->get_version()."</option>";
      }
      echo "</select>";
      
echo("</td></tr>");

echo("</td></tr>");
echo("<tr><td>Main Location <a href='add_location.php'>(Add a new location?)</a></td><td>");
createInput("select", "main_location",(isset($main_location) ? ("$main_location") : ""), tape_library_object::get_locations($db));
echo("</td></tr>");
echo("<tr><td>Notes:</td><td><textarea rows='2' name='notes' id='notes'>".(isset($notes) ? $notes : "")."</textarea></td></tr>");

echo("</table>");
echo("<input type='submit' name='submit_edit' value='Edit Backup Set'>");
echo("</form>");

echo("</form>");




//echo("<a href=add_tapes_to_backupset.php?backupset_id=".$backupset_id.">Add tapes to backup set</a>");

} else {
    echo(html::error_message("The backupset you selected does not exist. Please try again."));
}
include 'includes/footer.inc.php';

