
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
echo("<fieldset>");
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
    

    

$backupset = $db->get_backupset($backupset_id);

if($backupset == 0) {
    echo("Please select a valid backup set.");
} else {

//echo("Add tapes to backup set: ".$backupset['name']);
if(isset($_POST['add_tapes_submit'])) {
    //print_r($_POST);

    if(isset($_POST['checkbox'])) {
        
        foreach($_POST['checkbox'] as $checked) {

            //echo("ID $checked is checked.<BR>");
            
            $id = $checked;
            
            $result = $db->set_backupset($id, $backupset_id);
            $tape = $db->get_tape_by_id($id);
            $backupset = $db->get_backupset($backupset_id);
            if($result != 0) {
                echo("<div class='alert alert-success'>Tape ".$tape['label'] ." successfully added to ".$backupset['name'] ."</div>");
            } else {
                echo("<div class='alert alert-danger'>There was an error adding".$tape['label']. " to ".$backupset['name'] ."</div>" );
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
        echo("<div class='alert alert-warning'>Nothing checked</div>");
    }
}
echo("<h3>Adding tapes to ".$backupset['name']."</h3>");
echo("<form method=POST action=add_tapes_to_backupset.php>");

/*
//echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");
echo("Limit By:<BR>");
echo("<table  class='table table-bordered display'><tr>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr >";
        print "<td>Tape Number(s)</td>";
        print "<td>From: ";
        createInput("text","begin","");
        print "<br />To: ";
        createInput("text","end","");
        print "</td>";
        //        print "<td rowspan=6>";
        //print "<div id='add_multi_labels'>";
	//	print "</div>";
        //print "</td>";
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
    createInput("select","type","",$db->get_tape_types());
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","container","",$db->get_containers());
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='submit' value='Select'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("</form>");
echo("<BR>");
 * 
 */
//
$backupset = $db->get_backupset($backupset_id);
$backupset_name = $backupset['name'];
echo("Current tapes not assigned to a backup set:</B>:") ;
echo("<form method='POST' name='add_tapes_submit' action='add_tapes_to_backupset.php'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_unassigned_tapes = $db->get_tapes_without_backupset();

if(count($current_unassigned_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><th><input type=checkbox onClick='toggle(this)' /></th><th>Label</th><th>Type</th><th>Parent Location</th><th>Current Backup set</th></thead>");
    echo("<tbody>");
    foreach($current_unassigned_tapes as $tape) {
        
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<tr><td>");
        echo("<input type='checkbox' name=checkbox[] id='".$tape['id']."' value='".$tape['id']."'>");
        echo("</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".$db->get_container_type_name($tape['type'])."</td>");
        echo("<td>".$tape['container_name']."</td>");
        // Shouldn't have a backupset
        $curr_backupset_id = $tape['backupset'];
        $backupset = ""; 
        if($curr_backupset_id == -1) {
            $backupset = "None";
        } else {
            if($curr_backupset_id != null && $curr_backupset_id != "") {
                 $fullbackupset = $db->get_backupset($curr_backupset_id);
                 
                 //if(isset($fullbackupset['name'])) {
                     $backupset = $fullbackupset['name'];
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
}
include 'includes/footer.inc.php';
