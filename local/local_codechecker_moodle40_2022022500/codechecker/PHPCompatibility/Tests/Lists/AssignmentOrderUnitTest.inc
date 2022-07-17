<?php

/*
 * Same behaviour cross-version.
 */
list($id, $name) = $data;

foreach ($data as list($id, $name)) {}

list($a, list($b, $c)) = array(1, array(2, 3));

list( , , , ) = $data; // Empty list, not our concern and no variables to examine anyway.

/*
 * PHP 7.0: list() no longer assigns variables in reverse order.
 */
list($a[], $a[], $a[]) = [1, 2, 3];
list($a[], $a[0], $a[]) = [1, 2, 3];
list($a, $b, $a) = [1, 2, 3];
[$a->propA, $a->propB, $a      ->     propA] = [1, 2, 3];

list($a, list($b, $a)) = array(1, array(2, 3));

list(list($a, $b), list($b, $a)) = array(array(10, 11), array(2, 3));

// Also detect this in short list syntax.
[$a[], $a[], $a[]] = [1, 2, 3];
[$a[], $a[0], $a [ ] ] = [1, 2, 3];
[$a, $b, $a] = [1, 2, 3];
[$a->propA, $a->propB, $a->propA] = [1, 2, 3];

[$a, [$b, $a]] = array(1, array(2, 3));

[[$a, $b], [$b, $a]] = array(array(10, 11), array(2, 3));

// Also detect this in keyed lists.
list('name' => $a, 'id' => $b, 'field' => $a) = ['name' => 1, 'id' => 2, 'field' => 3];
['name' => $a, 'id' => $b, 'field' => $a] = ['name' => 1, 'id' => 2, 'field' => 3];

// Don't get confused with variable keys.
list($foo => $a, $bar => $b, 'field' => $c) = ['name' => 1, 'id' => 2, 'field' => 3];
list($b => $a, $a => $b, 'field' => $c) = ['name' => 1, 'id' => 2, 'field' => 3];

// Don't get confused when some of the entries are empty.
list( , $a, , $b, , $a, ,) = array[1, 2, 3, 4, 5, 6, 7, 8];
