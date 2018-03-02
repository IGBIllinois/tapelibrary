<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class tape_library_object {
    
    protected $db; // database
    protected $id =-1;
    //private $item_id;
    protected $label;
    protected $container=-1;
    protected $type;
    protected $time_created;
    protected $backupset;
    protected $active;
    
    protected $time_last_modified;
    protected $user_last_modified;
    
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {
        $result = $db->get_tape_library_object_data($id);

        $this->db = $db;
        if($result !=0) {
            
            $this->id = $result['id'];
            $this->label = $result['label'];
            $this->type = $result['type'];
            $this->container = $result['container'];
            $this->active = $result['active'];
            $this->backupset = $result['backupset'];
            
            
            
        } else {
            
        }
        
    }
    
    public function get_label() {
        return $this->label;
    }
    
    public function get_container_id() {
        return $this->container;
    }
    
    public function get_type() {
        return $this->type;
    }
    
    public function get_container_name() {
        
        return $this->db->get_container_name($this->container);
    }
    
    public function get_type_name() {

        return $this->db->get_container_type_name($this->type);
    }
    
    public function get_backupset() {
        return $this->backupset;
    }
    
    public function get_backupset_name() {
        $backupset = new backupset($this->db, $this->backupset);
        if($backupset->get_id() == -1) {
            return "None";
        } else {
            return $backupset->get_name();
        }
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function is_active() {
        return $this->active;
    }
    
    public function get_children() {
        //return $this->db->get_tapes_in_container($this->id);
        return $this->db->get_children($this->id);
    }
    

    
    public function get_max_slots() {
        $type = new type($this->db, $this->get_type());
        return $type->get_max_slots();
    }
    
    public function is_location() {
        if($this->container == null || $this->container == -1) {
            return 1;
        } else {
            return 0;
        }
    }

    
    public function is_tape() {
        if(count($this->can_contain_types()) == 0) {
            return 1;
        } else {
            return 0;
        }
    }
        
    public function can_contain_types() {
        $this_type = new type($this->db, $this->type);
        return $this_type->get_can_contain_types();
    }
    
    public function can_contain_tapes() {
        $this_type = new type($this->db, $this->type);
        return $this_type->can_contain_tapes();
    }

    
}