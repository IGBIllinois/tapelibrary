

<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
?>
<script type='text/javascript'>
    var current = -1;
function hide() {
    // collapse?
    //alert("hi");
    //alert("current = "+current);
    var value = document.getElementsByName("container_type")[0].value;
    //alert("newval = "+value);
    document.getElementById("containerdiv"+value).style.visibility = "visible";
    if(current != -1) {
        document.getElementById("containerdiv"+current).style.visibility = "collapse";
    }
    current = value;
    //alert("new current = "+current);
    return;
}
</script>
<?php
if(isset($_POST['container_type']) && $_POST['container_type'] != null) {
    ?>
    <script type='text/javascript'>
        current = <?php echo( $_POST['container_type']); ?>;
    </script>
    
<?php
}

if(!isset($_POST['container_id'])&& !isset($_GET['container_id'])) {
    echo(html::error_message("Please select a proper tape or container."));
} else {

if(isset($_POST['container_id'])) {
$container_id = $_POST['container_id'];
} else {
    $container_id = $_GET['container_id'];
}

$container = new tape_library_object($db, $container_id);
if($container->is_tape()) {
    $object_type = "Tape";
    
} else {
    $object_type = "Container";
}

    $container_type=$container->get_type();
    $parent_id = $container->get_container_id();
    $name = $container->get_label();
    $active = $container->is_active();
    $tape_label = $container->get_tape_label();

    $messages = "";
if(isset($_POST['submit_edit_container'])) {
if(isset($_POST['container_name'])) {

    if(isset($_POST['container_name']) && $_POST['container_name'] != null) {
        $name = $_POST['container_name'];

    } else {
        $messages .= html::error_message("Please select a valid name for this container.");
        
    }

    if(isset($_POST['container_type']) && $_POST['container_type'] != null) {
        $container_type = $_POST['container_type'];

    } else {
        $messages .= html::error_message("Please select a type for this container.");
        
    }

    
    if(isset($_POST['tape_label'])) {

        $tape_label = $_POST['tape_label'];
    }
    
    if(isset($_POST['parent_container_id'])) {
        $parent_id = $_POST['parent_container_id'];
    }
    
    if(strlen($messages) == 0) {

        $container = new tape_library_object($db, $container_id);

        $result = $container->edit($name, $parent_id, $active, $tape_label);

        if ($result['RESULT']) {
            $messages .=(html::success_message($result['MESSAGE']));
        } else {
            $messages .=(html::error_message($result['MESSAGE']));
        }
        
        }
    }
}
}


//$container = new tape_library_object($db, $container_id);
echo("<h3>Edit $object_type:".$container->get_label()."</h3>");

echo("<form name='edit_container' action='edit_container.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>$object_type Name:</td><td><input type='text' name='container_name' id='container_name'". (isset($name) ? " value='$name' " : "")."></td></tr>");
if($container->is_tape()) {
    echo("<tr><td width=20%>Tape Label:</td><td><input type='text' name='tape_label' id='tape_label'". (isset($tape_label) ? " value='$tape_label' " : "")."></td></tr>");
}
echo("<tr><td>$object_type Type :");
echo("<BR><a href='add_container_type.php'>(Add a new type?)</a>");
echo("</td><td>");

echo($container->get_type_name());

$container_type = new type($db, $container->get_type());

//$parent_id = $container->get_container_id();
echo(" </td></tr>");
echo("<tr><td>Location:</td><td>");
echo("<table>");

$containers = $container_type->get_containers_for_type();


echo "<select id='parent_container_id' name='parent_container_id'>";
      echo "<option value=''>None</option>";
foreach($containers as $curr_container) {

        echo "<option value='".$curr_container->get_id()."'";
        if (isset($parent_id) && $parent_id == $curr_container->get_id())
          echo " selected";
        
        echo ">".$curr_container->get_label()."</option>";
      }
      echo "</select>";
    echo("</table></td></tr>");




echo("</table>");
echo("<input type='hidden' name='container_id' value='".$container_id."'>");
echo("<input type='hidden' name='container_type' value='".$container->get_type()."'>");
echo("<input type='submit' name='submit_edit_container' value='Edit $object_type'>");
echo("</form>");
echo("<BR>");
if(strlen($messages) > 0) {
    echo($messages);
}

include 'includes/footer.inc.php';
