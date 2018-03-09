
<?php
//////////////////////////////////////////
//
//	db.class.inc.php
//
//	Class to create easy to use
//	interface with the database
//
//	By David Slater
//	June 2009
//
//////////////////////////////////////////
class db {
	////////////////Private Variables//////////
	private $link; //mysql database link
	private $host;	//hostname for the database
	private $database; //database name
	private $username; //username to connect to the database
	private $password; //password of the username
	////////////////Public Functions///////////
	public function __construct($host,$database,$username,$password) {
		$this->open($host,$database,$username,$password);
	}
	public function __destruct() {
		$this->close();
	}
	//open()
	//$host - hostname
	//$port - mysql port
	//$database - name of the database
	//$username - username to connect to the database with
	//$password - password of the username
	//$port - mysql port, defaults to 3306
	//opens a connect to the database
	public function open($host,$database,$username,$password,$port = 3306) {
		//Connects to database.
		try {
			$this->link = new PDO("mysql:host=$host;dbname=$database",$username,$password,
					array(PDO::ATTR_PERSISTENT => true));
                        
			$this->host = $host;
			$this->database = $database;
			$this->username = $username;
			$this->password = $password;
                        
		}
		catch(PDOException $e) {
                    echo "couldn't create db<BR>";
			echo $e->getMessage();
		}
	}
	//close()
	//closes database connection
	public function close() {
		$this->link = null;
	}

	//non_select_query()
	//$sql - sql string to run on the database
	//For update and delete queries
	//returns true on success, false otherwise
	public function non_select_query($sql) {
		$result = $this->link->exec($sql);
		return $result;
	}
	//query()
	//$sql - sql string to run on the database
	//Used for SELECT queries
	//returns an associative array of the select query results.
	public function query($sql) {
            //echo("query = $sql<BR>");
		$result = $this->link->query($sql);
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	//getLink
	//returns the mysql resource link
	public function get_link() {
		return $this->link;
	}

        function pdo_query($query, $params) {
            $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $statement->execute($params);
            //echo("user id= $user_id, copier_id = $copier_id, num_copies=$num_copies, date = $date <BR>");
            $result = $statement->fetchAll();
            return $result;
    }

    /*
    function add_tape_type($tape_type_name) {
        // TODO : Check container type existance first
        
        //return $this->add_type($tape_type_name, 0);
        try {
        $query = "INSERT INTO tape_type (name) VALUES(:type_name)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('type_name'=>$tape_type_name));
        
        $result = $statement->fetchAll();
        return $result;
        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return 0;
        }
        
    }*/
    
    function add_type($type_name, $can_contain_types, $max_slots=-1) {
        // TODO : Check container type existance first
        try {
            $find_query = "SELECT * from container_type where name = :name";
            $params = array("name"=>$type_name);
            $result = $this->get_query_result($find_query, $params);
            if(count($result) > 0) {
                return "There is already a type with the name $type_name. Please chooose a different name.";
            }
        $query = "INSERT INTO container_type (name, can_contain_types, max_slots) VALUES(:type_name, :can_contain_types, :max_slots)";
        $params = array('type_name'=>$type_name, 'can_contain_types'=>$can_contain_types, 'max_slots'=>$max_slots);
        $result = $this->get_insert_result($query, $params);
        return $result;

        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return "Error in adding type $type_name";
        }
        
    }
    
    function edit_type($id, $type_name, $can_contain_types, $max_slots) {
        $find_query = "SELECT * from container_type where name = :name and container_type_id != :id ";
        //echo("Find query = $find_query");
        
        $find_params = array("id"=>$id, "name"=>$type_name);
        //print_r($find_params);
        $result = $this->get_query_result($find_query, $find_params);
        if(count($result) > 0) {
            echo("<div class='alert alert-danger'>A type with the name $type_name already exists. Please choose a different name.</div>");
            return 0;
        }
        $query = "UPDATE container_type set name=:name, can_contain_types=:can_contain_types, max_slots=:max_slots where container_type_id=:id";
        $params = array("id"=>$id, "name"=>$type_name, "can_contain_types"=>$can_contain_types, "max_slots"=>$max_slots);
        $result = $this->get_query_result($query, $params);
        return $result;
        
    }
    
    function does_tape_exist($label) {
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = $this->get_query_result($search_query, $search_params);
 //echo("count = ".count($search_result));
        if(count($search_result) > 0) {
            return 1;
        }
        return 0;
    }
    
    function add_tape($label, $type, $container_id, $backupset, $user_id) {
        // TODO: user_id?
        
        if($this->does_tape_exist($label)) {
            //echo("<div class='alert alert-danger'>A tape or container with the name '$label' already exists. Please choose a different name.</div>");
            //return 0;
            $message = "A tape or container with the name '$label' already exists. Please choose a different name.";
            return $message;
        }
        //echo("container_id = $container_id");
        if($container_id != -1) {
            $container = new container($this, $container_id);
            $max_slots = $container->get_max_slots();
            $curr_count = $container->get_object_count();
            //echo("max_slots = $max_slots; curr_count = $curr_count<BR>");
            if($max_slots != -1 && ($curr_count >= $max_slots)) {
                //echo("<div class='alert alert-danger'>The parent location is full, and cannot contain any other objects.</div>");
                //return 0;
                $message = "The parent location '".$container->get_label()."' is full, and cannot contain any other objects.";
                return $message;
            }
        }
        if($backupset == null) {
            $backupset = -1;
        }

        
        //$new_location = new container($this, $container_id);
        //$new_location_type = $new_location->get_type();
        //echo("current_type= $type, new_loc_type = $new_location_type<BR>");
        
        //if($type == $new_location_type) {
        //    $location_type_name = $this->get_container_type_name($new_location_type);
        //    echo("Cannot add a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored<BR>");
        //    return 0;
        //}
        
        $query = "INSERT INTO tape_library ( label, type, container, backupset, user_id, last_update, active) VALUES(:label, :type, :container_id, :backupset, :user_id, NOW(),1)";
        //$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'backupset'=>$backupset, 'user_id'=>0));
        $params = array('label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'backupset'=>$backupset, 'user_id'=>0);
        //echo("item_id = $item_id, type = $type, container_id = $container_id, backupset=$backupset, user_id=$user_id");
        //echo("query = $query<BR>");
        try {
        //$result = $statement->fetchAll();
            $result = $this->get_insert_result($query, $params);
            //echo($result);
        
        } catch(Exception $e) {
            echo $e;
        }
        return $result;
        
    }    

    function add_container_basic($label, $type, $container_id, $user_id) {
            // No service
        // TODO: user_id?
        if($container_id==null) {
            $container_id=-1;
        }
        if(!isset($label) || ($label == "")) {
            echo("<div class='alert alert-danger'>Please input a valid name.</div>");
            return 0;
        }
        $search_query = "SELECT * from tape_library where label=:label and tape_library.type in (SELECT container_type_id from container_type where container_type.container=1)";
        $search_params = array("label"=>$label);
        echo("label = $label");
        $search_result = $this->get_query_result($search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A container with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        //echo("1");
        $new_location = new container($this, $container_id);
        $new_location_type = $new_location->get_type();
        echo("current_type= $type, new_loc_type = $new_location_type<BR>");
        //echo("2");
        if($type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("<div class='alert alert-danger'>Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored</div>");
            return 0;
        }
        
        $result = 0;
        try {
        $query = "INSERT INTO tape_library (label, type, container, user_id, last_update, active) VALUES(:label, :type, :container_id, :user_id, NOW(),1)";
        $params = array('label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'user_id'=>0);
        //echo("query = $query<BR>");
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        $result = $this->get_insert_result($query, $params);
        ////$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0));
        echo("result = $result");
        return $result;
        
        //$result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
            return 0;
        }
        
        
    }
    
    /* For adding top-level locations like IGB, Rantoul, etc.
     * 
     */
    function add_location($label, $type) {
        
        if(!isset($label) || ($label == "")) {
            echo("<div class='alert alert-danger'>Please input a valid name.</div>");
            return 0;
        }
        $search_query = "SELECT * from tape_library where label=:label and tape_library.type in (SELECT container_type_id from container_type where container_type.container=2)";
        $search_params = array("label"=>$label);
        //echo("label = $label");
        $search_result = $this->get_query_result($search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>An item with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        $result = 0;
        try {
        $query = "INSERT INTO tape_library (label, type, container, last_update, active) VALUES(:label, :type, -1, NOW(),1)";
        $params = array('label'=>$label, 'type'=>$type);
        //echo("query = $query<BR>");
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        $result = $this->get_insert_result($query, $params);
        ////$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0));
        return $result;
        
        //$result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
            return 0;
        }
    }
    
    function add_program($name) {
        try {
        $find_query = "SELECT * from programs where name=:name";
        $params = array("name"=>$name);
        $result = $this->get_query_result($find_query, $params);
        if(count($result) > 0) {
            // already exists
            echo("<div class='alert alert-danger'>A program with the name $name already exists. Please choose a different name or version.</div>");
            return 0;
        }
        $query = "INSERT INTO programs (name) VALUES (:name)";
        
        $result = $this->get_query_result($query, $params);
        return $result;
        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return 0;
        }
        
    }
    
    function get_type($id) {
        //echo("type id = $id");
        $query = "SELECT * from container_type where container_type_id = :id";
        $params = array("id"=>$id);
        $result = $this->get_query_result($query, $params);
        if(count($result)==1) {
            $result = $result[0];
            //print_r($result);
            return $result;
        }
        return null;
    }
    
    function get_programs() {
        $query = "SELECT id, name from programs";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    /*
    function add_item($item_id, $label, $type, $container_id, $service ,$user_id) {
        
        $query = "INSERT INTO tape_library (item_id, label, type, container, service, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :service, :user_id, NOW(),1)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0));
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        try {
        $result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
        }
        return $result;
        
    }
    */
    function get_container_types_array() {
        $query = "SELECT container_type_id as id, name from container_type  where can_contain_types is not null and can_contain_types != '' order by name";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_container_types() {
        $types_array = $this->get_container_types_array();
        $types = array();
        foreach($types_array as $type) {
            $type = new type($this, $type['id']);
            $types[] = $type;
        }
        return $types;
    }
     
     
    /*
    function get_container_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type where container=1";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
     * */
    
    function get_all_types() {
        $query = "SELECT container_type_id as id, name, container from container_type order by name";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_all_type_objects() {
        $types = $this->get_all_types();
        $all_types = array();
        foreach($types as $type) {
            $new_type = new type($this, $type['id']);
            $all_types[] = $new_type;
        }
        return $all_types;
    }
     
    
    function get_tape_types() {
        
        //$query = "SELECT container_type_id as id, name, container from container_type where container=0";
        $query = "SELECT container_type_id as id, name, container from container_type where can_contain_types is null or can_contain_types='' order by name";

        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        //print_r($result);
        return $result;
    }
    
    function get_tape_type_objects() {
        $types = $this->get_tape_types();
        $tape_types = array();
        foreach($types as $type) {
            $new_type = new type($this, $type['id']);
            $tape_types[] = $new_type;
        }
        return $tape_types;
    }
    
    
    /* Gets the types that can be put into a container type
     * (For example, a type of "TeraPack" can hold types "LTO4", "LTO5", "LTO6").
     */
    function get_tape_types_for_container_type($container_type_id) {
        
            $type = new type($this, $container_type_id);
            $can_contain_string = implode(",", $type->get_can_contain_types());
            //$query = "SELECT container_type_id as id, name, container from container_type where container=0";
            $query = "SELECT container_type_id as id, name, container from container_type where container_type_id in ($can_contain_string) and (can_contain_types is null or can_contain_types='')";
            //echo($query);
            $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result = $this->query($query);
            //print_r($result);
            return $result;
    }
   
    
    function get_tape_library_object_data($id) {

        $query = "SELECT * from tape_library where id = :id";
        $params = array("id"=>$id);
        $result = $this->get_query_result($query, $params);
        if(count($result)==1) {
            $result = $result[0];
        } else {
            $result = 0;
        }
        return $result;
    }
    
    function get_tape_by_id($id) {
        $query = "SELECT * from tape_library where id = :id";
        $params = array("id"=>$id);
        $result = $this->get_query_result($query, $params);
        if(count($result)==1) {
            $result = $result[0];
        } else {
            $result = 0;
        }
        return $result;
    }

    function get_tapes_without_backupset() {
        $query = "select tape_library.id as id, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library as containers where containers.id = tape_library.container) as container_name from tape_library  where (tape_library.backupset is null or tape_library.backupset = '-1') and tape_library.type in (SELECT container_type_id from container_type where container=0)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        
        $tape_array = array();
        foreach($result as $tape) {
            $new_tape = new tape($this, $tape['id']);
            $tape_array[] = $new_tape;
        }
        
        return $tape_array;
        
    }
    
    function remove_tape_from_backupset($tape_id, $backupset_id) {
        try {
            $find_query = "SELECT * from tape_library where id=:tape_id and backupset = :backupset_id";
            $params = array("tape_id"=>$tape_id, "backupset_id"=>$backupset_id);
            //print_r($params);
            $find_result = $this->get_query_result($find_query, $params);
           // print_r($find_result);
            if(count($find_result) == 0) {
                echo("<div class='alert alert-danger'>Tape not found</div>");
                return 0;
            }
            //echo("Removing tape $tape_id from backupset $backupset_id<BR>");
            $query = "UPDATE tape_library set backupset='-1' where id=:tape_id and backupset = :backupset_id";
            $result = $this->get_query_result($query, $params);

            return $result;
        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return 0;
        }
        
    }
    
    
    function get_tapes_array($begin=null, $end=null, $type=null, $parent=null, $active=1, $tapes=1) {
        //$query = "select tape_library.id as id, tape_library.label as label, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.container=0)";
        if($tapes) {
            $query = "select tape_library.id as id, tape_library.label as label, tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.can_contain_types is null or container_type.can_contain_types='')";
        } else {
            $query = "select tape_library.id as id, tape_library.label as label, tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.can_contain_types is not null && container_type.can_contain_types != '')";

        }
        //echo("begin = $begin<BR>");
        //echo("end = $end<BR>");
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
            //echo("parent = $parent<BR>");
            $params['parent'] = $parent;
        }
        
        //$query .= "left join containers as container on (tape.container = container.id)  join  container_type on  (container_type.container=$container and container_type_id=tape_library.type)";
        if($subquery != "") {
            $query .= " AND ($subquery) ";
        }
        $query .= " order by tape_library.label ASC ";
        //echo("type = $type<BR>");
        //echo("query = $query<BR>");
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute($params);

        $result = $statement->fetchAll();
        //$result = $this->get_query_result($query, $params);
        //print_r($result);
        return $result;
    }
    
    function get_tapes($begin=null, $end=null, $type=null, $parent=null, $active=1) {
        $tape_array = array();
        $tapes = $this->get_tapes_array($begin, $end, $type, $parent, $active);
        foreach($tapes as $tape) {
            $new_tape = new tape($this, $tape['id']);
            //echo("new tape id = ".$new_tape->get_id());
            $tape_array[] = $new_tape;
        }
        return $tape_array;
    }
    
    function get_containers_array($name=null, $type=null, $parent=null, $active=1) {
        return $this->get_tapes_array($name, null, $type, $parent, $active, 0);
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        //$query = "select tape_library.id as id, tape_library.label as name, tape_library.label as label, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.container=1)";
        //$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$result = $this->query($query);
        //return $result;
    }
    
    function get_containers($name=null, $type=null, $parent=null, $active=1) {
        //echo("name = $name");
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        //$query = "SELECT id from containers where container != -1";
        //$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$result = $this->query($query);
        $result = $this->get_containers_array($name, $type, $parent, $active);
        $containers = array();
        foreach($result as $container_id) {
            $id = $container_id['id'];
            
            $container = new container($this, $id);
            $containers[] = $container;
        }
        return $containers;
    }
     
   function get_locations() {
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        $query = "select id, label as name from tape_library where container is null or container=-1";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_location_objects() {
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        $locations = $this->get_locations();
        $result = array();
        foreach($locations as $location) {
            $new_loc = new container($this, $location['id']);
            $result[] = $new_loc;
        }
        return $result;
    }
    
    /*
     * These are things that can hold tapes
     */
    function get_containers_and_locations() {

        //$query = "select tape_library.id as id, tape_library.label as label,tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library where type in (SELECT container_type_id from container_type where container_type.container=1 or container_type.container=2)";
        $query = "select tape_library.id as id, tape_library.label as label,tape_library.label as name, tape_library.container as container, tape_library.type as type, tape_library.backupset as backupset, tape_library.active as active from tape_library";

//echo ("C&L query = $query<BR>");
        //$query = "select id, label as name from tape_library where container = -1";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;

    }
    /*
    function edit_tape($id, $tape_label, $container, $type, $active) {
        if($container == "") {
            //echo("container is blank, setting to null<BR>");
            $container = null;
            
        }
        
        if($container == $id) {
            echo("<div class='alert alert-danger'>Cannot move tape or container to itself.</div>");
            return 0;
        }
        
        $current_tape = $this->get_tape_by_id($id);
        $current_type = $current_tape['type'];
        $new_location = $this->get_container_by_id($container);
        $new_location_type = $new_location['type'];
        echo("current_type= $current_type, new_loc_type = $new_location_type<BR>");
        
        if($current_type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored<BR>");
            return 0;
        }
        
        $query = "UPDATE tapes set label=:label, container=:container, type=:type, service=:service, user_id=:user_id, active=:active, last_update=NOW() where id=:id";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('label'=>$tape_label, 'type'=>$type, 'container'=>$container, 'service'=>$service, 'user_id'=>0, 'id'=>$id, 'active'=>$active));
        //echo($statement->rowCount() . " rows updated.<BR>");
        try {
            //echo("query = $query<BR>");
        $result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
        }
    }
     * 
     */
    
    // Don't change type or service
    function edit_tape($id, $tape_label, $container, $active) {
        if($container == "") {
            //echo("container is blank, setting to null<BR>");
            $container = null;
        }
        if($container == $id) {
            //echo("<div class='alert alert-danger'>Cannot move tape or container to itself.</div>");
            //return 0;
            return "Cannot move tape or container to itself.";
        }
        
        $current_tape = new tape($this, $id);
        if($tape_label == null) {
            $tape_label = $current_tape->get_label();
        }
        $current_type = $current_tape->get_type();
        $new_location = new container($this,$container);
        $new_location_type = $new_location->get_type();
        //echo("current_type= $current_type, new_loc_type = $new_location_type<BR>");

        if($current_type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            //echo("<div class='alert alert-danger'>Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored</div>");
            //return 0;
            return "Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored";
        }

        $curr_container = $current_tape->get_container_id();
        if($curr_container != $container) {
            
            $new_container = new container($this, $container);
            
            if(($new_container->get_max_slots() != -1) && $new_container->get_object_count() >= $new_container->get_max_slots()) {
                //echo("<div class='alert alert-danger'>The container '".$new_container->get_label()."' is full. No changes have been made to this object.</div>");
                
                return "The container '".$new_container->get_label()."' is full. No changes have been made to this object.";
            }
        
        }
        
        $query = "UPDATE tape_library set label=:label, container=:container, user_id=:user_id, active=:active, last_update=NOW() where id=:id";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('label'=>$tape_label,  'container'=>$container,  'user_id'=>0, 'id'=>$id, 'active'=>$active));
        //print_r($statement);
        //echo($statement->rowCount() . " rows updated.<BR>");

        try {
            //echo("query = $query<BR>");
        $result = $statement->fetchAll();

        return $result;
        } catch(Exception $e) {
            echo $e;
        }
    }



/*
function is_admin($username) {
    
    $query = "SELECT admin from users where username=:username and active=1";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('username'=>$username));
    
    $result = $statement->fetchAll();
    //print_r($result);
    if($result[0]['admin'] == 1) {
        return true;
    }
    
    return false;
}
*/
function get_container_type_name($container_type_id) {
    //echo("container_type_id = $container_type_id<BR>");
    $query = "SELECT name from container_type where container_type_id=:container_type_id";
    //echo("query = $query<BR>");
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('container_type_id'=>$container_type_id));
    $result = $statement->fetchAll();
    if($result != null && $result[0]['name'] != null) {
        return $result[0]['name'];
    } else {
        return "None";
    }
}


function get_tape_type_name($tape_type_id) {
    //echo("query = SELECT name from tape_type where tape_type_id=$tape_type_id<BR>");
    $query = "SELECT name from container_type where container_type_id=:tape_type_id";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('tape_type_id'=>$tape_type_id));
    $result = $statement->fetchAll();
    if($result != null && $result[0]['name'] != null) {
        return $result[0]['name'];
    } else {
        return "None";
    }
}

function get_query_result($query_string, $query_array) {
    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute($query_array);
    $result = $statement->fetchAll();
    return $result;
    
}


function get_update_result($query_string, $query_params) {
    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $statement->execute($query_array);
    return $result;
}

function get_insert_result($query_string, $query_array) {
    //echo("insert query = $query_string<BR>");
    //print_r($query_array);
    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt = $statement->execute($query_array);
    //echo("stmt = $stmt<BR>");
    $result =  $this->get_link()->lastInsertId();
    //echo ("insert id = $result<BR>");
    return $result;
}

function add_backupset($name, $begin, $end, $program, $main_location, $notes) {
    $search_query = "SELECT * from backupset where name=:name";
    $search_params = array("name"=>$name);
    $search_result = $this->get_query_result($search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("<div class='alert alert-danger'>A backupset with the name '$name' already exists. Please choose a different name.</div>");
        return 0;
    }
    $query = "INSERT INTO backupset (name, begin, end, program, main_location, notes) VALUES (:name, :begin, :end, :program, :main_location, :notes)";

    $params = array('name'=>$name, 'begin'=>$begin, 'end'=>$end, 'program'=>$program, 'main_location'=>$main_location, 'notes'=>$notes);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function get_tapes_for_backupset_array($backupset_id) {
    $query = "SELECT * from tape_library where backupset=:backupset_id order by label";
    //$query = "select tapes.id as id, tapes.item_id as tape_number, tapes.label as label, tapes.container as parent, tapes.type as type, tapes.backupset as backupset, tapes.active as active, (SELECT label from tape_library where parent = id) as container_name";
        
    $params = array("backupset_id"=>$backupset_id);
    $result = $this->get_query_result($query, $params);
    //print_r($result);
    return $result;
}

function get_tapes_for_backupset($backupset_id) {
    //echo("1");
    $tape_array = array();
    $tapes = $this->get_tapes_for_backupset_array($backupset_id);
    foreach($tapes as $tape) {
        $new_tape = new tape_library_object($this, $tape['id']);
        if($new_tape->is_tape()) {
            $tape_array[] = $new_tape;
        }
    }
    return $tape_array;
}

function get_containers_for_backupset($backupset_id) {
    //echo("1");
    $tape_array = array();
    $tapes = $this->get_tapes_for_backupset_array($backupset_id);
    foreach($tapes as $tape) {
        $new_tape = new tape_library_object($this, $tape['id']);
        if(!$new_tape->is_tape()) {
            $tape_array[] = $new_tape;
        }
    }
    return $tape_array;
}


function get_tapes_in_container_array($container_id) {
    //$query = "SELECT * from tape_library where backupset=:backupset_id";
    $query = "select tape_library.id as id, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active where container=:container_id";
        
    $params = array("container_id"=>$container_id);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function get_tapes_in_container($container_id) {
    $tape_array = array();
    $tapes = $this->get_tapes_in_container_array($container_id);
    foreach($tapes as $tape) {
        $new_tape = new tape($this, $tape['id']);
        $tape_array[] = $new_tape;
    }
    return $tape_array;
}


function get_children($container_id) {
    $query = "SELECT id from tape_library where container=:container_id order by label";
    $params = array("container_id"=>$container_id);
    $result = $this->get_query_result($query, $params);
   
    return $result;
}

function get_children_objects($container_id) {
    $result = array();
    $children = $this->get_children($container_id);
    foreach($children as $child) {
        $new_child = new tape_library_object($this, $child['id']);
        $result[] = $new_child;
    }
   
    return $result;
}

function get_all_backups_array() {
        
    $query = "SELECT * from backupset";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($query);
    //print_r($results);
    return $result;
      
     
}
function get_all_backup_sets() {
    /*
    $query = "SELECT * from backupset";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($query);
    //print_r($results);
    return $result;
     * 
     */

    $query = "SELECT id from backupset order by name";
    $result = $this->query($query);
    $backupsets = array();

    foreach($result as $backupset_id) {
        $id = $backupset_id['id'];
        
        $backupset = new backupset($this, $id);
        
        $backupsets[] = $backupset;
    }
  
    return $backupsets;
    
}

function get_backupset_data($id) {
    $query = "SELECT * from backupset where id=:id";
    $params = array("id"=>$id);
    $result = $this->get_query_result($query, $params);
    
    if(count($result)==1) {
        $result = $result[0];
    } else {
        $result = 0;
    }
    return $result;
}

function edit_backupset($id, $name, $begin, $end, $program, $location, $notes) {
    $search_query = "SELECT * from backupset where name=:name and id != :id";
    $search_params = array("name"=>$name, "id"=>$id);
    $search_result = $this->get_query_result($search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("<div class='alert alert-danger'>A backupset with the name '$name' already exists. Please choose a different name.</div>");
        return 0;
    }
    $query = "UPDATE backupset set name=:name, begin=:begin, end=:end, program=:program, main_location=:main_location, notes=:notes where id=:id";
    $params = array("id"=>$id, "name"=>$name, "begin"=>$begin, "end"=>$end, "program"=>$program, "main_location"=>$location, "notes"=>$notes);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function deactivate_backupset($backupset_id) {
    $query = "UPDATE backupset set active=0 where $id=:id";
    $params = array("id"=>$backupset_id);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function set_backupset($tape_id, $backupset_id) {
    try {
    $query = "UPDATE tape_library set backupset=:backupset_id where id=:tape_id";
    $params = array("backupset_id"=>$backupset_id, "tape_id"=>$tape_id);
    $result = $this->get_query_result($query, $params);
    return $result;
    } catch(Exception $e) {
        echo($e->getTraceAsString());
        return 0;
    }
}

function get_all_from_type($type_id, $active=1) {
    $query = "SELECT * from tape_library where type=:type_id and active=:active";
    $params = array("type_id"=>$type_id, "active"=>$active);
}
/*
function get_all_backups() {
    echo("1");
    $query = "SELECT id from backupset";
    $result = $this->query($query);
    $backupsets = array();
    echo("2");
    foreach($result as $backupset_id) {
        $backupset = new backupset($this, $backupset_id);
        echo("backupset = ".$backupset->get_name());
        $backupsets[] = $backupset;
    }
    echo("3");
    return $backupsets;
}
*/
function get_tape_name($id) {
    $query = "SELECT label from tape_library where id=:id";
    $params = array("id"=>$id);
    $result = $this->get_query_result($query, $params);
    if(count($result) == 1) {
        //$result = $result['label'];
        $result = $result[0];
        //print_r($result);
        $result = $result['label'];

        return $result;
    }
    return 0;
}

function get_container_name($id) {
    $query = "SELECT label from tape_library where id=:id";
    $params = array("id"=>$id);
    $result = $this->get_query_result($query, $params);
    if(count($result) == 1) {
        //$result = $result['label'];
        $result = $result[0];
        //print_r($result);
        $result = $result['label'];

        return $result;
    }
    return 0;
}

function get_full_path($container_id) {
    $path = "";
    $container = new container($this, $container_id);
    $path = $container->get_label();
    
    $new_container_id = $container->get_container_id();

    $i=0;
    while(($new_container_id != -1 && $new_container_id != null && $new_container_id != "")) {
        $container = new container($this, $new_container_id);
        $path .= ", located in ".$container->get_label();
        $new_container_id = $container->get_container_id();
        $i++;
    }
    return $path;
}

function get_full_linked_path($container_id) {
    $path = "";
    $container = new container($this, $container_id);
    $path = $container->get_label();
    
    $new_container_id = $container->get_container_id();

    $i=0;
    while(($new_container_id != -1 && $new_container_id != null && $new_container_id != "")) {
        $container = new container($this, $new_container_id);
        $path .= ", located in ".$container->get_label();
        $new_container_id = $container->get_container_id();
        $i++;
    }
    return $path;
}

function get_program_name($program_id) {
    $query = "SELECT name from programs where id = :program_id";
    $params = array("program_id"=>$program_id);
    $result = $this->get_query_result($query, $params);
    if(count($result)== 1) {
        // just one;
        $program = $result[0];
        $name = $program['name'];
        return $name;
    }
    return "None";
}

/* Gets a list of potential containers for a type
*/
function get_container_types_for_type($type_id) {
    //print_r($type_id);
    $query = "SELECT container_type_id, can_contain_types from container_type";
    $containers = array();
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($query);
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

function get_container_type_names_for_type($type_id) {
    //print_r($type_id);
    $container_types = $this->get_container_types_for_type($type_id);
    $list = "";
    //print_r($containers);
    foreach($container_types as $container_type_id) {
        //echo($container_id);
        if(strlen($list) > 0) {
            $list .=", ";  
        }
        $type = new type($this, $container_type_id);
        $list .= $type->get_name();
        
    }
    return $list;
}

/* gets a list of all top-level locations, those that can't be placed in anything else
 * 
 */
function get_location_types() {
    $location_types = array();
    $all_types = array();
    $container_types = array();
    $all_types_query = "SELECT container_type_id as id, name, can_contain_types from container_type";
    //$statement = $this->get_link()->prepare($all_types_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($all_types_query);
    foreach($result as $type) {
        $type_id  = $type['id'];
        
        $these_types = $this->get_container_types_for_type($type_id);
        if(count($these_types) == 0) {
            $location_types[] = $type;
        }
    }

    return $location_types;
    
}

function get_containers_for_type($type_id) {
    $type_array = $this->get_container_types_for_type($type_id);
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
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($query);
    //print_r($result);
    }
    return $result;
}

function add_container_to_type($container_type_id, $new_type) {
    $query = "SELECT can_contain_types from container_type where container_type_id=:container_type_id";
    //echo($query);
    //echo("<BR>id = $container_type_id");
    $params = array("container_type_id"=>$container_type_id);
    $result = $this->get_query_result($query, $params);
    //print_r($result);
    if($result != null) {
        $result = $result[0];
        $new_types = $result['can_contain_types'];
        if($new_types != "") {
            $new_types .= ",";
        }
        $new_types .= $new_type;
        
        $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";
        //echo("update_query = $update_query<BR>");
        $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$new_types);
        //print_r($update_params);
        $update_result = $this->get_query_result($update_query, $update_params);
        
        return $update_result;
    }
}

function remove_container_from_type($container_type_id, $new_type) {
    //echo("id = $container_type_id, newtype=$new_type");
    $query = "SELECT can_contain_types from container_type where container_type_id=:container_type_id";
    $params = array("container_type_id"=>$container_type_id);
    
    $result = $this->get_query_result($query, $params);
    //print_r($result);
    if($result != null) {
        $result = $result[0];
        $new_types = $result['can_contain_types'];
        echo("Current types:<BR>");
        //print_r($new_types);
        echo("<BR>");
        $new_types=explode(",",$new_types);
        $new_values = array();
        foreach($new_types as $type) {
            if($type != $new_type) {
                $new_values[] = $type;
            }
        }
        //unset($new_types['$container_type_id']);
        $new_types_string = implode(",", $new_values);
        //echo("new types after delete: $new_types_string");
        
        
        $update_query = "UPDATE container_type set can_contain_types = :new_types where container_type_id=:container_type_id";
        //echo("update_query = $update_query<BR>");
        $update_params = array("container_type_id"=>$container_type_id, "new_types"=>$new_types_string);
        //print_r($update_params);
        $update_result = $this->get_query_result($update_query, $update_params);
        
        return $update_result;
    }
}




function get_child_types($type_id) {
    $query = "SELECT can_contain_types from container_type where container_type_id=:type_id";
    $params = array("type_id"=>$type_id);
    $result = $this->get_query_result($query, $params);
    //print_r($result);
    if($result == "" or $result == null) {
        return null;
    }
    $result = $result[0];
    $result = $result['can_contain_types'];
    $types = explode(",", $result);
    return $types;
}

function find_loop($parents, $children) {
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
        $grandparents = $this->get_container_types_for_type($ancestor);
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
        $grandchildren = $this->get_child_types($descendant);
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

function move_object($object_id, $container_id) {
    $object = new tape_library_object($this, $object_id);
    $container = new container($this, $container_id);

    if($object->get_id() == -1) {
        $result = "Please select a proper object to move.";
        return $result;
    }
    
    if($container->get_id() == -1) {
        $result = "Please select a proper container.";
        return $result;
    }
    $container_type = new type($this, $container->get_type());
    $object_type  = new type($this, $object->get_type());


    if(!in_array($object_type->get_id(), $container_type->get_can_contain_types())) {
        $result = "Error: An object of type ".$object_type->get_name(). " cannot be placed into a ".$container_type->get_name().". ".$object->get_label(). " has not been moved.";
        return $result;
    }

    if(($container->get_max_slots()) != -1 && $container->get_object_count() >= $container->get_max_slots()) {
        $result = "Error: The container ".$container->get_label() . " is full. ".$object->get_label() . " has not been moved.";
        return $result;
    }

    $query = "UPDATE tape_library set container = :container_id where id=:object_id";
    $params = array("container_id"=>$container_id, "object_id"=>$object_id);
    $result = $this->get_query_result($query, $params);

    return $result;   
}

function get_heirarchy($object_list, $level=0) {
    //$data = $db->get_tape_library_object($id);
    
    $data= array();
    $this_row = array();
            
        for($i=0; $i<$level; $i++) {
            $this_row[] = "";
        }
        $headers = array_merge($this_row, array("Name","Type","Backupset","Location"));
        $data[] = $headers;
    //print_r($object_list);
    foreach($object_list as $object) {
        //print_r($object);
        $id = $object->get_id();
        $tape_library_object = new tape_library_object($this, $id);
        //$headers = array();
        
        //test
        //echo("adding:"+$tape_library_object->get_label());
        //echo("<BR>");
        //$this_row = array();
        ////for($i=0; $i<$level; $i++) {
        //    $this_row[] = "";
        //}
        
        $data_row = array_merge($this_row, array($tape_library_object->get_label(), $tape_library_object->get_type_name(), $tape_library_object->get_backupset_name(), $tape_library_object->get_container_name()));

        $data[] = $data_row;

        $children = $this->get_children_objects($id);
        if(count($children) > 0) {
            $data[] = array();
            //$this_row = array();
            
                //for($i=0; $i<=$level; $i++) {
                //    $this_row[] = "";
                //}
               // $headers = array_merge($this_row, array("Name","Type","Backupset","Location"));
                //$data[] = $headers;
        
            $data = array_merge($data, $this->get_heirarchy($children, (1+$level)));
            $data[] = array();
            
        }
    
    }
    //print_r($data);
    return $data;
    }
}
?>
