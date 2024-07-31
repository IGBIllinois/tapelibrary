

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'includes/header.inc.php';
echo("<h3>Add Programs</h3>");
echo("For adding programs used for backup sets.<BR>");
if(isset($_POST['submit_add_program'])) {

    $program_id=-1;
    $errors = "";
    $version = null;

    if(isset($_POST['program_name']) && $_POST['program_name'] != null) {
        $name = $_POST['program_name'];

    } else {
        $errors .= "Please select a valid name for this program.";
        
    }

    if(isset($_POST['program'])) {

        $program_id = $_POST['program'];
    }

    if(isset($_POST['program_version'])) {

        $version = $_POST['program_version'];
    }
    
    if(strlen($errors) == 0) {

        $program = new program($db);
        $result = $program->add_program($name, $version);

    
     html::write_message($result);
     
    } else {
        echo(html::error_message($errors));
    }

}


echo("<form name='add_program' action='add_program.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Program Name:</td><td><input class='form-control' type='text' "
        . "name='program_name' id='program_name'". 
        (isset($name) ? " value='$name' " : "").
        "></td></tr>");
echo("<tr><td width=20%>Program Version:".
        "</td><td><input class='form-control' type='text' name='program_version' id='program_version'". 
        (isset($version) ? " value='$version' " : "").
        "></td></tr>");

echo("</table>");
echo("<input class='btn btn-primary' type='submit' name='submit_add_program' value='Add Program'>");
echo("</form>");

echo("<BR>");

echo("Current programs:");
echo("<table  class='table table-bordered table-hover table-striped display'><tr>");
echo("<th>Program Name</th><th>Version</th>");
$program = new program($db);

$programs = program::get_programs($db);

if(count($programs)== 0) {
    echo "<tr><td>No programs have been added.</td></tr>";
} else {
foreach($programs as $program) {
    echo("<tr><td><a href='edit_program.php?program_id=".$program->get_id()."'>".$program->get_name()."</a></td>");
    echo("<td>".$program->get_version()."</td></tr>");
}
}
echo("</table>");

require_once 'includes/footer.inc.php';
