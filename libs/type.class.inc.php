<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class type {
    
    private $db; // database
    private $id = -1;

    private $name;
    private $can_contain_types; // array of type ids
    private $max_slots = -1;

    
    public function __construct($db, $id=0) {
        // if no ID given, just create a blank type
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
            
            $this->id = $result['container_type_id'];
            $this->name = $result['name'];

            $types_string  = $result['can_contain_types'];

            if($types_string != null && $types_string != "") {
                $this->can_contain_types = explode(",", $result['can_contain_types']);
            } else {
                $this->can_contain_types = array();
            }
            $this->max_slots = $result['max_slots'];

            
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
    
    
    /**
     * Determines if this type is a Tape type. If it cannot contain any
     * other types, it is considered a Tape.
     * @return int 1 if this type is a Tape, else 0
     */
    public function is_tape() {
        if(count($this->get_can_contain_types()) == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    
    /**
     * Determines if this type is a top-level location . If it cannot be
     * placed in any other types, it is considered a Location.
     * @return int 1 if this type is a Location, else 0
     */
    public function is_location() {
        
        // if it can't be placed in anything, consider it a location
        if(count($this->get_container_types_for_type()) == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    /** 
     * Returns an array of Type IDs that this Type can contain
     * @return int[] An array of Type IDs that this Type can contain
     */
    public function can_contain_types() {
        return $this->get_can_contain_types();
    }
    
    /**
     * Determines if this Type can contain Tapes
     * @return int 1 if this Type can contain Tapes, else 0
     */
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
    
    
    /** Creates a new type
     * 
     * @param string $type_name
     * @param string $can_contain_types A comma-separated string of Type IDs
     *    that this new type can contain
     * @param int $max_slots The maximum number of objects this type can contain,
     *    or -1 if there is no limit.
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string],
     *   "type_id"=>int)
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message,
     * and "type_id" is the id of the newly created type, if successful
     */
    public function add_type($type_name, $can_contain_types, $max_slots=-1) {
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
            
            $query = "INSERT INTO container_type (name, can_contain_types, max_slots) VALUES(:type_name, :can_contain_types, :max_slots)";

            $params = array('type_name'=>$type_name, 'can_contain_types'=>$can_contain_types, 'max_slots'=>$max_slots);
            $result = $this->db->get_insert_result($query, $params);

            $this->id = $result;
            return array('RESULT'=>TRUE,
			'MESSAGE'=>"Type $type_name successfully created.",
			'type_id'=>$this->id);

        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return array('RESULT'=>FALSE,
                    "MESSAGE"=>"Error in adding type $type_name");
        }
        
    }
    
    /**
     * Edits the data for this Type
     * 
     * @param string $type_name The new name for this Type
     * @param string $can_contain_types A comma-delimited string of ints that are
     *               ids of Types that this Type can contain
     * @param int $max_slots The maximum number of objects this Type can contain,
     *              or -1 if there is no limit.
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function edit($type_name, $can_contain_types, $max_slots) {
           $message = "";
           $error = false;
           $id = $this->get_id();
            if(type::type_name_exists($this->db, $type_name, $id)) {
                
            $message .= "A type with the name $type_name already exists. Please choose a different name.";
            return array('RESULT'=>false,
			'MESSAGE'=>$message);
            
            }
        $query = "UPDATE container_type set name=:name, ".
                " can_contain_types=:can_contain_types, ".
                " max_slots=:max_slots ".
                " where container_type_id=:id";
        $params = array("id"=>$id, "name"=>$type_name, "can_contain_types"=>$can_contain_types, "max_slots"=>$max_slots);
        $result = $this->db->get_query_result($query, $params);
        $this->load_by_id($this->db, $id);
        return array('RESULT'=>true,
			'MESSAGE'=>"Type ".$type_name." successfully updated.",
			'type_id'=>$this->get_id());
        
    }
   
    

    
    /**
     *  Gets a list of potential container types that this container type can be placed in
     * 
     * @return type[] An array of Type objects that this Type can be placed into.
    */
    public function get_container_types_for_type() {
        $type_id = $this->id;
        $query = "SELECT container_type_id, can_contain_types from container_type";
        $containers = array();
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        foreach($result as $types) {
            $curr_list = $types['can_contain_types'];
            $id = $types['container_type_id'];
            $type_list = explode(",", $curr_list);
            // if this id is in the list of 'can_contain_types' for the given type
            if(in_array($type_id, $type_list)) {
                
                $new_type = new type($this->db, $id);
                $containers[] = $new_type;
           }
        }

        return $containers;
    }
    
    /**
     * Gets a list of names of the Types this Type can contain
     * 
     * @return string A comma-separated list of Type name that this Type
     * can be placed in
     */
    public function get_container_type_names_for_type() {
        $container_types = $this->get_container_types_for_type();
        $list = "";
        foreach($container_types as $container_type) {
            $container_type_id = $container_type->get_id();

            if(strlen($list) > 0) {
                $list .=", ";  
            }

            $list .= $container_type->get_name();

        }
        return $list;
    }
    
    /**
     * 
     * @return tape_library_object[] An Array of all the
     * Tape_library_objects that are of this type
     */
    public function get_containers_for_type() {
        
        $type_array = $this->get_container_types_for_type($this->id);
        $type_string = "";

        $containers = array();
        foreach($type_array as $type) {
            $type_id = $type->get_id();
            if($type_string != "") {
                $type_string .= ",";

            }
            $type_string .= $type_id;
        }
        if($type_string != "") {
        $query = "SELECT id, label as name from tape_library where type in (".$type_string.") order by label";
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        foreach($result as $curr_container) {
            $new_container = new tape_library_object($this->db, $curr_container['id']);
            $containers[] = $new_container;
        }
        }
        return $containers;

    }
    
       
    /** 
     * Adds a new Type to the list of Types this Type can contain
     * 
     * @param int $new_type_id ID of the new type 
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function add_container_type_to_type($new_type_id) {
       $container_type_id = $this->id;

       $current_types_string = "";
            $current_types = $this->can_contain_types;
            if(!in_array($new_type_id, $current_types)) {
                $current_types[] = $new_type_id;
                $this->can_contain_types = $current_types;
                $current_types_string = implode(",", $current_types);
            
                $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";
                $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$current_types_string);

                $update_result = $this->db->get_query_result($update_query, $update_params);
                $new_type = new type($new_type_id);
                $new_type_name = $new_type->get_name();
                
                return array('RESULT'=>true,
			'MESSAGE'=>"Type ".$new_type_name." successfully added to ".$this->get_name());
            }
    }
    
    /** 
     * Removes a Type from the list of Types this Type can contain
     * 
     * @param type $new_type_id ID of the type to remove
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message
     */
    public function remove_container_type_from_type($remove_type) {

        $container_type_id = $this->id;
        $query = "SELECT can_contain_types from container_type where container_type_id=:container_type_id";
        $params = array("container_type_id"=>$container_type_id);

        $result = $this->db->get_query_result($query, $params);

        if($result != null) {
            $result = $result[0];
            $new_types = $result['can_contain_types'];

            $new_types=explode(",",$new_types);
            $new_values = array();
            foreach($new_types as $type) {
                if($type != $remove_type) {
                    $new_values[] = $type;
                }
            }

            $new_types_string = implode(",", $new_values);

            $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";

            $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$new_types_string);

            $update_result = $this->db->get_query_result($update_query, $update_params);

            $removed_type = new type($remove_type);
            $removed_type_name = $removed_type->get_name();
            
            //return $update_result;
            if($update_result) {
                return array('RESULT'=>true,
			'MESSAGE'=>"Type ".$removed_type_name." successfully removed from ".$this->get_name());
            } else {
                return array('RESULT'=>false,
			'MESSAGE'=>"There was an error removing ".$removed_type_name." from ".$this->get_name() .
                                   ". Please check your data and connections and try again.");
            }
        }
    }
    
    /** Gets the types that can be put into a container type
     * (For example, a type of "TeraPack" can hold types "LTO4", "LTO5", "LTO6").
     * 
     * @return type[] An array of Type objects that this Type can hold
     */
    public function get_tape_types_for_container_type() {
        
        $can_contain_string = implode(",", $this->get_can_contain_types());
        $query = "SELECT container_type_id as id, name from container_type ".
                " where container_type_id in ($can_contain_string) and (can_contain_types is null or can_contain_types='')";
        $statement = $this->db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->db->query($query);
        $tapes = array();
        foreach($result as $curr_tape_type) {
            $tape_type = new type($this->db, $curr_tape_type['id']);
            $tapes[] = $tape_type;
        }

        return $tapes;
    }
   
    
    
    /** Static functions **/
    
    /** Gets an array of all Types in the database
     * 
     * @param type $db The Database object
     * @return \type An array of Type objects
     */
    public static function get_all_types($db) {
        $query = "SELECT container_type_id as id, name from container_type order by name";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $types = $db->query($query);
        
        $all_types = array();
        foreach($types as $type) {
            $new_type = new type($db, $type['id']);
            $all_types[] = $new_type;
        }
        return $all_types;
    }
    
     /** Gets an array of container Types in the database
      * If a type can contain something else, it is considered a container
      * (as opposed to a tape)
     * 
     * @param type $db The Database object
     * @return \type An array of Type objects that are containers
     */
    public static function get_container_types($db) {
        $query = "SELECT container_type_id as id, name from container_type ".
                " where can_contain_types is not null and can_contain_types != '' order by name";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $types_array = $db->query($query);
        $types = array();
        foreach($types_array as $type) {
            $type = new type($db, $type['id']);
            $types[] = $type;
        }
        return $types;
       
    }
    
     /** Gets an array of tape Types in the database
     * If a type cannot contain any other types, it is considered a tape
     * (as opposed to a container)
     * 
     * @param type $db The Database object
     * @return \type An array of Type objects that are containers
     */   
    public static function get_tape_types($db) {
        
        $query = "SELECT container_type_id as id, name from container_type ".
                " where can_contain_types is null or can_contain_types='' order by name";

        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $types = $db->query($query);

        $tape_types = array();
        foreach($types as $type) {
            $new_type = new type($db, $type['id']);
            $tape_types[] = $new_type;
        }
        return $tape_types;
    }

    
    /* Gets a list of all top-level locations, those that can't be placed in anything else
     *  If a type cannot be placed within another container, it is considered a top-level
     * location
     * 
     * @param type $db The Database object
     * @return type[] An array of type objects that are top-level locations
    */
   public static function get_location_types($db) {
       $location_types = array();
       $all_types = array();
       $container_types = array();
       $all_types_query = "SELECT container_type_id as id, name, can_contain_types from container_type";
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
    
    /**
     * Determines if a Type with a given name already exists.
     * If an ID is provided, it checks for Types not matching that ID.
     * 
     * @param db $db The Database Object
     * @param string $name The Type name to check for
     * @param int $id Optional ID. If provided, it checks for Types not matching that ID.
     * @return boolean True if a Type with the given name exists, else false
     */
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
    
    
    /**
     * Determines if there is a potential loop for 
     * container types that a Type can be placed into,
     * and Types which can be placed into a Type.
     * 
     * For example, if Type A can contain Type B, and Type B can contain
     * Type C, we want to make sure Type C cannot contain Type A.
     * 
     * @param type $db The Database Object
     * @param type $parents An array of parent Type IDs
     * @param type $children An array of child Type IDs
     * @return int
     */
    public static function find_loop($db, $parents, $children) {

        $result = 0;
        $ancestors = $parents;
        $descendants = $children;
        if($parents == null || $children == null) {
            // no loop
            return 0;
        }

        // Get a list of all ancestors. For each parent Type, find their
        // parent types, and so on.
        foreach($ancestors as $ancestor) {
            $ancestor_type = new type($db, $ancestor);
            $grandparent_types = $ancestor_type->get_container_types_for_type();
            $grandparents = array();
            foreach($grandparent_types as $grand) {
                $grandparents[] = $grand->get_id();
            }
            foreach($grandparents as $gp) {
            if(!in_array($gp, $ancestors)) {
                $gp_id = $gp;
                $ancestors[] = $gp_id;
            }
        }

        // Get a list of all possible descendants. For each child Type,
        // find their child types, and so on.
        foreach($descendants as $descendant) {
            $descendant_type = new type($db, $descendant);
            $grandchildren = $descendant_type->get_can_contain_types();
            if(count($grandchildren) > 0) {
            foreach($grandchildren as $gc) {

                if(!in_array($gc, $descendants)) {
                    $descendants[] = $gc;
                }
            }
            }
        }
        // check for loops. In an id is in both
        // ancestors and descendants, there's a loop
        foreach($descendants as $d) {
            if(in_array($d, $ancestors)) {
                return $d;
            }
        }

        return 0;

        }
    }
    
    /**  Private functions **/
    
    /** 
     * Gets Type data from the database and returns it in array form
     * 
     * @param db $db The Database object
     * @param int $id ID of the type to get data for
     * @return null
     */
    private function get_type($db, $id) {

        $query = "SELECT * from container_type where container_type_id = :id";
        $params = array("id"=>$id);
        $result = $db->get_query_result($query, $params);
        if(count($result)==1) {
            $result = $result[0];
            return $result;
        }
        return null;
    }
}