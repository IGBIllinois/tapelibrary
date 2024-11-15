<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class backupset {
    
    private $db; // database
    private $id = -1;
    private $name = "None";
    private $begin_date;
    private $end_date;
    private $program;
    private $notes = "";
    
    private $active;
    
    public function __construct($db, $id=0) {
        $this->db = $db;
    
        if($id != null && $id != 0) {
            $this->load_by_id($db, $id);
        }
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {

        $result = $this->get_backupset_data($db, $id);

        if($result !=0) {
            //print_r($result);
            
            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->begin_date = $result['begin'];
            $this->end_date = $result['end'];
	    $this->active = $result['active'];
	    $this->notes = $result['notes'];
            $this->program = $result['program'];
            
        } else {
            return null;
        }
        
    }
    
    public function get_name() {
        return $this->name;
    }
    
    public function get_begin_date() {
        return $this->begin_date;
    }
    
    public function get_end_date() {
        return $this->end_date;
    }
    
    public function get_notes() {
        return $this->notes;
    }
    
    public function get_program() {
        return $this->program;
    }
    
    /** 
     * Gets the name and version of the Program associated
     * with this Backup Set
     * @return string The name (and version if it exists) of the
     * program associated with this Backup Set
     */
    public function get_program_name() {
        $program = new program($this->db, $this->program);
        return $program->get_name() . 
                (($program->get_version() != null && $program->get_version() != "") ? 
                (" (Version ".$program->get_version()).")" : "");
            
            
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function is_active() {
        return $this->active;
    }
    

    /** 
     * 
     * @param string $name
     * @param string $begin
     * @param string $end
     * @param int $program
     * @param string $notes
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string],
     *  "backupset_id"=>[int])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message,
     * and "backupset_id" is the id of the newly created backup set
     */
    function add_backupset($name, $begin, $end, $program, $notes = "") {

        //print_r($search_result);

        if(backupset::backupset_exists($this->db, $name)) {
            return array("RESULT"=>FALSE,
                    "MESSAGE"=>"A backupset with the name '$name' already exists. Please choose a different name.");
        }
        $query = "INSERT INTO backupset (name, begin, end, program, notes) VALUES (:name, :begin, :end, :program, :notes)";
        if( $program == "") {
            $program = null;
        }
	if (empty($notes)) {
		$notes = "";
	}
        $params = array('name'=>$name, 'begin'=>$begin, 'end'=>$end, 'program'=>$program, 'notes'=>$notes);

        $result = $this->db->insert_query($query, $params);

        $this->id = $result;
        return array("RESULT"=>TRUE,
                    "MESSAGE"=>"Backup set $name created successfully.",
                    "backupset_id"=>$result);
    }
    

    


    /**
     * 
     * Edits this Backup Set
     * 
     * @param string $name The new name for this Backup Set
     * @param string $begin The new start date for this Backup Set (YYYY-MM-DD)
     * @param string $end The new end date for this Backup Set (YYYY-MM-DD)
     * @param int $program The new Program ID
     * @param string $notes Additional notes for this Backup Set
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function edit_backupset($name, $begin, $end, $program, $notes) {
        $id = $this->id;
        $search_query = "SELECT * from backupset where name=:name and id != :id";
        $search_params = array("name"=>$name, "id"=>$id);
        $search_result = $this->db->query($search_query, $search_params);

        if(count($search_result) > 0) {

            return array("RESULT"=>FALSE,
                        "MESSAGE"=>"A backupset with the name '$name' already exists. Please choose a different name.");
        }
        $query = "UPDATE backupset set name=:name, begin=:begin, end=:end, program=:program, notes=:notes where id=:id";
        $params = array("id"=>$id, "name"=>$name, "begin"=>$begin, "end"=>$end, "program"=>$program, "notes"=>$notes);
        $result = $this->db->non_select_query($query, $params);

        return array("RESULT"=>TRUE,
                    "MESSAGE"=>"Backup set $name edited successfully,");
    }

    /**
     * 
     * Deactivates this Backup Set
     * 
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function deactivate_backupset() {

        if(count($this->get_tapes_in_backupset()) == 0) {
        $query = "UPDATE backupset set active=0 where id=:id";
        $params = array("id"=>$this->get_id());
        $result = $this->db->non_select_query($query, $params);
        $return_result = array("RESULT"=>TRUE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " successfully deactivated.");
        } else {
            $return_result = array("RESULT"=>FALSE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " is not empty. Please remove all tapes and containers from it before deactivating.");
        }
        
        return $return_result;
    }

    /**
     * 
     * Activates this Backup Set
     * 
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function activate_backupset() {

        $backupset_id = $this->get_id();
        $query = "UPDATE backupset set active=1 where id=:id";
        $params = array("id"=>$backupset_id);
        $result = $this->db->non_select_query($query, $params);
        $return_result = array("RESULT"=>TRUE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " successfully activated.");

        return $return_result;
    }

     /**
     * 
     * Deactivates this Backup Set
     * 
     * @return tape_library_object[] An array of Tape Library Objects that
      *     are in this Backup Set
     */
    public function get_tapes_in_backupset() {

        $backupset_id = $this->id;
        $tape_array = array();
        $backupset_id = $this->id;
        $query = "SELECT * from tape_library where backupset=:backupset_id order by tape_label, label";

        $params = array("backupset_id"=>$backupset_id);
        $tapes = $this->db->query($query, $params);
        
        foreach($tapes as $tape) {
            $new_tape = new tape_library_object($this->db, $tape['id']);
            if($new_tape->is_tape()) {
                $tape_array[] = $new_tape;
            }
        }
        return $tape_array;
       
    }

    /**
     * Adds a Tape to this Backup Set
     * @param type $tape_id
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function add_tape_to_backupset($tape_id) {
        if(!$this->is_active()) {
            return  array("RESULT"=>FALSE,
                            "MESSAGE"=>"Cannot add tapes to a deactivated backup set. Please re-activate it or use a different backup set.");
        }
        try {
            $query = "UPDATE tape_library set backupset=:backupset_id where id=:tape_id";
            $params = array("backupset_id"=>$this->id, "tape_id"=>$tape_id);
            $result = $this->db->non_select_query($query, $params);
            $tape = new tape_library_object($this->db, $tape_id);
            return  array("RESULT"=>TRUE,
                            "MESSAGE"=>"Tape ".$tape->get_label(). " successfully added to backup set ". $this->name . ".");
        } catch(Exception $e) {
            return  array("RESULT"=>FALSE,
                            "MESSAGE"=>$e->getTraceAsString());
        }
    }
    
    
    /**
     * Removes a Tape to this Backup Set, setting the backup set of the tape 
     * to NULL
     * @param type $tape_id
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function remove_tape_from_backupset($tape_id) {
        try {
            $backupset_id = $this->id;
            $find_query = "SELECT * from tape_library where id=:tape_id and backupset = :backupset_id";
            $params = array("tape_id"=>$tape_id, "backupset_id"=>$backupset_id);

            $find_result = $this->db->query($find_query, $params);

            if(count($find_result) == 0) {
                
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>"Tape not found.");
            }

            $query = "UPDATE tape_library set backupset = NULL where id=:tape_id and backupset = :backupset_id";
            $result = $this->db->non_select_query($query, $params);
            $tape = new tape_library_object($this->db, $tape_id);
            return  array("RESULT"=>TRUE,
                            "MESSAGE"=>"Tape ".$tape->get_label(). " successfully removed from backup set ". $this->name . ".");
        } catch(Exception $e) {

            return array("RESULT"=>FALSE,
                            "MESSAGE"=>$e->getTraceAsString());
        }
        
    }
    
        /**
     * Removes a Tape to this Backup Set, setting the backup set of the tape 
     * to NULL
     * @param type $tape_id
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function move_tape_to_new_backupset($tape_id, $new_backupset_id) {
        try {
            $backupset_id = $this->id;
            $find_query = "SELECT * from tape_library where id=:tape_id and backupset = :backupset_id";
            $params = array("tape_id"=>$tape_id, "backupset_id"=>$backupset_id);

            $find_result = $this->db->query($find_query, $params);

            if(count($find_result) == 0) {
                
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>"Tape not found.");
            }

            $new_backup_set = new backupset($this->db, $new_backupset_id);
            if($new_backup_set == null) {
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>"New backup set not found.");
            }
            
            
            $query = "UPDATE tape_library set backupset = :new_backupset_id where id=:tape_id and backupset = :backupset_id";
            $update_params = array("tape_id"=>$tape_id, "new_backupset_id"=>$new_backupset_id, "backupset_id"=>$backupset_id);
            $result = $this->db->non_select_query($query, $update_params);
            $tape = new tape_library_object($this->db, $tape_id);
            return  array("RESULT"=>TRUE,
                            "MESSAGE"=>"Tape ".$tape->get_label(). " successfully moved from backup set ". $this->name . " to ". $new_backup_set->get_name().".");
        } catch(Exception $e) {

            return array("RESULT"=>FALSE,
                            "MESSAGE"=>$e->getTraceAsString());
        }
        
    }
    
    // Static functions
    
    /**
     * Gets a list of all Backup Sets
     * 
     * @param db $db The Database Object
     * @param int $active 1 to return active backup sets, 0 to return inactive
     *  backup sets, null to return both
     * @return backupset[] An array of Backupset Objects
     */
    public static function get_all_backupsets($db, $active=null) {

        $query = "SELECT id from backupset ".(($active != null) ? " where active=:active " : ""). " order by name";
        if($active != null) {
            $params = array(":active"=>$active);
            $result = $db->query($query, $params);
        } else {
        
        $result = $db->query($query);
        }
        
        $backupsets = array();

        foreach($result as $backupset_id) {
            $id = $backupset_id['id'];

            $backupset = new backupset($db, $id);

            $backupsets[] = $backupset;
        }

        return $backupsets;

    }
    
        /**
     * 
     * Determines if a backupset with a given name already exists
     * 
     * @param db $db Database Object
     * @param string $name the name of the Backup Set to search for.
     * @return int 1 if a Backup Set with the given name exists, else 0
     */
    public static function backupset_exists($db, $name) {
        $search_query = "SELECT * from backupset where name=:name";
        $search_params = array("name"=>$name);
        $search_result = $db->query($search_query, $search_params);
        
        if(count($search_result) > 0) { 
            return 1;
        } else {
            return 0;
        }
    }

    
    // Private Functions
    
    
     /**
     *  Gets data for a Backup Set from the database
     * @param db $db The Database Object
     * @param type $id ID number of the Backupset Object to load
     * @return mixed An array of Backupset data if success, else 0
     */
    private function get_backupset_data($db, $id) {
        $query = "SELECT * from backupset where id=:id";
        $params = array("id"=>$id);
        $result = $db->query($query, $params);

        if(count($result)==1) {
            $result = $result[0];
        } else {
            $result = 0;
        }
        return $result;
    }
}
