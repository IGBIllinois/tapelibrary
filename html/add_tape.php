
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
    var value = document.getElementsByName("tape_type")[0].value;
    //alert("newval = "+value);
    document.getElementById("tapediv"+value).style.visibility = "visible";
    if(current != -1) {
        document.getElementById("tapediv"+current).style.visibility = "collapse";
    }
    current = value;
    //alert("new current = "+current);
    return;
}
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
    $service=null;
    $errors = "";
    $backupset = null;
    $messages = "";   
    $name_errors = "";
if(isset($_POST['submit'])) {
    
    if(isset($_POST['tape_type']) && $_POST['tape_type'] != null) {
        $tape_type = $_POST['tape_type'];
    } else {
        $errors .= "<div class='alert alert-danger'>Please input a tape type</div>";
    }
    if(isset($_POST['container'.$tape_type]) && $_POST['container'.$tape_type] != null) {
        $container_id = $_POST['container'.$tape_type];
    } else {
        $errors .= "<div class='alert alert-danger'>Please input a container</div>";
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
    //$tape_from = 1;
    //$tape_from = $_POST['tape_from'];
    //$tape_to = $_POST['tape_to'];
    
    if(!is_numeric($tape_to)) {
        $errors .= "<div class='alert alert-danger'>Please input a proper number of tapes.</div>";
    }
    if(strlen($errors) > 0) {
        //echo $errors;
    } else {

    $label = array();
    
    //for ($i=$tape_from;$i<=$tape_to;$i++) {
    //echo("from = $tape_from")
    $numtapes = $tape_to - $tape_from + 1;
    //echo("numtapes = $numtapes<BR>");
    for($i=0; $i<$numtapes; $i++) {
        // check for duplicates before starting to commit
        //echo ("i = $i, label = "+$_POST['label'.$i]);
        if(isset($_POST['label'.$i])  && $_POST['label'.$i]!="") {
            if(tape_library_object::does_tape_exist($db, $_POST['label'.$i])) {
                $name_errors .= "<div class='alert alert-danger'>Tape ". $_POST['label'.$i]. " already exists. Please change the name before adding this tape.</div>";
            }
        } else {
            $name_errors .= "<div class='alert alert-danger'>Please input a name for Tape $i</div>";
        }
        
    }
    if(strlen($name_errors) > 0) {
        //echo($name_errors);
        
    } else {
    //for ($i=$tape_from;$i<=$tape_to;$i++) {
        for($i=0; $i<$numtapes; $i++) {
            $label[$i] = $_POST['label'.$i];
            //echo("label[$i] = ".$label[$i]."<BR>");
	}
    	if (is_numeric($tape_to) && $tape_from <= $tape_to) {
		//for ($i = $tape_from; $i <= $tape_to; $i++) {
                for($i=0; $i<$numtapes; $i++) {
                    //echo("Adding tape : ".$label[$i]."<BR>");

			//mysql_query("insert into tape (type,capacity,tape_number,container,backup_set,carton,label) values ('$type','$capacity','$i','$container','$backup_set','$carton','$label[$i]')");
                    $result = tape_library_object::add_tape($db, $label[$i], $tape_type, $container_id, $backupset, 0 ); //TODO: userid?

                    if ($result['RESULT']) {
                        $messages .=("<div class='alert alert-success'>".$result['MESSAGE']."</div>");
                    } else {
                        $messages .=("<div class='alert alert-danger'>".$result['MESSAGE']."</div>");
                    }
                }
		//print "<script type=\"text/javascript\">parent.window.container.href='index.php'</script>";
                //print("<BR>Tapes added<BR>");
                //unset($_POST);
	} else {
		$messages .= "<p><b><div class='alert alert-danger'>Something went wrong, please try again</div></b></p>";
            
	}
    }
    }
    }
    
    if(isset($_GET['tape_type'])) {
        $tape_type = $_GET['tape_type'];
    }
    //$result = add_item($db, $name, $tape_type, $container_id, $service, 0 );
     //if($result) {
        
     //} else {
         //echo("ERROR: ");
     //}




echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");
echo("<table class='table  display'><tr><td width=50% valign='top'>");
echo("<table class='table table-bordered display'>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr >";
        print "<td width=40%>Tapes to add:</td>";
        print "<td>From:";
        createInput("text","tape_from",isset($tape_from) ? $tape_from : "");
        print "<br />To: ";
        createInput("text","tape_to",isset($tape_to) ? $tape_to : "");
        print "</td>";
        
      print "</tr>";
echo("<tr><td>Tape Type: ");
echo("<BR><a href=add_container_type.php>(Add a new tape type?)</a>");
echo("</td><td>");
    createInput("select","tape_type",$tape_type, type::get_tape_types($db),"","hide()");

echo(" </td></tr>");
echo("<tr><td>Parent Location:");
echo("<BR><a href=add_container.php>(Add a new container?)</a>");
echo("</td><td>");
echo("<table>");
$all_types = type::get_tape_type_objects($db);
foreach($all_types as $type) {
    $id = $type->get_id();
    
    echo("<tr id='tapediv$id' ".((isset($tape_type) && $tape_type == $id) ? " style='visibility:visible' ": " style='visibility:collapse' ") ."><td> ");
    createInput("select","container".$id,(isset($container_id)? $container_id : ""),$type->get_containers_for_type());
    echo("</td></tr>");
}
echo("</table>");
echo(" </td></tr>");
//echo("<tr><td>Service:</td><td><input type='text' name='backupset' id='service'></td></tr>");
echo("<tr><td>Backup Set:");
echo("<BR><a href=add_backupset.php>(Add a new backup set?)</a>");
echo("</td><td>");
createInput("select","backupset",$backupset,backupset::get_all_backupsets_array($db));
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
include 'includes/footer.inc.php';
