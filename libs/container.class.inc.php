<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class container {
    
    private $db; // database
    private $id;
    //private $item_id;
    private $label;
    private $container;
    private $type;
    private $time_created;
    private $backupset;
    private $active;
    
    private $time_last_modified;
    private $user_last_modified;
    
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {
        $result = $db->get_container_data($id);
        $this->db = $db;
        if($result !=0) {
            //print_r($result);
            
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
    
    public function is_location() {
        return ($this->container == -1);
    }
    
}