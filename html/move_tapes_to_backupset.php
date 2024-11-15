<?php

/* 
 * Move tapes from one backup set to another
 * 
 */

require_once 'includes/header.inc.php';

if(!isset($_POST['backupset_id'])) {
    echo("Error: Please select a backup set.");
    
} else{
    $backupset_id = $_POST['backupset_id'];
    $backupset = new backupset($db, $backupset_id);
    $messages = "";
    
    if(isset($_POST['submit_move'])) {
    
        
    if(!isset($_POST['new_backup_set_id'])) {
        $new_backupset_id = $_POST['new_backupset_id'];
    $new_backupset = new backupset($db, $new_backupset_id);
            $new_backup_set = $_POST['new_backupset_id'];
            
    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {

            $tape_id = $checked;
            $result = $backupset->move_tape_to_new_backupset($tape_id, $new_backup_set);

            html::write_message($result);
             
        }
    } else {
        echo(html::error_message("Nothing checked"));
    }
    } else {
        echo(html::error_message("Please select a backup set to move the tapes to."));
    }
}

$backupset_id = $_POST['backupset_id'];


$backupset_data = new backupset($db, $backupset_id);

$tapes = $backupset_data->get_tapes_in_backupset();

echo("<h3>Removing tapes from ".$backupset_data->get_name()."</h3>");
if($messages != "") {
    echo("<BR>");
    echo($messages);
}
echo("Tapes currently in <B>".$backupset_data->get_name()."</B>:<BR>");
echo("<form class='form-inline' name='remove_tapes_form' method='POST'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<fieldset><table id='remove_tapes' class='table table-bordered table-hover table-striped display'>");

if(count($tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th><input type=checkbox onClick=toggleAll(this,'checkbox') /><th>Label</th><th>Type</th><th>Parent Location</th></thead>");
    echo("<tbody>");
    foreach($tapes as $tape) {
        echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$tape->get_id()."' value='".$tape->get_id()."'>");
    echo("</td>");

        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_full_path()."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");

echo("<div class='row'><select class='form-control' id='new_backupset_id' name='new_backupset_id'>");
$all_backup_sets = backupset::get_all_backupsets($db,1);
foreach($all_backup_sets as $backup_set) {
    echo("<option value=".$backup_set->get_id(). ">".$backup_set->get_name(). "</option>");
}
echo("</select>");

echo("&nbsp;<input class='btn btn-primary' type='submit' name='submit_move' value='Move Selected Tapes to New Backupset' class='icon_submit' id='move_tapes_from_backup_submit'>");
    echo("&nbsp;<input class='btn btn-warning' type='button' onclick=\"window.location='view_backupsets.php'\" name='cancel' value='Cancel'></div>");
    echo("</form>");

    
}
require_once 'includes/footer.inc.php';

?>
