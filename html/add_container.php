

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

echo("<h3>Add Container</h3>");
//$name = $_POST['container_name'];
    $container_type=null;
    $container_id=-1;
    //$errors = "";
    $backupset = null;
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
    //echo("Adding container : ".$_POST['container_name']."<BR>");

    if(isset($_POST['container_name']) && $_POST['container_name'] != null) {
        $name = $_POST['container_name'];

    } else {
        $messages .= html::error_message("Please select a valid name for this container.");
        
    }
    
    if(isset($_POST['backupset'])) {
        $backupset = $_POST['backupset'];
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
                //$this_type = new type($db, $type);
                $container_id=-1;
            }
        }
    
        } else {
            $messages .= html::error_message("Please select a type for this container.");
        }

    

    if(strlen($messages) == 0) {

        $result = tape_library_object::add_tape($db, $name, $container_type, $container_id, $backupset, 0 );
   

        if ($result['RESULT']) {
            //header('Location:view_container.php?container_id='.$result['id'].'&add_success=1');
            $messages .= html::success_message($result['MESSAGE']);
        } else {
            $messages .= html::error_message($result['MESSAGE']);
        }
    } else {
        //echo($errors);
    }
}
}


echo("<form name='add_container' action='add_container.php' method='POST'>");

echo("<table class='table table-bordered display'>");
echo("<tr><td width=20%>Container Name:</td><td><input type='text' name='container_name' id='container_name'". (isset($name) ? " value='$name' " : "")."></td></tr>");
echo("<tr><td>Container Type :");
echo("<BR><a href='add_container_type.php'>(Add a new container type?)</a>");
echo("</td><td>");
    createInput("select","container_type",(isset($container_type) ? $container_type : ""),type::get_container_types($db), "", "hide()");
echo(" </td></tr>");
echo("<tr><td>Parent Location:<BR>(Leave blank for a top-level location)</td><td>");
echo("<table>");
$all_types = type::get_container_type_objects($db);
foreach($all_types as $type) {
    $id = $type->get_id();
    
    echo("<tr id='containerdiv$id' ".((isset($container_type) && $container_type == $id) ? " style='visibility:visible' ": " style='visibility:collapse' ") ."><td> ");
    createInput("select","container".$id,(isset($container_id)? $container_id : ""),$type->get_containers_for_type());
    echo("</td></tr>");
}
echo("</table>");
echo(" </td></tr>");
//echo("<tr><td>Service:</td><td><input type='text' name='service' id='service'></td></tr>");
echo("<tr><td>Backup Set:</td><td>");
createInput("select","backupset",$backupset,backupset::get_all_backupsets_array($db));
echo("</td></tr>");
echo("</table>");
echo("<input type='submit' name='submit_add_container' value='Add Container'>");
echo("</form>");
echo("<BR>");
if(strlen($messages) > 0) {
    echo($messages);
}
include 'includes/footer.inc.php';
