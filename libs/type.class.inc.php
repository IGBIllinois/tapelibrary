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
    private $max_slots = -1;
    //private $is_location;
    
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

        $result = $this->get_type($db, $id);
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
            //$this->is_location = $result['is_location'];
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
    
    public function is_location() {
        //return $this->is_location;
        
        // if it can't be placed in anything, consider it a location
        if(count($this->get_container_types_for_type()) == 0) {
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
    
    
    public function add_type($type_name, $can_contain_types, $max_slots=-1, $is_location=0) {
        // TODO : Check container type existance first
        $message = "";
        $error = false;
        try {
            if(type::type_name_exists($this->db, $type_name)) {
                $error = true;
                $message .= "There is already a type with the name $type_name. Please chooose a different name.";
            }
            if ($error) {
			return array('RESULT'=>false,
					'MESSAGE'=>$message);
            }
            
            //$query = "INSERT INTO container_type (name, can_contain_types, max_slots, is_location) VALUES(:type_name, :can_contain_types, :max_slots, :is_location)";
            $query = "INSERT INTO container_type (name, can_contain_types, max_slots) VALUES(:type_name, :can_contain_types, :max_slots)";

            $params = array('type_name'=>$type_name, 'can_contain_types'=>$can_contain_types, 'max_slots'=>$max_slots);
            $result = $this->db->get_insert_result($query, $params);
            //return $result;
            $this->id = $result;
            return array('RESULT'=>true,
			'MESSAGE'=>"<div class='alert'>Project successfully created.</div>",
			'type_id'=>$this->id);

        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return array('RESULT'=>FALSE,
                    "Error in adding type $type_name");
        }
        
    }
    
       function edit($type_name, $can_contain_types, $max_slots) {
           $message = "";
           $error = false;
           $id = $this->get_id();
            if(type::type_name_exists($this->db, $type_name, $id)) {
                
            $message .= "A type with the name $type_name already exists. Please choose a different name.";
            return array('RESULT'=>false,
			'MESSAGE'=>$message);
            
            }
        $query = "UPDATE container_type set name=:name, can_contain_types=:can_contain_types, max_slots=:max_slots where container_type_id=:id";
        $params = array("id"=>$id, "name"=>$type_name, "can_contain_types"=>$can_contain_types, "max_slots"=>$max_slots);
        $result = $this->db->get_query_result($query, $params);
        $this->load_by_id($this->db, $id);
        return array('RESULT'=>true,
			'MESSAGE'=>"<div class='alert'>Type ".$type_name." successfully updated.</div>",
			'type_id'=>$this->get_id());
        
    }
    
    public static function type_name_exists($db, $name, $id=0) {
        $find_query = "SELECT * from container_type where name = :name";
        if($id != 0) {
            $find_query .= " where id != $id ";
        }
            $params = array("name"=>$name);
            $result = $db->get_query_result($find_query, $params);
            if(count($result) > 0) {
                return true;
            } else {
                return false;
            }
    }
    
    private function get_type($db, $id) {
        //echo("type id = $id");
        $query = "SELECT * from container_type where container_type_id = :id";
        $params = array("id"=>$id);
        $result = $db->get_query_result($query, $params);
        if(count($result)==1) {
            $result = $result[0];
            //print_r($result);
            return $result;
        }
        return null;
    }
    
    /* Gets a list of potential containers for a type
    */
    function get_container_types_for_type() {
        //print_r($type_id);
        $type_id = $this->id;
        $query = "SELECT container_type_id, can_contain_types from container_type";
        $containers = array();
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        foreach($result as $types) {
            $curr_list = $types['can_contain_types'];
            $id = $types['container_type_id'];
            $type_list = explode(",", $curr_list);
            if(in_array($type_id, $type_list)) {
                 $containers[] = $id;
           }
        }
        //echo("Container types for $type_id:");
        //print_r($containers);
        //echo("<BR>");
        return $containers;
    }
    
    function get_container_type_names_for_type() {
        //print_r($type_id);
        $container_types = $this->get_container_types_for_type();
        $list = "";
        //print_r($containers);
        foreach($container_types as $container_type_id) {
            //echo($container_id);
            if(strlen($list) > 0) {
                $list .=", ";  
            }
            $type = new type($this->db, $container_type_id);
            $list .= $type->get_name();

        }
        return $list;
    }
    
    function get_containers_for_type() {
        
        $type_array = $this->get_container_types_for_type($this->id);
        $type_string = "";
        $result = array(array("id"=>-1, "name"=>"None"));
        foreach($type_array as $type) {
            if($type_string != "") {
                $type_string .= ",";

            }
            $type_string .= $type;
        }
        if($type_string != "") {
        $query = "SELECT id, label as name from tape_library where type in (".$type_string.") order by label";
        //echo("gcft query = $query<BR>");
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        //print_r($result);
        }
        return $result;
    }
    
       
   function add_container_type_to_type($new_type) {
       $container_type_id = $this->id;

       $current_types_string = "";
            $current_types = $this->can_contain_types;
            if(!in_array($new_type, $current_types)) {
                $current_types[] = $new_type;
                $this->can_contain_types = $current_types;
                $current_types_string = implode(",", $current_types);
            
                $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";
                //echo("update_query = $update_query<BR>");
                $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$current_types_string);
                //print_r($update_params);
                $update_result = $this->db->get_query_result($update_query, $update_params);
                
                return $update_result;
            }
    }
    
    function remove_container_type_from_type($new_type) {
        //echo("id = $container_type_id, newtype=$new_type");
        $container_type_id = $this->id;
        $query = "SELECT can_contain_types from container_type where container_type_id=:container_type_id";
        $params = array("container_type_id"=>$container_type_id);

        $result = $this->db->get_query_result($query, $params);
        //print_r($result);
        if($result != null) {
            $result = $result[0];
            $new_types = $result['can_contain_types'];

            $new_types=explode(",",$new_types);
            $new_values = array();
            foreach($new_types as $type) {
                if($type != $new_type) {
                    $new_values[] = $type;
                }
            }
            //unset($new_types['$container_type_id']);
            $new_types_string = implode(",", $new_values);


            $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";
            //echo("update_query = $update_query<BR>");
            $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$new_types_string);
            //print_r($update_params);
            $update_result = $this->db->get_query_result($update_query, $update_params);

            return $update_result;
        }
    }
    
        /* Gets the types that can be put into a container type
     * (For example, a type of "TeraPack" can hold types "LTO4", "LTO5", "LTO6").
     */
    function get_tape_types_for_container_type() {
        
        $can_contain_string = implode(",", $this->get_can_contain_types());
        //$query = "SELECT container_type_id as id, name, container from container_type where container=0";
        $query = "SELECT container_type_id as id, name, container from container_type where container_type_id in ($can_contain_string) and (can_contain_types is null or can_contain_types='')";
        //echo($query);
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        //print_r($result);
        return $result;
    }
    
    
    
    /** Static functions **/
    
    public static function get_all_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type order by name";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    public static function get_all_type_objects($db) {
        $types = type::get_all_types($db);
        $all_types = array();
        foreach($types as $type) {
            $new_type = new type($db, $type['id']);
            $all_types[] = $new_type;
        }
        return $all_types;
    }
    
    public static function get_container_types($db) {
        $query = "SELECT container_type_id as id, name from container_type  where can_contain_types is not null and can_contain_types != '' order by name";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    public static function get_container_type_objects($db) {
        $types_array = type::get_container_types($db);
        $types = array();
        foreach($types_array as $type) {
            $type = new type($db, $type['id']);
            $types[] = $type;
        }
        return $types;
    }
    
    /* gets a list of all top-level locations, those that can't be placed in anything else
    * 
    */
   public static function get_location_types($db) {
       $location_types = array();
       $all_types = array();
       $container_types = array();
       $all_types_query = "SELECT container_type_id as id, name, can_contain_types from container_type";
       //$statement = $this->get_link()->prepare($all_types_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
       $result = $db->query($all_types_query);
       foreach($result as $type) {
           $type_id  = $type['id'];
           $this_type = new type($db, $type_id);
           $these_types = $this_type->get_container_types_for_type();
           if(count($these_types) == 0) {
               $location_types[] = $type;
           }
       }

       return $location_types;

   }
   
    public static function get_tape_types($db) {
        
        //$query = "SELECT container_type_id as id, name, container from container_type where container=0";
        $query = "SELECT container_type_id as id, name, container from container_type where can_contain_types is null or can_contain_types='' order by name";

        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        //print_r($result);
        return $result;
    }
    
    public static function get_tape_type_objects($db) {
        $types = type::get_tape_types($db);
        $tape_types = array();
        foreach($types as $type) {
            $new_type = new type($db, $type['id']);
            $tape_types[] = $new_type;
        }
        return $tape_types;
    }

    public function get_child_types() {
        
        $query = "SELECT can_contain_types from container_type where container_type_id=:type_id";
        $params = array("type_id"=>$type_id);
        $result = $db->get_query_result($query, $params);
        //print_r($result);
        if($result == "" or $result == null) {
            return null;
        }
        $result = $result[0];
        $result = $result['can_contain_types'];
        $types = explode(",", $result);
        return $types;
    }
    
    public static function find_loop($db, $parents, $children) {
        //echo("type = $type<BR>");
        //echo("parents:");
        //print_r($parents);
        //echo("children:");
        //print_r($children);
        $result = 0;
        $ancestors = $parents;
        $descendants = $children;
        if($parents == null || $children == null) {
            // no loop
            return 0;
        }

        foreach($ancestors as $ancestor) {
            //echo("<BR>Ancestor:<BR>");
            //print_r($ancestor);
            //echo("<BR>");
            $ancestor_type = new type($db, $ancestor);
            $grandparents = $ancestor_type->get_container_types_for_type();
            foreach($grandparents as $gp) {
            if(!in_array($gp, $ancestors)) {
                $gp_id = $gp;
                $ancestors[] = $gp_id;
            }
        }
        //echo("Ancestors:<BR>");    
        //print_r($ancestors);
        //echo("<BR>");

        foreach($descendants as $descendant) {
            $descendant_type = new type($db, $descendant);
            $grandchildren = $descendant_type->get_can_contain_types();
            //echo("<BR>grandchildren = ");
            //print_r($grandchildren);
            if(count($grandchildren) > 0) {
            foreach($grandchildren as $gc) {

                if(!in_array($gc, $descendants)) {
                    //$gc_id = $gc['container_type_id'];
                    $descendants[] = $gc;
                }
            }
            }
        }
        //echo("Descendants:<BR>");
        //print_r($descendants);
        //echo("<BR>");
        // check for loops. In an id is in both, there's a loop
        foreach($descendants as $d) {
            if(in_array($d, $ancestors)) {
                return $d;
            }
        }

        return 0;

        }
    }
}