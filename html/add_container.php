

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'includes/header.inc.php';
?>
<script type='text/javascript'>
    var current = -1;

</script>
<?php

echo("<h3>Add Container</h3>");

    $container_type=null;
    $container_id=-1;
    $messages = "";
if(isset($_POST['container_type']) && $_POST['container_type'] != null) {
?>
    <script type='text/javascript'>
        current = <?php echo( $_POST['container_type']); ?>;
    </script>
    
<?php
}
if(isset($_GET['container_type'])) {
    $container_type = $_GET['container_type'];
?>
    <script type='text/javascript'>
        current = <?php echo( $_GET['container_type']); ?>;
    </script>
    
<?php
}

if(isset($_POST['submit_add_container'])) {
if(isset($_POST['container_name'])) {

    if(isset($_POST['container_name']) && $_POST['container_name'] != null) {
        $name = $_POST['container_name'];

    } else {
        $messages .= html::error_message("Please select a valid name for this container.");
        
    }

    if(isset($_POST['container_type']) && $_POST['container_type'] != null) {
        $container_type = $_POST['container_type'];
        
        if(isset($_POST['container'.$container_type])) {
            $container_id = $_POST['container'.$container_type];
        }
        if($container_id == "" || $container_id == -1) {
            $this_type = new type($db, $container_type);
            if(!$this_type->is_location()) {
                $messages .= html::error_message("Please select a valid parent location for this container.");
            } else {

                $container_id=-1;
            }
        }
    
        } else {
            $messages .= html::error_message("Please select a type for this container.");
        }

    

    if(strlen($messages) == 0) {

        $result = tape_library_object::add_tape($db, $name, $container_type, $container_id, -1, $login_user->get_username());
        html::write_message($result);
        
    } else {

    }
}
}


echo("<form name='add_container' action='add_container.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Container Name:</td><td><input class='form-control' type='text' name='container_name' id='container_name'". (isset($name) ? " value='$name' " : "")."></td></tr>");
echo("<tr><td>Container Type :");
echo("<BR><a href='add_container_type.php'>(Add a new container type?)</a>");
echo("</td><td>");

// Select the Type for the new Container
$container_types = type::get_container_types($db);
      echo "<select class='form-control' id='container_type' name='container_type' onChange=hide('container_type','containerdiv')>";
      echo "<option value=''>None</option>";

      foreach ($container_types as $curr_container_type) {
        echo "<option value='".$curr_container_type->get_id()."'";
        if (isset($container_type) && $container_type == $curr_container_type->get_id())
          echo " selected";
        
        echo ">".$curr_container_type->get_name()."</option>";
      }
      echo "</select>";

echo(" </td></tr>");

// Set the location of this container
echo("<tr><td>Parent Location:<BR>(Leave blank for a top-level location)</td><td>");
echo("<table>");
$all_types = type::get_container_types($db);
foreach($all_types as $type) {
    $id = $type->get_id();
    
    echo("<tr id='containerdiv$id' ".((isset($container_type) && $container_type == $id) ? " style='visibility:visible' ": " style='visibility:collapse' ") ."><td> ");
    $containers = $type->get_containers_for_type();
      echo "<select id='container".$id."' name='container".$id."'>";
      echo "<option value=''>None</option>";

      foreach ($containers as $curr_container) {
        echo "<option value='".$curr_container->get_id()."'";
        if (isset($container_id) && $container_id == $curr_container->get_id())
          echo " selected";
        
        echo ">".$curr_container->get_label()."</option>";
      }
      echo "</select>";
      
    echo("</td></tr>");
}
echo("</table>");
echo(" </td></tr>");
echo("</table>");
echo("<input class='btn btn-primary' type='submit' name='submit_add_container' value='Add Container'>");
echo("</form>");
echo("<BR>");
if(strlen($messages) > 0) {
    echo($messages);
}
require_once 'includes/footer.inc.php';
