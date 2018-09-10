<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;
echo $DB->execute('DELETE FROM {talentospilos_est_estadoases}');
echo $DB->execute('DELETE FROM {talentospilos_estados_ases}');