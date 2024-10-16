<?php

require_once 'includes/header.inc.php';
?>

<?php
if(!isset($_POST['backupset_id'])) {
    echo("Error: Please select a backup set.");
    
} else{
    $messages = "";
    $backupset_id = $_POST['backupset_id'];
    $backupset = new backupset($db, $backupset_id);
    if(isset($_POST['submit_remove'])) {
        
    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {

            $tape_id = $checked;

            $result = $backupset->remove_tape_from_backupset($tape_id);

            html::write_message($result);
             
        }
    } else {
        echo(html::error_message("Nothing checked"));
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
echo("<form name='remove_tapes_form' method='POST'>");
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
echo("<input type=submit name=submit_remove value='Remove Selected Tapes from Backupset' class=icon_submit id=remove_tapes_from_backup_submit>");
    echo("<input type=button onclick=\"window.location='view_backupsets.php'\" name=cancel value='Cancel'>");
    echo("</form>");

    
}
require_once 'includes/footer.inc.php';

?>
