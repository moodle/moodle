<?php
include_once('../adodb.inc.php');
$db = NewADOConnection('postgres7');
$db->Connect('localhost','tester','test','test') || die("failed connection");

$enc = "GIF89a%01%00%01%00%80%FF%00%C0%C0%C0%00%00%00%21%F9%04%01%00%00%00%00%2C%00%00%00%00%01%00%01%00%00%01%012%00%3Bt_clear.gif%0D";
$val = rawurldecode($enc);
$db->debug=1;

### TEST BEGINS

$db->Execute("insert into photos (id,name) values(9999,'dot.gif')");
$db->UpdateBlob('photos','photo',$val,'id=9999');
$v = $db->GetOne('select photo from photos where id=9999');


### CLEANUP

$db->Execute("delete from photos where id=9999");

### VALIDATION

if ($v !== $val) echo "<b>*** ERROR: Inserted value does not match downloaded val<b>";
else echo "<b>*** OK: Passed</b>";

echo "<pre>";
echo "INSERTED: ", $enc;
echo "<hr>";
echo"RETURNED: ", rawurlencode($v);
echo "<hr><p>";
echo "INSERTED: ", $val;
echo "<hr>";
echo "RETURNED: ", $v;

?>