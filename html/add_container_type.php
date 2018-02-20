<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
?>

<?php
$types = null;
$placed_types = null;
$max_slots = -1;

echo("<H3>Add Container Types</H3>");
if(isset($_POST['submit'])) {

if(!isset($_POST['container_type_name']) || $_POST['container_type_name'] == "") {
    echo("<div class='alert alert-danger'>Please input a name for this container type.</div>");
} else {
if(isset($_POST['types'])) {
    $types = $_POST['types'];
}
if(isset($_POST['placedtypes'])) {
    $placed_types = $_POST['placedtypes'];
}
if($types != null && $placed_types != null) {
    foreach($types as $type) {
        if(in_array($type, $placed_types)) {
            echo("<div class='alert alert-danger'>Error, a container cannot be placed and contain the same type.");
        }
    }
}

if(isset($_POST['max_slots'])) {
    if(is_numeric($_POST['max_slots'])) {
        $max_slots = $_POST['max_slots'];
    }
}
    //print_r($types);
    $can_contain_types = "";
    if($types != null) {
    foreach($types as $type) {
        if($can_contain_types != "") {
            $can_contain_types .= ",";
        }
        //echo("Can contain type " . $type ." <BR>");
        $can_contain_types .= $type;
    }
    //echo("type list = $can_contain_types");
    }
    
    
    //echo("Adding container type ".$_POST['container_type_name']."<BR>");
    //echo("types:");
    //print_r($types);
    //echo("placed types:");
    //print_r($placed_types);
    $loop_error = $db->find_loop($placed_types, $types);
    if($loop_error == 0) {
        $result = $db->add_type($_POST['container_type_name'], $can_contain_types, $max_slots);
        if($placed_types != null) {
            foreach($placed_types as $placed_type) {

                $add_result = $db->add_container_to_type($placed_type, $result);
            }
        }
     if($result != 0) {
         echo("<div class='alert alert-success'>Container ".$_POST['container_type_name']." successfully added.</div>");
     }
    } else {
        echo("Loop error = $loop_error<BR>");
        $loop_type = new type($db,$loop_error);
        $name = $loop_type->get_name();
        echo("<div class='alert alert-danger'>There is an error in where this container can be placed. <BR> It could both contain and be placed in a <B>$name</B>.<BR>Please double check and try again.</div>");
    }
}
}
/*
echo("Current container types:") ;
echo("<table id='container_types' class='table table-striped table-bordered'>");
echo("<thead><tr><th>Container Type</th></tr></thead>");
echo("<tbody>");
$current_container_types = $db->get_container_types();
if(count($current_container_types)== 0) {
    echo "<tr><td>No container types have been added.</td></tr>";
    
} else {
    foreach($current_container_types as $container_type) {
        echo("<tr><td>".$container_type['name']."</td></tr>");
    }
}
    echo("</tbody></table>");

echo("<BR>");
 * 
 */
echo("<form name='add_container_type' action='add_container_type.php' method='POST'>");

echo("<table id='container_types' class='table table-bordered'>");
echo("<tr><td width=40%>New Location type name</td>");
echo("<td><input type='text' name='container_type_name' id='container_type_name'></td></tr>");

echo("<tr><td>How many objects can be put in a container? (if there is a limit)</td><td><input name='max_slots' value='Any'></td></tr>");
echo("</td></tr></table>");


echo("<table class='table table-bordered'><tr><td>");

echo("<tr><Td>What types can this container contain?</td></tr>");
echo("<TR><TD>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    echo("<input type=checkbox  id='type$id' onclick=toggle('placedtype$id') name=types[".$type['id']."] value='".$type['id']."'>".$type['name']."<BR>");
}
echo("</td></tr><tr><td>");
echo("In what types can this container be placed?</td></tr><tr><td>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    echo("<input type=checkbox  id='placedtype$id' onclick=toggle('type$id') name=placedtypes[".$type['id']."] value='".$type['id']."'>".$type['name']."<BR>");
}
echo("</td></tr></table>");
echo("<input type='submit' name='submit' value='Add Location Type'>");
echo("</form>");

include 'includes/footer.inc.php';
