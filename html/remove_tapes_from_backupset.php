
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
?>
<script>
    

function toggle(source) {
  checkboxes = document.getElementsByName('checkbox[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}

function changeAllCheckedLocations(source) {
    //containers = document.getElementsByName('tape_container');
    //alert("numLocations = "+containers.length);
    //for(var i=0, n=containers.length; i<n; i++) {
    //    containers[i].value = source.value;
    //}
    checkboxes = document.getElementsByName('checkbox[]');
    //alert("numCheckboxes = "+checkboxes.length);
        for(var i=0, n=checkboxes.length;i<n;i++) {
            //alert("i = "+i + ":"+checkboxes[i].checked);
            if(checkboxes[i].checked) {
                var id = checkboxes[i].id;
                //alert("id = "+id);
                newLoc = document.getElementById('tape_container_'+id);
                //alert("newVal = "+source.value);
                newLoc.value = source.value;
            }
  }
}
</script>
<?php
if(!isset($_POST['backupset_id'])) {
    echo("Error: Please select a backup set.");
    
} else{
    $backupset_id = $_POST['backupset_id'];
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
            $result = $db->remove_tape_from_backupset($tape_id, $backupset_id);
            $tape = $db->get_tape_by_id($tape_id);
            $backupset = $db->get_backupset($backupset_id);
            
            if($result != 0) {
                echo("<div class='alert alert-success'>Tape ".$tape['label'] ." successfully removed from ".$backupset['name'] ."</div>");
            } else {
                echo("<div class='alert alert-danger'>There was an error removing ".$tape['label'] ."  from ".$backupset['name'] ."</div>");
            }
             
        }
    } else {
        echo("<div class='alert alert-warning'>Nothing checked</div>");
    }
}

$backupset_id = $_POST['backupset_id'];

$backupset_data = $db->get_backupset($backupset_id);

$tapes = $db->get_tapes_for_backupset($backupset_id);

//echo("Backupset: ".$backupset_data['name']."<BR>");
//echo("Begin Date: ".$backupset_data['begin']."<BR>");
//echo("End Date: ".$backupset_data['end']."<BR>");
//echo("Program: ".$backupset_data['program']."<BR>");
//echo("Notes: ".$backupset_data['notes']."<BR>");
echo("<h3>Adding tapes to ".$backupset_data['name']."</h3>");

echo("Tapes currently in <B>".$backupset_data['name']."</B>:<BR>");
echo("<form name='remove_tapes_form' method='POST'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<fieldset><table id='remove_tapes' class='table table-bordered table-hover table-striped display'>");

if(count($tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th><input type=checkbox onClick='toggle(this)' /><th>Label</th><th>Type</th><th>Parent Location</th></thead>");
    echo("<tbody>");
    foreach($tapes as $tape) {
        echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$tape['id']."' value='".$tape['id']."'>");
    echo("</td>");
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".$db->get_container_type_name($tape['type'])."</td>");
        echo("<td>".$tape['container_name']."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");
echo("<input type=submit name=submit_remove value='Remove Selected Tapes from Backupset' class=icon_submit id=remove_tapes_from_backup_submit>");
    echo("<input type=button onclick=\"window.location='view_backupsets.php'\" name=cancel value='Cancel'>");
    echo("</form>");
}
include_once 'includes/footer.inc.php';