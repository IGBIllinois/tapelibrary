<?php

require_once '../libs/db.class.inc.php';

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
function createInput($type, $name, $default, $array=array(), $id="", $onChange="", $id_name="id") {
  $formName = $name;
    if($id != "") {
        $formName = $name . "_" . $id;
    }
      
    switch ($type) {
      
    case "select":
      print "<select id='{$formName}' name='{$formName}' ". ($onChange != "" ? " onChange='$onChange' " : "") . (($id != "") ? " id='{$name}_{$id}' ": "") .">";
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
      print "<input type=text id=from class={$name} name={$formName} value={$default}>";
      break;
    case "end":
      print "<input type=text id=to class={$name} name={$formName} value={$default}>";
      break;
    default:
        print "<input class='{$name}' name='{$formName}' ".(($id != "") ? " id='{$formName}' " : "" ) . " value=\"{$default}\">";
  }
}


/**
 * Redirects to a new web page
 * 
 * @param string $url URL to redirect to
 */
    function redirect($url) {
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }
