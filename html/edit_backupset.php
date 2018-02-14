

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
        echo("<div class='alert alert-danger'>The backupset you selected does not exist. Please try again.</div>");
        return;
    }
    $backupset = $db->get_backupset($backupset_id);
    if($backupset == 0) {
        echo("<div class='alert alert-danger'>The backupset you selected does not exist. Please try again.</div>");

    } else {
    $name = $backupset['name'];
    $begin = $backupset['begin'];
    $end = $backupset['end'];
    $program = $backupset['program'];
    $notes = $backupset['notes'];

}


if(isset($_POST['submit_delete'])) {
    if(isset($_POST['backupset_id'])) {
        $backupset_id = $_POST['backupset_id'];
    }
    $backupset = $db->get_backupset($id);
    $db->deactivate_backupset($backupset_id);
    echo("<div class='alert alert-success'>Backupset ". $backupset['name'] . " successfully deactivated.</div>");
    return;
    
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
        $error .= "<div class='alert alert-danger'>Please enter a name for the backup set.</div>";
    }
    if(isset($_POST['begin']) && $_POST['begin'] != "") {
        $begin = $_POST['begin'];
    } else {
        $error .= "<div class='alert alert-danger'>Please enter a start date for the backup set. (YYYY-MM-DD)</div>";
    }
    if(isset($_POST['end']) && $_POST['end']!="") {
        $end = $_POST['end'];
    } else {
        $error .= "<div class='alert alert-danger'>Please enter an end date for the backup set. (YYYY-MM-DD)</div>";
    }

    if(isset($_POST['program'])) {
        $program = $_POST['program'];
    }
    if(isset($_POST['notes'])) {
        $notes = $_POST['notes'];
    }
    if($begin > $end) {
        $error .= "<div class='alert alert-danger'>Please make sure the start date is before the end date. (YYYY-MM-DD)</div>";
    }
    if(strlen($error) == 0) {
        //echo("name = $name, begin = $begin, end = $end, program = $program, notes = $notes <BR>");
        $result = $db->edit_backupset($backupset_id, $name, $begin, $end, $program, $notes);
        //echo("result = $result<BR>");
        if($result != 0) {
            echo("<div class='alert alert-success'>Backup Set ".$_POST['name']." successfully edited.</div>");
        } else {
            echo("<div class='alert alert-danger'>Error in editing backup set.</div>");
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
echo("<tr><td>Program (Crashplan, Backula, etc.):</td><td><input type='text' name='program' id='program' ".(isset($program) ? ("value='$program'") : "")."></td></tr>");
echo("<tr><td>Notes:</td><td><textarea rows='2' name='notes' id='notes'>".(isset($notes) ? $notes : "")."</textarea></td></tr>");


echo("</table>");
echo("<input type='submit' name='submit_edit' value='Edit Backup Set'>");
echo("</form>");
echo("<BR>");


/*
echo("<form name='delete_backupset' action='edit_backupset.php onsubmit=\"return confirm('Do you really want to remove this backupset?')>\"");
echo("<input type='hidden' name='id' value='".$backupset_id."'>");
echo("<input type='submit' name='submit_delete' value='Delete Backup Set'>");
echo("</form>");
*/



//echo("<a href=add_tapes_to_backupset.php?backupset_id=".$backupset_id.">Add tapes to backup set</a>");

} else {
    echo("<div class='alert alert-danger'>The backupset you selected does not exist. Please try again.</div>");
}
include 'includes/footer.inc.php';

