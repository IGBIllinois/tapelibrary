<?php
class user {

	////////////////Private Variables//////////

	private $username;
	private $name;
	private $ldap;
	private $uidnumber;
	private $email;
	private $ldap_attributes = array(
			"uid",
			"cn",
			"sn",
			"givenname",
			"mail"
	);
	private $sn;
	private $givenName;

	////////////////Public Functions///////////

	public function __construct($ldap, $username = "") {
		$this->ldap = $ldap;
		if ($username != "") {
			$this->load_by_username($username);
		}
	}


	public function __destruct() {
	}


	public function get_username() {
		return $this->username;
	}

	public function get_email() {
		return $this->email;
	}


	public function get_name() {
		return $this->name;
	}

	public function authenticate($password) {
		$rdn = $this->get_user_rdn();
		if ($this->ldap->bind($rdn, $password)){
			if (user::is_ldap_user($this->ldap,$this->username)) {
				$in_admin_group = $this->ldap->search("(memberuid=".$this->username.")", LDAP_ADMIN_GROUP);
				if ($in_admin_group['count']>0) {
					return 0;
				} else {
					return 3;
				}
			} else {
				return 2;
			}
		} else {
// 			echo $this->ldap->get_error();
			return 1;
		}
	}


	
	public static function is_ldap_user($ldap, $username) {
		$username = trim(rtrim($username));
		$filter = "(uid=" . $username . ")";
		$attributes = array('');
		$result = $ldap->search($filter, settings::get_ldap_people_ou(), $attributes);
		if ($result['count']) {
			return true;
		} else {
			return false;
		}
	}

        
	public function get_user_rdn() {
		$filter = "(uid=" . $this->get_username() . ")";
		$attributes = array('dn');
		$result = $this->ldap->search($filter, '', $attributes);
		if (isset($result[0]['dn'])) {
			return $result[0]['dn'];
		}
		else {
			return false;
		}
	}

//////////////////Private Functions//////////

	private function load_by_username($username) {
		$filter = "(uid=".$username.")";
		$result = $this->ldap->search($filter, LDAP_PEOPLE_OU, $this->ldap_attributes);
		if($result['count']>0){
			$this->name = $result[0]['cn'][0];
			$this->sn = $result[0]['sn'][0];
			if(isset($result[0]['givenname'])){
				$this->givenName = $result[0]['givenname'][0];
			} else {
				$this->givenName = trim(strstr($this->name,$this->sn,true));
			}
			$this->username = $result[0]['uid'][0];

			$this->email = isset($result[0]['mail'])?$result[0]['mail'][0]:null;

		}
	}
	
}


?>
