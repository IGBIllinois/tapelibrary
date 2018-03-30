<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'includes/header.inc.php';
require_once 'includes/ExcelWriter.php';

if(isset($_POST['submit'])) {
    $container = $_POST['container'];
    $filename = "tapelibraryreport.xls";
    write_container_report($db, $container, $filename);
    
}
    echo("<form method=POST action=reports.php>");
    echo("<table><tr><td>Parent Location:</td><td>");
    createInput("select","container","",tape_library_object::get_containers($db));
echo(" </td></tr>");
echo("</table>");
echo("<input type='submit' name='submit' value='Generate Report'>");
echo("</form>");

function write_report($headers, $titles, $data, $filename) {

    try {
	$excel= new ExcelWriter("excel/".$filename);
        
	//$myArr=array("<b>Last Name</b>","<b>First Name</b>","<b>Theme</b>","<b>Status</b>","<b>Room Number</b>","<b>Phone Number</b>",
        //    "<b>Email</b>","<b>UIN</b>","<b>Supervisor</b>","<b>Home Department</b>","<b>Home College</b>");
        
	$excel->writeLine($headers);
        
        $titleLine = array();
        foreach($titles as $title) {
            $titleLine[] = "<B>".$title."</B>";
        }
        $excel->writeLine($titleLine);
        
        
	foreach($data as $line) {
		$excel->writeLine($line);
	}
	$excel->close();
	header("Location: excel/". $filename);
    } catch(Exception $e) {
        echo($e);
        echo($e->getTrace());
    }
}
    
    function write_container_report($db, $container_id, $filename) {
        /*
        try {
            $container = get_container_by_id($db, $container_id);
            if(count($container) == 0) {
                echo("No such container.<BR>");
                return;
            }
            $container = $container[0];
            print_r($container);
            
            //$backupset = get_backupset($db, $container['backupset']);
            //print_r($backupset);
            //$backupset_name = $backupset['name'];
            $type_name = get_container_type_name($db, $container['type']);
            $header = array("Report for ".$container['label'], "Type: ".$type_name);

            $tapes = tape_library_object::get_tapes($db, null, null, null, $container_id);
            $titles = array("<B>Tape Label</B>", "<B>Tape Type</B>", "<B>Backupset</B>");

            $excel= new ExcelWriter("excel/".$filename);

            $excel->writeLine($header);
            $excel->writeLine($titles);

            foreach($tapes as $tape) {
                $backupset = $db->get_backupset( $tape['backupset']);
                if($backupset == 0) {
                    $backupset_name = "None";
                } else {
                    $backupset_name = $backupset['name'];
                }
                $tape_array = array($tape['label'], $db->get_container_type_name( $tape['type']), $backupset_name);
                $excel->writeLine($tape_array);
            }
            $excel->close();
            header("Location: excel/". $filename);
        } catch(Exception $e) {
            echo($e);
            echo($e->getTrace());
        }
         * 
         */
        
    }
    
    function write_backupset_report($db, $backupset) {
        
    }
    
    include_once 'includes/footer.inc.php';

    
    ?>