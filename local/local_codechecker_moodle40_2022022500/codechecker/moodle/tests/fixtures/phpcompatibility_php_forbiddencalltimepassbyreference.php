<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

$a = my_function($a1); // Ok.
$a = my_function($a1, $a2); // Ok.
$b = my_function(&$b1); // Wrong.
$b = my_function($b1, &$b2); // Wrong.
