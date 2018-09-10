<?php
require_once dirname(__FILE__) . '/../../../config.php';
global $DB;

$sql_query = "TRUNCATE TABLE {talentospilos_inst_cohorte} RESTART IDENTITY";
$result_truncate_est_estado = $DB->execute($sql_query);

print_r($result_truncate_est_estado);