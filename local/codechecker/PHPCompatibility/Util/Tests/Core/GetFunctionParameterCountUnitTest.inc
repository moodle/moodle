<?php
/*
 * Count function parameters.
 */
/* Case S1 */
myfunction(1);
/* Case S2 */
myfunction( 1, 2 );
/* Case S3 */
myfunction(1, 2, 3);
/* Case S4 */
myfunction(1, 2, 3, 4);
/* Case S5 */
myfunction(1, 2, 3, 4, 5);
/* Case S6 */
myfunction(1, 2, 3, 4, 5, 6);
/* Case S7 */
myfunction( 1, 2, 3, 4, 5, 6, true );

/*
 * Propertly deal with nested parenthesis.
 * Also see Github issues #111 / #114 / #151.
 */
/* Case S8 */
dirname( dirname( __FILE__ ) ); // 1
/* Case S9 */
(dirname( dirname( __FILE__ ) )); // 1
/* Case S10 */
dirname( plugin_basename( __FILE__ ) ); // 1
/* Case S11 */
dirname( plugin_basename( __FILE__ ), 2 ); // 2
/* Case S12 */
unserialize(trim($value, "'")); // 1
/* Case S13 */
dirname(str_replace("../","/", $value)); // 1
/* Case S14 */
dirname(str_replace("../", "/", trim($value))); // 1
/* Case S15 */
dirname( plugin_basename( __FILE__ ), trim( 2 ) ); // 2
/* Case S16 */
mktime($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6
/* Case S17 */
mktime(0, 0, 0, date('m'), date('d'), date('Y')); // 6
/* Case S18 */
mktime(0, 0, 0, date('m'), date('d') - 1, date('Y') + 1); // 6
/* Case S19 */
mktime(0, 0, 0, date('m') + 1, date('d'), date('Y')); // 6
/* Case S20 */
mktime(date('H'), 0, 0, date('m'), date('d'), date('Y')); // 6
/* Case S21 */
mktime(0, 0, date('s'), date('m'), date('d'), date('Y')); // 6
/* Case S22 */
mktime(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6

/*
 * Testing multi-line function calls.
 */
/* Case S23 */
filter_input_array(
    INPUT_POST,
    $args,
    false
); // 3

/* Case S24 */
gettimeofday (
               true
             ); // 1

/*
 * Deal with unnecessary comma after last param.
 */
/* Case S25 */
json_encode( array(), );

/*
 * Issue #211 - deal with short array syntax within parameters.
 */
/* Case S26 */
json_encode(['a' => 'b',]);
/* Case S27 */
json_encode(['a' => $a,]);
/* Case S28 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []));
/* Case S29 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b, 'c' => $c,] : []));
/* Case S30 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d] : []));
/* Case S31 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d,] : []));
/* Case S32 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d, $c => 'c'] : []));
/* Case S33 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []) + ['c' => $c, 'd' => $d,]);
/* Case S34 */
json_encode(['a' => 'b', 'c' => 'd',]);
/* Case S35 */
json_encode(['a' => ['b',],]);
/* Case S36 */
json_encode(['a' => ['b' => 'c',],]);
/* Case S37 */
json_encode(['a' => ['b' => 'c',], 'd' => ['e' => 'f',],]);
/* Case S38 */
json_encode(['a' => $a, 'b' => $b,]);
/* Case S39 */
json_encode(['a' => $a,] + ['b' => $b,]);
/* Case S40 */
json_encode(['a' => $a] + ['b' => $b, 'c' => $c,]);
/* Case S41 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d]);
/* Case S42 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d,]);
/* Case S43 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d, $c => 'c']);
/* Case S44 */
json_encode(['a' => $a, 'b' => $b,] + ['c' => $c]);
/* Case S45 */
json_encode(['a' => $a, 'b' => $b,] + ['c' => $c,]);
/* Case S46 */
json_encode(['a' => $a, 'b' => $b, 'c' => $c]);
/* Case S47 */
json_encode(['a' => $a, 'b' => $b, 'c' => $c,] + ['c' => $c, 'd' => $d,]);

/*
 * Even though a language construct and not a function call, the functions should
 * work just as well for long arrays.
 */
/* Case A1 */
$foo = array( 1, 2, 3, 4, 5, 6, true );
/* Case A2 */
$foo = array(str_replace("../", "/", trim($value))); // 1
/* Case A3 */
$foo = array($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6
/* Case A4 */
$foo = array(0, 0, date('s'), date('m'), date('d'), date('Y')); // 6
/* Case A5 */
$foo = array(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6
/* Case A6 */
$foo = array('a' => $a, 'b' => $b, 'c' => $c);
/* Case A7 */
$foo = array('a' => $a, 'b' => $b, (isset($c) ? 'c' => $c : null));
/* Case A8 */
$foo = array(0 => $a, 2 => $b, (isset($c) ? 6 => $c : 6 => null));

// Same goes for short arrays.
/* Case A9 */
$bar = [ 1, 2, 3, 4, 5, 6, true ];
/* Case A10 */
$bar = [str_replace("../", "/", trim($value))]; // 1
/* Case A11 */
$bar = [$stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]]; // 6
/* Case A12 */
$bar = [0, 0, date('s'), date('m'), date('d'), date('Y')]; // 6
/* Case A13 */
$bar = [some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6]; // 6
/* Case A14 */
$bar = ['a' => $a, 'b' => $b, 'c' => $c];
/* Case A15 */
$bar = ['a' => $a, 'b' => $b, (isset($c) ? 'c' => $c : null)];
/* Case A16 */
$bar = [0 => $a, 2 => $b, (isset($c) ? 6 => $c : 6 => null)];
