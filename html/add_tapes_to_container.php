
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
    $container = new container($db, $container_id); 


echo("<H3>Add Tapes to ".$container->get_label()."</H3>");
echo("Type:".$container->get_type_name());
echo("<BR>");
echo("Located in:".$db->get_full_path($container->get_container_id())."<BR><BR>");    
    $tape_type=null;

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
            if($db->does_tape_exist($_POST['label'.$i])) {
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
                    $result = $db->add_tape($label[$i], $tape_type, $container_id, $backupset, 0 ); //TODO: userid?
                    $is_num = is_numeric($result);

                    if ($is_num) {
                        $messages .=("<div class='alert alert-success'>Tape ".$label[$i]." successfully added.</div>");
                    } else {
                        $messages .=("<div class='alert alert-danger'>Error in adding tape: ".$label[$i].".<BR>$result</div>");
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
    //$result = add_item($db, $name, $tape_type, $container_id, $service, 0 );
     //if($result) {
        
     //} else {
         //echo("ERROR: ");
     //}


/*
echo("Current tapes:") ;
echo("<table id='curr_tapes' class='display'>");

$current_tapes = get_tapes($db, null, null, null, null, null);
if(count($current_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><td>Tape ID</td><td>Label</td><td>Type</td><td>Parent Location</td></tr></thead>");
    foreach($current_tapes as $tape) {
        echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".get_tape_type_name($db, $tape['type'])."</td>");
        echo("<td>".$tape['container_name']."</td></tr>");
        
    }
    echo("</table>");
}
echo("<BR>");
 * 
 */

echo("<form id='addform' name='add_tape' action='add_tapes_to_container.php' method='POST'>");
echo("<input type=hidden name='container_id' value='$container_id'>");
echo("<table><tr><td valign='top'>");
echo("<table class='table table-bordered display'>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr >";
        print "<td width=20%>Tapes to add:</td>";
        print "<td>From:";
        createInput("text","tape_from",isset($tape_from) ? $tape_from : "");
        print "<br />To: ";
        createInput("text","tape_to",isset($tape_to) ? $tape_to : "");
        print "</td>";
        
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
    createInput("select","tape_type",$tape_type, $db->get_tape_types_for_container_type($container->get_type()),"","hide()");
echo(" </td></tr>");

//echo("<tr><td>Service:</td><td><input type='text' name='backupset' id='service'></td></tr>");
echo("<tr><td>Backup Set:</td><td>");
createInput("select","backupset",$backupset,$db->get_all_backups_array());
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
    echo("<thead><tr><th>Label</th><th>Type</th><th>Backup Set</th></thead>");
    echo("<tbody>");
    foreach($current_tapes as $tape_data) {
        $tape_id = $tape_data['id'];
        $tape = new tape($db, $tape_id);
        $backupset_id = $tape->get_backupset();
        //$backupset_id = $tape['backupset'];
        //echo("backupset = $backupset_id<BR>");
        $backupset_name = "";
        if($backupset_id == null || $backupset_id == -1) {
            $backupset_name = "None";
        } else {
            $backupset = new backupset($db, $backupset_id);
            $backupset_name = $backupset->get_name();
            
            //$backupset = $db->get_backupset($backupset_id);
            //if($backupset == 0) {
            //    $backupset_name = "None";
            //} else {
            //    $backupset_name = $backupset['name'];
            //}
            
        }
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td><a href='view_backupset_data.php?backupset_id=$backupset_id'>".$backupset_name."</a></td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");

echo("<BR>");
} else {
    $errors = "Please input a proper container";
}
include 'includes/footer.inc.php';
