<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
if(!isset($_GET['backupset_id'])&& !isset($_POST['backupset_id'])) {
    echo("Error: Please select a backup set.");
    
} else{
    if(isset($_POST['backupset_id'])) {

        $backupset_id = $_POST['backupset_id'];
        if(isset($_POST['report_submit'])) {

            echo("Writing Report...");
            $filename = "backupsetreport.xls";
            write_backupset_report($db, $backupset_id, $filename);
        }
    } else {
        $backupset_id = $_GET['backupset_id'];
    }
//$backupset_id = $_GET['backupset_id'];

$backupset_data = $db->get_backupset($backupset_id);
if($backupset_data != 0) {
$tapes = $db->get_tapes_for_backupset($backupset_id);

echo("<h3>".$backupset_data['name']."</h3>");
echo("<B>Begin Date:</B> ".$backupset_data['begin']."<BR>");
echo("<B>End Date:</B> ".$backupset_data['end']."<BR>");
echo("<B>Program:</B> ".$backupset_data['program']."<BR>");
echo("<B>Notes:</B> ".$backupset_data['notes']."<BR>");
echo("<BR>");
echo("Tapes in this backupset:<BR>");

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

if(count($tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Label</th><th>Type</th><th>Parent Location</th></thead>");
    echo("<tbody>");
    foreach($tapes as $tape) {
        //echo("<tr><td>".$tape['tape_number']."</td>");
        echo("<td>".$tape['label']."</td>");
        echo("<td>".$db->get_container_type_name($tape['type'])."</td>");
        echo("<td>".$tape['container_name']."</td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");
}
echo("<form method='POST' action='add_tapes_to_backupset.php' name='add_tapes'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Add Tapes to Backup set'>");
echo("</form>");

echo("<form method='POST' action='remove_tapes_from_backupset.php' name='remove_tapes'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Remove Tapes from Backup set'>");
echo("</form>");

echo("<form method='POST' action='edit_backupset.php' name='edit_backupset'>");
echo("<input type='hidden' name='id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Edit this Backup set'>");
echo("</form>");

echo("<form method='POST' action='view_backupset_data.php' name='get_report'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='report_submit' value='Get Report'>");
echo("</form>");
}
include_once 'includes/footer.inc.php';