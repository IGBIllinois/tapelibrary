<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class program {

    private $name = "";
    private $id = -1;
    private $db;
    

    
    public function __construct($db, $id=0) {
        $this->db = $db;
        if($id != 0) {
            $this->load_by_id($db, $id);
        }
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {

        $result = $this->get_program($db, $id);

        if($result !=0) {
            
            $this->id = $result['id'];
            $this->name = $result['name'];

            
        } else {
            return null;
        }
        
    }
    
    public function get_name() {
        return $this->name;
    }    
    
    
    function add_program($name) {
        try {
            $messages = "";
            $errors = false;
        
        if(program::program_exists($this->db, $name)) {
            // already exists
            $messages .= ("A program with the name $name already exists. Please choose a different name or version.");
            return array("RESULT"=>FALSE,
                        "MESSAGE"=>$messages);
        }
        $query = "INSERT INTO programs (name) VALUES (:name)";
        $params = array("name"=>$name);
        $result = $this->db->get_insert_result($query, $params);
        $this->id = $result;
            return array('RESULT'=>true,
			'MESSAGE'=>"Program $name successfully created.",
			'type_id'=>$this->id);
        } catch(Exception $e) {
            return array("RESULT"=>FALSE,
                        "MESSAGE"=>$e->getTraceAsString());
        }
    }
        
    public static function program_exists($db, $name, $id=0) {
            $find_query = "SELECT * from programs where name=:name";
            $params = array("name"=>$name);
            $result = $db->get_query_result($find_query, $params);
            if(count($result) > 0) {   
                return 1;
            } else {
                return 0;
            }
    }
    
    
    function get_program($db, $program_id) {
        $query = "SELECT * from programs where id = :program_id";
        $params = array("program_id"=>$program_id);
        
        $result = $db->get_query_result($query, $params);
        if(count($result)==1) {
                $result = $result[0];
            } else {
                $result = 0;
            }
        return $result;

    }

    public static function get_programs($db) {
        $query = "SELECT id, name from programs";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    public static function get_program_objects($db) {
        $results = array();
        $programs = program::get_programs($db);
        foreach($programs as $program) {
            $program = new program($db, $program['id']);
            $results[] = $program;
        }
        
        return $results;
    }
    
    
    
        
    
}