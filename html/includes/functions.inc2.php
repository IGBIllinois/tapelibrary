<?php

require_once '../libs/db.class.inc.php';

function cleanArray($array) {
    global $db;
  foreach ($array as $key => $value){
    $array[$key] = mysql_real_escape_string($value);
  }
  return $array;
}

function mysqlToArray($mysqlResult) {
  $dataArray = array();
  $i = 1;
  while($row = mysql_fetch_array($mysqlResult,MYSQL_ASSOC)) {	
    foreach($row as $key=>$data) {
       
      $dataArray[$i][$key] = $data;
    }
    $i++;
  }
  return $dataArray;
}

function createInput($type, $name, $default, $array=array(), $id="", $onChange="") {
  $formName = $name;
    if($id != "") {
        $formName = $name . "_" . $id;
    }
      
    switch ($type) {
      
    case "select":
      print "<select name='{$formName}' ". ($onChange != "" ? " onChange='$onChange' " : "") . (($id != "") ? " id='{$name}_{$id}' ": "") .">";
      print "<option value=''>Select {$formName}</option>";
      $i=0;
      foreach ($array as $value) {
        print "<option value={$value['id']}";
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
      //print "<input class={$name} name={$formName} ".(($id != "") ? " id={$formName} " : "" ) . " value=\"{$default}\" placeholder=\"Enter {$name}\">";
        print "<input class='{$name}' name='{$formName}' ".(($id != "") ? " id='{$formName}' " : "" ) . " value=\"{$default}\">";
  }
}

function printSQL($query, $tableid) {
  if ($tableid == 'tape') {
    $backupset = mysqlToArray(mysql_query("select * from backupset"));
    $carton = mysqlToArray(mysql_query("select * from carton"));
    $container = mysqlToArray(mysql_query("select * from container"));
  }
  $result=mysql_query($query);
  if (mysql_num_rows($result) == 0)
    print "<p>No Results</p>";
  print "<div id=log>0 records checked</div>";
  print "<fieldset>";
  print "<br />";
  print "<form action=edit.php method=post>";
  if ($tableid == 'tape') {
    print "<input type=button value=add onclick=\"location.href='add_multiple.php'\" />&nbsp;&nbsp;&nbsp;";
  } else {
    print "<input type=button value=add class=iframe_add href='add.php?table={$tableid}' />&nbsp;&nbsp;&nbsp;";
  }
  print "<input type=submit name=submit value=edit class=icon_submit id=edit_submit>&nbsp;&nbsp;&nbsp;";
  print "<input type=submit name=submit value=delete class=icon_submit id=delete_submit>";
  print "<br /><br />";
  print "<table id={$tableid} class='sortable'>";
  $headresult=mysql_query($query . " limit 1");
  $row=mysql_fetch_assoc($headresult);
  print "<thead><tr>";
  foreach(array_keys($row) as $piece) {
    switch ($piece) {
	  case "id":
	    print "<th><input type=checkbox id=checkall /></th>";
	    break;
	  case "capacity":
	    print "<th>capacity<br />(in GB)</th>";
	    break;
	  case "tape_number":
	    print "<th>$piece</th>";
	    break;
	  default:
	    print "<th>$piece</th>";
    }
  }
  print "</tr></thead>";
  print "<tfoot><tr>";
  print "<td colspan=2 align=center>";
  if ($tableid == 'tape') {
    print "<input type=button value=add onclick=\"location.href='add_multiple.php'\" />&nbsp;&nbsp;&nbsp;";
  } else {
    print "<input type=button value=add class=iframe_add href='add.php?table={$tableid}' />&nbsp;&nbsp;&nbsp;";
  }
  print "<input type=submit name=submit value=edit class=icon_submit id=edit_submit>&nbsp;&nbsp;&nbsp;";
  print "<input type=submit name=submit value=delete class=icon_submit id=delete_submit>";
  print "</td>";
  print "</tr></tfoot>";
  print "</table>";
  print "<input type=hidden name=table value={$tableid}>";
  print "</form>";
  print "</fieldset>";

}

function get_all_tapes($db) {
    $query = "SELECT id,type,capacity,tape_number,container,backup_set,carton,label, time_created FROM tape t";
    $result = $db->query($query);
    return $result;
}

function get_all_cartons($db) {
    $query = "SELECT * from carton";
    $result = $db->query($query);
    return $result;
}

function get_all_backups($db) {
    $query = "SELECT * from backupset";
    $result = $db->query($query);
    return $result;
}

function get_all_containers($db) {
    $query = "SELECT * from container";
    $result = $db->query($query);
    return $result;
}

function print_table($tableid, $headers, $fields, $data) {


  print "<table  class='sortable'>";

  
  print("<thead><tr><th><input type=checkbox id=checkall /></th>");
  foreach($headers as $header) {
      print("<th>".$header."</th>");
  }
  print("</tr></thead>");
 
  foreach($data as $record) {
      print("<tr>");
      foreach($fields as $fieldname) {
          
          if($fieldname == "id") {
              print("<td><input type=checkbox name='checkbox[]' value='".$record[$fieldname]."' id='".$record[$fieldname]."'></td>");
          } else {
              print("<td>".$record[$fieldname]."</td>");
          }
      }
      

      print("</tr>");
  }
  print "<tfoot><tr>";
  print "<td colspan=3 align=center>";
  if ($tableid == 'tape') {
    print "<input type=button value=add onclick=\"location.href='add_multiple.php'\" />&nbsp;&nbsp;&nbsp;";
  } else {
    print "<input type=button value=add class=iframe_add href='add.php?table={$tableid}' />&nbsp;&nbsp;&nbsp;";
  }
  print "<input type=submit name=submit value=edit class=icon_submit id=edit_submit>&nbsp;&nbsp;&nbsp;";
  print "<input type=submit name=submit value=delete class=icon_submit id=delete_submit>";
  print "</td>";
  print "</tr></tfoot>";
  print "</table>";
  print "<input type=hidden name=table value={$tableid}>";

  
}

function add_tape_old($db, $tape_number, $label, $type, $capacity, $container, $backup_set, $carton) {
   
        //$query = "insert into tape (tape_number, label, type ,capacity,container,backup_set,carton) values ($i, '$label[$i]', '$type','$capacity','$container','$backup_set','$carton')";
        $query = "insert into tape (tape_number, label, type ,capacity,container,backup_set,carton) values (:tape_number, :label, :type, :capacity,:container, :backup_set, :carton)";
        
        $statement = $db->get_link()->prepare($query,  array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('tape_number'=>$tape_number, 'label'=>$label, 'type'=>$type, 'capacity'=>$capacity, 'container'=>$container, 'backup_set'=>$backup_set, 'carton'=>$carton));
        //$result = $db->insert_query($query);
        $result = $statement->fetchAll();
        return $result;
}

function get_tape($db, $tape_id) {
    $query = "SELECT * from tape where id=:id";
    
    $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    
    $statement->execute(array('id'=>$tape_id));
    $result = $statement->fetchAll();
    if($result != null) {
        return $result[0];
    } else {
        return null;
    }
}
    
    function add_container_type($db, $container_type_name, $container=0) {
        // TODO : Check container type existance first
        
        $query = "INSERT INTO container_type (name, container) VALUES(:container_type_name, :container)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('container_type_name'=>$container_type_name, 'container'=>$container));
        
        $result = $statement->fetchAll();
        return $result;
        
    }
    
    function does_tape_exist($db, $label) {
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = get_query_result($db, $search_query, $search_params);
 //echo("count = ".count($search_result));
        if(count($search_result) > 0) {
            return 1;
        }
        return 0;
    }
    
    function add_tape($db, $item_id, $label, $type, $container_id, $backupset, $user_id) {
        // TODO: user_id?
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = get_query_result($db, $search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A tape with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        
        $query = "INSERT INTO tape_library (item_id, label, type, container, backupset, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :backupset, :user_id, NOW(),1)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'backupset'=>$backupset, 'user_id'=>0));
        //echo("item_id = $item_id, type = $type, container_id = $container_id, backupset=$backupset, user_id=$user_id");
        try {
        $result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
        }
        return $result;
        
    }    
    
    function add_container($db, $item_id, $label, $type, $container_id, $service, $user_id) {
        // TODO: user_id?
        $search_query = "SELECT * from tape_library where label=:label";
        $search_params = array("label"=>$label);
        $search_result = get_query_result($db, $search_query, $search_params);
       
        if(count($search_result) > 0) {
            echo("<div class='alert alert-danger'>A container with the name '$label' already exists. Please choose a different name.</div>");
            return 0;
        }
        
        
        $query = "INSERT INTO tape_library (item_id, label, type, container, service, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :service, :user_id, NOW(),1)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute(array('item_id'=>$item_id, 'label'=>$label, 'type'=>$type, 'container_id'=>$container_id, 'service'=>$service, 'user_id'=>0));
        //echo("item_id = $item_id, type = $type, container_id = $container_id, service=$service, user_id=$user_id");
        try {
        $result = $statement->fetchAll();
        //print_r($result);
        } catch(Exception $e) {
            echo $e;
            return 0;
        }
        return $result;
        
    }
    
    function add_item($db, $item_id, $label, $type, $container_id, $service ,$user_id) {
        
        $query = "INSERT INTO tape_library (item_id, label, type, container, service, user_id, last_update, active) VALUES(:item_id, :label, :type, :container_id, :service, :user_id, NOW(),1)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
    
    function get_container_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type where container=1";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    /*
    function get_container_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type where container=1";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
     * */
    
    function get_all_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
     
    
    function get_tape_types($db) {
        $query = "SELECT container_type_id as id, name, container from container_type where container=0";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    function get_all_tapes2($db) {
        $query = "select tape_library.id as id, tape_library.item_id as name, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join container on (tape_library.container = container.id)  join  container_type on  (container_type.container=0 and container_type_id=type)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    function get_tapes($db, $begin=null, $end=null, $type=null, $parent=null, $active=1, $container=0) {
        $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library ";
        $subquery = "";
        $params = array();
        if($begin != null && $end != null) {
            $subquery .= " label between :begin and :end ";
            $params['begin'] = $begin;
            $params['end'] = $end;
        }
        if($type != null) {
            if($subquery != "") {
                $subquery .= " AND ";
            }
            $subquery .= " type = :type ";
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
        
        $query .= "left join container on (tape_library.container = container.id)  join  container_type on  (container_type.container=$container and container_type_id=type)";
        if($subquery != "") {
            $query .= " WHERE ($subquery) ";
        }
       //echo("parent = $parent<BR>");
        //echo("query = $query<BR>");
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $statement->execute($params);

        $result = $statement->fetchAll();
        return $result;
    }
    
    function get_containers($db) {
        $query = "select tape_library.id as id, tape_library.item_id as tape_id, tape_library.label as name, tape_library.type as type, tape_library.container as parent, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library left join container on (tape_library.container = container.id)  join  container_type on  (container_type.container=1 and container_type_id=type)";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $db->query($query);
        return $result;
    }
    
    function edit_tape($db, $id, $tape_label, $container, $type, $service, $active) {
        if($container == "") {
            echo("container is blank, setting to null<BR>");
            $container = null;
        }
        
        $query = "UPDATE tape_library set label=:label, container=:container, type=:type, service=:service, user_id=:user_id, active=:active, last_update=NOW() where id=:id";
        $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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


function list_all($db, $parent=null) {
    if($parent == null || $parent == "") {
        $query = "select * from tape_library where container IS NULL";
    } else {
        $query = "select * from tape_library where container = '$parent'";
    }
   
    //echo("parent = ".$parent .", query = $query");
    $result = $db->query($query);
    if($result == null) {
        return;
    }
    echo("<ul style='margin-left:10px;'>");
    foreach($result as $child) {
        //print_r($child);
        echo("<li>".$child['item_id']. "</li>");
        //echo("child id = ".$child['id']);
        
        list_all($db, $child['id']);
        
    }
    echo("</ul>");
}

function is_admin($db, $username) {
    
    $query = "SELECT admin from users where username=:username and active=1";
    $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('username'=>$username));
    
    $result = $statement->fetchAll();
    //print_r($result);
    if($result[0]['admin'] == 1) {
        return true;
    }
    
    return false;
}

function get_container_type_name($db, $container_type_id) {
    $query = "SELECT name from container_type where container_type_id=:container_type_id";
    $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute(array('container_type_id'=>$container_type_id));
    $result = $statement->fetchAll();
    if($result != null && $result[0]['name'] != null) {
        return $result[0]['name'];
    } else {
        return "None";
    }
}

function get_query_result($db, $query_string, $query_array) {
    $statement = $db->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $statement->execute($query_array);
    $result = $statement->fetchAll();
    return $result;
    
}

function add_backupset($db, $name, $begin, $end, $program, $notes) {
    $search_query = "SELECT * from backupset where name=:name";
    $search_params = array("name"=>$name);
    $search_result = get_query_result($db, $search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("A backupset with the name '$name' already exists. Please choose a different name.<BR>");
        return 0;
    }
    $query = "INSERT INTO backupset (name, begin, end, program, notes) VALUES (:name, :begin, :end, :program, :notes)";

    $params = array('name'=>$name, 'begin'=>$begin, 'end'=>$end, 'program'=>$program, 'notes'=>$notes);
    $result = get_query_result($db, $query, $params);
    return $result;
}

function get_tapes_for_backupset($db, $backupset_id) {
    //$query = "SELECT * from tape_library where backupset=:backupset_id";
    $query = "select tape_library.id as id, tape_library.item_id as tape_number, tape_library.label as label, tape_library.container as parent, tape_library.type as type, tape_library.service as service, tape_library.backupset as backupset, tape_library.active as active, (SELECT label from tape_library where parent = id) as container_name, (SELECT container from container_type where container_type_id=tape_library.type) as is_container from tape_library where backupset=:backupset_id";
        
    $params = array("backupset_id"=>$backupset_id);
    $result = get_query_result($db, $query, $params);
    return $result;
}

function get_all_backup_sets($db) {
    $query = "SELECT * from backupset";
    $statement = $db->get_link()->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $result = $db->query($query);
    return $result;
    
}

function get_backupset($db, $id) {
    $query = "SELECT * from backupset where id=:id";
    $params = array("id"=>$id);
    $result = get_query_result($db, $query, $params);
    
    return $result;
}

function edit_backupset($db, $id, $name, $begin, $end, $program, $notes) {
    $search_query = "SELECT * from backupset where name=:name and id != :id";
    $search_params = array("name"=>$name, "id"=>$id);
    $search_result = get_query_result($db, $search_query, $search_params);
    
    
    //print_r($search_result);
    
    if(count($search_result) > 0) {
        echo("A backupset with the name '$name' already exists. Please choose a different name.<BR>");
        return 0;
    }
    $query = "UPDATE backupset set name=:name, begin=:begin, end=:end, program=:program, notes=:notes where id=:id";
    $params = array("id"=>$id, "name"=>$name, "begin"=>$begin, "end"=>$end, "program"=>$program, "notes"=>$notes);
    $result = get_query_result($db, $query, $params);
    return $result;
}

function delete_backupset($db, $backupset_id) {
    $query = "UPDATE backupset set active=0 where $id=:id";
    $params = array("id"=>$backupset_id);
    $result = get_query_result($db, $query, $params);
    return $result;
}

function set_backupset($db, $tape_id, $backupset_id) {
    $query = "UPDATE tape_library set backupset=:backupset_id where id=:tape_id";
    $params = array("backupset_id"=>$backupset_id, "tape_id"=>$tape_id);
    $result = get_query_result($db, $query, $params);
    return $result;
}