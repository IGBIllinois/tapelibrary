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
    

    
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    public function load_by_id($db, $id) {

        $result = $db->get_program($id);
        $this->db = $db;
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
}