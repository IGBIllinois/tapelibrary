

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
    $messages = "";
    
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
echo("<tr><td>Container Type :</td><td>");
    createInput("select","type","",type::get_container_types($db));
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","select_container","", tape_library_object::get_containers($db));
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='limit_submit' value='Select'>");
echo("</form><BR>");

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
            
            if(isset($_POST['container_location_'.$id])) {
                $container = $_POST['container_location_'.$id];
            } else {
                $messages .= "<div class='alert alert-danger'>Error:Cannot move $tape_label to the specified location. Please check that it is a valid container for this type of object.</div>";
                continue;
                
            }
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
$containers = tape_library_object::get_container_objects($db, $begin, $select_type, $select_container, $active, 1);
  if(strlen($messages) > 0) {
      echo($messages);
  }
  print "<fieldset>";
echo("<form name='edit_containers' method='POST'>");
echo("<table id='edit_container' class='table table-bordered table-hover table-striped display'><thead><tr>");
echo("<th><input type=checkbox onClick='toggleAll(this,\"checkbox\")' /></th><th>Label</th><th>Type</th><th>Location");
// Does this make sense anymore?
echo("<BR>Move selected containers to:");
createInput("select", "tape_container", "", tape_library_object::get_containers($db), "",  "changeAllCheckedLocations(this, \"checkbox\", \"container_location\")");
echo("</th><th>Active</th></tr></thead>");
foreach($containers as $container) {
    //$container_id = $container_info['id'];
    //$container = new container($db, $container_id);
    $container_id = $container->get_id();
    echo("<tr>");
    echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$container_id."' value='".$container_id."'>");
    echo("</td><td>");

    echo("<input type='hidden' name='container_label_".$container->get_id()."' id='container_label_".$container->get_id()."' value='".$container->get_label()."'>");
    //echo("</td><td>");

    echo("<a href='edit_container.php?container_id=".$container->get_id()."'>".$container->get_label()."</a>");
    echo("</td>");
    echo("<td>".$container->get_type_name()."</td><td>");
    if(!$container->is_location()) {
    $this_type = new type($db, $container->get_type());   
    createInput("select", "container_location", $container->get_container_id(), $this_type->get_containers_for_type(), $container_id);
    } else {
        echo "None";
    }
    echo("</td><td>");

    echo("<input type='checkbox' name=active_".$container_id." id='active_".$container_id."' value='active_".$container_id."'". ($container->is_active() ? " checked " : "" ). " >");
    echo("</td></tr>");

}



echo("</table>");
echo("<BR><BR>");
    echo("<input type=submit name=submit value='Edit Selected Records' class=icon_submit id=edit_submit href='edit_tapes.php'>");
    echo("<input type=button onclick=\"window.location='edit_tapes.php'\" name=cancel value='Cancel'>");
    echo("</form>");
  print "</fieldset>";
  echo("<BR>");

include 'includes/footer.inc.php';


