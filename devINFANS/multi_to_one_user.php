<?php

set_include_path('/var/www/html/user');
require_once('../config.php');
$lien = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname) or die("echec de la connection mysql");
$lien->set_charset("latin1");
$lienschema = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, 'information_schema') or die("echec de la connection mysql");
$lienschema->set_charset("latin1");

$sql = "SELECT * FROM `COLUMNS` WHERE `COLUMN_NAME` LIKE 'userid'";
$res = $lienschema->query($sql);
$tabcol = array();
while ($row = $res->fetch_assoc())
{
    array_push($tabcol, $row['TABLE_NAME']);
}

for ($i=0; $i<count($tabcol); $i++)
    {
        $update = "UPDATE ".$tabcol[$i]." SET userid = 2792 WHERE userid = 11590";
        // $lien->query($delete);
        $lien->query($update);
        echo $update.'<br>';
    }

?>