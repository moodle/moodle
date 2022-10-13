<?php

/*
 * Forbidden global variable variables.
 */

// OK.
global $test;

// Multiple variables on one line - should throw warnings for the last two.
global $test, $$test, $$$test;

// OK: simple variables.
global $var,
    $var['key'], // Always parse error, but not our concern.
	$var->key, // Always parse error, but not our concern.
	$$var, // Warning about using non-bare variable.
	$$$var; // Warning about using non-bare variable.

// Error: complex variables, not allowed since PHP 7.0.
global $$var['key'],
    $$var->key,
	$$var->key['key'],
    $$var::$staticvar,
	$$var::$staticvar['key'];

// Test to make sure that the sniff works code-style independently.
// Whitespace and comments are allowed between the $ and $var.
global $   $test->bar, $
	// Comment.
	$test->bar;

// Complex variables using curly braces are fine in both PHP 5.x as well as PHP 7.0+.
// These will all throw a warning about using non-bare variables.
global ${$var['key']},
    ${$var->key},
	${$var->key['key']},
    ${$var::$staticvar},
	${$var::$staticvar['key']};

// Warning for using something other than a bare variable.
global ${$test};
global ${"name"};
global ${"name_$type"};
global ${$var['key1']['key2']};
global ${$obj->$bar};
global ${$obj->{$var['key']}};

// Variant on issue #460, the last two should throw a warning.
global $test,
    $$test,
	$$$test ?> <?php

// Live coding. Ignore.
global $$test
