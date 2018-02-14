

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<h3>Add Container</h3>");
if(isset($_POST['submit_add_container'])) {
if(isset($_POST['container_name'])) {
    //echo("Adding container : ".$_POST['container_name']."<BR>");

    //$name = $_POST['container_name'];
    $container_type=null;
    $container_id=-1;
    $errors = "";

    if(isset($_POST['container_name']) && $_POST['container_name'] != null) {
        $name = $_POST['container_name'];

    } else {
        $errors .= "<div class='alert alert-danger'>Please select a valid name for this container.</div>";

    }

    if(isset($_POST['container_type']) && $_POST['container_type'] != null) {
        $container_type = $_POST['container_type'];

    } else {
        $errors .= "<div class='alert alert-danger'>Please select a type for this container.</div>";
        
    }

    if(isset($_POST['container'])) {

        $container_id = $_POST['container'];
    }

    if(strlen($errors) == 0) {

    $result = $db->add_container_basic(-1, $name, $container_type, $container_id, 0 );

    
     if($result != 0) {
        echo("<div class='alert alert-success'>Container ".$_POST['container_name']." successfully added.</div>");
     } else {
         echo("<div class='alert alert-danger'>ERROR: Container not added.</div>");
     }
    } else {
        echo($errors);
    }
}
}
/*
echo("Current containers:") ;
echo("<table id='containers' class='table table-bordered table-hover table-striped display'>");

$current_containers = $db->get_containers();
if(count($current_containers)== 0) {
    echo "<tr><td>No containers have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Type</th><th>Parent Container</th></tr></thead>");
    echo("<tbody>");
    foreach($current_containers as $container) {
        echo("<tr><td>".$container['name']."</td>");
        echo("<td>".$db->get_container_type_name($container['type'])."</td>");
        echo("<td>".$container['container_name']."</td></tr>");
        
    }
    echo("</tbody></table>");
}
echo("<BR>");
 * 
 */
echo("<form name='add_container' action='add_container.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Container Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");
echo("<tr><td>Container Type :</td><td>");
    createInput("select","container_type","",$db->get_container_types());
echo(" </td></tr>");
echo("<tr><td>Parent Container (if any):</td><td>");
    createInput("select","container","",$db->get_containers());
echo(" </td></tr>");
//echo("<tr><td>Service:</td><td><input type='text' name='service' id='service'></td></tr>");

echo("</table>");
echo("<input type='submit' name='submit_add_container' value='Add Container'>");
echo("</form>");

include 'includes/footer.inc.php';
