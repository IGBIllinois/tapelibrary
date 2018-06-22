
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
?>

<?php
if(!isset($_POST['backupset_id'])) {
    echo("Error: Please select a backup set.");
    
} else{
    $messages = "";
    $backupset_id = $_POST['backupset_id'];
    $backupset = new backupset($db, $backupset_id);
    if(isset($_POST['submit_remove'])) {
    //print_r($_POST);
        
    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {

            //echo("ID $checked is checked.<BR>");
            $tape_id = $checked;
            
            //$tape_id = $_POST['tape_id_'.$id];
            
            /*
            echo("id = $id<BR>");
            echo("container = $container<BR>");
            echo("type = $type<BR>");
            echo("service = $service<BR>");
            echo("name = $tape_name<BR>");
            echo("active = $active<BR>");
             * 
             */
            //$db->edit_tape($id, $tape_label, $container, $type, $service, $active);
            $result = $backupset->remove_tape_from_backupset($tape_id);

            //$tape = new tape($db, $tape_id);
            //$backupset = new backupset($db, $backupset_id);
            if($result['RESULT']) {
                //$messages .= ("<div class='alert alert-success'>Tape ".$tape->get_label() ." successfully removed from ".$backupset->get_name() ."</div>");
                $messages .= ("<div class='alert alert-success'>".$result['MESSAGE']."</div>");
            } else {
                //$messages .= ("<div class='alert alert-danger'>There was an error removing ".$tape->get_label() ."  from ".$backupset->get_name() ."</div>");
                $messages .= (html::error_message($result['MESSAGE']));
            }
             
        }
    } else {
        echo(html::error_message("Nothing checked"));
    }
}

$backupset_id = $_POST['backupset_id'];

//$backupset_data = $db->get_backupset($backupset_id);
$backupset_data = new backupset($db, $backupset_id);

$tapes = $backupset_data->get_tapes_in_backupset();

//echo("Backupset: ".$backupset_data['name']."<BR>");
//echo("Begin Date: ".$backupset_data['begin']."<BR>");
//echo("End Date: ".$backupset_data['end']."<BR>");
//echo("Program: ".$backupset_data['program']."<BR>");
//echo("Notes: ".$backupset_data['notes']."<BR>");
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
        //echo("<tr><td>".$tape['tape_number']."</td>");
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
include_once 'includes/footer.inc.php';