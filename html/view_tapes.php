
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
    createInput("select","type","",type::get_tape_types($db));
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","container","",tape_library_object::get_containers($db));
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='submit' value='Select'>");
echo("</form>");
echo("<BR>");
//
echo("Current tapes:") ;

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_tapes = tape_library_object::get_tape_objects($db, $begin, $end, $type, $container, $active);

if(count($current_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Tape ID Number</th><th>Type</th><th>Tape Label</th><th>Parent Location</th><th>Backup Set</th></thead>");
    echo("<tbody>");
    foreach($current_tapes as $tape) {
        //echo("id = ".$tape->get_id());
        //echo("id = ".$tape_result['id']);
        //$tape = new tape($db, $tape_result['id']);
        $backupset_id = $tape->get_backupset();
        
        if($backupset_id == null || $backupset_id == -1) {
            $backupset_name = "None";
        } else {
            $backupset= new backupset($db, $backupset_id);
            $backupset_name = $backupset->get_name();
            
        }
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_tape_label()."</td>");
        //echo("<td><a href=view_container.php?container_id=".$tape->get_container_id().">".$tape->get_container_name()."</a></td>");
        echo("<td><a href=view_container.php?container_id=".$tape->get_container_id().">".$tape->get_full_path()."</a></td>");
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

