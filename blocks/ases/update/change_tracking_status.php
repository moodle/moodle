<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
   cuando tracking status este en 0 lo modifica a 1
*/

global $DB;

$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 1 WHERE tracking_status=0";
$success = $DB->execute($sql_query);

echo $success;

