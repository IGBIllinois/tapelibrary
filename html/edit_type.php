<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'includes/header.inc.php';
?>

<?php

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
$type = new type($db, $type_id);
echo("<h3>Edit Type: ".$type->get_name()."</H3>");
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
            echo(html::error_message("Error, a container cannot be placed and contain the same type."));
            $errors = 1;
        }
    }
}
if(!$errors) {
    $type = new type($db, $type_id);
    $this_type = new type($db,$type_id);

    //parents
    $current_placed_types = $type->get_container_types_for_type();

    $name = $_POST['container_type_name'];

    
    $loop_error = type::find_loop($db, $placed_types, $types);
    if( $loop_error == 0) {
        
        foreach($current_placed_types as $curr_type) {
            $curr_type_id = $curr_type->get_id();
            if(!in_array($curr_type_id, $placed_types)) {
                // remove type
                $curr_type->remove_container_type_from_type($this_type->get_id());
            }
        }
        foreach($placed_types as $new_type) {
            $parent = new type($db, $new_type);
            $parent_types = $parent->get_can_contain_types();
            if(!in_array($this_type->get_id(), $parent_types)) {
                //echo("<BR>Adding ".$this_type->get_id() . " to ".$parent->get_id());
                $parent->add_container_type_to_type($this_type->get_id());
            }
        }
        $can_contain_types_string = implode(",", $types);

        $result = $this_type->edit($name, $can_contain_types_string, $max_slots);
        
        
        if($placed_types != null) {
            foreach($placed_types as $placed_type_id) {
                $placed_type = new type($db, $placed_type_id);
                //echo("Adding type  $type_id so it can be placed in type $placed_type.<BR>");
                if(!in_array($placed_type, $current_placed_types)) {
                    $add_result = $placed_type->add_container_type_to_type($type_id);
                }
            }
        }
        html::write_message($result);
     
    } else {
        $loop_type = new type($db,$loop_error);
        $name = $loop_type->get_name();
        echo(html::error_message("There is an error in where this container can be placed. <BR> It could both contain and be placed in a <B>$name</B>.<BR>Please double check and try again."));
    }
}
}

$this_type = new type($db, $type_id);
$containers = $this_type->get_containers_for_type();
$name = $this_type->get_name();
$max_slots = $this_type->get_max_slots();
$can_contain_types = $this_type->get_can_contain_types();

if($can_contain_types == null) {
    $can_contain_types = array();
}

if($max_slots == -1) {
    $max_slots = "Any";
}

$container_type_objects = $this_type->get_container_types_for_type();
$container_types = array();
foreach($container_type_objects as $curr_type) {
    $container_types[] = $curr_type->get_id();
}

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
$types = type::get_all_types($db);
foreach($types as $type) {
    $id = $type->get_id();
    if($id != $type_id) {
        echo("<input type=checkbox ".(in_array($id, $can_contain_types) ? " CHECKED ": ""). (in_array($id, $container_types)? " disabled " : ""). " id='type$id' onclick=toggle('placedtype$id') name=types[".$type->get_id()."] value='".$type->get_id()."'>".$type->get_name()."<BR>");
    }
}
echo("</td></tr><tr><td>");
echo("In what types can this container be placed?</td></tr><tr><td>");
$types = type::get_all_types($db);

foreach($types as $type) {
    $id = $type->get_id();
    if($id != $type_id) {
        echo("<input type=checkbox  ".(in_array($id, $container_types) ? " CHECKED " : ""). (in_array($id, $can_contain_types)? " disabled " : ""). " id='placedtype$id' onclick=toggle('type$id') name=placedtypes[".$type->get_id()."] value='".$type->get_id()."'>".$type->get_name()."<BR>");
    }
}
echo("</td></tr></table>");
echo("<input type='submit' name='submit_type_edit' value='Edit Type'>");
echo("</form>");


}

require_once 'includes/footer.inc.php';


?>