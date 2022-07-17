<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Let's try various deprecated functions.

// Moodle own ones.
print_error('error');

// PHP internal ones.
print_r(mbsplit("\s", "hello world"));
