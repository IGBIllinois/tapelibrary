<?php
include_once 'includes/main.inc.php';
include_once 'includes/session.inc.php';


if (isset($_POST['create_full_report'])) {
    try {
    $type = $_POST['report_type'];
    $filename = "fulltapereport";
    $backupsets = backupset::get_all_backupsets($db);
    $data = array();
        //$excel->writeLine($backupsets);
        foreach($backupsets as $backupset_info) {
            
            $backupset_id = $backupset_info->get_id();

            $backupset_name = $backupset_info->get_name();
            $backupset_begin = $backupset_info->get_begin_date();
            $backupset_end = $backupset_info->get_end_date();
            
            $header = array($backupset_name);
            $start_line = array("Start Date", $backupset_begin);
            $end_line = array("End Date", $backupset_end);
            $data[] = array();
            $data[] = ($header);
            $data[] = ($start_line);
            $data[] = ($end_line);
            $data[] = (array());
            $titles = array("Tape ID Number", "Tape Type", "Tape Label", "Container");
            $data[] = ($titles);
            
            $tapes = $backupset->get_tapes_in_backupset();
            foreach($tapes as $tape) {
                
                $container_id = $tape->get_container_id();
                //$container = $db->get_container_by_id($container_id);
                $container_name = $tape->get_container_name();
                
                $data[] = array($tape->get_label(), $tape->get_type_name(), $tape->get_tape_label(), $container_name);
                
            }
        //$excel->writeLine(array());
        //$excel->writeLine(array());
        }
        
        // unassigned tapes
        $unassigned_tapes = tape_library_object::get_tapes_without_backupset($db);
        if(count($unassigned_tapes) > 0) {
            $data[] = array();
            $data[] = array("Unassigned tapes");
            //$excel->writeLine($header);
            //$excel->writeLine(array());
            foreach($unassigned_tapes as $tape) {

                    $container_id = $tape->get_id();
                    //$container = $db->get_container_by_id($container_id);
                    $container_name = $tape->get_container_name();

                    $data[] = array($tape->get_label(), $tape->get_type_name(), $container_name);
                    //$excel->writeLine($tape_array);
            }
        }
            
            

            //header("Location: excel/". $filename);
            //unlink("excel/".$filename);
        } catch(Exception $e) {
            //echo($e);
            //echo($e->getTrace());
        }

}

if(isset($_POST['create_container_report'])) {
    $type = $_POST['report_type'];
    $filename = "containerreport";
    
    try {
        $container_id = $_POST['container_id'];
        $data = array();
            //$container = $db->get_container_by_id($container_id);
            $container = new tape_library_object($db, $container_id);
            if($container->get_id() == -1) {
                //echo("No such container.<BR>");
                return;
            }
            
            
            
            //$backupset = get_backupset($db, $container['backupset']);
            //print_r($backupset);
            //$backupset_name = $backupset['name'];
            $type_name = $container->get_type_name();
            $data[] = array("Report for ".$container->get_label(), "Type: ".$type_name);
            $data[] = array();
            $tapes = tape_library_object::get_tapes($db, null, null, null, $container_id);
            $data[] = array("Tape Label", "Tape Type", "Backupset");

            //$excel= new ExcelWriter("excel/".$filename);
            
            //$excel->writeLine($header);
            //$excel->writeLine($titles);

            foreach($tapes as $tape) {
                $backupset = new backupset($db, $tape->get_backupset());
                
                $backupset_name = $backupset->get_name();
                $data[] = array($tape->get_label(), $tape->get_type_name(), $backupset_name);
                //$excel->writeLine($tape_array);
            }
            //$excel->close();
            //header("Location: excel/". $filename);
            //unlink("excel/".$filename);
        } catch(Exception $e) {
            //echo($e);
            //echo($e->getTrace());
        }
}

if(isset($_POST['create_container_detail_report'])) {
    $type = $_POST['report_type'];
    $filename = "containerdetailreport";
    
    $name = $_POST['name'];
    $container_type = $_POST['type'];
    $parent = $_POST['parent'];

    $data = array();
    
    $containers = tape_library_object::get_container_objects($db, $name, $container_type, $parent);

    foreach($containers as $container) {
        $container_id = $container->get_id();
        $type_name = $container->get_type_name();
            $data[] = array("Report for ".$container->get_label(), "Type: ".$type_name);

            $tapes = tape_library_object::get_tapes($db, null, null, null, $container_id);
            $data[] = array("Tape ID Number", "Tape Type", "Tape Label", "Backupset");

            //$excel= new ExcelWriter("excel/".$filename);
            
            //$excel->writeLine($header);
            //$excel->writeLine($titles);

            foreach($tapes as $tape) {
                $backupset = new backupset($db, $tape->get_backupset());
                
                $backupset_name = $backupset->get_name();
                $data[] = array($tape->get_label(), $tape->get_type_name(), $tape->get_tape_label(), $backupset_name);
                //$excel->writeLine($tape_array);
            }
            $data[] = array();
    }
    
}


if(isset($_POST['create_backupset_report'])) {
    $type = $_POST['report_type'];
    $filename = "backupsetreport";
    $data = array();
        $backupset_id = $_POST['backupset_id'];    
        try {
            //$backupset = $db->get_backupset($backupset_id);
            $backupset = new backupset($db, $backupset_id);
            if($backupset == null) {
                //echo("No such backupset.<BR>");
                return;
            }

            $header = array($backupset->get_name());
            $start_line = array("Start Date", $backupset->get_begin_date());
            $end_line = array("End Date", $backupset->get_end_date());
            $program_line = array("Program", $backupset->get_program_name());
            $notes_line = array("Notes", $backupset->get_notes());
            
            $tapes = $backupset->get_tapes_in_backupset();
            $titles = array("Tape ID Number", "Tape Type", "Label", "Container", "Full Path");

            
            $data[] = ($header);
            $data[] = ($start_line);
            $data[] = ($end_line);
            $data[] = $program_line;
            $data[] = $notes_line;
            $data[] = (array());
            
            $data[] = ($titles);
            $data[] = array();
            

            foreach($tapes as $tape) {
                
                $container_id = $tape->get_container_id();
                $container_name = $tape->get_container_name();
                //$container_name = $container['label'];
                
                $tape_array = array($tape->get_label(), $tape->get_type_name(), $tape->get_tape_label(), $container_name, $tape->get_full_path());
                $data[] = ($tape_array);
            }

        } catch(Exception $e) {
            //echo($e);
            //echo($e->getTrace());
        }
        
}

if(isset($_POST['create_heirarchy_report'])) {
            
    if(isset($_POST['name']) ||
       isset($_POST['type']) ||
       isset($_POST['parent'])) {
        
        $name = $_POST['name'];
        $container_type = $_POST['type'];
        $parent = $_POST['parent'];
    
        $containers = tape_library_object::get_container_objects($db, $name, $container_type, $parent);
        $data = report::get_heirarchy($db, $containers);
    } else
    if(isset($_POST['container_id'])) {
        $data = report::get_heirarchy($db, array(new tape_library_object($db, $_POST['container_id'])));
    } else {
        $data = report::get_heirarchy($db, tape_library_object::get_location_objects($db));
    }
    $filename = "heirarchyreport";
    $type = $_POST['report_type'];
}

$filename = $filename . "." . $type;

switch ($type) {
    
	case 'csv':
		report::create_csv_report($data,$filename);
		break;
	case 'xls':
	      	report::create_excel_2003_report($data,$filename);
                break;
	case 'xlsx':
		report::create_excel_2007_report($data,$filename);
		break;
}


function get_container_data($container_id) {
    $result_array = array();
    $container = new tape_library_object($db, $container_id);
    $header;
    $headers = array($container->get_label(), "Type: ".$type_name);
    $children = $container->get_children();
    foreach($children as $child) {
        //$child = new tape_library_o
    }
    
}


?>
