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
    //private $item_id;
    private $begin_date;
    private $end_date;
    private $program;
    private $notes;
    private $main_location;
    
    private $active;
    
    private $time_last_modified;
    private $user_last_modified;
    
    public function __construct($db, $id=0) {
        $this->db = $db;
    
        if($id != 0) {
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
            $this->main_location = $result['main_location'];
            
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
    
    public function get_program_name() {
        $program = new program($this->db, $this->program);
        return $program->get_name();
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function is_active() {
        return $this->active;
    }
    
    public function get_main_location() {
        return $this->main_location;
    }
    
    public function get_children() {
        $children_info = $this->db->get_tapes_in_backupset($this->id);
        $children = array();
        foreach($children_info as $child) {
            $tape = new tape_library_object($child['id']);
            $children[] = $child;
        }
        return $children;
    }
    
    function add_backupset($name, $begin, $end, $program, $main_location, $notes) {

        //print_r($search_result);

        if(backupset::backupset_exists($this->db, $name)) {
            return array("RESULT"=>FALSE,
                    "MESSAGE"=>"A backupset with the name '$name' already exists. Please choose a different name.");
        }
        $query = "INSERT INTO backupset (name, begin, end, program, main_location, notes) VALUES (:name, :begin, :end, :program, :main_location, :notes)";
        if( $program == "") {
            $program = null;
        }
        if($main_location == "") {
            $main_location = null;
        }
        $params = array('name'=>$name, 'begin'=>$begin, 'end'=>$end, 'program'=>$program, 'main_location'=>$main_location, 'notes'=>$notes);

        $result = $this->db->get_insert_result($query, $params);

        $this->id = $result;
        return array("RESULT"=>TRUE,
                    "MESSAGE"=>"Backup set $name created successfully.",
                    "backupset_id"=>$result);
    }
    
    public static function backupset_exists($db, $name) {
        $search_query = "SELECT * from backupset where name=:name";
        $search_params = array("name"=>$name);
        $search_result = $db->get_query_result($search_query, $search_params);
        
        if(count($search_result) > 0) { 
            return 1;
        } else {
            return 0;
        }
    }
    
function get_backupset_data($db, $id) {
    $query = "SELECT * from backupset where id=:id";
    $params = array("id"=>$id);
    $result = $db->get_query_result($query, $params);
    
    if(count($result)==1) {
        $result = $result[0];
    } else {
        $result = 0;
    }
    return $result;
}

function edit_backupset($name, $begin, $end, $program, $location, $notes) {
    $id = $this->id;
    $search_query = "SELECT * from backupset where name=:name and id != :id";
    $search_params = array("name"=>$name, "id"=>$id);
    $search_result = $this->db->get_query_result($search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        //echo("<div class='alert alert-danger'>A backupset with the name '$name' already exists. Please choose a different name.</div>");
        //return 0;
        return array("RESULT"=>FALSE,
                    "MESSAGE"=>"A backupset with the name '$name' already exists. Please choose a different name.");
    }
    $query = "UPDATE backupset set name=:name, begin=:begin, end=:end, program=:program, main_location=:main_location, notes=:notes where id=:id";
    $params = array("id"=>$id, "name"=>$name, "begin"=>$begin, "end"=>$end, "program"=>$program, "main_location"=>$location, "notes"=>$notes);
    $result = $this->db->get_query_result($query, $params);
    //return $result;
    return array("RESULT"=>TRUE,
                "MESSAGE"=>"Backup set $name edited successfully,");
}

    function deactivate_backupset() {

        if(count($this->get_tapes_in_backupset()) == 0 && count($this->get_containers_in_backupset()) == 0){
        $query = "UPDATE backupset set active=0 where id=:id";
        $params = array("id"=>$this->get_id());
        $result = $this->db->get_query_result($query, $params);
        $return_result = array("RESULT"=>TRUE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " successfully deactivated.");
        } else {
            $return_result = array("RESULT"=>FALSE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " is not empty. Plese remove all tapes and containers from it before deactivating.");
        }
        
        return $return_result;
    }
    
    function activate_backupset() {

        $backupset_id = $this->get_id();
        $query = "UPDATE backupset set active=1 where id=:id";
        $params = array("id"=>$backupset_id);
        $result = $this->db->get_query_result($query, $params);
        $return_result = array("RESULT"=>TRUE,
                                "MESSAGE"=>"Backupset ".$this->get_name() . " successfully activated.");

        return $return_result;
    }
    
    function get_tapes_in_backupset() {

        $backupset_id = $this->id;
        $tape_array = array();
        $backupset_id = $this->id;
        $query = "SELECT * from tape_library where backupset=:backupset_id order by tape_label, label";
        //$query = "select tapes.id as id, tapes.item_id as tape_number, tapes.label as label, tapes.container as parent, tapes.type as type, tapes.backupset as backupset, tapes.active as active, (SELECT label from tape_library where parent = id) as container_name";

        $params = array("backupset_id"=>$backupset_id);
        $tapes = $this->db->get_query_result($query, $params);
        
        foreach($tapes as $tape) {
            $new_tape = new tape_library_object($this->db, $tape['id']);
            if($new_tape->is_tape()) {
                $tape_array[] = $new_tape;
            }
        }
        return $tape_array;
       
    }
    
    function get_containers_in_backupset() {
        //echo("1");
        $tape_array = array();
        $backupset_id = $this->get_id();
        
        $query = "SELECT * from tape_library where backupset=:backupset_id order by label";
        //$query = "select tapes.id as id, tapes.item_id as tape_number, tapes.label as label, tapes.container as parent, tapes.type as type, tapes.backupset as backupset, tapes.active as active, (SELECT label from tape_library where parent = id) as container_name";

        $params = array("backupset_id"=>$backupset_id);
        $tapes = $this->db->get_query_result($query, $params);
        
        foreach($tapes as $tape) {
            $new_tape = new tape_library_object($this->db, $tape->get_id());
            if(!$new_tape->is_tape()) {
                $tape_array[] = $new_tape;
            }
        }
        return $tape_array;
    }
    
    public static function get_all_backupsets_array($db) {
        
    $query = "SELECT * from backupset order by name";
    $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $db->query($query);
    //print_r($results);
    return $result;
      
    }

    public static function get_all_backupsets($db, $active=null) {

        $query = "SELECT id from backupset ".(($active != null) ? " where active=:active " : ""). " order by name";
        if($active != null) {
            $params = array("active"=>$active);
            $result = $db->get_query_result($query, $params);
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

    function add_tape_to_backupset($tape_id) {
        if(!$this->is_active()) {
            return  array("RESULT"=>FALSE,
                            "MESSAGE"=>"Cannot add tapes to a deactivated backup set. Please re-activate it or use a different backup set.");
        }
        try {
            $query = "UPDATE tape_library set backupset=:backupset_id where id=:tape_id";
            $params = array("backupset_id"=>$this->id, "tape_id"=>$tape_id);
            $result = $this->db->get_query_result($query, $params);
            $tape = new tape_library_object($this->db, $tape_id);
            return  array("RESULT"=>TRUE,
                            "MESSAGE"=>"Tape ".$tape->get_label(). " successfully added to backup set ". $this->name . ".");
        } catch(Exception $e) {
            //echo($e->getTraceAsString());
            return  array("RESULT"=>FALSE,
                            "MESSAGE"=>$e->getTraceAsString());
        }
    }
    
    function remove_tape_from_backupset($tape_id) {
        try {
            $backupset_id = $this->id;
            $find_query = "SELECT * from tape_library where id=:tape_id and backupset = :backupset_id";
            $params = array("tape_id"=>$tape_id, "backupset_id"=>$backupset_id);
            //print_r($params);
            $find_result = $this->db->get_query_result($find_query, $params);
           // print_r($find_result);
            if(count($find_result) == 0) {
                
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>"Tape not found.");
            }
            //echo("Removing tape $tape_id from backupset $backupset_id<BR>");
            $query = "UPDATE tape_library set backupset='-1' where id=:tape_id and backupset = :backupset_id";
            $result = $this->db->get_query_result($query, $params);
            $tape = new tape_library_object($this->db, $tape_id);
            return  array("RESULT"=>TRUE,
                            "MESSAGE"=>"Tape ".$tape->get_label(). " successfully removed from backup set ". $this->name . ".");
        } catch(Exception $e) {
            //echo($e->getTraceAsString());
            return array("RESULT"=>FALSE,
                            "MESSAGE"=>$e->getTraceAsString());
        }
        
    }
}