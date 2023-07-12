<?php

set_include_path('/var/www/html/user');
require_once('../config.php');
$lien = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname) or die("echec de la connection mysql");
$lien->set_charset("latin1");
$lienschema = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, 'information_schema') or die("echec de la connection mysql");
$lien->set_charset("latin1");

$sql = "SELECT * FROM `COLUMNS` WHERE `COLUMN_NAME` LIKE 'userid'";
$res = $lienschema->query($sql);
$tabcol = array();
while ($row = $res->fetch_assoc())
{
    array_push($tabcol, $row['TABLE_NAME']);
}

$sqluser = "SELECT * FROM `mdl_user` WHERE `lastaccess` = 0 AND `timecreated` <= 1570456101";
// $sqluser = "SELECT * FROM mdl_user WHERE id = 9163";
$resuser = $lien->query($sqluser);
$i = 0;
while ($rowu = $resuser->fetch_assoc())
{
$deleteuser = "DELETE FROM mdl_user WHERE id = ".$rowu['id'];
$lien->query($deleteuser);

    for ($i=0; $i<count($tabcol); $i++)
    {
        $delete = "DELETE FROM ".$tabcol[$i]." WHERE userid = ".$rowu['id'];
        $lien->query($delete);
        echo $delete.'<br>';
    }
}
exit;
?>