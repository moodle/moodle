<?php
/*
 * Tests for doesFunctionCallHaveParameters()
 *
 * Added after Github issue #120 / #152.
 */

/*
 * No parameters.
 */
/* Case S1 */
some_function();
/* Case S2 */
some_function(     );
/* Case S3 */
some_function( /*nothing here*/ );
/* Case S4 */
some_function(/*nothing here*/);

/*
 * Has parameters.
 */
/* Case S5 */
some_function( 1 );
/* Case S6 */
some_function(1,2,3);
/* Case S7 */
some_function(true);

/*
 * Even though a language construct and not a function call, the function should work just as well for arrays.
 */

/*
 * No parameters.
 */
/* Case A1 */
$foo = array();
/* Case A2 */
$foo = array(     );
/* Case A3 */
$foo = array( /*nothing here*/ );
/* Case A4 */
$foo = array(/*nothing here*/);
/* Case A5 */
$bar = [];
/* Case A6 */
$bar = [     ];
/* Case A7 */
$bar = [ /*nothing here*/ ];
/* Case A8 */
$bar = [/*nothing here*/];

/*
 * Has parameters.
 */
/* Case A9 */
$foo = array( 1 );
/* Case A10 */
$foo = array(1,2,3);
/* Case A11 */
$foo = array(true);
/* Case A12 */
$bar = [ 1 ];
/* Case A13 */
$bar = [1,2,3];
/* Case A14 */
$bar = [true];
