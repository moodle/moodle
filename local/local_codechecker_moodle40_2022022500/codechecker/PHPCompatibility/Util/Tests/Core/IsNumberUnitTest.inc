<?php

/*
 * Make sure that numbers are correctly identified.
 *
 * The below should *NOT* be recognized as numbers.
 */
 
/* Case 1 */
$a = [];

/* Case 2 */
$a = - $b;

/* Case 4 */
$a = +;

/* Case 5 */
$a = new SomeClass;

/* Case 6 */
$a = 1-;

/* Case 7 */
$a = 1.23-;

/* Case 8 */
$a = 1.23 - 1;

/* Case 9 */
$a = 5 * 8;

/* Case 10 */
$a = '10 things' . ' or nothing';

/*
 * Make sure that zero numbers are correctly identified.
 *
 * The below should be recognized as numbers (integers).
 */

/* Case ZI1 */
$a = 0;

/* Case ZI2 */
$a = +0;

/* Case ZI3 */
$a = - false;

/* Case ZI4 */
$a = '0';

/* Case ZI5 */
$a = - '        0 things';

/* Case ZI6 */
$a = null;

/* Case ZI7 */
$a = - 'not a numeric string';


/*
 * Make sure that zero numbers are correctly identified.
 *
 * The below should be recognized as numbers (integers).
 */

/* Case ZF1 */
$a = 0.0;

/* Case ZF2 */
$a = - 0.0000000000;


/*
 * Make sure that negative numbers are correctly identified.
 *
 * The below should be recognized as negative numbers (integers).
 */

/* Case I1 */
$a = 1;

/* Case I2 */
$a = -10;

/* Case I3 */
$a = /* */     +          10;

/* Case I4 */
$a = - /* comment */ 10;

/* Case I5 */
$a = +
    // comment
	10;

/* Case I6 */
$a = '10';

/* Case I7 */
$a = +  /* comment */ "10";

/* Case I8 */
$a = - '10 barbary lane'; // PHP 7.1+: Non well-formed numeric value, but will still work.

/* Case I9 */
$a = <<<EOT
10
EOT;

/* Case I10 */
// PHP will only look at the first line!
$a = - <<<'EOT'
1
0
EOT;

/* Case I11 */
$a = '        10 barbary lane';

/* Case I12 */
$a = + '
        10 barbary lane';

/* Case I13 */
$a = - '0xCC00F9'; // Though the behaviour is different between PHP 5 vs PHP 7.

/* Case I14 */
$a = - true;

/* Case I15 */
$a = + '  0123 things';

/* Case I16 */
$a = -+-+10;

/*
 * Make sure that numbers are correctly identified.
 *
 * The below should be recognized as numbers (floats).
 */

/* Case F1 */
$a = 1.23;

/* Case F2 */
$a = -10.123;

/* Case F3 */
$a = +          10.123;

/* Case F4 */
$a = - /* comment */ 10.123;

/* Case F5 */
$a = +
    // phpcs:ignore Standard.Category.Sniff -- testing handling of PHPCS annotations.
	10.123;

/* Case F6 */
$a = '10.123';

/* Case F7 */
$a = +  /* comment */ "10.123";

/* Case F8 */
$a = - '10E3 barbary lane'; // PHP 7.1+: Non well-formed numeric value, but will still work.

/* Case F9 */
$a = - '10e8 barbary lane'; // PHP 7.1+: Non well-formed numeric value, but will still work.

/* Case F10 */
$a = <<<EOT
10.123
EOT;

/* Case F11 */
$a = +'0.123';
