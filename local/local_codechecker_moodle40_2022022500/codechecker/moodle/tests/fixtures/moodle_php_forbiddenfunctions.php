<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Let's try various forbidden functions.
$b = sizeof($a);
delete($a);
// Some more, usually development leftovers.
error_log('I should not exist');
print_r('I should not exist');
print_object('I should not exist');
// These are dangerous and forbidden by coding style. Yes we have some uses (lang strings)
// but those are the exception confirming the rule ;-)
$a = extract($b);
$a = eval($b);
goto a;
a: echo 'Goto labels, oh my!'
b:
echo 'More goto labels, re-oh my!'
// Fair enough.

