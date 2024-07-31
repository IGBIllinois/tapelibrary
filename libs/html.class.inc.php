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
        
        /** 
         * Writes a message based on a result array
         *
         * @param array $result An array of the format: 
         *  ("RESULT"=>TRUE | FALSE,
         *   "MESSAGE"=>[string])
         *       If "RESULT" is FALSE it will display "MESSAGE" as an error message, 
         *      else it will display it as a success message.
         */
        public static function write_message($result) {
            if($result['RESULT']) {
                echo(self::success_message($result['MESSAGE']));
            } else {
                echo(self::error_message($result['MESSAGE']));
            }
        }
        
        /**
        * Creates an HTML input based on the parameters given
        * 
        * @param string $type Type of input to create. They include:
        *      "select": a drop-down selection box
        *      "date": a date selection input
        *      "begin": Text input for the start of a range of values
        *      "end": Text input for the end for a range of values
        *      "default": Text input
        * @param string $name Name of the input
        * @param string $default Default value, if any
        * @param array $array Array of values, used for options in the "select" input
        * @param int $id optional ID number for this input
        * @param string $onChange javascript for "onChange" method (optional)
        * @param type $id_name 
        */
       public static function createInput($type, $name, $default, $array=array(), $id="", $onChange="", $id_name="id") {
         $formName = $name;
           if($id != "") {
               $formName = $name . "_" . $id;
           }

           switch ($type) {

           case "select":
             print "<select class='form-control' id='{$formName}' name='{$formName}' ". ($onChange != "" ? " onChange='$onChange' " : "") . (($id != "") ? " id='{$name}_{$id}' ": "") .">";
             print "<option value=''>None</option>";
             $i=0;
             foreach ($array as $value) {
               print "<option value={$value[$id_name]}";
               if ($value['id'] == $default)
                 print " selected";

               print ">{$value['name']}</option>";
             }
             print "</select>";
             break;
           case "date":
             print "<input type=text id=datepicker class={$name} name={$formName} value={$default}>";
             break;
           case "begin":
             print "<input type='text' id='from' class='form-control' name={$formName} value={$default}>";
             break;
           case "end":
             print "<input type='text' id='to' class='form-control' name={$formName} value={$default}>";
             break;
           default:
               print "<input class='form-control' name='{$formName}' ".(($id != "") ? " id='{$formName}' " : "" ) . " value=\"{$default}\">";
         }
       }


    /**
     * Redirects to a new web page
     * 
     * @param string $url URL to redirect to
     */
    public static function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }

}

?>
