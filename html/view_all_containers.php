<?php

require_once 'includes/header.inc.php';
echo("<H3>View Containers</H3>");
$name = null;
$type = null;
$parent = null;

if(isset($_POST['submit'])) {
    
    
    if(isset($_POST['begin'])) {

        $name = $_POST['begin'];

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

echo("<form method=POST action=view_all_containers.php>");

echo("Limit By:<BR>");
echo("<table  class='table table-bordered display'><tr>");

      print "<tr >";
        print "<td>Container Name</td>";
        print "<td>";
        html::createInput("text","begin",$name);
        print "</td>";

      print "</tr>";
echo("<tr><td>Container Type :</td><td>");
$container_types = type::get_container_types($db);
      echo "<select class='form-control' id='type' name='type'>";
      echo "<option value=''>None</option>";

      foreach ($container_types as $curr_container_type) {
        echo "<option value='".$curr_container_type->get_id()."'";
        if (isset($type) && $type == $curr_container_type->get_id())
          echo " selected";
        
        echo ">".$curr_container_type->get_name()."</option>";
      }
      echo "</select>";
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");

$containers = tape_library_object::get_containers($db);
      echo "<select class='form-control' id='container' name='container'>";
      echo "<option value=''>None</option>";

      foreach ($containers as $curr_container) {
        echo "<option value='".$curr_container->get_id()."'";
        if (isset($container) && $container == $curr_container->get_id())
          echo " selected";
        
        echo ">".$curr_container->get_label()."</option>";
      }
      echo "</select>";
echo(" </td></tr>");


echo("</table>");
echo("<input class='btn btn-primary' type='submit' name='submit' value='Select'>");
echo("</form>");
echo("<BR>");

echo("Current containers:") ;
echo("<table id='containers' class='table table-bordered table-hover table-striped display'>");

$current_containers = tape_library_object::get_containers($db, $name, $type, $parent);

if(count($current_containers)== 0) {
    echo "<tr><td>No containers have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Type</th><th>Parent Container</th><th>Total Number of Tapes in Container</th></tr></thead>");
    echo("<tbody>");
    foreach($current_containers as $container) {
        echo("<tr><td><a href=view_container.php?container_id=".$container->get_id().">".$container->get_label()."</a></td>");
        echo("<td>".$container->get_type_name()."</td>");
        echo("<td><a href='view_container.php?container_id=".$container->get_container_id()."'>".$container->get_full_path()."</a></td>");
        echo("<td>".$container->get_total_tapes()."</td>");
        echo("</tr>");
    }
}

echo("</tbody></table>");
echo("<BR>");

?>


<form class='form-inline' action='report.php' method='post'>
<input class='form-control' type='submit' name='create_heirarchy_report' value='Download Full Container Report'>
<select name='report_type' class='input-medium'>
                <option value='xlsx'>Excel</option>
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

require_once 'includes/footer.inc.php';

?>
