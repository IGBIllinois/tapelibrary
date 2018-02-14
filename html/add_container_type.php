<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>Container Types</H3>");
if(isset($_POST['container_type_name'])) {

    //echo("Adding container type ".$_POST['container_type_name']."<BR>");
    $result = $db->add_container_type($_POST['container_type_name']);
     if($result != 0) {
         echo("Container ".$_POST['container_type_name']." successfully added.<BR>");
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
        echo("<tr><td>".$container_type['name']."</td></tr>");
    }
}
    echo("</tbody></table>");

echo("<BR>");
echo("<form name='add_container_type' action='add_container_type.php' method='POST'>");

echo("<table id='container_types' class='table table-bordered'>");
echo("<tr><td>New Location type name</td>");
echo("<tr><td><input type='text' name='container_type_name' id='container_type_name'></td>");
echo("</tr></table>");
echo("<input type='submit' name='submit' value='Add Location Type'>");
echo("</form>");

include 'includes/footer.inc.php';
