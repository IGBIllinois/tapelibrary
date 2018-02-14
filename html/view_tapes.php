
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
echo("<H3>View Tapes</H3>");
echo("<fieldset>");
$begin = null;
    $end = null;
    $type = null;
    $container = null;
    $active = 1;
    
if(isset($_POST['submit'])) {
    
    
    if(isset($_POST['begin'])) {

        //if(is_numeric($begin)) {
        $begin = $_POST['begin'];
        //}
    }
    if(isset($_POST['end'])) {
        //if(is_numeric($end)) {
        $end = $_POST['end'];
        //}
    }

    if(isset($_POST['type'])) {
        $type = $_POST['type'];
    }
    
    if(isset($_POST['container'])) {
        $container = $_POST['container'];
    }
    
    if(isset($_POST['active'])) {
        $active = $_POST['active'];
    }


    //$result = add_item($db, $name, $container_type, $container_id, $service, 0 );
     //if($result) {
        
     //} else {
         //echo("ERROR: ");
     //}
}

echo("<form method=POST action=view_tapes.php>");
//
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
echo("</form>");
echo("<BR>");
//
echo("Current tapes:") ;

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_tapes = $db->get_tapes($begin, $end, $type, $container, $active);

if(count($current_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Label</th><th>Type</th><th>Parent Location</th><th>Backup Set</th></thead>");
    echo("<tbody>");
    foreach($current_tapes as $tape) {
        $backupset_id = $tape['backupset'];
        //echo("backupset = $backupset_id<BR>");
        $backupset_name = "";
        if($backupset_id == null || $backupset_id == -1) {
            $backupset_name = "None";
        } else {
            $backupset = $db->get_backupset($backupset_id);
            if($backupset == 0) {
                $backupset_name = "None";
            } else {
                $backupset_name = $backupset['name'];
            }
            
        }
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".$db->get_container_type_name($tape['type'])."</td>");
        echo("<td><a href=view_container.php?container_id=".$tape['parent'].">".$tape['container_name']."</a></td>");
        if($backupset_id != -1) {
            echo("<td><a href=view_backupset_data.php?backupset_id=".$backupset_id.">".$backupset_name."</a></td></tr>");
        } else {
            echo("<td>".$backupset_name."</td></tr>");
        }
    }
    echo("</tbody>");

}

echo("</table></fieldset>");

echo("<BR><a href='add_tape.php'>Add new tapes</a><BR>");

//list_all($db);
echo("</fieldset>");
include 'includes/footer.inc.php';

