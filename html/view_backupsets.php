
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
    $active = null;

    if(isset($_GET['active'])) {
        $active = $_GET['active'];
    }
       
echo("Current Backup Sets:") ;

echo("<fieldset><table id='view_backupsets' class='table table-bordered table-hover table-striped display'>");

$current_backupsets = backupset::get_all_backupsets($db, $active);

if(count($current_backupsets)== 0) {
    echo "<tr><td>No backup sets have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Name</th><th>Begin Date</th><th>End Date</th><th>Program/Version</th><th>Notes</th><th>Status</th></thead>");
    echo("<tbody><tr>");
    foreach($current_backupsets as $backupset) {
        //echo("<tr><td>".$tape['tape_number']."</td>");
        $id = $backupset->get_id();
        $name = $backupset->get_name();
        $start_date = $backupset->get_begin_date();
        $end_date = $backupset->get_end_date();
        $program_name = $backupset->get_program_name();
        $notes = $backupset->get_notes();
        $is_active = $backupset->is_active();
        echo("<td><a href=view_backupset_data.php?backupset_id=".$id.">".$name."</a></td>");
echo("<td>".$start_date."</td>");
echo("<td>".$end_date."</td>");
echo("<td>".$program_name."</td>");
$notes = $backupset->get_notes();
if(strlen($notes) > 256) {
    $full_notes = $notes;
    $notes = "<div id=noteDiv".$id."-orig>".substr($notes, 0, 256) . "...".
            "<a onClick=showText('noteDiv".$id."')>Show more</a></div>".
            "<div id=noteDiv".$id." style='display:none'>".$full_notes.
            "<a onClick=showText('noteDiv".$id."')>Show less</a></div>";
                
}
echo("<td>".$notes."</td>");
echo("<td>".(($is_active) ? "Active" : "Inactive")."</td></tr>");        
    }
    echo("</tbody>");
}

echo("</table></fieldset>");

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


