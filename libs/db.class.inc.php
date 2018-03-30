
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

     
     
    /*
    function get_container_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type where container=1";
        $statement = $this->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $this->query($query);
        return $result;
    }
     * */
    

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





}
?>
