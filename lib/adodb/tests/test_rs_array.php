<?php

include_once('../adodb.inc.php');
$rs = new ADORecordSet_array();

$array = array(
array ('Name', 'Age'),
array ('John', '12'),
array ('Jill', '8'),
array ('Bill', '49')
);

$typearr = array('C','I');


$rs->InitArray($array,$typearr);

while (!$rs->EOF) {
	print_r($rs->fields);echo "<br>";
	$rs->MoveNext();
}

echo "<hr> 1 Seek<br>";
$rs->Move(1);
while (!$rs->EOF) {
	print_r($rs->fields);echo "<br>";
	$rs->MoveNext();
}

echo "<hr> 2 Seek<br>";
$rs->Move(2);
while (!$rs->EOF) {
	print_r($rs->fields);echo "<br>";
	$rs->MoveNext();
}

echo "<hr> 3 Seek<br>";
$rs->Move(3);
while (!$rs->EOF) {
	print_r($rs->fields);echo "<br>";
	$rs->MoveNext();
}



die();
?>