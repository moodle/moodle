<?php

require_once('../../../config.php');
require_once('../../weblib.php');

add_to_log(0, 'adodb', 'intrusion attempt', 'lib/adodb/tests/tmssql.php');
trigger_error('SECURITY WARNING: intrusion attempt against lib/tests/tmssql.php from ' . getremoteaddr());
error('SECURITY WARNING: logged intrusion attempt against lib/adodb/tests/tmssql.php');

?>