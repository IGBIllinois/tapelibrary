

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'includes/header.inc.php';
echo("<h3>Edit Program</h3>");
echo("For editing programs used for backup sets.<BR>");


    $program_id=-1;
    $errors = "";
    $version = null;

if(isset($_POST['submit_edit_program'])) {   
    
    if(isset($_POST['program_id'])) {

        $program_id = $_POST['program_id'];
    } else {
        $errors .= "Please select a valid program.";

    }

    if(isset($_POST['program_name']) && $_POST['program_name'] != null) {
        $name = $_POST['program_name'];

    } else {
        $errors .= "Please select a valid name for this program.";
        
    }

    if(isset($_POST['program_version'])) {

        $version = $_POST['program_version'];
    }
    
    if(strlen($errors) == 0) {

        $program = new program($db, $program_id);
        $result = $program->edit_program($name, $version);

    
     html::write_message($result);
     
    } else {
        echo(html::error_message($errors));
    }

} else {

    if(isset($_GET['program_id']) && $_GET['program_id'] != null) {
        $program_id = $_GET['program_id'];
        $program = new program($db, $program_id);
        $name = $program->get_name(); 
        $version = $program->get_version();

    } else {
        $errors .= "Please select a valid program.";
        
    }
    if(strlen($errors) != 0) {

        echo(html::error_message($errors));
    }
}

    


echo("<form name='edit_program' action='edit_program.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Program Name:</td><td><input type='text' "
        . "name='program_name' id='program_name'". 
        (isset($name) ? " value='$name' " : "").
        "></td></tr>");
echo("<tr><td width=20%>Program Version:".
        "</td><td><input type='text' name='program_version' id='program_version'". 
        (isset($version) ? " value='$version' " : "").
        "></td></tr>");

echo("</table>");
echo("<input type='hidden' name='program_id' value='$program_id'>");
echo("<input type='submit' name='submit_edit_program' value='Edit Program'>");
echo("</form>");

echo("<BR>");

    
require_once 'includes/footer.inc.php';
