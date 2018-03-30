
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';

$container_id = -1;
if(!isset($_GET['container_id']) && !isset($_POST['container_id'])) {
    echo("Please choose a valid container.");
} else {
    if(isset($_POST['container_id'])) {
/*
        $container_id = $_POST['container_id'];
        if(isset($_POST['report_submit'])) {
            echo("Writing Report...");
            $filename = "tapelibraryreport.xls";
            write_container_report($db, $container_id, $filename);
        }
 * */
 
    } else {
        $container_id = $_GET['container_id'];
    }
//$container = $db->get_container_by_id($container_id);
$container = new container($db, $container_id);

if(isset($_GET['add_success']) && ($_GET['add_success'] == 1)) {
    echo("<div class='alert alert-success'>Container ".$container->get_label()." successfully added.</div>");

}
echo("<h3>");
echo($container->get_label());
echo("</h3>");

if($container->is_tape()) {
    $object_type = "Tape";
} else {
    $object_type= "Container";
}

echo("Type:".$container->get_type_name());
echo("<BR>");
echo("Located in:".$container->get_full_path()."<BR><BR>");

if(!$container->is_tape()) {
echo("Current objects in ".$container->get_label().":<BR>") ;
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
        echo("<td><a href='view_container.php?container_id=".$tape->get_id()."'>".$tape->get_label()."</a></td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td><a href='view_backupset_data.php?backupset_id=$backupset_id'>".$backupset_name."</a></td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");
}
//list_all($db);
echo("</fieldset>");

if($container->can_contain_tapes()) {
    echo("<form method='POST' action='add_tapes_to_container.php' id='add_tapes_to_container' name='add_tapes_to_container'>");
    echo("<input type='hidden' name='container_id' value='$container_id'>");
    echo("<input type='submit' name='report_submit' value='Add new tapes to this container'>");
    echo("</form>");
}
 
 
echo("<form method='POST' action='edit_container.php' id='edit_container_form' name='edit_container_form'>");
echo("<input type='hidden' name='container_id' value='$container_id'>");
echo("<input type='submit' name='edit_container' value='Edit this $object_type'>");
echo("</form>");

/*
echo("<form method='POST' action='view_container.php' id='get_report' name='get_report'>");
echo("<input type='hidden' name='container_id' value='$container_id'>");
echo("<input type='submit' name='report_submit' value='Get Report'>");
echo("</form>");
*/
}
?>

<form class='form-inline' action='report.php' method='post'>
 <!--<input class='btn btn-primary' type='submit'-->
<input type='submit'
                name='create_heirarchy_report' value='Download Report'>
  <select
                name='report_type' class='input-medium'>
                <option value='xls'>Excel 2003</option>
                <option value='xlsx'>Excel 2007</option>
                <option value='csv'>CSV</option>
        </select>
 <?php       echo("<input type='hidden' name='container_id' value='$container_id'>"); ?>

</form>
<?php
include 'includes/footer.inc.php';

