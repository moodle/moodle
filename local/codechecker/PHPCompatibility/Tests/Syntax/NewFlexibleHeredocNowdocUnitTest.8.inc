<?php

/*
 * Test for errors.
 * Each test case using PHP 7.3 syntax has to be in its own file as otherwise they can't
 * be tested using PHP < 7.3.
 * Everything after the first test case in PHP < 7.3 will be tokenized as T_ENCAPSED_AND_WHITESPACE.
 */

// PHP 7.3+ follow on code allowed on the same line as the closing marker.
$values = [<<<END
a
b
c
END, 'd e f'];
