<?php 

////
//// jquery AJAX DataTables backend
//// Prints out JSON for DataTables to use
////

//// initialize variables and functions
// Set number of records to show
$amt=25; 
if(isset($_REQUEST['iDisplayLength'])){ 
	$amt=(int)$_REQUEST['iDisplayLength']; 
	if($amt>200 || $amt<0)
		$amt=25;
}
// Set where to start showing the records from
$start=0; 
if(isset($_REQUEST['iDisplayStart'])){
	$start=(int)$_REQUEST['iDisplayStart']; 
	if($start<0)
		$start=0;
}

// MySQL information
$mysqlSettings = array(
		'host' => 'localhost',
		'username' => 'tapelibraryuser',
		'password' => 'yCx7wZUmr:6,9sVD',
		'database' => 'tapelibrary'
);
// connect to database
$link = mysql_connect($mysqlSettings['host'],$mysqlSettings['username'],$mysqlSettings['password']) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysqlSettings['database'],$link) or die("Unable to select database");
// MySQL query function that converts to array
function dbRow($sql) {
	$q=mysql_query($sql);
	$r=mysql_fetch_array($q);
	return $r; 
}
function dbAll($sql){
	$q=mysql_query($sql);
	while($r=mysql_fetch_array($q))
		$rs[]=$r;
	return $rs;
}

// which table to print out
$table = $_REQUEST['table'];

// Array for name of columns
if ($table == 'tape')
	$cols=array('id','type','capacity','tape_number','location','backup_set','carton','label');
if ($table == 'carton')
	$cols=array('id','name','date_added');
if ($table == 'location')
	$cols=array('id','name','contact');
if ($table == 'backupset')
	$cols=array('id','name','begin','end');

// Sorting information
$scol = 0; 
if (isset($_REQUEST['iSortCol_0'])) {
	$scol=(int)$_REQUEST['iSortCol_0'];
	if ($scol>7 || $scol<0)
		$scol=0;
}
$sdir = 'asc';
if (isset($_REQUEST['sSortDir_0'])) {
	if ($_REQUEST['sSortDir_0'] != 'asc')
		$sdir = 'desc';
}
$scol_name = $cols[$scol];

// tape table print out code
if ($table == 'tape') {
	// count existing records
	$r=dbRow("select count(tape_number) as c from $table");
	$total_records=$r['c'];
	$total_after_filter=$total_records; 
	if($search_sql){ 
		$r=dbRow("select count(tape_number) as c from $table $search_sql"); 
		$total_after_filter=$r['c'];
	}
	// search function
	$search_sql='';
	if(isset($_REQUEST['sSearch']) && ''!=$_REQUEST['sSearch']){
		$stext=addslashes($_REQUEST['sSearch']); 
		$search_sql='where ';
		$search_sql.="type like '%$stext%'";
		$search_sql.="or capacity like '%$stext%'";
		$search_sql.="or tape_number like '%$stext%'";
		$search_sql.="or location.name like '%$stext%'";
		$search_sql.="or backupset.name like '%$stext%'";
		$search_sql.="or carton.name like '%$stext%'";
		$search_sql.="or label like '%$stext%'";
	}
	// start displaying records 
	echo '{"iTotalRecords":'.$total_records.',"iTotalDisplayRecords":'.$total_after_filter.',"aaData":[';
	$rs = dbAll("select tape.id, tape.type, tape.capacity, tape.tape_number, location.name as location, backupset.name as backup_set, carton.name as carton, tape.label from tape left join location on tape.location=location.id left join backupset on tape.backup_set=backupset.id left join carton on tape.carton=carton.id $search_sql order by $scol_name $sdir limit $start,$amt");
	$f = 0;
	foreach ($rs as $r) {
		if($f++)
			echo ',';
		echo '["<input type=checkbox name=multiedit[] id=',$r['id'],' class=multiedit value=',$r['id'],'>",
		"',$r['type'],'",
		"',$r['capacity'],'",
		"',$r['tape_number'],'",
		"',$r['location'],'",
		"<a href=# id=\"',$r['backup_set'],'\" class=backupset_filter>',$r['backup_set'],'</a>",
		"',$r['carton'],'",
		"',$r['label'],'"]';
	}
	echo ']}';
}

// carton table print out code
if ($table == 'carton') {
	// count existing records
	$r=dbRow("select count(name) as c from $table");
	$total_records=$r['c'];
	$total_after_filter=$total_records;
	if($search_sql){ 
		$r=dbRow("select count(name) as c from $table $search_sql"); 
		$total_after_filter=$r['c'];
	}
	// search function
	$search_sql='';
	if(isset($_REQUEST['sSearch']) && ''!=$_REQUEST['sSearch']){
		$stext=addslashes($_REQUEST['sSearch']); 
		$search_sql='where ';
		$search_sql.="name like '%$stext%'";
		$search_sql.="or date_added like '%$stext%'";
	} 
	// start displaying records 
	echo '{"iTotalRecords":'.$total_records.',"iTotalDisplayRecords":'.$total_after_filter.',"aaData":[';
	$rs = dbAll("select id, name, date_added from $table $search_sql order by $scol_name $sdir limit $start,$amt");
	$f = 0;
	foreach ($rs as $r) {
		if($f++)
			echo ',';
		echo '["<input type=checkbox name=multiedit[] id=',$r['id'],' class=multiedit value=',$r['id'],'>",
		"',$r['name'],'",
		"',$r['date_added'],'"]';
	}
	echo ']}';
}

// location table print out code
if ($table == 'location') {
	// count existing records
	$r=dbRow("select count(name) as c from $table");
	$total_records=$r['c'];
	$total_after_filter=$total_records;
	if($search_sql){ 
		$r=dbRow("select count(name) as c from $table $search_sql"); 
		$total_after_filter=$r['c'];
	}
	// search function
	$search_sql='';
	if(isset($_REQUEST['sSearch']) && ''!=$_REQUEST['sSearch']){
		$stext=addslashes($_REQUEST['sSearch']); 
		$search_sql='where ';
		$search_sql.="name like '%$stext%'";
		$search_sql.="or contact like '%$stext%'";
	} 
	// start displaying records 
	echo '{"iTotalRecords":'.$total_records.',"iTotalDisplayRecords":'.$total_after_filter.',"aaData":[';
	$rs = dbAll("select id, name, contact from $table $search_sql order by $scol_name $sdir limit $start,$amt");
	$f = 0;
	foreach ($rs as $r) {
		if($f++)
			echo ',';
		echo '["<input type=checkbox name=multiedit[] id=',$r['id'],' class=multiedit value=',$r['id'],'>",
		"',$r['name'],'",
		"',$r['contact'],'"]';
	}
	echo ']}';
}

// backupset table print out code
if ($table == 'backupset') {
	// count existing records
	$r=dbRow("select count(name) as c from $table");
	$total_records=$r['c'];
	$total_after_filter=$total_records;
	if($search_sql){ 
		$r=dbRow("select count(name) as c from $table $search_sql"); 
		$total_after_filter=$r['c'];
	}
	// search function
	$search_sql='';
	if(isset($_REQUEST['sSearch']) && ''!=$_REQUEST['sSearch']){
		$stext=addslashes($_REQUEST['sSearch']); 
		$search_sql='where ';
		$search_sql.="name like '%$stext%'";
		$search_sql.="begin like '%$stext%'";
		$search_sql.="or end like '%$stext%'";
	} 
	// start displaying records 
	echo '{"iTotalRecords":'.$total_records.',"iTotalDisplayRecords":'.$total_after_filter.',"aaData":[';
	$rs = dbAll("select id, name, begin, end from $table $search_sql order by $scol_name $sdir limit $start,$amt");
	$f = 0;
	foreach ($rs as $r) {
		if($f++)
			echo ',';
		echo '["<input type=checkbox name=multiedit[] id=',$r['id'],' class=multiedit value=',$r['id'],'>",
		"',$r['name'],'",
		"',$r['begin'],'",
		"',$r['end'],'"]';
	}
	echo ']}';
}

?>