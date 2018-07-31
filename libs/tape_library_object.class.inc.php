<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class tape_library_object {
    
    protected $db; // database
    protected $id =-1;
    protected $label; // unique name
    protected $container=-1;
    protected $type;
    protected $time_created;
    protected $backupset;
    protected $active;
    protected $tape_label; // optional extra label for tapes
    
    protected $time_last_modified;
    protected $user_last_modified;
    
    /** Constructor
     * 
     * @param db $db database object 
     * @param int $id id number of the tape_library_object
     */
    public function __construct($db, $id) {
        
        $this->load_by_id($db, $id);
    }
    
    public function __destruct() {
       
    }
    
    /**
     * Loads data from the database to create a new Tape Library Object
     * 
     * @param db $db database object 
     * @param int $id id number of the tape_library_object
     */
    public function load_by_id($db, $id = 0) {

        $this->db = $db;
        if($id != 0) {
            $this->get_tape_library_object($id);
        
        }
    }
    
    public function get_label() {
        return $this->label;
    }
    
    public function get_tape_label() {
        return $this->tape_label;
    }
    
    public function get_container_id() {
        return $this->container;
    }
    
    public function get_type() {
        return $this->type;
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function is_active() {
        return $this->active;
    }

    
    /** 
     * Get the name of the container of this Tape Library Object
     * 
     * @return string the name of the container of this object, 
     * or "None" if there is none.
     */
    public function get_container_name() {
        
        $container = new tape_library_object($this->db, $this->container);
        if($container->get_id() != -1) {
            return $container->get_label();
        } else {
            return "None";
        }
    }
    
    /**
     * Gets the name of the Type of this Tape Library Object
     * @return string The name of the Type of this Tape Library Object or "None" 
     * if it cannot be found
     */
    public function get_type_name() {

        $type = new type($this->db, $this->type);
        return $type->get_name();
    }
    
    /** 
     * Gets the id number of the Backup Set for this Tape Library Object
     * 
     * @return integer the id number of the backup set 
     * for this Tape Library Object
     */
    public function get_backupset() {
        return $this->backupset;
    }
    
    /** 
     * Gets the name of the Backup Set that this Tape Library Object
     * is in, or "None" if it is in none.
     * 
     * @return string The name of 
     */
    public function get_backupset_name() {
        $backupset = new backupset($this->db, $this->backupset);
        if($backupset->get_id() == -1) {
            return "None";
        } else {
            return $backupset->get_name();
        }
    }
    
    /**
     * Returns the maximum number of slots this container can hold,
     * as designated by its type.
     * @return int the number of slots this container can hold
     *  (-1 if therre is no limit)
     */
    public function get_max_slots() {
        $type = new type($this->db, $this->get_type());
        return $type->get_max_slots();
    }
    
    /**
     * Determines if this Tape Library Object is a top-level location
     * (it is a top-level location if it cannot be placed in
     * any other container.)
     * 
     * @return bool 1 if it is a top-level location, else 0
     * 
     */
    public function is_location() {
        if($this->container == null || $this->container == -1) {
            return 1;
        } else {
            return 0;
        }
    }

    
    /**
     * 
     * Determines if theis Tape Library Object is a tape.
     * (it is a tape if it cannot contain any other objects.)
     * @return bool 1 if this object is a tape, else 0
     */
    public function is_tape() {
        if(count($this->can_contain_types()) == 0) {
            return 1;
        } else {
            return 0;
        }
    }
        
    /**
     * Returns a list of type ids that this Tape Library Object can contain, as
     * designated by its Type.
     * @return array An array of type ids that this Tape Library Object can contain.
     */
    public function can_contain_types() {
        $this_type = new type($this->db, $this->type);
        return $this_type->get_can_contain_types();
    }
    
    /**
     * Determines if this Tape Library Object can contain tapes
     * (as opposed to only containers), as determined by its type.
     * @return bool 1 if this object can contain tapes, else 0
     */
    public function can_contain_tapes() {
        $this_type = new type($this->db, $this->type);
        return $this_type->can_contain_tapes();
    }
    
    
    


    /** 
     * 
     * Edits this Tape Library Object, and updates the database
     * 
     * @param string $label Unique ID for this Object
     * @param int $container Container ID for where this object is to be placed
     * @param bool $active Is this Object active?
     * @param string $tape_label Additional optional label for tapes
     * @param string $username Name of the user doing the updating
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message.
     * 
     */
    public function edit($label, $container, $active, $tape_label=null, $username=null) {
        $id = $this->id;
        if($container == "") {
            $container = null;
        }
        if($container == $id) {

            return array("RESULT"=>FALSE,
                        "MESSAGE"=>"Cannot move tape or container to itself.");
        }
        $current_tape = new tape_library_object($this->db, $id);
        if($label == null) {
            $abel = $current_tape->get_label();
        }
        $current_type = $current_tape->get_type();
        $new_location = new tape_library_object($this->db,$container);
        $new_location_type = $new_location->get_type();

        if($current_type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);

            return array("RESULT"=>FALSE,
                        "MESSAGE"=>"Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored");
        }

        $curr_container = $current_tape->get_container_id();
        if($curr_container != $container) {

            $new_container = new tape_library_object($this->db, $container);

            if(($new_container->get_max_slots() != -1) && ($new_container->get_object_count() >= $new_container->get_max_slots())) {                
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>"The container '".$new_container->get_label()."' is full. No changes have been made to this object.");
            }
        
        }
        $tape_exists = tape_library_object::does_tape_exist($this->db, $label, $current_tape->get_type());
        
        if( $tape_exists > 0 && $tape_exists != $this->get_id()) {

            $type = new type($this->db, $this->get_type());
            $type_name = $type->get_name();

            return array("RESULT"=>FALSE,
                            "MESSAGE"=>"A tape or container with the name '$label' of type $type_name already exists. Please choose a different name.");
        }
        
        try {

        $query = "UPDATE tape_library set label=:label, container=:container, last_update_username=:username, active=:active, tape_label=:tape_label, last_update=NOW() where id=:id";
        $params = array('label'=>$label,  'container'=>$container,  'username'=>$username, 'id'=>$id, 'active'=>$active, 'tape_label'=>$tape_label);

        $result = $this->db->get_query_result($query, $params);

        $this->label = $label;
        $this->tape_label = $tape_label;
        $this->container = $container;
        $this->active = $active;
        
        return array("RESULT"=>TRUE,
                    "MESSAGE"=>"".$label. " successfully edited.");
        

        } catch(Exception $e) {
            echo $e;
        }
    }
    
    
    /** 
     * 
     * Gets all the child objects (tapes and containers) of this 
     * Tape Library Object.
     * 
     * @return \tape_library_object an array of all the objects contained
     * within this Tape Library Object, sorted by label.
     */
    public function get_children() {
        
        $container_id = $this->id;
        $query = "SELECT id from tape_library where container=:container_id order by label";
        $params = array("container_id"=>$container_id);
        $children = $this->db->get_query_result($query, $params);
        $result = array();
        foreach($children as $child) {
            $new_child = new tape_library_object($this->db, $child['id']);
            $result[] = $new_child;
        }

        return $result;
    }


    /**
     * 
     * @return array An array of Tape Library Objects tracing a full
     * path for this object, with the top-level object first.
     */
    public function get_full_path_array() {
        $path = array();
        $container_id = $this->container;
        $container = new tape_library_object($this->db, $container_id);
        $path[] = $container;

        $new_container_id = $container->get_container_id();

        while(($new_container_id != -1 && $new_container_id != null && $new_container_id != "")) {
            $container = new tape_library_object($this->db, $new_container_id);
            $path[] = $container;
            $new_container_id = $container->get_container_id();

        }
        return $path;
    }
    
    /**
     * Gets a full path for this object
     * 
     * @param string a text string representing the path for this 
     * Tape Library Object
     */
    public function get_full_path() {
        $text = "";
        $path = $this->get_full_path_array();
        foreach($path as $obj) {
            if($text != "") {
                $text .= " / ";
            }
            $text .= $obj->get_label();
            
        }
        return $text;
    }

    
    /**
     *  Moves an object with the given id to this container, if possible
     * 
     * @param $object_id The ID of the object to move to this
     * Tape Library Object
     * 
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else true,
     * and "MESSAGE" is an output message.
     */
    public function move_object($object_id) {

        $object = new tape_library_object($this->db, $object_id);
        if($object->get_id() == -1) {
            $result = array("RESULT"=>FALSE,
                            "MESSAGE"=>"Please select a proper object to move.");
            return $result;
        }

        if($this->get_id() == -1) {
            $result = array("RESULT"=>FALSE,
                            "MESSAGE"=>"Please select a proper container.");
            return $result;
        }
        $container_type = new type($this->db, $this->get_type());
        $object_type  = new type($this->db, $object->get_type());


        if(!in_array($object_type->get_id(), $container_type->get_can_contain_types())) {
            $result = array("RESULT"=>FALSE,
                            "MESSAGE"=>"An object of type ".$object_type->get_name(). " cannot be placed into a ".$container_type->get_name().". ".$object->get_label(). " has not been moved.");
            return $result;
        }

        if(($container_type->get_max_slots()) != -1 && $this->get_object_count() >= $this->get_max_slots()) {
            $result = array("RESULT"=>FALSE,
                            "MESSAGE"=>"The container ".$this->get_label() . " is full. ".$object->get_label() . " has not been moved.");
            return $result;
        }

        $query = "UPDATE tape_library set container = :container_id where id=:object_id";
        $params = array("container_id"=>$this->id, "object_id"=>$object_id);
        $result = $this->db->get_query_result($query, $params);
        $result = array("RESULT"=>TRUE,
                        "MESSAGE"=>"".$object->get_label(). " successfully moved to ". $this->get_label());
        return $result;   
    }

    /** Gets the number of objects currently in this container
     * 
     * @return int the number of objects in this container
     */
    public function get_object_count() {
        return count($this->get_children());
    }

    /**
     * 
     * @param bool $active
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else TRUE,
     * and "MESSAGE" is an output message.
     */
    public function set_active($active) {
        $id = $this->get_id();
        $query = "UPDATE tape_library set active=:active where id=:id";
        $params = array("active"=>$active, "id"=>$id);
        $this->db->get_update_result($query, $params);
        $result = array("RESULT"=>TRUE,
                        "MESSAGE"=>"".$this->get_label(). " successfully ".($active ? " activated " : " deactivated "));
        $this->active = $active;
        return $result;  
    }
    
    
    /** Static functions */
    
    /**
     * Gets a list of all top-level locations. A Tape Library object is considered
     * a location if it cannot be placed into another container.
     * @param db $db The Database object
     * @return type
     */
    public static function get_locations($db) {
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        $query = "select id, label as name from tape_library where container is null or container=-1";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $locations = $db->query($query);

        $result = array();
        foreach($locations as $location) {
            $new_loc = new tape_library_object($db, $location['id']);
            $result[] = $new_loc;
        }
        return $result;
    }
    
    /** Determines if a tape of with a label and optional type exists
     * 
     * @param db $db Database object
     * @param string $label Name of the tape to check
     * @param int $type Optional type of the tape to check
     * @return int ID of the tape if it exists, or 0 if it does not.
     */
    public static function does_tape_exist($db, $label, $type=null) {
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        
        // temporarily allow same name with different types
        if($type != null) {
            $search_query .= (" and type=:type");
            $search_params = array("label"=>$label, 'type'=>$type);
        }
        
        $search_result = $db->get_query_result($search_query, $search_params);
        
        if(count($search_result) > 0) {
            return $search_result[0]['id'];
        }
        return 0;
    }
    
    /**
     * 
     * @param db $db
     * @param string $label
     * @param int $type
     * @param int $container_id
     * @param int $backupset
     * @param string $username
     * @param string $tape_label
     * @return array An array of the format: 
     *  ("RESULT"=>TRUE | FALSE,
     *   "MESSAGE"=>[string])
     * Where "RESULT" is FALSE if there was an error, else TRUE,
     * and "MESSAGE" is an output message.
     */
    public static function add_tape($db, $label, $type, $container_id, $backupset, $username, $tape_label=null) {
        
        if(tape_library_object::does_tape_exist($db,$label,$type)) {

            $new_type = new type($db, $type);
            $type_name = $new_type->get_name();    
            $message = "A tape or container with the name '$label' of type $type_name already exists. Please choose a different name.";
            return array("RESULT"=>FALSE,
                            "MESSAGE"=>$message);
        }

        if($container_id != "" && $container_id != -1) {
            $container = new tape_library_object($db, $container_id);
            
            
            $max_slots = $container->get_max_slots();
            $curr_count = $container->get_object_count();
            if($max_slots != -1 && ($curr_count >= $max_slots)) {

                $message = "The parent location '".$container->get_label()."' is full, and cannot contain any other objects.";
                return array("RESULT"=>FALSE,
                            "MESSAGE"=>$message);
            }
        }
        if($backupset == null) {
            $backupset = -1;
        }

        $query = "INSERT INTO tape_library ( label, type, container, backupset, last_update_username, tape_label, last_update, active) VALUES(:label, :type, :container_id, :backupset, :username, :tape_label, NOW(),1)";
        $params = array('label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'backupset'=>$backupset, 'username'=>$username, 'tape_label'=>$tape_label);

        try {

            $result = $db->get_insert_result($query, $params);
            return array("RESULT"=>TRUE,
                        "MESSAGE"=>"$label added successfully.",
                        "id"=>$result);

        
        } catch(Exception $e) {
            echo $e;
        }

        
    }
    
    /**
     * Returns an array of tapes that are not currently in a backup set.
     * 
     * @param db $db Database Object
     * @return tape_library_object[] Array of tapes not currently in a 
     * Backup Set
     */
    public static function get_tapes_without_backupset($db) {
        $query = "select tape_library.id as id, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library as containers where containers.id = tape_library.container) as container_name from tape_library   left join container_type on (tape_library.type = container_type.container_type_id ) where (tape_library.backupset is null or tape_library.backupset = '-1') and (container_type.can_contain_types is null or container_type.can_contain_types='')";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        
        $tape_array = array();
        foreach($result as $tape) {
            $new_tape = new tape_library_object($db, $tape['id']);
            $tape_array[] = $new_tape;
        }
        
        return $tape_array;
        
    }
    
    /** 
     * 
     * Returns a list of tapes based on certain criteria. 
     * 
     * @param db $db Database Object
     * @param string $begin beginning of a range to search for. If 'end' is null,
     *     it will search only on this entry
     * @param string $end end of the range to search for. If null, it will
     *     only search on 'begin'.
     * @param int $type ID of the type to return
     * @param int $parent The id of the parent container
     * @param bool $active If true, return only active tapes, else inactive tapes
     * @param int $tapes If 1, return tapes, else return containers
     * @return tape_library_object An array of Tape Library Objects
     * that fit the given criteria.
     */
    public static function get_tapes($db, $begin=null, $end=null, $type=null, $parent=null, $active=1, $tapes=1) {
        $tape_array = array();
        if($tapes) {
            $query = "select tape_library.id as id, tape_library.label as label, tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.can_contain_types is null or container_type.can_contain_types='')";
        } else {
            $query = "select tape_library.id as id, tape_library.label as label, tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.can_contain_types is not null && container_type.can_contain_types != '')";

        }
        

        $subquery = "";
        $params = array();
        if($begin != null && $end == null) {
            $subquery .= " tape_library.label like :begin ";
            $params['begin'] = "%".$begin."%";
        }
        if($begin != null && $end != null) {
            $subquery .= " tape_library.label between :begin and :end ";
            $params['begin'] = $begin;
            $params['end'] = $end;
        }
        if($type != null) {
            if($subquery != "") {
                $subquery .= " AND ";
            }
            $subquery .= " tape_library.type = :type ";
            $params['type'] = $type;
        }
        if($parent != null) {
            if($subquery != "") {
                $subquery .= " AND ";
            }
            $subquery .= " tape_library.container = :parent ";
            $params['parent'] = $parent;
        }
        
        if($subquery != "") {
            $query .= " AND ($subquery) ";
        }
        $query .= " order by tape_library.label ASC ";

        $tapes = $db->get_query_result($query, $params);

        foreach($tapes as $tape) {
            $new_tape = new tape_library_object($db, $tape['id']);
            $tape_array[] = $new_tape;
        }
        return $tape_array;
    }
    
    /** 
     * 
     * Returns a list of containers based on certain criteria. 
     * 
     * @param db $db Database Object
     * @param string $name the name or part of a name to search for
     * @param int $type ID of the type to return
     * @param int $parent the id of the parent container
     * @param bool $active if true, return only active tapes, else inactive tapes
     * @param int $tapes if 1, return tapes, else return containers
     * @return tape_library_object an array of Tape Library Objects
     * that fit the given criteria.
     */
    public static function get_containers($db, $name=null, $type=null, $parent=null, $active=1) {
        return tape_library_object::get_tapes($db, $name, null, $type, $parent, $active, 0);

    }

    

    
    /* Private functions */
    
    /** 
     * Gets data for a Tape Library Object and loads it into this object.
     * 
     * @param int $id The ID number of the Tape Library Object to load
     */
        private function get_tape_library_object($id) {

            $query = "SELECT * from tape_library where id = :id";
            $params = array("id"=>$id);

            $result = $this->db->get_query_result($query, $params);

            if($result) {
                $this->id = $result[0]['id'];
                $this->label = $result[0]['label'];
                $this->type = $result[0]['type'];
                $this->container = $result[0]['container'];
                $this->active = $result[0]['active'];
                $this->backupset = $result[0]['backupset'];
                $this->tape_label = $result[0]['tape_label'];
            } else {
                return false;
            }
        }

}