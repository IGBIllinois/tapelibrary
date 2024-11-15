<?php
/* 
This page allows a user to add existing tapes to a Backup Set
 */

require_once 'includes/header.inc.php';

?>

<?php
$begin = null;
    $end = null;
    $type = null;
    $container = null;
    $active = 1;

if(isset($_POST['backupset_id'])) {
    $backupset_id = $_POST['backupset_id'];
} else {   
    echo(html::error_message("There was an error with the specified backup set. Please try again."));
    return 0;
}    

$backupset = new backupset($db, $backupset_id);

if($backupset == null) {
    echo(html::error_message("Please select a valid backup set."));
} else {
$messages = "";


if(isset($_POST['add_tapes_submit'])) {
// Submit the form...    
    if(isset($_POST['checkbox'])) {
        // Update each checked checkbox
        foreach($_POST['checkbox'] as $checked) {
            
            $id = $checked;
            
            $tape = new tape_library_object($db, $id);
            $backupset = new backupset($db, $backupset_id);
            
            $result = $backupset->add_tape_to_backupset($id);
            
            html::write_message($result);

        }
    } else {
        $messages .= (html::warning_message("Nothing checked"));
    }
}


echo("<h3>Adding tapes to ".$backupset->get_name()."</h3>");
echo("<fieldset>");
echo("<form method=POST action=add_tapes_to_backupset.php>");

$backupset_name = $backupset->get_name();
echo("Current tapes not assigned to a backup set:</B>:") ;
echo("<form method='POST' name='add_tapes_submit' action='add_tapes_to_backupset.php'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");


// Get a list of tapes currently not in a Backup Set

$current_unassigned_tapes = tape_library_object::get_tapes_without_backupset($db);

if(count($current_unassigned_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><th><input type=checkbox onClick=toggleAll(this,'checkbox') /></th><th>Label</th><th>Type</th><th>Parent Location</th><th>Current Backup set</th></thead>");
    echo("<tbody>");
    foreach($current_unassigned_tapes as $tape) {
        
        echo("<tr><td>");
        echo("<input type='checkbox' name=checkbox[] id='".$tape->get_id()."' value='".$tape->get_id()."'>");
        echo("</td>");
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_full_path()."</td>");
        // Shouldn't have a backupset
        $curr_backupset_id = $tape->get_backupset();
	$tape_backupset_name = "";
        if($curr_backupset_id == -1) {
            $tape_backupset_name = "None";
        } else {
            if($curr_backupset_id != null && $curr_backupset_id != "") {

                 $tape_backupset = new backupset($db, $curr_backupset_id);
                 $tape_backupset_name = $tape_backupset->get_name();

             }
        }
        echo("<td>".$tape_backupset_name."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table>");

echo("<BR><BR>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
    echo("<input class='btn btn-primary' type='submit' name='add_tapes_submit' value='Add selected tapes to backup set'>");
    echo("&nbsp;<input class='btn btn-warning' type='button' onclick=\"window.location='view_backupsets.php'\" name=cancel value='Cancel'>");
    echo("</form>");

echo("</fieldset>");

if($messages != "") {
    echo("<BR>");
    echo($messages);
}
}
require_once 'includes/footer.inc.php';

?>
