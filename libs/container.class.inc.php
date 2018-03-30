<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class container extends tape_library_object {
    /*
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
    */
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    /*
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
    */

    
    public function is_location() {
        return ($this->container == -1);
    }
    
    public function get_max_slots() {
        $type = new type($this->db, $this->get_type());
        return $type->get_max_slots();
    }
    
    /* returns the number of objects currently in this container
     * 
     */
    public function get_object_count() {
        return count($this->get_children());
    }
    

    
}