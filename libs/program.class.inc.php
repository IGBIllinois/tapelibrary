<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class program {

    private $name = "";
    private $id = -1;
    private $version = "";
    private $db;
    

    
    public function __construct($db, $id=0) {
        $this->db = $db;
        if($id != 0) {
            $this->load_by_id($id);
        }
    }
    
    public function __destruct() {
       
    }
   
    
    public function get_name() {
        return $this->name;
    }    
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_version() {
        return $this->version;
    }
    
    
    /** Adds a new program
     * 
     * @param string $name Name for the program
     * @param string $vesrion Version for the program
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string],
     *  "id"=>type)
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message,
     * and "id" is the id of the newly created program, if successful
     */
    public function add_program($name, $version) {
        try {
            $messages = "";
            $errors = false;
        
        if(self::program_exists($this->db, $name, $version)) {
            // already exists
            $messages .= ("A program with the name $name and version $version already exists. Please choose a different name or version.");
            return array("RESULT"=>FALSE,
                        "MESSAGE"=>$messages);
        }
        $query = "INSERT INTO programs (name, version) VALUES (:name, :version)";
        $params = array("name"=>$name, "version"=>$version);
        $result = $this->db->get_insert_result($query, $params);
        $this->id = $result;
            return array('RESULT'=>true,
			'MESSAGE'=>"Program $name successfully created.",
			'id'=>$this->id);
        } catch(Exception $e) {
            return array("RESULT"=>FALSE,
                        "MESSAGE"=>$e->getTraceAsString());
        }
    }
    
    
    
    /** Edits a program
     * 
     * @param type $name New name for this program
     * @param type $version New version for this program
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message.
     */
    public function edit_program($name, $version) {
        $program_exists = self::program_exists($this->db, $name, $version);
        if(($program_exists > 0) && ($this->get_id() != $program_exists)) {
            // A program with this name and version already exists
            return array('RESULT'=>false,
			'MESSAGE'=>"A Program with the name $name and version $version already exists.",
			);
        }
        
        $query = "UPDATE programs set name=:name, version=:version where id=:id";
        $params = array("name"=>$name, "version"=>$version, "id"=>$this->get_id());
        $this->db->get_update_result($query, $params);
        
        $this->name = $name;
        $this->version = $version;
        
        return array('RESULT'=>true,
			'MESSAGE'=>"The Program $name has been updated successfully.",
			);
        
    }    
        
    /** 
     * Determines if a program already exists
     * 
     * @param type $db Database Object
     * @param type $name Name of the Program to check
     * @param type $version Version of the program to check
     * @return int The id of the Program, if it exists, 0 if it doesn't
     */
    public static function program_exists($db, $name, $version) {
            $find_query = "SELECT * from programs where name=:name and version=:version";
            $params = array("name"=>$name, "version"=>$version);
            $result = $db->get_query_result($find_query, $params);
            if(count($result) > 0) {   
                return $result[0]["id"];
            } else {
                return 0;
            }
    }
    
     /**
     * 
     * @param type $db Database object
     * @return array An array of all existing Program objects 
     */
    public static function get_programs($db) {
        $query = "SELECT id from programs order by name";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $programs = $db->query($query);
        $results = array();
        foreach($programs as $program) {
            $curr_program = new program($db, $program['id']);
            $results[] = $curr_program;
        }
        
        return $results;
        
    }
    
    
    /* Private functions */
    
    private function load_by_id($id) {

        $this->get_program($id);
        
    }
    
    /** Gets program data and loads it into this program object
     * 
     * @param int $program_id The ID of the program to load
     * @return boolean
     */
    private function get_program($program_id) {
        $query = "SELECT * from programs where id = :program_id LIMIT 1";
        $params = array("program_id"=>$program_id);
        
        $result = $this->db->get_query_result($query, $params);
        if($result) {
            $this->id = $result[0]['id'];
            $this->name = $result[0]['name'];
            $this->version = $result[0]['version'];
        } else {
            return false;
        }
    }



    
        
    
}