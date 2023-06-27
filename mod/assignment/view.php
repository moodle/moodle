<?php

require_once("../../config.php");

require_login();
$PAGE->set_context(context_system::instance());

print_error('assignmentneedsupgrade', 'assignment', '');
