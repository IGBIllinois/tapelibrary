
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
        echo("1");
        $container_id = $_POST['container_id'];
        if(isset($_POST['report_submit'])) {
            echo("2");
            echo("Writing Report...");
            $filename = "tapelibraryreport.xls";
            write_container_report($db, $container_id, $filename);
        }
    } else {
        $container_id = $_GET['container_id'];
    }
$container = $db->get_container_by_id($container_id);
echo("<h3>");
echo($container['label']);
echo("</h3>");

echo("Type:".$db->get_container_type_name($container['type']));
echo("<BR>");
echo("Current tapes in ".$container['label'].":<BR><BR>") ;

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_tapes = $db->get_tapes_in_container($container_id);

if(count($current_tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Label</th><th>Type</th><th>Backup Set</th></thead>");
    echo("<tbody>");
    foreach($current_tapes as $tape) {
        $backupset_id = $tape['backupset'];
        //echo("backupset = $backupset_id<BR>");
        $backupset_name = "";
        if($backupset_id == null || $backupset_id == -1) {
            $backupset_name = "None";
        } else {
            $backupset = $db->get_backupset($backupset_id);
            if($backupset == 0) {
                $backupset_name = "None";
            } else {
                $backupset_name = $backupset['name'];
            }
            
        }
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".$db->get_container_type_name($tape['type'])."</td>");
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

