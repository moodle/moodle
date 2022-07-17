<?php

/* Case 1 */
array() = $b;

/* Case 2 */
list( $a ) = $b;

/* Case 3 */
$b[] = $c;

/* Case 4 */
[$b] = $c;

/* Case 5 */
[$a, $b] = $c;

/* Case 6 */
[$a, /* Case 7 */ [$b]] = array(new stdclass, array(new stdclass));

/* Case 8 */
foreach ($data as [$id, $name]) {}

/* Case 9 */
$foo = [$baz, $bat] = [$a, $b];

/* Case 10 */
["id" => $id1, "name" => $name1] = $data[0];

/* Case 11 */
foreach ($data as ["id" => $id, "name" => $name]) {}

// Nested short list syntaxes.
[$x, /* Case 12 */ [], $y] = $a;

[$x, [ $y, /* Case 13 */ [$z]], $q] = $a;

/* Case 14 */
[$a, /* Case 15 */ [$b]] = $array;

/* Case 16 */
[
	/* Case 17 */
	["x" => $x1, "y" => $y1],
	/* Case 18 */
	["x" => $x2, "y" => $y2],
	/* Case 19 */
	["x" => $x3, "y" => $y3],
] = $points;

/* Case 20 */
// Invalid list as it doesn't contain variables, but it is short list syntax.
[42] = [1];

/* Case 21 */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
[list($a, $b), list($c, $d)] = [[1, 2], [3, 4]];

/* Case 22 */
// Parse error, but not short list syntax.
use Something as [$a];

/* Case 23 */
$var[$x] = $a;

/* Case 24 */
$var->prop[$x] = $a;

/* Case 25 */
${$var}[$x] = $a;

/* Case 26 */
$var[][$x] = $a;

/* Case final */
[$a, $b
