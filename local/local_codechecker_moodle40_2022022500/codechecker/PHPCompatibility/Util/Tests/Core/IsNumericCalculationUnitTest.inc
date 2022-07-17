<?php

/*
 * Make sure that numeric calculations are correctly identified.
 *
 * The below should *NOT* be recognized as numeric calculations.
 */

/* Case A1 */
$a = 10;

/* Case A2 */
$a = [] + array();

/* Case A3 */
$a = $b + $c;

/* Case A4 */
$a = 'not a numeric string' . 'nor this';

/* Case A5 */
$a = 10 << 2;


/*
 * The below should be recognized as numeric calculations.
 */

/* Case B1 */
$a = 10 * 5;

/* Case B2 */
$a = 10 + 5;

/* Case B3 */
$a = -10 - +-+5;

/* Case B4 */
$a = 10 + 5 * -3.2 - 20 / 2.1 % 1 ** 3;

/* Case B5 */
$b = - false + '0';

/* Case B6 */
$a = 10 + 'not a numeric string' * 3;

/* Case B7 */
$a = 10 * 3 + '123 numeric start of string';
