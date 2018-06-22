

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<h3>Add Program</h3>");
echo("For adding programs used for backup sets.<BR>");
if(isset($_POST['submit_add_program'])) {
if(isset($_POST['program_name'])) {
    //echo("Adding program : ".$_POST['program_name']."<BR>");

    //$name = $_POST['program_name'];
    $program_type=null;
    $program_id=-1;
    $errors = "";

    if(isset($_POST['program_name']) && $_POST['program_name'] != null) {
        $name = $_POST['program_name'];

    } else {
        $errors .= html::error_message("Please select a valid name for this program.");
        
    }

    if(isset($_POST['program'])) {

        $program_id = $_POST['program'];
    }

    if(strlen($errors) == 0) {

    //$result = $db->add_program( $name);
        $program = new program($db);
        $result = $program->add_program($name);

    
     if($result['RESULT']) {
        echo(html::success_message("Program ".$_POST['program_name']." successfully added."));
     } else {
         echo(html::error_message("ERROR: ".$result['MESSAGE']));
     }
    } else {
        echo(html::error_message($result['MESSAGE']));
    }
}
}


echo("<form name='add_program' action='add_program.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Program Name:</td><td><input type='text' name='program_name' id='program_name'". (isset($name) ? " value='$name' " : "")."></td></tr>");


//echo("<tr><td>Service:</td><td><input type='text' name='service' id='service'></td></tr>");

echo("</table>");
echo("<input type='submit' name='submit_add_program' value='Add Program'>");
echo("</form>");

echo("<BR>");

echo("Current programs:");
echo("<table  class='table table-bordered table-hover table-striped display'><tr>");
echo("<th>Program Name</th>");
$program = new program($db);

$programs = program::get_program_objects($db);

if(count($programs)== 0) {
    echo "<tr><td>No programs have been added.</td></tr>";
} else {
foreach($programs as $program) {
    echo("<tr><td>".$program->get_name()."</td></tr>");
}
}
echo("</table>");

include 'includes/footer.inc.php';
