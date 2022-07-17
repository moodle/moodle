<?php

/*
 * Valid cross-version.
 */
list($id1, $name1) = $data[0];

foreach ($data as list($id, $name)) {}

list($a, list($b, $c)) = array(1, array(2, 3));

list($foo, $bar) = list($baz, $bat) = [$a, $b];

/*
 * PHP 7.1: symmetric array destructuring.
 */
[$id1, $name1] = $data[0];
[$a, $b, $c] = [1, 2 => 'x', 'z' => 'c'];
[$a, [$b]] = array(new stdclass, array(new stdclass));

foreach ($data as [$id, $name]) {}

[[$a, $b], [$c, $d]] = [[1, 2], [3, 4]];

[$foo, $bar] = [$baz, $bat] = [$a, $b];

// Mixing long and short list syntax. Parse error, but that's not our concern.
[list($a, $b), list($c, $d)] = [[1, 2], [3, 4]];

// List does not contain variables.
[42] = [1];
