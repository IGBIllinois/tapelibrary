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
                
    echo(html::error_message("Error: Please select a backup set."));
    
} else{

    if(isset($_POST['submit_deactivate'])) {

    if(isset($_POST['backupset_id'])) {
        $backupset_id = $_POST['backupset_id'];
    
    $backupset = new backupset($db, $backupset_id);
    $result = $backupset->deactivate_backupset();
    if($result['RESULT'] == TRUE) {
        echo(html::success_message($result['MESSAGE']));
    } else {
        echo(html::error_message($result['MESSAGE']));
    }
    } else {
        echo(html::error_message("Please input a valid backupset id."));
    }
    
}

    if(isset($_POST['submit_activate'])) {

    if(isset($_POST['backupset_id'])) {
        $backupset_id = $_POST['backupset_id'];
    
    $backupset = new backupset($db, $backupset_id);
    $result = $backupset->activate_backupset();
    if($result['RESULT'] == TRUE) {
        echo(html::success_message($result['MESSAGE']));
    } else {
        echo(html::error_message($result['MESSAGE']));
    }
    } else {
        echo(html::error_message("Please input a valid backupset id."));
    }
    
}


    if(isset($_POST['backupset_id'])) {

        $backupset_id = $_POST['backupset_id'];

    } else {
        $backupset_id = $_GET['backupset_id'];
    }


$backupset_data = new backupset($db, $backupset_id);
if($backupset_data != null) {
    
$tapes = $backupset_data->get_tapes_in_backupset();
$notes = $backupset_data->get_notes();
if(strlen($notes) > 256) {
    $full_notes = $notes;
    $notes = "<div id=noteDiv".$backupset_id."-orig>".substr($notes, 0, 256) . "...".
            "<a onClick=showText('noteDiv".$backupset_id."')>Show more</a></div>".
            "<div id=noteDiv".$backupset_id." style='display:none'>".$full_notes.
            "<a onClick=showText('noteDiv".$backupset_id."')>Show less</a></div>";
                
}
echo("<h3>".$backupset_data->get_name()."</h3>");
echo("<B>Begin Date: </B> ".$backupset_data->get_begin_date()."<BR>");
echo("<B>End Date: </B> ".$backupset_data->get_end_date()."<BR>");
echo("<B>Program: </B> ".$backupset_data->get_program_name()."<BR>");
echo("<B>Notes: </B> ".$notes."<BR>");
echo("<B>Status: </B>".($backupset_data->is_active() ? "Active" : "Inactive"));
echo("<BR>");
echo("Tapes in this backupset:<BR>");

echo("<fieldset><table id='view_tapes' class='table table-bordered table-hover table-striped display'>");

if(count($tapes)== 0) {
    echo "<tr><td>No tapes have been added.</td></tr>";
} else {
    echo("<thead><tr><th>Tape ID Number</th><th>Type</th><th>Label</th><th>Parent Location</th></thead>");
    echo("<tbody>");
    foreach($tapes as $tape) {
        echo("<td>".$tape->get_label()."</td>");
        echo("<td>".$tape->get_type_name()."</td>");
        echo("<td>".$tape->get_tape_label()."</td>");
        echo("<td><a href=view_container.php?container_id=".$tape->get_container_id().">".$tape->get_full_path()."</a></td></tr>");
        
    }
    echo("</tbody>");

}

echo("</table></fieldset>");
}
if($backupset_data->is_active()) {
echo("<form method='POST' action='add_tapes_to_backupset.php' name='add_tapes'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Add Tapes to Backup set'>");
echo("</form>");


echo("<form method='POST' action='remove_tapes_from_backupset.php' name='remove_tapes'>");
echo("<input type='hidden' name='backupset_id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Remove Tapes from Backup set'>");
echo("</form>");
}

echo("<form method='POST' action='edit_backupset.php' name='edit_backupset'>");
echo("<input type='hidden' name='id' value='$backupset_id'>");
echo("<input type='submit' name='submit' value='Edit this Backup set'>");
echo("</form>");


if($backupset_data->is_active()) {
echo("<form method='POST' name='deactivate_backupset' action='view_backupset_data.php' onsubmit=\"return confirm('Do you really want to deactivate this backupset?')\">");
echo("<input type='hidden' name='backupset_id' value='".$backupset_id."'>");
echo("<input type='submit' name='submit_deactivate' value='Deactivate Backup Set'>");
echo("</form>");
} else {
 echo("<form method='POST' name='activate_backupset' action='view_backupset_data.php' onsubmit=\"return confirm('Do you really want to activate this backupset?')\">");
echo("<input type='hidden' name='backupset_id' value='".$backupset_id."'>");
echo("<input type='submit' name='submit_activate' value='Activate Backup Set'>");
echo("</form>");   
}



}
?>
<form class='form-inline' action='report.php' method='post'>

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

