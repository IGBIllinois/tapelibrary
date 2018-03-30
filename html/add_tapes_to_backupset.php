
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';

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
    echo("There was an error with the specified backup set. Please try again.");
    return 0;
}    
    

    

//$backupset = $db->get_backupset($backupset_id);
$backupset = new backupset($db, $backupset_id);

if($backupset == null) {
    echo("<div class='alert alert-danger'>Please select a valid backup set.</div");
} else {
$messages = "";
//echo("Add tapes to backup set: ".$backupset['name']);
if(isset($_POST['add_tapes_submit'])) {
    //print_r($_POST);

    if(isset($_POST['checkbox'])) {
        
        foreach($_POST['checkbox'] as $checked) {

            //echo("ID $checked is checked.<BR>");
            
            $id = $checked;
            
            $tape = new tape($db, $id);
            $backupset = new backupset($db, $backupset_id);
            
            $result = $backupset->add_tape_to_backupset($id);

            
            if($result['RESULT']) {
                $messages .= ("<div class='alert alert-success'>".$result['MESSAGE']."</div>");
            } else {
                $messages .=("<div class='alert alert-danger'>".$result['MESSAGE']."</div>" );
            }
            
            
            
            //$tape_id = $_POST['tape_id_'.$id];
            ////$tape_label = $_POST['tape_label_'.$id];
            //$container = $_POST['tape_container_'.$id];
            //if($container == "") {
            //    $container = null;
            //}
            //$type = $_POST['tape_type_'.$id];
            //$service = $_POST['service_'.$id];
            //$active = (isset($_POST['active_'.$id]) ? 1 : 0);
            /*
            echo("id = $id<BR>");
            echo("container = $container<BR>");
            echo("type = $type<BR>");
            echo("service = $service<BR>");
            echo("name = $tape_name<BR>");
            echo("active = $active<BR>");
             * 
             */
            //edit_tape($db, $id, $tape_id, $tape_label, $container, $type, $service, $active);
             
             
        }
    } else {
        $messages .= ("<div class='alert alert-warning'>Nothing checked</div>");
    }
}
echo("<h3>Adding tapes to ".$backupset->get_name()."</h3>");
echo("<fieldset>");
echo("<form method=POST action=add_tapes_to_backupset.php>");


//
//$backupset = $db->get_backupset($backupset_id);
$backupset = new backupset($db, $backupset_id);
$backupset_name = $backupset->get_name();
echo("Current tapes not assigned to a backup set:</B>:") ;
echo("<form method='POST' name='add_tapes_submit' action='add_tapes_to_backupset.php'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_unassigned_tapes = tape_library_object::get_tapes_without_backupset($db);

if(count($current_unassigned_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><th><input type=checkbox onClick=toggleAll(this,'checkbox') /></th><th>Label</th><th>Type</th><th>Parent Location</th><th>Current Backup set</th></thead>");
    echo("<tbody>");
    foreach($current_unassigned_tapes as $tape) {
        
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<tr><td>");
        echo("<input type='checkbox' name=checkbox[] id='".$tape->get_id()."' value='".$tape->get_id()."'>");
        echo("</td>");
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_full_path()."</td>");
        // Shouldn't have a backupset
        $curr_backupset_id = $tape->get_backupset();
        $backupset = ""; 
        if($curr_backupset_id == -1) {
            $backupset = "None";
        } else {
            if($curr_backupset_id != null && $curr_backupset_id != "") {
                 //$fullbackupset = $db->get_backupset($curr_backupset_id);
                 $fullbackupset = new backupset($db, $curr_backupset_id);
                 //if(isset($fullbackupset['name'])) {
                     $backupset = $fullbackupset->get_name();
                 //}
             }
        }
        echo("<td>".$backupset."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table>");

echo("<BR><BR>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
    echo("<input type=submit name=add_tapes_submit value='Add selected tapes to backup set'>");
    echo("<input type=button onclick=\"window.location='view_backupsets.php'\" name=cancel value='Cancel'>");
    echo("</form>");

//list_all($db);
echo("</fieldset>");

if($messages != "") {
    echo("<BR>");
    echo($messages);
}
}
include 'includes/footer.inc.php';
