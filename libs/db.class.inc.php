
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
	//insert_query()
	//$sql - sql string to run on the database
	//returns the id number of the new record, 0 if it fails
	public function insert_query($sql) {
		$result = $this->link->exec($sql);
		if ($result === false) {
			functions::log_message("INSERT ERROR: " . $sql);
		}
		return $this->link->lastInsertId();
	}
	//build_insert()
	//$table - string - database table to insert data into
	//$data - associative array with index being the column and value the data.
	//returns the id number of the new record, 0 if it fails
	public function build_insert($table,$data) {
		$sql = "INSERT INTO " . $table;
		$values_sql = "VALUES(";
		$columns_sql = "(";
		$count = 0;
		foreach ($data as $key=>$value) {
			if ($count == 0) {
				$columns_sql .= $key;
				$values_sql .= "'" . $value . "'";
			}
			else {
				$columns_sql .= "," . $key;
				$values_sql .= ",'" . $value . "'";
			}
			$count++;
		}
		$values_sql .= ")";
		$columns_sql .= ")";
		$sql = $sql . $columns_sql . " " . $values_sql;
		return $this->insert_query($sql);
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
	//ping
	//pings the mysql server to see if connection is alive
	//returns true if alive, false otherwise
	public function ping() {
		if ($this->link->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
			return true;
		}
		return false;
	}
	public function transaction($sql) {
		$this->link->beginTransaction();
		$result = $this->link->exec($sql);
		$this->link->commit();
		return $result;
	}
        
        function pdo_query($query, $params) {
            $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $statement->execute($params);
            //echo("user id= $user_id, copier_id = $copier_id, num_copies=$num_copies, date = $date <BR>");
            $result = $statement->fetchAll();
            return $result;
    }


    
    function add_container_type($container_type_name) {
        // TODO : Check container type existance first
        
        return $this->add_type($container_type_name, 1);
        
    }
    
    function add_location_type($container_type_name) {
        // TODO : Check container type existance first
        
        return $this->add_type($container_type_name, 2);
        
    }
    
    function add_tape_type($tape_type_name) {
        // TODO : Check container type existance first
        
        return $this->add_type($tape_type_name, 0);
        
    }
    
    function add_type($type_name, $container=0) {
        // TODO : Check container type existance first
        try {
        $query = "INSERT INTO container_type (name, container) VALUES(:type_name, :container)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('type_name'=>$type_name, 'container'=>$container));
        
        $result = $statement->fetchAll();
        return $result;
        } catch(Exception $e) {
            echo($e->getTraceAsString());
            return 0;
        }
        
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
    
    function add_tape($item_id, $label, $type, $container_id, $backupset, $user_id) {
        // TODO: user_id?
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = $this->get_query_result($search_query, $search_params);
        if($backupset == null) {
            $backupset = -1;
        }
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A tape with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        $new_location = $this->get_container_by_id($container_id);
        $new_location_type = $new_location['type'];
        //echo("current_type= $type, new_loc_type = $new_location_type<BR>");
        
        if($type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("Cannot add a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored<BR>");
            return 0;
        }
        
        $query = "INSERT INTO tape_library (item_id, label, type, container, backupset, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :backupset, :user_id, NOW(),1)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'backupset'=>$backupset, 'user_id'=>0));
        
        //echo("item_id = $item_id, type = $type, container_id = $container_id, backupset=$backupset, user_id=$user_id");
        //echo("query = $query<BR>");
        try {
        $result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
        }
        return $result;
        
    }    
    
    function add_container($item_id, $label, $type, $container_id, $service, $user_id) {
        // TODO: user_id?
        if($container_id==null) {
            $container_id=-1;
        }
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = $this->get_query_result($search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A container with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        $new_location = $this->get_container_by_id($container_id);
        $new_location_type = $new_location['type'];
        echo("current_type= $current_type, new_loc_type = $new_location_type<BR>");
        
        if($current_type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored<BR>");
            return 0;
        }
        
        $result = 0;
        
        try {
        $query = "INSERT INTO tape_library (item_id, label, type, container, service, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :service, :user_id, NOW(),1)";
        $params = array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0);
        //echo("query = $query<BR>");
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        $result = $this->get_query_result($query, $params);
        ////$statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        //$statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0));
        
        
        //$result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
            return 0;
        }
        return $result;
        
    }
    
        function add_container_basic($item_id, $label, $type, $container_id, $user_id) {
            // No service
        // TODO: user_id?
        if($container_id==null) {
            $container_id=-1;
        }
        if(!isset($label) || ($label == "")) {
            echo("<div class='alert alert-danger'>Please input a valid name.</div>");
            return 0;
        }
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        //echo("label = $label");
        $search_result = $this->get_query_result($search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A container with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        $new_location = $this->get_container_by_id($container_id);
        $new_location_type = $new_location['type'];
        //echo("current_type= $type, new_loc_type = $new_location_type<BR>");
        
        if($type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("<div class='alert alert-danger'>Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored</div>");
            return 0;
        }
        
        $result = 0;
        try {
        $query = "INSERT INTO tape_library (item_id, label, type, container, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :user_id, NOW(),1)";
        $params = array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'user_id'=>0);
        //echo("query = $query<BR>");
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        $result = $this->get_query_result($query, $params);
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
    
    function get_container_types() {
        $query = "SELECT container_type_id as id, name, container from container_type where container=1";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_location_types() {
        $query = "SELECT container_type_id as id, name, container from container_type where container=2";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
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
        $query = "SELECT container_type_id as id, name, container from container_type";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
     
    
    function get_tape_types() {
        $query = "SELECT container_type_id as id, name, container from container_type where container=0";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_all_tapes2() {
        $query = "select tape_library.id as id, tape_library.item_id as name, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join container on (tape_library.container = container.id)  join  container_type on  (container_type.container=0 and container_type_id=type)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function get_container_by_id($id) {
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
        return $this->get_container_by_id($id);
    }
    
    
    
    
    
    function get_tapes_without_backupset() {
        $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library  left join tape_library as container on (tape_library.container = container.id)  join  container_type on  (container_type.container=0 and container_type_id=tape_library.type) where tape_library.backupset is null or tape_library.backupset = '-1'";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
        
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
    
    
    function get_tapes($begin=null, $end=null, $type=null, $parent=null, $active=1, $container=0) {
        $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library ";
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
        
        $query .= "left join tape_library as container on (tape_library.container = container.id)  join  container_type on  (container_type.container=$container and container_type_id=tape_library.type)";
        if($subquery != "") {
            $query .= " WHERE ($subquery) ";
        }
        //echo("type = $type<BR>");
        //echo("query = $query<BR>");
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute($params);

        $result = $statement->fetchAll();
        return $result;
    }
    
    function get_containers() {
        //$query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library on (tape_library.container = tape_library.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        $query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join tape_library as container on (tape_library.container = container.id)  join  container_type on  (container_type.container=1 and container_type_id=tape_library.type)";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
    
    function edit_tape($id, $tape_label, $container, $type, $service, $active) {
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
        
        $query = "UPDATE tape_library set label=:label, container=:container, type=:type, service=:service, user_id=:user_id, active=:active, last_update=NOW() where id=:id";
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
    // Don't change type or service
    function edit_tape_basic($id, $tape_label, $container, $active) {
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
        //echo("current_type= $current_type, new_loc_type = $new_location_type<BR>");
        
        if($current_type == $new_location_type) {
            $location_type_name = $this->get_container_type_name($new_location_type);
            echo("<div class='alert alert-danger'>Cannot move a $location_type_name to another $location_type_name. Please select a proper location where this object can be stored</div>");
            return 0;
        }
        
        $query = "UPDATE tape_library set label=:label, container=:container, user_id=:user_id, active=:active, last_update=NOW() where id=:id";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('label'=>$tape_label,  'container'=>$container,  'user_id'=>0, 'id'=>$id, 'active'=>$active));
        //print_r($statement);
        //echo($statement->rowCount() . " rows updated.<BR>");
        try {
            //echo("query = $query<BR>");
        $result = $statement->fetchAll();
        //print_r($result);
        return $result;
        } catch(Exception $e) {
            echo $e;
        }
    }


function list_all($parent=null) {
    if($parent == null || $parent == "") {
        $query = "select * from tape_library where container IS NULL";
    } else {
        $query = "select * from tape_library where container = '$parent'";
    }
   
    //echo("parent = ".$parent .", query = $query");
    $result = $this->query($query);
    if($result == null) {
        return;
    }
    echo("<ul style='margin-left:10px;'>");
    foreach($result as $child) {
        //print_r($child);
        echo("<li>".$child['item_id']. "</li>");
        //echo("child id = ".$child['id']);
        
        list_all($child['id']);
        
    }
    echo("</ul>");
}

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

function get_container_type_name($container_type_id) {
    $query = "SELECT name from container_type where container_type_id=:container_type_id";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('container_type_id'=>$container_type_id));
    $result = $statement->fetchAll();
    if($result != null && $result[0]['name'] != null) {
        return $result[0]['name'];
    } else {
        return "None";
    }
}

function get_type_name($container_type_id) {
    $query = "SELECT name from container_type where container_type_id=:container_type_id";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('container_type_id'=>$container_type_id));
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

function add_backupset($name, $begin, $end, $program, $notes) {
    $search_query = "SELECT * from backupset where name=:name";
    $search_params = array("name"=>$name);
    $search_result = $this->get_query_result($search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("<div class='alert alert-danger'>A backupset with the name '$name' already exists. Please choose a different name.</div>");
        return 0;
    }
    $query = "INSERT INTO backupset (name, begin, end, program, notes) VALUES (:name, :begin, :end, :program, :notes)";

    $params = array('name'=>$name, 'begin'=>$begin, 'end'=>$end, 'program'=>$program, 'notes'=>$notes);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function get_tapes_for_backupset($backupset_id) {
    //$query = "SELECT * from tape_library where backupset=:backupset_id";
    $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library where backupset=:backupset_id";
        
    $params = array("backupset_id"=>$backupset_id);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function get_tapes_in_container($container_id) {
    //$query = "SELECT * from tape_library where backupset=:backupset_id";
    $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library where container=:container_id";
        
    $params = array("container_id"=>$container_id);
    $result = $this->get_query_result($query, $params);
    return $result;
}

function get_children($container_id) {
    $query = "SELECT id from tape_library where container=:container_id";
    $params = array("container_id"=>$container_id);
    $result = $this->get_query_result($query, $params);

    return $result;
}


function get_all_backup_sets() {
    $query = "SELECT * from backupset";
    $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $this->query($query);
    return $result;
    
}

function get_backupset($id) {
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

function edit_backupset($id, $name, $begin, $end, $program, $notes) {
    $search_query = "SELECT * from backupset where name=:name and id != :id";
    $search_params = array("name"=>$name, "id"=>$id);
    $search_result = $this->get_query_result($search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("<div class='alert alert-danger'>A backupset with the name '$name' already exists. Please choose a different name.</div>");
        return 0;
    }
    $query = "UPDATE backupset set name=:name, begin=:begin, end=:end, program=:program, notes=:notes where id=:id";
    $params = array("id"=>$id, "name"=>$name, "begin"=>$begin, "end"=>$end, "program"=>$program, "notes"=>$notes);
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

function get_all_backups() {
    $query = "SELECT * from backupset";
    $result = $this->query($query);
    return $result;
}

function get_name($id) {
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


}
?>
