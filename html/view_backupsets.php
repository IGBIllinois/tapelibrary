
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
    /*
        if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "fullbackupsetreport.xls";
            write_full_report($db, $filename);
        }
     * 
     
    
    if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "fullbackupsetreport";
            $data = array();
            $containers = $db->get_containers();
        $excel->writeLine($backupsets);
        foreach($containers as $container) {
            
            $container_id = $container->get_id();

            $container_name = $container->get_label();
            $container_type = $db->get_container_type_name($container->get_type());
            $container_location = $container->get_container_name();
            
            $data[] = array("<B>".$container_name."</B>");
            $data[] = array("Type:".$container_type);
            $data[] = array("Located in:".$container_location);
            
            
        }
        //report::test();
    }
     */
     
        


//
echo("Current Backup Sets:") ;

echo("<fieldset><table id='view_backupsets' class='table table-bordered table-hover table-striped display'>");

$current_backupsets = $db->get_all_backup_sets();

if(count($current_backupsets)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Begin Date</th><th>End Date</th><th>Program</th><th>Notes</th></thead>");
    echo("<tbody>");
    foreach($current_backupsets as $backupset) {
        //echo("<tr><td>".$tape['tape_number']."</td>");
        $id = $backupset->get_id();
        $name = $backupset->get_name();
        $start_date = $backupset->get_begin_date();
        $end_date = $backupset->get_end_date();
        $program_name = $backupset->get_program_name();
        $notes = $backupset->get_notes();
        echo("<td><a href=view_backupset_data.php?backupset_id=".$id.">".$name."</s></td>");
echo("<td>".$start_date."</td>");
echo("<td>".$end_date."</td>");
echo("<td>".$program_name."</td>");
        echo("<td>".$notes."</td></tr>");
        
    }
    echo("</tbody>");
}

echo("</table></fieldset>");
/*
echo("<form method='POST' action='view_backupsets.php' name='get_report'>");
echo("<input type='submit' name='report_submit' value='Get Full Report'>");
echo("</form>");

 * 
 */
?>
<form class='form-inline' action='report.php' method='post'>
<!-- <input class='btn btn-primary' type='submit'-->
<!--
<input type='submit'
                name='create_full_report' value='Download Full Report'>
 <select
                name='report_type' class='input-medium'>
                <option value='xls'>Excel 2003</option>
                <option value='xlsx'>Excel 2007</option>
                <option value='csv'>CSV</option>
        </select>
</form>
-->
        <?php
//list_all($db);
echo("</fieldset>");
include 'includes/footer.inc.php';


