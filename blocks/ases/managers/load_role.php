<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);

echo json_encode($result);