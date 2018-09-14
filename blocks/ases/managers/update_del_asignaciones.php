<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
global $DB;
$updateAsig = "DELETE FROM {talentospilos_monitor_estud}";
echo $DB->execute($updateAsig);
