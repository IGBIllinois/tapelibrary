<?php
// Various html-generation functions
class html {


	
		
	public static function success_message($message){
		return "<div class='alert alert-success'>".$message."</div>";
	}
	public static function error_message($message){
		return "<div class='alert alert-danger'>".$message."</div>";
	}
	public static function warning_message($message){
		return "<div class='alert alert-warning'>".$message."</div>";
	}
}

?>
