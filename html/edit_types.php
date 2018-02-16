<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
?>

<?php
echo("<H3>Container Types</H3>");
if(isset($_POST['container_type_name'])) {

    $types = $_POST['types'];
    $placed_types = $_POST['placedTypes'];
    foreach($types as $type) {
        if(in_array($type, $placed_types)) {
            echo("<div class='alert alert-danger'>Error, a container cannot be placed and contain the same type.");
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
    echo("type list = $can_contain_types");
    //echo("Adding container type ".$_POST['container_type_name']."<BR>");
    //$result = $db->add_container_type($_POST['container_type_name'], $can_contain_types);
     if($result != 0) {
         echo("Container ".$_POST['container_type_name']." successfully edited.<BR>");
     }
}

echo("Current container types:") ;
echo("<table id='container_types' class='table table-striped table-bordered'>");
echo("<thead><tr><th>Container Type</th></tr></thead>");
echo("<tbody>");
$current_container_types = $db->get_container_types();
if(count($current_container_types)== 0) {
    echo "<tr><td>No container types have been added.</td></tr>";
    
} else {
    foreach($current_container_types as $container_type) {
        echo("<tr><td><a href='edit_type.php?type_id=".$container_type['id']."'>".$container_type['name']."</a></td></tr>");
    }
}
    echo("</tbody></table>");

echo("<BR>");
/*
echo("<form name='add_container_type' action='add_container_type.php' method='POST'>");

echo("<table id='container_types' class='table table-bordered'>");
echo("<tr><td>New Location type name</td>");
echo("<tr><td><input type='text' name='container_type_name' id='container_type_name'></td></tr>");
echo("<tr><Td>What types can this container contain?</td></tr>");
echo("<TR><TD>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    echo("<input type=checkbox id='type$id' onclick=toggle('placedtype$id') name=type[".$type['id']."] value='".$type['id']."'>".$type['name']."<BR>");
}
echo("</td></tr><tr><td>");
echo("In what types can this container be placed?</td></tr><tr><td>");
$types = $db->get_all_types();
foreach($types as $type) {
    $id = $type['id'];
    echo("<input type=checkbox id='placedtype".$type['id']."' onclick=toggle('type$id') name=placedtypes[".$type['id']."] value='placed".$type['id']."'>".$type['name']."<BR>");
}
echo("</td></tr></table>");
echo("<input type='submit' name='submit' value='Add Type'>");
echo("</form>");
*/
include 'includes/footer.inc.php';
