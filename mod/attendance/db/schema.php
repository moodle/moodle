<?php 
include_once('tohtml.inc.php'); 
include_once('adodb.inc.php'); 
include_once('adodb-xmlschema.inc.php'); 
$database = "oci8po";
$db = ADONewConnection("$database"); 
$db->debug = true;
$server = "false";
$user = "scott";
$password = "tiger";
$db->Connect(false, $user, $password); 
$rs = $db->Execute('select * from dept'); 
print "<pre>"; 
print_r($rs->GetRows()); 
print "</pre>"; 
rs2html($rs,'border=2 cellpadding=3',array('Deptno','DName','Loc'));
$dict = NewDataDictionary($db);
$rs = $db->Execute('drop table attendance');
$schema = new adoSchema($db);
$rs = $db->Execute('drop table attendance_roll');
$schema = new adoSchema($db);
$sql = $schema->ParseSchema("schema.xml");
$result = $schema->ExecuteSchema( $sql );
$sql = "insert into attendance(id,name,course,day,hours,roll,notes,timemodified,dynsection,edited,autoattend) values (2,'2',2,2,2,2,'2',2,2,2,2)";
$rs = $db->Execute($sql); 
$rs = $db->Execute('select * from attendance'); 
rs2html($rs,'border=2 cellpadding=3',array('id','name','course','day','hours','roll','notes','timemodified','dynsection','edited','autoattend'));
?>
</body></html>
