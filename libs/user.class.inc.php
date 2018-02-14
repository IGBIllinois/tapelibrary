<?php
require_once '../libs/db.class.inc.php';

class user {
	////////////////Private Variables//////////
	private $db; //mysql database object
	private $id;
	private $user_name;
	private $enabled;
	private $time_created;
	private $ldap;
	private $email;
	private $admin;

	////////////////Public Functions///////////
	public function __construct($db,$ldap,$username = "",$id = 0) {
		$this->db = $db;
		$this->ldap = $ldap;
		if ($id != 0) {
			$this->load_by_id($id);
		}
		elseif (($id == 0) && ($username != "")) {
			$this->load_by_username($username);
			$this->user_name = $username;
		}
	}
	public function __destruct() {
	}
	public function create($username,$email,$admin=1) {
		$username = trim(rtrim($username));
                		$error = false;
		//Verify Username
		if ($username == "") {
			$error = true;
			$message = "<div class='alert'>Please enter a username.</div>";
		}
		elseif ($this->get_user_exist($username)) {
			$error = true;
			$message .= "<div class='alert'>User already exists in database.</div>";
		}
		elseif (!$this->ldap->is_ldap_user($username)) {
			$error = true;
			$message = "<div class='alert'>User does not exist in LDAP database.</div>";
		}
                		
                
                if ($error) {
			return array('RESULT'=>false,
					'MESSAGE'=>$message);
		}
                
                else {
		
			if ($this->is_disabled($username)) {
				$this->load_by_username($username);
				$this->enable();
				
			}
			else {
				$full_name = $this->ldap->get_ldap_full_name($username);
				$home_dir = $this->ldap->get_home_dir($username);
				$user_array = array('user_name'=>$username,
						'user_full_name'=>$full_name,
						'user_admin'=>$admin,
						'user_supervisor'=>$supervisor_id,
						'user_enabled'=>1
				);
				$user_id = $this->db->build_insert("users",$user_array);
				$this->load_by_id($user_id);

			}
			return array('RESULT'=>true,
					'MESSAGE'=>'User succesfully added.',
					'user_id'=>$user_id);
		}
      
        }	
                
 
        
        	//////////////////Private Functions//////////
	private function load_by_id($id) {
		$this->id = $id;
		$this->get_user();
	}
	private function load_by_username($username) {
		//$sql = "SELECT user_id FROM users WHERE user_name = '" . $username . "' LIMIT 1";
		$sql = "SELECT id FROM users WHERE username = '" . $username . "' LIMIT 1";

                $result = $this->db->query($sql);
		if (isset($result[0]['user_id'])) {
			$this->id = $result[0]['user_id'];
			$this->get_user();
		}
	}
	private function get_user() {
		$sql = "SELECT users.user_id, users.user_admin, users.user_name, ";
		$sql .= "users.user_enabled, users.email, users.user_time_created ";
		$sql .= "FROM users ";

                //echo ("user query = $sql<BR>");
                $result = $this->db->query($sql);
                
		if (count($result)) {
			$this->user_name = $result[0]['user_name'];
			$this->admin = $result[0]['user_admin'];
			$this->time_created = $result[0]['user_time_created'];
			$this->enabled = $result[0]['user_enabled'];
			$this->email = $result[0]['email'];
		}
	}
        
        public function get_user_rdn() {
                $filter = "(uid=" . $this->get_username() . ")";       
                $attributes = array('dn');
                $result = $this->ldap->search($filter,'',$attributes);
                if (isset($result[0]['dn'])) {
                        return $result[0]['dn'];
                }
                else {
                        return false;
                }
        }
        
        public function authenticate($password) {
		$result = false;
                $rdn = $this->get_user_rdn();
                if (($this->ldap->bind($rdn,$password)) && ($this->get_user_exist($this->user_name))) {
                        $result = true;
                }
                return $result;
        }
        
        private function get_user_exist($username) {
		$sql = "SELECT COUNT(1) as count FROM users WHERE username='" . $username . "' AND active='1'";
		$result = $this->db->query($sql);
		return $result[0]['count'];
	}
        
        public function get_user_id() {
		return $this->id;
	}
	public function get_username() {
		return $this->user_name;
	}
	public function get_email() {
		return $this->email;
	}
	public function get_enabled() {
		return $this->enabled;
	}
	public function get_time_created() {
		return $this->time_created;
	}
        
        public function is_admin() {
		return $this->admin;
	}
        
        
}
?>