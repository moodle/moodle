<?php
require_once(dirname(__FILE__). '/../../../config.php');
global $DB;
$updateAsig = "UPDATE {talentospilos_monitor_estud} SET id_semestre = 5 WHERE id_semestre IS NULL";
echo $DB->execute($updateAsig);

