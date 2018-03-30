

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>View Containers</H3>");
$name = null;
$type = null;
$parent = null;

if(isset($_POST['submit'])) {
    
    
    if(isset($_POST['begin'])) {

        //if(is_numeric($begin)) {
        $name = $_POST['begin'];
        //}
    }

    if(isset($_POST['type'])) {
        $type = $_POST['type'];
    }
    
    if(isset($_POST['container'])) {
        $parent = $_POST['container'];
    }
    
    if(isset($_POST['active'])) {
        $active = $_POST['active'];
    }

}
/*
if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "allcontainerreport.xls";
            write_all_containers_report($db, $filename);
        }
        
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
echo("<form method=POST action=view_all_containers.php>");
//
//echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");
echo("Limit By:<BR>");
echo("<table  class='table table-bordered display'><tr>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr >";
        print "<td>Container Name</td>";
        print "<td>";
        createInput("text","begin",$name);
        print "</td>";
        //        print "<td rowspan=6>";
        //print "<div id='add_multi_labels'>";
	//	print "</div>";
        //print "</td>";
      print "</tr>";
echo("<tr><td>Container Type :</td><td>");
    createInput("select","type",$type,type::get_container_types($db));
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","container",$parent, tape_library_object::get_containers($db));
echo(" </td></tr>");


echo("</table>");
echo("<input type='submit' name='submit' value='Select'>");
echo("</form>");
echo("<BR>");

echo("Current containers:") ;
echo("<table id='containers' class='table table-bordered table-hover table-striped display'>");

$current_containers = tape_library_object::get_container_objects($db, $name, $type, $parent);
//$current_containers = array();
if(count($current_containers)== 0) {
    echo "<tr><td>No containers have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Type</th><th>Parent Container</th></tr></thead>");
    echo("<tbody>");
    foreach($current_containers as $container) {
        echo("<tr><td><a href=view_container.php?container_id=".$container->get_id().">".$container->get_label()."</a></td>");
        echo("<td>".$container->get_type_name()."</td>");
        echo("<td><a href='view_container.php?container_id=".$container->get_container_id()."'>".$container->get_full_path()."</a></td></tr>");
    }
}

echo("</tbody></table>");
echo("<BR>");

?>


<form class='form-inline' action='report.php' method='post'>
<!--<input class='btn btn-primary' type='submit'-->
<input  type='submit'
                name='create_heirarchy_report' value='Download Full Container Report'>

 <select
                name='report_type' class='input-medium'>
                <option value='xls'>Excel 2003</option>
                <option value='xlsx'>Excel 2007</option>
                <option value='csv'>CSV</option>
</select>         
<?php 
if($name != null) {
echo("<input type='hidden' name='name' value='$name'>"); 
}
if($type != null) {
 echo("<input type='hidden' name='type' value='$type'>"); 
}
if($parent != null) {
 echo("<input type='hidden' name='parent' value='$parent'>"); 
}
 ?>
</form>
<?php

include 'includes/footer.inc.php';
