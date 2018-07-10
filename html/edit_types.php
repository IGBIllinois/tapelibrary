<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
?>

<?php
echo("<H3>Edit Container and Tape Types</H3>");
if(isset($_GET['add_success']) && $_GET['add_success'] == 1) {
    $type = new type($db, $_GET['type_id']);
    echo(html::success_message("Type ".$type->get_name()." successfully added.<BR>"));
    if($type->is_tape()) {
        echo("<a href='add_tape.php?tape_type=".$type->get_id()."'>Add new tapes of type ".$type->get_name()."?</a><BR>");
    } else {
        echo("<a href='add_container.php?container_type=".$type->get_id()."'>Add a new ".$type->get_name()."?</a><BR>");
    }
    echo("</div>");
}
if(isset($_POST['container_type_name'])) {

    $types = $_POST['types'];
    $placed_types = $_POST['placedTypes'];
    foreach($types as $type) {
        if(in_array($type, $placed_types)) {
            echo(html::error_message("Error, a container cannot be placed and contain the same type."));
        }
    }
    //print_r($types);
    $can_contain_types = "";
    foreach($types as $type) {
        if($can_contain_types != "") {
            $can_contain_types .= ",";
        }
        //echo("Can contain type " . $type ." <BR>");
        $can_contain_types .= $type;
    }
    //echo("type list = $can_contain_types");

     if($result != 0) {
         echo("Container ".$_POST['container_type_name']." successfully edited.<BR>");
     }
}

echo("Current container types:") ;
echo("<table id='container_types' class='table table-striped table-bordered'>");
echo("<thead><tr><th>Container Type</th><th>Can contain types</th><th>Can be placed in</th><th>Max slots</th></tr></thead>");
echo("<tbody>");
$current_container_types = type::get_container_types($db);
if(count($current_container_types)== 0) {
    echo "<tr><td>No container types have been added.</td></tr>";
    
} else {
    foreach($current_container_types as $container_type) {
        echo("<tr><td><a href='edit_type.php?type_id=".$container_type->get_id()."'>".$container_type->get_name()."</a></td>");
        echo("<td>");
        $can_contain = $container_type->get_can_contain_types();
        $types = "";
        foreach($can_contain as $c) {
            $cc = new type($db, $c);
            if(strlen($types) > 0) {
                $types .= ", ";
            }
            $types .= ($cc->get_name());
        }
        echo($types);
        echo("</td><td>");
        echo($container_type->get_container_type_names_for_type());
        echo("</td><td>");
        echo(($container_type->get_max_slots() < 0) ? "Any" :  ($container_type->get_max_slots()));
        echo("</td></tr>");
        
    }
}
    echo("</tbody></table>");

echo("<BR>");

echo("Current Tape Types:");
echo("<table id='tape_types' class='table table-striped table-bordered'>");
echo("<thead><tr><th>Tape Type</th><th>Can contain types</th><th>Can be placed in</th><th>Max slots</th></tr></thead>");
echo("<tbody>");
$current_tape_types = type::get_tape_type_objects($db);
if(count($current_tape_types)== 0) {
    echo "<tr><td>No tape types have been added.</td></tr>";
    
} else {
    foreach($current_tape_types as $tape_type) {
        echo("<tr><td><a href='edit_type.php?type_id=".$tape_type->get_id()."'>".$tape_type->get_name()."</a></td>");
        echo("<td>");
        $can_contain = $tape_type->get_can_contain_types();
        foreach($can_contain as $c) {
            $cc = new type($db, $c);
            echo($cc->get_name(). " ");
        }
        echo("</td><td>");
        echo($tape_type->get_container_type_names_for_type());
        echo("</td><td>");
        echo(($tape_type->get_max_slots() < 0) ? "Any" :  ($tape_type->get_max_slots()));
        echo("</td></tr>");
        
    }
}
    echo("</tbody></table>");

echo("<BR>");


include 'includes/footer.inc.php';
