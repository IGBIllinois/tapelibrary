

<?php


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
?>

<?php 
$begin = null;
    $end = null;
    $select_type = null;
    $select_container = null;
    $active = 1;
    $messages = "";
echo("<H3>Edit Tapes</H3>");
echo("Limit By:<BR>");

if(isset($_POST['limit_submit'])) {
    
    
    if(isset($_POST['begin'])) {
        
        $begin = $_POST['begin'];
        
    }
    if(isset($_POST['end'])) {
        
        $end = $_POST['end'];
        
    }
    
    if(isset($_POST['type'])) {
        $select_type = $_POST['type'];
    }
    
    if(isset($_POST['select_container'])) {
        $select_container = $_POST['select_container'];
    }
    
    if(isset($_POST['active'])) {
        $active = $_POST['active'];
    }
}
echo("<form method=POST action=edit_tapes.php>");
//
//echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");

echo("<table class='table table-bordered display'><tr>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr>";
        print "<td>Tape Number(s)</td>";
        print "<td>From: ";
        createInput("text","begin",$begin);
        print "<br />To: ";
        createInput("text","end",$end);
        print "</td>";
        //        print "<td rowspan=6>";
        //print "<div id='add_multi_labels'>";
	//	print "</div>";
        //print "</td>";
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
    createInput("select","type",$select_type,type::get_tape_types($db));
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","select_container",$select_container, tape_library_object::get_containers($db));
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='limit_submit' value='Select'>");
echo("</form><BR>");

if(isset($_POST['submit'])) {
    //print_r($_POST);

    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {

            //echo("ID $checked is checked.<BR>");
            
            $id = $checked;
            //$tape_id = $_POST['tape_id_'.$id];
            $tape_label = $_POST['tape_label_'.$id];
            $container = $_POST['tape_container_'.$id];
            if($container == "") {
                $container = null;
            }
            //$type = $_POST['tape_type_'.$id];
            //$service = $_POST['service_'.$id];
            $active = (isset($_POST['active_'.$id]) ? 1 : 0);
            /*
            echo("id = $id<BR>");
            echo("container = $container<BR>");
            echo("type = $type<BR>");
            echo("service = $service<BR>");
            echo("name = $tape_name<BR>");
            echo("active = $active<BR>");
             * 
             */
            //$result = $db->edit_tape_basic($id, $tape_label, $container, $active);
            $container_object = new tape_library_object($db, $container);
            $result = $container_object->move_object($id);
            
            if($result['RESULT']) {
                $messages.=("<div class='alert alert-success'>".$result['MESSAGE']."</div>");
            } else {
                $messages .= ("<div class='alert alert-danger'>".$result['MESSAGE']."</div>");
            }
             
             
             
        }
    } else {
        $messages .= ("<div class='alert alert-warning'>Nothing checked</div>");
    }
}
/*
echo("Testing fake move:<BR>");
$result = $db->move_object(187, -5);
echo($result);
echo("<BR>ending fake move1<BR>");
 * 
 */
$tapes = tape_library_object::get_tape_objects($db, $begin, $end, $select_type, $select_container, $active);
  if(strlen($messages) > 0) {
      echo($messages);
  }
  print "<fieldset>";
echo("<form name='edit_tapes_form' method='POST'>");
echo("<table id='edit_tapes_table' name='edit_tapes_table' class='table table-bordered table-hover table-striped display'><thead><tr>");
echo("<th><input type=checkbox onClick=toggleAll(this,'checkbox') /><th>Label</th><th>Type</th><th>Location");
echo("<BR>Change selected containers:");
createInput("select", "tape_container", "", tape_library_object::get_containers($db), "",  "changeAllCheckedLocations(this, \"checkbox\", \"tape_container\")");
echo("</th><th>Backup Set</th><th>Active</th></tr></thead>");
foreach($tapes as $tape) {
    echo("<tr>");
    echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$tape->get_id()."' value='".$tape->get_id()."'>");
    echo("</td><td>");
    //createInput("text", "tape_label", $tape['label'], "", $tape['id']);
    echo($tape->get_label()."<input type=hidden name='tape_label_".$tape->get_id()."' id='tape_label_".$tape->get_id()."' value='".$tape->get_label())."'>";
    echo("</td>");
    echo("<td>".$tape->get_type_name()."</td><td>");
    createInput("select", "tape_container", $tape->get_container_id(), tape_library_object::get_containers($db), $tape->get_id());
    //createInput("text", "tape_label", $tape['label'], "", $tape['id']);
    echo("</td>");

    $backupset = new backupset($db, $tape->get_backupset());
    echo("<td><a href='view_backupset_data.php?backupset_id=".$backupset->get_id()."'>".$backupset->get_name()."</a></td>");
    echo("<td><input type='checkbox' name=active_".$tape->get_id()." id='active_".$tape->get_id()."' value='active_".$tape->get_id()."'". ($tape->is_active() ? " checked " : "" ). " >");
    echo("</td></tr>");

}



echo("</table>");
echo("<BR><BR>");
    echo("<input type=submit name=submit value='Edit Selected Records' class=icon_submit id=edit_submit href='edit_tapes.php'>");
    echo("<input type=button onclick=\"window.location='edit_tapes.php'\" name=cancel value='Cancel'>");
    echo("</form>");
  print "</fieldset>";

include 'includes/footer.inc.php';

