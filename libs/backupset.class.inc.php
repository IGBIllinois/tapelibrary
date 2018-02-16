<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class backupset {
    
    private $db; // database
    private $id;
    private $name;
    //private $item_id;
    private $begin_date;
    private $end_date;
    private $program;
    private $notes;
    private $main_location;
    
    private $active;
    
    private $time_last_modified;
    private $user_last_modified;
    
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {

        $result = $db->get_backupset_data($id);
        $this->db = $db;
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
            //return null;
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
        return $this->db->get_program_name($this->program);
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
            $tape = new tape($child['id']);
            $children[] = $child;
        }
        return $children;
    }
    
    
}