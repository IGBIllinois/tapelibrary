<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class type {
    
    private $db; // database
    private $id = -1;
    //private $item_id;
    private $name;
    private $can_contain_types; // array of type ids
    private $max_slots;
    
    private $time_last_modified;
    private $user_last_modified;
    
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {
        $result = $db->get_type($id);
        $this->db = $db;
        if($result !=0) {
            //print_r($result);
            
            
            $this->id = $result['container_type_id'];
            $this->name = $result['name'];
            //$this->can_contain_types = array();
            $types_string  = $result['can_contain_types'];
            //echo("types string = "+$types_string);
            if($types_string != null && $types_string != "") {
                $this->can_contain_types = explode(",", $result['can_contain_types']);
            } else {
                $this->can_contain_types = array();
            }
            $this->max_slots = $result['max_slots'];
            //print_r($this->can_contain_types);
            
        } else {
            
        }
        
    }
    
    public function get_name() {
        return $this->name;
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_can_contain_types() {
        return $this->can_contain_types;
    }
    
   
    public function get_max_slots() {
        return $this->max_slots;
    }
    
    
    public function is_tape() {
        if(count($this->get_can_contain_types()) == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function can_contain_types() {
        return $this->get_can_contain_types();
    }
    
    public function can_contain_tapes() {
        $types = $this->can_contain_types();
        foreach($types as $type) {
            $new_type = new type($this->db, $type);
            if($new_type->is_tape()) {
                return 1;
            }
        }
        return 0;
    }
    
    
}