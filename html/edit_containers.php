

<?php


include 'includes/header.inc.php';
echo("<H3>Edit Containers</H3>");
?>

<?php
$begin = null;
    $end = null;
    $select_type = null;
    $select_container = null;
    $active = 1;
    
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

echo("<form method=POST action=edit_containers.php>");
//
//echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");

echo("<table class='table table-bordered'><tr>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr>";
        print "<td>Container name</td>";
        print "<td>";
        createInput("text","begin","");
        //print "<br />To: ";
        //createInput("text","end","");
        print "</td>";
                print "<td rowspan=6>";
        print "<div id='add_multi_labels'>";
		print "</div>";
        print "</td>";
      print "</tr>";
echo("<tr><td>Location Type :</td><td>");
    createInput("select","type","",$db->get_container_types());
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","select_container","",$db->get_containers_array());
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='limit_submit' value='Select'>");
echo("</form>");

if(isset($_POST['submit'])) {
    //print_r($_POST);

    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {
            $id="";
            $tape_id="";
            $tape_label="";
            $container=null;
            $type="";
            $service="";
            $active="";
            
            //echo("ID $checked is checked.<BR>");
            
            $id = $checked;
            //$tape_id = $_POST['tape_id_'.$id];
            $tape_label = $_POST['container_label_'.$id];
            $container = $_POST['container_location_'.$id];
            //$type = $_POST['tape_type_'.$id];
            //$service = $_POST['service_'.$id];
            $active = (isset($_POST['active_'.$id]) ? 1 : 0);
            /*
            echo("id = $id<BR>");
            echo("container = $container<BR>");
            echo("type = $type<BR>");
            echo("service = $service<BR>");
            echo("name = $tape_label<BR>");
            echo("active = $active<BR>");
             */
             
            $result = $db->edit_tape_basic($id, $tape_label, $container, $active);
            
            if($result != 0) {
                echo("<div class='alert alert-success'>$tape_label successfully modified.</div>");
            } else {
                echo("<div class='alert alert-danger'>There was an error in modifying $tape_label.</div>");
            }
             
             
        }
    } else {
        echo("<div class='alert alert-warning'>Nothing checked</div>");
    }
}

$containers = $db->get_containers($begin, $end, $select_type, $select_container, $active, 1);
  print "<fieldset>";
echo("<form name='edit_containers' method='POST'>");
echo("<table id='edit_container' class='table table-bordered table-hover table-striped display'><thead><tr>");
echo("<th><input type=checkbox onClick=toggleAll(this, 'checkbox') /></th><th>Label</th><th>Type</th><th>Location");
// Does this make sense anymore?
// echo("<BR>Change selected containers:");
//createInput("select", "tape_container", "", $db->get_containers_and_locations(), "",  "changeAllCheckedLocations(this)");
echo("</th><th>Active</th></tr></thead>");
foreach($containers as $container) {
    //$container_id = $container_info['id'];
    //$container = new container($db, $container_id);
    $container_id = $container->get_id();
    echo("<tr>");
    echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$container_id."' value='".$container_id."'>");
    echo("</td><td>");
    //createInput("text", "tape_id", $tape['tape_number'], "", $tape['id']);
    echo("<input type='hidden' name='container_label_".$container->get_id()."' id='container_label_".$container->get_id()."' value='".$container->get_label()."'>");
    //echo("</td><td>");
    //createInput("text", "container_label", $container->get_label(), "", $container_id);
    echo($container->get_label());
    echo("</td>");
    echo("<td>".$container->get_type_name()."</td><td>");
    if(!$container->is_location()) {
        
    createInput("select", "container_location", $container->get_container_id(), $db->get_containers_for_type($container->get_type()), $container_id);
    } else {
        echo "None";
    }
    echo("</td><td>");
    //createInput("select", "tape_type", $tape['type'], $db->get_container_types(), $tape['id']);
    //echo("</td><td>");
    //createInput("text", "service", $tape['service'], "", $tape['id']);
    //echo("</td><td>");
    echo("<input type='checkbox' name=active_".$container_id." id='active_".$container_id."' value='active_".$container_id."'". ($container->is_active() ? " checked " : "" ). " >");
    echo("</td></tr>");

}



echo("</table>");
echo("<BR><BR>");
    echo("<input type=submit name=submit value='Edit Selected Records' class=icon_submit id=edit_submit href='edit_tapes.php'>");
    echo("<input type=button onclick=\"window.location='edit_tapes.php'\" name=cancel value='Cancel'>");
    echo("</form>");
  print "</fieldset>";
include 'includes/footer.inc.php';


