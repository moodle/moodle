<?php

include_once('../adodb-time.inc.php');
//adodb_date_test();
?>
<?php 
//require("adodb-time.inc.php"); 

$datestring = "1963-12-04"; // string normally from mySQL 
$stringArray = explode("-", $datestring);
$date = adodb_mktime(0,0,0,$stringArray[1],$stringArray[2],$stringArray[0]); 

$convertedDate = date("d-M-Y", $date); // converted string to UK style date

echo( "Birthday: $convertedDate" ); //why is string returned as one day (3 not 4) less for this example??

?>