

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>View Containers</H3>");

if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "allcontainerreport.xls";
            write_all_containers_report($db, $filename);
        }
        /*
if(isset($_POST['container_name'])) {
    //echo("Adding container : ".$_POST['container_name']."<BR>");
    //print_r($_POST);
    
    $name = $_POST['container_name'];
    $container_type=null;
    $container_id=-1;
    $service=null;
    $errors = "";
    if(isset($_POST['container_type'])) {
        $container_type = $_POST['container_type'];
    } else {
        $errors .= "<div class='alert alert-danger'>Please select a type for this container.</div>";

    }
    if(isset($_POST['container'])) {
        $container_id = $_POST['container'];
    }
    if(isset($_POST['service'])) {
        $service = $_POST['service'];
    }
    if($errors != "") {
    $result = $db->add_container(-1, $name, $container_type, $container_id, $service, 0 );
    print_r($result);
    
     if($result != 0) {
        echo("<div class='alert alert-success'>Container ".$_POST['container_name']." successfully added.</div>");
     } else {
         echo("<div class='alert alert-danger'>ERROR: Container not added.</div>");
     }
    } else {
        echo($errors);
    }
}
*/
echo("Current containers:") ;
echo("<table id='containers' class='table table-bordered table-hover table-striped display'>");

$current_containers = $db->get_containers();
//$current_containers = array();
if(count($current_containers)== 0) {
    echo "<tr><td>No containers have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Type</th><th>Parent Container</th></tr></thead>");
    echo("<tbody>");
    foreach($current_containers as $container) {
        echo("<tr><td><a href=view_container.php?container_id=".$container->get_id().">".$container->get_label()."</a></td>");
        echo("<td>".$container->get_type_name()."</td>");
        echo("<td>".$db->get_full_path($container->get_container_id())."</td></tr>");
        
    }
    echo("</tbody></table>");
}
echo("<BR>");
/*
echo("<form name='add_container' action='add_container.php' method='POST'>");

echo("<table>");
echo("<tr><td>Container Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");
echo("<tr><td>Container Type :</td><td>");
    createInput("select","container_type","",$db->get_container_types());
echo(" </td></tr>");
echo("<tr><td>Parent Container (if any):</td><td>");
    createInput("select","container","",$db->get_containers());
echo(" </td></tr>");
echo("<tr><td>Service:</td><td><input type='text' name='service' id='service'></td></tr>");


echo("</table>");
echo("<input type='submit' name='submit' value='Add Container'>");
echo("</form>");

*/
echo("<form method='POST' action='view_all_containers.php' name='get_report'>");
echo("<input type='submit' name='report_submit' value='Get Full Report'>");
echo("</form>");
include 'includes/footer.inc.php';
