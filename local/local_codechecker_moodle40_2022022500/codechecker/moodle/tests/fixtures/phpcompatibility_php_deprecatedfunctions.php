<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Let's try various deprecated functions.
$d = ereg_replace(call_user_method(mysql_escape_string('oh, my!')));
// Fair enough.

