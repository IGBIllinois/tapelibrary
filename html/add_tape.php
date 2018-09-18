
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
if(isset($_POST['tape_type']) && $_POST['tape_type'] != null) {
    ?>
    <script type='text/javascript'>
        current = <?php echo( $_POST['tape_type']); ?>;
    </script>
    
<?php
}
echo("<H3>Add Tapes</H3>");
    
    $tape_type=null;
    $container_id=null;

    $errors = "";
    $backupset = null;
    $messages = "";   
    $name_errors = "";
    $tape_from = null;
    $tape_to = null;
if(isset($_POST['submit'])) {
    
    if(isset($_POST['tape_type']) && $_POST['tape_type'] != null) {
        $tape_type = $_POST['tape_type'];
    } else {
        $errors .= html::error_message("Please input a tape type");
    }
    if(isset($_POST['container'.$tape_type]) && $_POST['container'.$tape_type] != null) {
        $container_id = $_POST['container'.$tape_type];
    } else {
        $errors .= html::error_message("Please input a container");
    }

    if(isset($_POST['backupset'])) {
        $backupset = $_POST['backupset'];
    }
    if(isset($_POST['tape_from'])) {
        $tape_from = $_POST['tape_from'];
    } 
    if(isset($_POST['tape_to'])) {
        $tape_to = $_POST['tape_to'];
    }

    if(($tape_to != null && !is_numeric($tape_to)) && ($tape_from != null && !is_numeric($tape_from))) {
        $errors .= html::error_message("'From' and 'To' fields cannot both contain alphabetical characters.<BR>".
                "Please make both numeric, or only input one in the 'From' field.");
    }

   
    
    
    if($tape_from == null) {
        $errors .= html::error_message("Please input a value for the 'From' field.");
    }
    if(is_numeric($tape_to) && is_numeric($tape_from)) {
        if($tape_to <= $tape_from) {
            $errors .= html::error_message("For numeric inputs, the 'To' field must be greater than the 'From' field.");
        }
        $numtapes = $tape_to - $tape_from + 1;
    } else {
        $numtapes = 1;
    }

 if(strlen($errors) > 0) {
        //echo $errors;
    } else {

    $label = array();
    $ids = array();

        for($i=0; $i<$numtapes; $i++) {
            $ids[$i] = $_POST['tape_id'.$i];
            $label[$i] = $_POST['tape_label'.$i];
            
	}

        if((is_null($tape_to) || $tape_to === "") && !is_null($tape_from)) {
            // just add one
            $i = 0;

            $result = tape_library_object::add_tape($db, $ids[$i], $tape_type, $container_id, $backupset, $login_user->get_username(), $label[$i] );
            if ($result['RESULT']) {
                $messages .=(html::success_message($result['MESSAGE']));
            } else {

                $messages .=(html::error_message($result['MESSAGE']));
            }
        } else if (is_numeric($tape_to) && $tape_from <= $tape_to) {

                for($i=0; $i<$numtapes; $i++) {

                    $result = tape_library_object::add_tape($db, $ids[$i], $tape_type, $container_id, $backupset, $login_user->get_username(), $label[$i] );

                    if ($result['RESULT']) {
                        $messages .=(html::success_message($result['MESSAGE']));
                    } else {
                        $messages .=(html::error_message($result['MESSAGE']));
                    }
                }

	} else {
		$messages .= html::error_message("<p><b>Something went wrong, please try again</b></p>");
            
	}
    }
    }


echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");
echo("<table class='table  display'><tr><td width=50% valign='top'>");
echo("<table class='table table-bordered display'>");

      print "<tr >";
        print "<td width=40%>Tapes to add:</td>";
         
        print "<td>From:";
        html::createInput("text","tape_from",isset($tape_from) ? $tape_from : "");
        print "<br />To: ";
        html::createInput("text","tape_to",isset($tape_to) ? $tape_to : "");
        print "</td>";
        
      print "</tr>";
echo("<tr><td>Tape Type: ");
echo("<BR><a href=add_container_type.php>(Add a new tape type?)</a>");
echo("</td><td>");

$all_types = type::get_tape_types($db);
      echo "<select id='tape_type' name='tape_type' onchange=hide('tape_type','tapediv')>";
      echo "<option value=''>None</option>";

      foreach ($all_types as $curr_type) {
        echo "<option value='".$curr_type->get_id()."'";
        if (isset($tape_type) && $tape_type == $curr_type->get_id())
          echo " selected";
        
        echo ">".$curr_type->get_name()."</option>";
      }
      echo "</select>";

echo(" </td></tr>");
echo("<tr><td>Parent Location:");
echo("<BR><a href=add_container.php>(Add a new container?)</a>");
echo("</td><td>");
echo("<table>");
$all_types = type::get_tape_types($db);
foreach($all_types as $type) {
    $id = $type->get_id();
    
    echo("<tr id='tapediv$id' ".((isset($tape_type) && $tape_type == $id) ? " style='visibility:visible' ": " style='visibility:collapse' ") ."><td> ");
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
echo("<tr><td>Backup Set:");
echo("<BR><a href=add_backupset.php>(Add a new backup set?)</a>");
echo("</td><td>");

$all_backupsets = backupset::get_all_backupsets($db);
      echo "<select id='backupset' name='backupset'>";
      echo "<option value=''>None</option>";

      foreach ($all_backupsets as $curr_backupset) {
        echo "<option value='".$curr_backupset->get_id()."'";
        if (isset($backupset) && $backupset == $curr_backupset->get_id())
          echo " selected";
        
        echo ">".$curr_backupset->get_name()."</option>";
      }
      echo "</select>";
echo("</td></tr>");

echo("</table>");

echo("<input type='submit' name='submit' value='Add Tapes'>");

echo("</td><td>");
echo("<table class='table'><tr><td>");

print "<div id='add_multi_labels'>";

		print "</div>";
        print "</td>";
echo("</td></tr></table>");
echo("</td></tr></table>");

echo("</form>");
echo("<BR>");
if(strlen($errors) > 0) {
    echo($errors);
}
if(strlen($name_errors) > 0) {
    echo($name_errors);
}
if(strlen($messages) > 0) {
    echo($messages);
}
require_once 'includes/footer.inc.php';
