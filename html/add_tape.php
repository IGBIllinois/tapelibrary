
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'includes/header.inc.php';
echo("<H3>Add Tapes</H3>");
    
    $tape_type=null;
    $container_id=null;
    $service=null;
    $errors = "";
    $backupset = null;
if(isset($_POST['submit'])) {
    
    if(isset($_POST['tape_type']) && $_POST['tape_type'] != null) {
        $tape_type = $_POST['tape_type'];
    } else {
        $errors .= "<div class='alert alert-danger'>Please input a tape type</div>";
    }
    if(isset($_POST['container']) && $_POST['container'] != null) {
        $container_id = $_POST['container'];
    } else {
        $errors .= "<div class='alert alert-danger'>Please input a container</div>";
    }
    if(isset($_POST['service'])) {
        $service = $_POST['service'];
    }
    if(isset($_POST['backupset'])) {
        $backupset = $_POST['backupset'];
    }
    $tape_from = 1;
    $tape_to = $_POST['tape_to'];
    if(!is_numeric($tape_to)) {
        $errors .= "<div class='alert alert-danger'>Please input a proper number of tapes.</div>";
    }
    if(strlen($errors) > 0) {
        echo $errors;
    } else {
        
    $label = array();
    $name_errors = "";
    for ($i=$tape_from;$i<=$tape_to;$i++) {
        // check for duplicates before starting to commit
        
        if(isset($_POST['label'.$i])  && $_POST['label'.$i]!="") {
            if($db->does_tape_exist($_POST['label'.$i])) {
                $name_errors .= "<div class='alert alert-danger'>Tape ". $_POST['label'.$i]. " already exists. Please change the name before adding this group.</div>";
            }
        } else {
            $name_errors .= "<div class='alert alert-danger'>Please input a name for Tape $i</div>";
        }
        
    }
    if(strlen($name_errors) > 0) {
        echo($name_errors);
        
    } else {
    for ($i=$tape_from;$i<=$tape_to;$i++) {
            $label[$i] = $_POST['label'.$i];
            //echo("label[$i] = ".$label[$i]."<BR>");
	}
    	if (is_numeric($tape_to) && $tape_from <= $tape_to) {
		for ($i = $tape_from; $i <= $tape_to; $i++) {
                    //echo("Adding tape : ".$label[$i]."<BR>");

			//mysql_query("insert into tape (type,capacity,tape_number,container,backup_set,carton,label) values ('$type','$capacity','$i','$container','$backup_set','$carton','$label[$i]')");
                    $result = $db->add_tape($i, $label[$i], $tape_type, $container_id, $backupset, 0 ); //TODO: userid?

                    if ($result  !=0) {
                        echo("<div class='alert alert-success'>Tape ".$label[$i]." successfully added.</div>");
                    } else {
                        echo("<div class='alert alert-danger'>Error in adding tape: ".$label[$i].".</div>");
                    }
                }
		//print "<script type=\"text/javascript\">parent.window.container.href='index.php'</script>";
                //print("<BR>Tapes added<BR>");
                //unset($_POST);
	} else {
		print "<p><b><div class='alert alert-danger'>Something went wrong, please try again</div></b></p>";
            
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
echo("<form id='addform' name='add_tape' action='add_tape.php' method='POST'>");

echo("<table class='table table-bordered display'><tr>");
//echo("<tr><td>Location Name:</td><td><input type='text' name='container_name' id='container_name'></td></tr>");

      print "<tr >";
        print "<td width=20%>Number of tapes</td>";
        print "<td>";
        //createInput("text","tape_from","");
        //print "<br />To: ";
        createInput("text","tape_to","");
        print "</td>";
                print "<td rowspan=6>";
        print "<div id='add_multi_labels'>";
		print "</div>";
        print "</td>";
      print "</tr>";
echo("<tr><td>Tape Type :</td><td>");
    createInput("select","tape_type",$tape_type, $db->get_tape_types());
echo(" </td></tr>");
echo("<tr><td>Parent Location:</td><td>");
    createInput("select","container",$container_id, $db->get_containers());
echo(" </td></tr>");
//echo("<tr><td>Service:</td><td><input type='text' name='backupset' id='service'></td></tr>");
echo("<tr><td>Backup Set:</td><td>");
createInput("select","backupset","",$db->get_all_backups());
echo("</td></tr>");

echo("</table>");
echo("<input type='submit' name='submit' value='Add Tapes'>");
echo("</form>");

include 'includes/footer.inc.php';
