<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'includes/header.inc.php';
if((!isset($_GET['backupset_id'])&& !isset($_POST['backupset_id']) || 
        (isset($_POST['backupset_id']) && $_POST['backupset_id']=="") ||
        (isset($_GET['backupset_id']) && $_GET['backupset_id'] == ""))) {
                
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

$backupset_data = new backupset($db, $backupset_id);
if($backupset_data != null) {
    
$tapes = $db->get_tapes_for_backupset($backupset_id);

echo("<h3>".$backupset_data->get_name()."</h3>");
echo("<B>Begin Date:</B> ".$backupset_data->get_begin_date()."<BR>");
echo("<B>End Date:</B> ".$backupset_data->get_end_date()."<BR>");
echo("<B>Program:</B> ".$backupset_data->get_program_name()."<BR>");
echo("<B>Notes:</B> ".$backupset_data->get_notes()."<BR>");
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
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td><a href=view_container.php?container_id=".$tape->get_container_id().">".$db->get_full_path($tape->get_container_id())."</a></td></tr>");
        
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
/*
echo("<form method='POST' action='view_backupset_data.php' name='get_report'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='report_submit' value='Get Report'>");
echo("</form>");
}=
 */
}
?>
<form class='form-inline' action='report.php' method='post'>
    <!--class='btn btn-primary'-->
 <input  type='submit' 
                name='create_backupset_report' value='Download Backup Set Report'>
  <select
                name='report_type' class='input-medium'>
                <option value='xls'>Excel 2003</option>
                <option value='xlsx'>Excel 2007</option>
                <option value='csv'>CSV</option>
        </select>
        <?php echo("<input type='hidden' name='backupset_id' value='$backupset_id'>"); ?>

</form>
<?php

include_once 'includes/footer.inc.php';

