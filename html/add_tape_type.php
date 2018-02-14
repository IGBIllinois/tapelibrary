<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>Tape Types</H3>");
if(isset($_POST['tape_type_name'])) {

    //echo("Adding tape type ".$_POST['tape_type_name']."<BR>");
    $result = $db->add_tape_type($_POST['tape_type_name']);

     if($result != 0) {
         echo("Tape type ".$_POST['tape_type_name']." successfully added.<BR>");
     }
}

echo("Current tape types:") ;
echo("<table id='tape_types' class='table table-striped table-bordered'>");
echo("<thead><tr><th>Container Type</th></tr></thead>");
echo("<tbody>");
$current_tape_types = $db->get_tape_types();
if(count($current_tape_types)== 0) {
    echo "<tr><td>No tape types have been added.</td></tr>";
    
} else {
    foreach($current_tape_types as $tape_type) {
        echo("<tr><td>".$tape_type['name']."</td></tr>");
    }
}
    echo("</tbody></table>");

echo("<BR>");

echo("Add new tape type:<BR>");
echo("<form name='add_tape_type' action='add_tape_type.php' method='POST'>");

echo("<table id='tape_types' class='table table-bordered'>");
echo("<tr><td>New Tape type name</td>");
echo("</tr>");
echo("<tr><td><input type='text' name='tape_type_name' id='tape_type_name'></td>");
echo("</tr></table>");
echo("<input type='submit' name='submit' value='Add Tape Type'>");
echo("</form>");

include 'includes/footer.inc.php';
