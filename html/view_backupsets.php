
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
echo("<H3>View Backup Sets</H3>");
echo("<fieldset>");
$begin = null;
    $end = null;
    $type = null;
    $container = null;
    $active = 1;
    
    
    if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "fullbackupsetreport.xls";
            write_full_report($db, $filename);
        }
if(isset($_POST['submit'])) {
    
    
    if(isset($_POST['begin'])) {
        if(is_numeric($begin)) {
        $begin = $_POST['begin'];
        }
    }
    if(isset($_POST['end'])) {
        if(is_numeric($end)) {
        $end = $_POST['end'];
        }
    }
    
    if(isset($_POST['type'])) {
        $type = $_POST['type'];
    }
    
    if(isset($_POST['container'])) {
        $container = $_POST['container'];
    }
    
    if(isset($_POST['active'])) {
        $active = $_POST['active'];
    }


    //$result = add_item($db, $name, $container_type, $container_id, $service, 0 );
     //if($result) {
        
     //} else {
         //echo("ERROR: ");
     //}
}


//
echo("Current Backup Sets:") ;

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

$current_backupsets = $db->get_all_backup_sets();

if(count($current_backupsets)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Begin Date</th><th>End Date</th><th>Program</th><th>Notes</th></thead>");
    echo("<tbody>");
    foreach($current_backupsets as $backupset) {
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td><a href=view_backupset_data.php?backupset_id=".$backupset['id'].">".$backupset['name']."</s></td>");
echo("<td>".$backupset['begin']."</td>");
echo("<td>".$backupset['end']."</td>");
echo("<td>".$backupset['program']."</td>");
        echo("<td>".$backupset['notes']."</td></tr>");
        
    }
    echo("</tbody>");
}

echo("</table></fieldset>");

echo("<form method='POST' action='view_backupsets.php' name='get_report'>");
echo("<input type='submit' name='report_submit' value='Get Full Report'>");
echo("</form>");

//list_all($db);
echo("</fieldset>");
include 'includes/footer.inc.php';


