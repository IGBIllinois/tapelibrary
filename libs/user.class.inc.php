<?php
class user {

	////////////////Private Variables//////////

	private $username;
	private $name;

	private $ldap;
	private $uidnumber;
	private $email;


	


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
				$in_admin_group = $this->ldap->search("(memberuid=".$this->username.")", __LDAP_ADMIN_GROUP__);
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
		$result = $ldap->search($filter, __LDAP_PEOPLE_OU__, $attributes);
		if ($result['count']) {
			return true;
		} else {
			return false;
		}
	}


//////////////////Private Functions//////////

	public function load_by_username($username) {
		$filter = "(uid=".$username.")";
		$attributes = array("uid","cn",'sn','givenname',"homeDirectory","loginShell","mail","shadowExpire","creatorsName", "createTimestamp", "modifiersName", "modifyTimestamp","uidnumber",'sambaPwdLastSet','postalAddress');
		$result = $this->ldap->search($filter, __LDAP_PEOPLE_OU__, $attributes);
		if($result['count']>0){
			$this->name = $result[0]['cn'][0];
			$this->sn = $result[0]['sn'][0];
			if(isset($result[0]['givenname'])){
				$this->givenName = $result[0]['givenname'][0];
			} else {
				$this->givenName = trim(strstr($this->name,$this->sn,true));
			}
			$this->username = $result[0]['uid'][0];
			$this->homeDirectory = $result[0]['homedirectory'][0];
			$this->loginShell = $result[0]['loginshell'][0];
			$this->email = isset($result[0]['mail'])?$result[0]['mail'][0]:null;
			$this->emailforward = isset($result[0]['postaladdress'][0])?$result[0]['postaladdress'][0]:null; // Yes, postalAddress holds the forwarding email. 
			if(isset($result[0]['shadowexpire'])){
				$this->expiration = $result[0]['shadowexpire'][0];
			}
			if( preg_match("/uid=(.*?),/um", $result[0]['creatorsname'][0], $matches) ){
				$this->creator = $matches[1];
			} else {
				$this->creator = $result[0]['creatorsname'][0];
			}
			$this->createTime = strtotime($result[0]['createtimestamp'][0]);
			if( preg_match("/uid=(.*?),/um", $result[0]['modifiersname'][0], $matches) ){
				$this->modifier = $matches[1];
			} else {
				$this->modifier = $result[0]['modifiersname'][0];
			}
			$this->modifyTime = strtotime($result[0]['modifytimestamp'][0]);
			$this->uidnumber = $result[0]['uidnumber'][0];
			if(isset($result[0]['sambapwdlastset'])){
				$this->passwordSet = $result[0]['sambapwdlastset'][0];
			}
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
	
	// returns random int between $min,$max inclusive
	private static function devurandom_rand($min = 0, $max = 0x7FFFFFFF) {
	    $diff = $max - $min;
	    if ($diff < 0 || $diff > 0x7FFFFFFF) {
		throw new RuntimeException("Bad range");
	    }
	    $bytes = mcrypt_create_iv(4, MCRYPT_DEV_URANDOM);
	    if ($bytes === false || strlen($bytes) != 4) {
	        throw new RuntimeException("Unable to get 4 bytes");
	    }
	    $ary = unpack("Nint", $bytes);
	    $val = $ary['int'] & 0x7FFFFFFF;   // 32-bit safe
	    $fp = (float) $val / 2147483647.0; // convert to [0,1]
	    return intval(round($fp * $diff) + $min);
	}

	private static function sorter($key,$asc){
		if($asc == "true"){
			return function ($a,$b) use ($key) {
				return strcasecmp($a[$key], $b[$key]);
			};
		} else {
			return function ($a,$b) use ($key) {
				return strcasecmp($b[$key], $a[$key]);
			};
		}
	}
	
	private static function MD5Hash($password) {
		$saltchars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/.';
		$salt = $saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)].$saltchars[rand(0,63)];
		return '{CRYPT}'.Md5Crypt::unix($password,$salt);
	}
	
	private static function SSHAHash($password) {
		$salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',4)),0,4);
		return '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ). $salt);
	}
	
	private static function NTLMHash($cleartext){
		// Convert to UTF16 little endian
		$cleartext = iconv('UTF-8','UTF-16LE',$cleartext);
		//Encrypt with MD4
		$MD4Hash=hash('md4',$cleartext);
		$NTLMHash=strtoupper($MD4Hash);
		return $NTLMHash;
	}

	private static function LMhash($string) {
	    $string = strtoupper(substr($string,0,14));
	
	    $p1 = self::LMhash_DESencrypt(substr($string, 0, 7));
	    $p2 = self::LMhash_DESencrypt(substr($string, 7, 7));
	
	    return strtoupper($p1.$p2);
	}
	
	private static function LMhash_DESencrypt($string) {
	    $key = array();
	    $tmp = array();
	    $len = strlen($string);
	
	    for ($i=0; $i<7; ++$i)
	        $tmp[] = $i < $len ? ord($string[$i]) : 0;
	
	    $key[] = $tmp[0] & 254;
	    $key[] = ($tmp[0] << 7) | ($tmp[1] >> 1);
	    $key[] = ($tmp[1] << 6) | ($tmp[2] >> 2);
	    $key[] = ($tmp[2] << 5) | ($tmp[3] >> 3);
	    $key[] = ($tmp[3] << 4) | ($tmp[4] >> 4);
	    $key[] = ($tmp[4] << 3) | ($tmp[5] >> 5);
	    $key[] = ($tmp[5] << 2) | ($tmp[6] >> 6);
	    $key[] = $tmp[6] << 1;
	   
	    $is = mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($is, MCRYPT_RAND);
	    $key0 = "";
	   
	    foreach ($key as $k)
	        $key0 .= chr($k);
	    $crypt = mcrypt_encrypt(MCRYPT_DES, $key0, "KGS!@#$%", MCRYPT_MODE_ECB, $iv);
	
	    return bin2hex($crypt);
	}

}


?>
