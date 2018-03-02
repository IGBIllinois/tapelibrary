<?php
include_once 'includes/main.inc.php';
include_once 'includes/session.inc.php';
/*
if (isset($_POST['create_job_report'])) {

	$month = $_POST['month'];
	$year = $_POST['year'];
	$type = $_POST['report_type'];
	$data = job_functions::get_jobs_bill($db,$month,$year);
	$server_name = settings::get_server_name();
	$filename = $server_name . "-job-" . $month . "-" . $year . "." . $type; 
}

elseif (isset($_POST['user_job_report'])) {
	$user = new user($db,$ldap,$_POST['user_id']);
	$type = $_POST['report_type'];
	$filename = $user->get_username() . "-" . $_POST['start_date'] . "-" . $_POST['end_date'] . "." . $type;
	$data = $user->get_jobs_report($_POST['start_date'],$_POST['end_date']);
}

elseif (isset($_POST['create_data_report'])) {
	$month = $_POST['month'];
        $year = $_POST['year'];
        $type = $_POST['report_type'];
        $data = data_functions::get_data_bill($db,$month,$year);
	$server_name = settings::get_server_name();
	$filename = $server_name . "-data-" . $month . "-" . $year . "." . $type;
}

elseif (isset($_POST['create_user_report'])) {
	$type = $_POST['report_type'];
	$data = user_functions::get_users($db,$ldap);
	$filename = "users." . $type;
}

elseif (isset($_POST['create_job_boa_report'])) {
	$month = $_POST['month'];
        $year = $_POST['year'];
        $type = $_POST['report_type'];
        $data = job_functions::get_jobs_boa_bill($db,$month,$year);
        $server_name = settings::get_server_name();
        $filename = $server_name . "-job-boa-" . $month . "-" . $year . "." . $type;



}
elseif (isset($_POST['create_data_boa_report'])) {

	$month = $_POST['month'];
        $year = $_POST['year'];
        $type = $_POST['report_type'];
        $data = data_functions::get_data_boa_bill($db,$month,$year);
        $server_name = settings::get_server_name();
        $filename = $server_name . "-data-boa-" . $month . "-" . $year . "." . $type;

}
*/

if (isset($_POST['create_full_report'])) {
    try {
    $type = $_POST['report_type'];
    $filename = "fulltapereport";
    $backupsets = $db->get_all_backup_sets();
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
            $titles = array("Tape Label", "Tape Type", "Container");
            $data[] = ($titles);
            
            $tapes = $db->get_tapes_for_backupset($backupset_id);
            foreach($tapes as $tape) {
                
                $container_id = $tape->get_container_id();
                //$container = $db->get_container_by_id($container_id);
                $container_name = $tape->get_container_name();
                
                $data[] = array($tape->get_label(), $tape->get_type_name(), $container_name);
                
            }
        //$excel->writeLine(array());
        //$excel->writeLine(array());
        }
        
        // unassigned tapes
        $unassigned_tapes = $db->get_tapes_without_backupset();
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
            echo($e);
            echo($e->getTrace());
        }

}

if(isset($_POST['create_container_report'])) {
    $type = $_POST['report_type'];
    $filename = "containerreport";
    
    try {
        $container_id = $_POST['container_id'];
        $data = array();
            //$container = $db->get_container_by_id($container_id);
            $container = new container($db, $container_id);
            if($container->get_id() == -1) {
                echo("No such container.<BR>");
                return;
            }
            
            
            
            //$backupset = get_backupset($db, $container['backupset']);
            //print_r($backupset);
            //$backupset_name = $backupset['name'];
            $type_name = $container->get_type_name();
            $data[] = array("Report for ".$container->get_label(), "Type: ".$type_name);
            $data[] = array();
            $tapes = $db->get_tapes(null, null, null, $container_id);
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
    
    $containers = $db->get_containers($name, $container_type, $parent);
    //print_r($containers);
    foreach($containers as $container) {
        $container_id = $container->get_id();
        $type_name = $container->get_type_name();
            $data[] = array("Report for ".$container->get_label(), "Type: ".$type_name);

            $tapes = $db->get_tapes(null, null, null, $container_id);
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
                echo("No such backupset.<BR>");
                return;
            }
            
            
            
            //$backupset = get_backupset($db, $container['backupset']);
            //print_r($backupset);
            //$backupset_name = $backupset['name'];
            //$backupset = $db->get_backupset($backupset_id);
            $header = array($backupset->get_name());
            $start_line = array("Start Date", $backupset->get_begin_date());
            $end_line = array("End Date", $backupset->get_end_date());
            $tapes = $db->get_tapes_for_backupset($backupset_id);
            $titles = array("Tape Label", "Tape Type", "Container");

            
            $data[] = ($header);
            $data[] = ($start_line);
            $data[] = ($end_line);
            $data[] = (array());
            $data[] = ($titles);
            $data[] = array();

            foreach($tapes as $tape) {
                
                $container_id = $tape->get_container_id();
                $container_name = $tape->get_container_name();
                //$container_name = $container['label'];
                
                $tape_array = array($tape->get_label(), $tape->get_type_name(), $container_name);
                $data[] = ($tape_array);
            }

        } catch(Exception $e) {
            echo($e);
            echo($e->getTrace());
        }
        
}

if(isset($_POST['create_heirarchy_report'])) {
            
    if(isset($_POST['name']) ||
       isset($_POST['type']) ||
       isset($_POST['parent'])) {
        
        $name = $_POST['name'];
        $container_type = $_POST['type'];
        $parent = $_POST['parent'];
    
        $containers = $db->get_containers($name, $container_type, $parent);
        $data = $db->get_heirarchy($containers);
    } else
    if(isset($_POST['container_id'])) {
        $data = $db->get_heirarchy(array(new container($db, $_POST['container_id'])));
    } else {
        $data = $db->get_heirarchy($db->get_location_objects());
    }
    $filename = "heirarchyreport";
    $type = $_POST['report_type'];
}

echo("type = $type");
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
