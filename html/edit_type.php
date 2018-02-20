<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include 'includes/header.inc.php';
?>

<?php
echo("<h3>Edit Type</H3>");
$type_id = -1;
$errors=0;
if(!isset($_GET['type_id']) && !isset($_POST['type_id'])) {
    echo("Please choose a valid type.");
} else {
    if(isset($_POST['type_id'])) {
        $type_id = $_POST['type_id'];

    } else {
        $type_id = $_GET['type_id'];
    }
    
if(isset($_POST['submit_type_edit'])) {

    $types = array();
    $placed_types = array();
if(isset($_POST['types'])) {
    $types = $_POST['types'];
}
if(isset($_POST['placedtypes'])) {
    $placed_types = $_POST['placedtypes'];
}
if(isset($_POST['max_slots'])) {
    $max_slots = $_POST['max_slots'];
    if($max_slots == "Any" || !is_numeric($max_slots)) {
        $max_slots = -1;
    }
}
if($types != null && $placed_types != null) {
    foreach($types as $type) {
        if(in_array($type, $placed_types)) {
            echo("<div class='alert alert-danger'>Error, a container cannot be placed and contain the same type.</div>");
            $errors = 1;
        }
    }
}
if(!$errors) {
    //echo("Adding container type ".$_POST['container_type_name']."<BR>");
    $current_placed_types = $db->get_container_types_for_type($type_id);
    //echo("Current placed types:");
    //print_r($current_placed_types);

    //echo("<BR>new placed typess:");
    //print_r($placed_types);
    //echo("<BR>types:<BR>");
    //print_r($types);
    $name = $_POST['container_type_name'];
    //echo("name = $name");
    $loop_error = $db->find_loop($placed_types, $types);
    if( $loop_error == 0) {
        foreach($current_placed_types as $curr_type) {
            //echo("<BR>curr_type=$curr_type<BR>");
            if(!in_array($curr_type, $placed_types)) {
                // remove type
                //echo("removing $curr_type from $type_id<BR>");
                $db->remove_container_from_type($curr_type, $type_id);
            }
        }
        $can_contain_types_string = implode(",", $types);
        //$result = $db->add_type($_POST['container_type_name'], $can_contain_types);
        $result = $db->edit_type($type_id, $name, $can_contain_types_string, $max_slots);
        
        if($placed_types != null) {
            foreach($placed_types as $placed_type) {
                //echo("Adding type  $type_id so it can be placed in type $placed_type.<BR>");
                if(!in_array($placed_type, $current_placed_types)) {
                    $add_result = $db->add_container_to_type($placed_type, $type_id);
                }
            }
        }
     if($result != 0) {
         echo("<div class='alert alert-success'>Container Type ".$_POST['container_type_name']." successfully edited.</div>");
     }
    } else {
        //echo("Loop error = $loop_error<BR>");
        $loop_type = new type($db,$loop_error);
        $name = $loop_type->get_name();
        echo("<div class='alert alert-danger'>There is an error in where this container can be placed. <BR> It could both contain and be placed in a <B>$name</B>.<BR>Please double check and try again.</div>");
    }
}
}

$this_type = new type($db, $type_id);
$containers = $db->get_containers_for_type($type_id);
$name = $this_type->get_name();
$max_slots = $this_type->get_max_slots();
$can_contain_types = $this_type->get_can_contain_types();

if($can_contain_types == null) {
    $can_contain_types = array();
}

if($max_slots == -1) {
    $max_slots = "Any";
}

//print_r($can_contain_types);
//$can_contain_types_array = explode(",", $can_contain_types);
$container_types = $db->get_container_types_for_type($type_id);

echo("<form name='edit_type' action='edit_type.php' method='POST'>");
echo("<input type='hidden' name='type_id' value='$type_id'>");
echo("<table id='container_types' class='table table-bordered'>");
echo("<tr><td width=40%> Location type name</td>");
echo("<td><input type='text' name='container_type_name' value='$name' id='container_type_name'></td></tr>");

echo("<tr><td>How many objects can be put in a container? (if there is a limit)</td><td><input name='max_slots' value='$max_slots'></td></tr>");
echo("</td></tr></table>");

echo("</table>");
echo("<table class='table table-bordered'><tr><td>");
echo("<tr><Td>What types can this container contain?</td></tr>");
echo("<TR><TD>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    if($id != $type_id) {
        echo("<input type=checkbox ".(in_array($id, $can_contain_types) ? " CHECKED ": ""). (in_array($id, $container_types)? " disabled " : ""). " id='type$id' onclick=toggle('placedtype$id') name=types[".$type['id']."] value='".$type['id']."'>".$type['name']."<BR>");
    }
}
echo("</td></tr><tr><td>");
echo("In what types can this container be placed?</td></tr><tr><td>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    if($id != $type_id) {
        echo("<input type=checkbox  ".(in_array($id, $container_types) ? " CHECKED " : ""). (in_array($id, $can_contain_types)? " disabled " : ""). " id='placedtype$id' onclick=toggle('type$id') name=placedtypes[".$type['id']."] value='".$type['id']."'>".$type['name']."<BR>");
    }
}
echo("</td></tr></table>");
echo("<input type='submit' name='submit_type_edit' value='Edit Type'>");
echo("</form>");


}

include 'includes/footer.inc.php';


?>