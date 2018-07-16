
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
    

function get_query_result($query_string, $query_array) {
    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute($query_array);
    $result = $statement->fetchAll();
    return $result;
    
}


function get_update_result($query_string, $query_params) {
    // Update queries should probably only update one record. This will ensure 
    // only one record gets updated in case of a malformed query.
    $query_string .= " LIMIT 1"; 
    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $statement->execute($query_params);
    return $result;
}

function get_insert_result($query_string, $query_array) {

    $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt = $statement->execute($query_array);
    $result =  $this->get_link()->lastInsertId();
    return $result;
}





}
?>
