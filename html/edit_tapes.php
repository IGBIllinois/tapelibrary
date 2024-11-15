<?php

require_once 'includes/header.inc.php';

$begin = null;
    $end = null;
    $select_type = null;
    $select_container = null;
    $active = 1;
    $messages = "";
echo("<H3>Edit Tapes</H3>");
echo("Limit By:<BR>");

if(isset($_POST['limit_submit'])) {
    
    
    if(isset($_POST['begin'])) {
        
        $begin = $_POST['begin'];
        
    }
    if(isset($_POST['end'])) {
        
        $end = $_POST['end'];
        
    }
    
    if(isset($_POST['type'])) {
        $select_type = $_POST['type'];
    }
    
    if(isset($_POST['select_container'])) {
        $select_container = $_POST['select_container'];
    }
    
    if(isset($_POST['active'])) {
        $active = $_POST['active'];
    }
}
echo("<form method=POST action=edit_tapes.php>");

echo("<table class='table table-bordered display'><tr>");

      print "<tr>";
        print "<td>Tape Number(s)</td>";
        print "<td>From: ";
        html::createInput("text","begin",$begin);
        print "<br />To: ";
        html::createInput("text","end",$end);
        print "</td>";
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
$tape_types = type::get_tape_types($db);

      echo "<select class='form-control' id='type' name='type'>";
      echo "<option value=''>None</option>";

      foreach ($tape_types as $curr_tape_type) {
        echo "<option value='".$curr_tape_type->get_id()."'";
        if (isset($select_type) && $select_type == $curr_tape_type->get_id()) {
          echo " selected";
        }
        echo ">".$curr_tape_type->get_name()."</option>";
      }
      echo "</select>";
      
echo(" </td></tr>");

echo("<tr><td>Parent Location:</td><td>");
$containers = tape_library_object::get_containers($db);
      echo "<select class='form-control' id='select_container' name='select_container'>";
      echo "<option value=''>None</option>";

      foreach ($containers as $curr_container) {
        echo "<option value='".$curr_container->get_id()."'";
        if (isset($select_container) && $select_container == $curr_container->get_id())
          echo " selected";
        
        echo ">".$curr_container->get_label()."</option>";
      }
      echo "</select>";
echo(" </td></tr>");


echo("</table>");
echo("<input class='btn btn-primary' type='submit' name='limit_submit' value='Select'>");
echo("</form><BR>");

if(isset($_POST['submit'])) {

    if(isset($_POST['checkbox'])) {

        foreach($_POST['checkbox'] as $checked) {
            
            $id = $checked;
            $this_tape = new tape_library_object($db, $id);
            $tape_label = $_POST['tape_label_'.$id];
            if(isset($_POST['tape_container']) && $_POST['tape_container'] != "") {
                $container = $_POST['tape_container'];
            } else {
                $container = $this_tape->get_container_id();
            }
           
            $new_tape_label = $_POST['new_tape_label_'.$id];

            $active = (isset($_POST['active_'.$id]) ? 1 : 0);

                
                $result = $this_tape->edit($tape_label, $container, $active, $new_tape_label, $login_user->get_username());
                html::write_message($result);

        }
    } else {
        $messages .= (html::warning_message("Nothing checked"));
    }
}

$tapes = tape_library_object::get_tapes($db, $begin, $end, $select_type, $select_container, $active);
  if(strlen($messages) > 0) {
      echo($messages);
  }
  print "<fieldset>";
echo("<form name='edit_tapes_form' method='POST'>");
echo("<table id='edit_tapes_table' name='edit_tapes_table' class='table table-bordered table-hover table-striped display'><thead><tr>");
echo("<th><input type=checkbox onClick=toggleAll(this,'checkbox') /><th>Tape ID Number</th><th>Type</th><th>Label</th><th>Location");
echo("<BR>Move selected to:");

$containers = tape_library_object::get_containers($db);
      echo "<select id='tape_container' name='tape_container'>";
      echo "<option value=''>None</option>";

      foreach ($containers as $curr_container) {
        echo "<option value='".$curr_container->get_id()."'";
        if (isset($container) && $container == $curr_container->get_id())
          echo " selected";
        
        echo ">".$curr_container->get_label()."</option>";
      }
      echo "</select>";

echo("</th><th>Backup Set</th><th>Active</th></tr></thead>");
foreach($tapes as $tape) {
    echo("<tr>");
    echo("<td>");
    echo("<input type='checkbox' name=checkbox[] id='".$tape->get_id()."' value='".$tape->get_id()."'>");
    echo("</td><td>");
    echo($tape->get_label()."<input type=hidden name='tape_label_".$tape->get_id()."' id='tape_label_".$tape->get_id()."' value='".$tape->get_label())."'>";
    echo("</td>");
    
    echo("<td>".$tape->get_type_name()."</td>");
    
    echo("<td>");
    echo("<input type=text name='new_tape_label_".$tape->get_id()."' id='new_tape_label_".$tape->get_id()."' value='".$tape->get_tape_label())."'>";
    echo("</td>");
    
    echo("<td>");
    //createInput("select", "tape_container", $tape->get_container_id(), tape_library_object::get_containers($db), $tape->get_id());
    echo($tape->get_container_name());
    echo("</td>");
    
    $backupset = new backupset($db, $tape->get_backupset());
    echo("<td><a href='view_backupset_data.php?backupset_id=".$backupset->get_id()."'>".$backupset->get_name()."</a></td>");
    echo("<td><input type='checkbox' name=active_".$tape->get_id()." id='active_".$tape->get_id()."' value='active_".$tape->get_id()."'". ($tape->is_active() ? " checked " : "" ). " >");
    echo("</td></tr>");

}



echo("</table>");
echo("<BR><BR>");
    echo("<input class='btn btn-primary' type='submit' name='submit' value='Edit Selected Records' class='icon_submit' id='edit_submit' href='edit_tapes.php'>");
    echo("&nbsp;<input class='btn btn-warning' type='button' onclick=\"window.location='edit_tapes.php'\" name='cancel' value='Cancel'>");
    echo("</form>");
  print "</fieldset>";

require_once 'includes/footer.inc.php';

?>
