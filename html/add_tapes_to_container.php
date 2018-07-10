
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

if(isset($_POST['container_id']) && $_POST['container_id'] != null) {
    $container_id = $_POST['container_id'];
    $container = new tape_library_object($db, $container_id); 


echo("<H3>Add Tapes to ".$container->get_label()."</H3>");
echo("Type:".$container->get_type_name());
echo("<BR>");
echo("Located in:".$container->get_full_path()."<BR><BR>");    
    $tape_type=null;

    $errors = "";
    $backupset = null;
    $messages = "";   
    $name_errors = "";
if(isset($_POST['submit'])) {
    
    if(isset($_POST['tape_type']) && $_POST['tape_type'] != null) {
        $tape_type = $_POST['tape_type'];
    } else {
        $errors .= html::error_message("Please input a tape type");
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
    if(($tape_to != null && !is_numeric($tape_to)) && ($tape_from != null && !is_numeric($tape_from))) {
        $errors .= html::error_message("'From' and 'To' fields cannot both contain alphabetical characters.<BR>Please make both numeric, or only input one in the 'From' field.");
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
            
            //echo("label[$i] = ".$label[$i]."<BR>");
            //echo("ids[$i] = ".$ids[$i]."<BR>");
	}
        //echo("tape_to = $tape_to, tape_from = $tape_from<BR>");
        if((is_null($tape_to) || $tape_to === "") && !is_null($tape_from)) {
            // just add one
            $i = 0;
            //echo("Adding just one tape : ".$label[$i]."<BR>");
            $result = tape_library_object::add_tape($db, $ids[$i], $tape_type, $container_id, $backupset, $login_user->get_username(), $label[$i] );
            if ($result['RESULT']) {
                $messages .=(html::success_message($result['MESSAGE']));
            } else {

                $messages .=(html::error_message($result['MESSAGE']));
            }
        } else if (is_numeric($tape_to) && $tape_from <= $tape_to) {

                for($i=0; $i<$numtapes; $i++) {
                    //echo("Adding tape : ".$ids[$i]."<BR>");

                    $result = tape_library_object::add_tape($db, $ids[$i], $tape_type, $container_id, $backupset, $login_user->get_username(), $label[$i] );

                    if ($result['RESULT']) {
                        $messages .=(html::success_message($result['MESSAGE']));
                    } else {
                        
                        $messages .=(html::error_message($result['MESSAGE']));
                    }
                }
		//print "<script type=\"text/javascript\">parent.window.container.href='index.php'</script>";
                //print("<BR>Tapes added<BR>");
                //unset($_POST);
	} else {
		$messages .= html::error_message("<p><b>Something went wrong, please try again</b></p>");
            
	}
    }
    }

echo("<form id='addform' name='add_tape' action='add_tapes_to_container.php' method='POST'>");
echo("<input type=hidden name='container_id' value='$container_id'>");
echo("<table><tr><td valign='top'>");
echo("<table class='table table-bordered display'>");

      print "<tr >";
        print "<td width=20%>Tapes to add:</td>";
        print "<td>From:";
        createInput("text","tape_from",isset($tape_from) ? $tape_from : "");
        print "<br />To: ";
        createInput("text","tape_to",isset($tape_to) ? $tape_to : "");
        print "</td>";
        
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
$container_type = new type($db, $container->get_type());
    createInput("select","tape_type",$tape_type, $container_type->get_tape_types_for_container_type(),"","hide()");
echo(" </td></tr>");

//echo("<tr><td>Service:</td><td><input type='text' name='backupset' id='service'></td></tr>");
echo("<tr><td>Backup Set:</td><td>");
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

echo("Current tapes in ".$container->get_label().":<BR>") ;
echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_tapes = $container->get_children();

if(count($current_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Tape ID Number</th><th>Type</th><th>Label</th><th>Backup Set</th></thead>");
    echo("<tbody>");
    foreach($current_tapes as $tape) {

        $backupset_id = $tape->get_backupset();

        $backupset_name = "";
        if($backupset_id == null || $backupset_id == -1) {
            $backupset_name = "None";
        } else {
            $backupset = new backupset($db, $backupset_id);
            $backupset_name = $backupset->get_name();
            
        }

        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_tape_label()."</td>");
        echo("<td><a href='view_backupset_data.php?backupset_id=$backupset_id'>".$backupset_name."</a></td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");

echo("<BR>");
} else {
    $errors = html::error_message("Please input a proper container.");
}
include 'includes/footer.inc.php';
