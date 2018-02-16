
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

        $container_id = $_POST['container_id'];
        if(isset($_POST['report_submit'])) {
            echo("Writing Report...");
            $filename = "tapelibraryreport.xls";
            write_container_report($db, $container_id, $filename);
        }
    } else {
        $container_id = $_GET['container_id'];
    }
//$container = $db->get_container_by_id($container_id);
$container = new container($db, $container_id);
echo("<h3>");
echo($container->get_label());
echo("</h3>");

echo("Type:".$container->get_type_name());
echo("<BR>");
echo("Located in:".$db->get_full_path($container->get_container_id())."<BR><BR>");
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
        echo("<td>".$backupset_name."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");

//list_all($db);
echo("</fieldset>");

echo("<form method='POST' action='view_container.php' id='get_report' name='get_report'>");
echo("<input type='hidden' name='container_id' value='$container_id'>");
echo("<input type='submit' name='report_submit' value='Get Report'>");
echo("</form>");

}
include 'includes/footer.inc.php';

