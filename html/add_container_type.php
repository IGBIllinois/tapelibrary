<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'includes/header.inc.php';
?>

<?php
$types = null;
$placed_types = null;
$max_slots = -1;
$errors = "";
$container_type_name = null;
echo("<H3>Add Container and Tape Types</H3>");
if(isset($_POST['submit'])) {

if(!isset($_POST['container_type_name']) || $_POST['container_type_name'] == "") {
    echo(html::error_message("Please input a name for this container type."));
} else {
    $container_type_name = $_POST['container_type_name'];
    
if(isset($_POST['types'])) {
    $types = $_POST['types'];
}
if(isset($_POST['placedtypes'])) {
    $placed_types = $_POST['placedtypes'];
}
if($types != null && $placed_types != null) {
    foreach($types as $type) {
        if(in_array($type, $placed_types)) {
            $errors .= html::error_message("Error, a container cannot be placed and contain the same type.");
        }
    }
}

if(isset($_POST['max_slots'])) {
    if(is_numeric($_POST['max_slots'])) {
        $max_slots = $_POST['max_slots'];
    }
}

    

    $can_contain_types = "";
    if($types != null) {
    foreach($types as $type) {
        if($can_contain_types != "") {
            $can_contain_types .= ",";
        }

        $can_contain_types .= $type;
    }

    }
    
 if(strlen($errors) == 0) {
     // Check for loops in this type, to make sure it can't be placed in a
     // parent Type
    $loop_error = type::find_loop($db, $placed_types, $types);
    if($loop_error == 0) {
        $type = new type($db);
        
        $result = $type->add_type($container_type_name, $can_contain_types, $max_slots);
        
        if($placed_types != null) {
            foreach($placed_types as $placed_type_id) {
                $placed_type = new type($db, $placed_type_id);
                $add_result = $placed_type->add_container_type_to_type($type->get_id());
            }
        }
     
        html::write_message($result);
        
    } else {
        // There's a dependency error
        $loop_type = new type($db,$loop_error);
        $name = $loop_type->get_name();
        echo(html::error_message("There is an error in where this container can ".
                "be placed. <BR> It could both contain and be placed in a ".
                "<B>$name</B>.<BR>Please double check and try again."));
    }
    
} else {
    echo($errors);
}

}
}

echo("<form name='add_container_type' action='add_container_type.php' method='POST'>");

echo("<table id='container_types' class='table table-bordered'>");
echo("<tr><td width=40%>New type name</td>");
echo("<td><input type='text' name='container_type_name' id='container_type_name'".
        "value='$container_type_name'></td></tr>");

echo("<tr><td>How many objects can be put in a container? (if there is a limit) ".
        "</td><td><input name='max_slots' value='".
        ((is_numeric($max_slots) && $max_slots >=0) ? $max_slots : "Any").
        "'></td></tr>");
echo("</table>");


echo("<table class='table table-bordered'><tr><td>");

echo("<tr><Td>What types can this container contain?</td></tr>");
echo("<TR><TD>");
$types = type::get_all_types($db);
foreach($types as $type) {
    $id = $type->get_id();
    echo("<input type=checkbox  id='type$id' onclick=toggle('placedtype$id') ".
            "name=types[".$id."] value='".$id."'>".$type->get_name()."<BR>");
}
echo("</td></tr><tr><td>");
echo("In what types can this container be placed?<BR>".
        "(If it cannot be placed in anything, ".
        "it will be considered a top-level location type)</td></tr><tr><td>");

$types = type::get_all_types($db);
foreach($types as $type) {
    $id = $type->get_id();
    echo("<input type=checkbox  id='placedtype$id' onclick=toggle('type$id') ".
            "name=placedtypes[".$id."] value='".$id."'>".$type->get_name()."<BR>");
}
echo("</td></tr></table>");
echo("<input type='submit' name='submit' value='Add Location Type'>");
echo("</form>");

require_once 'includes/footer.inc.php';
