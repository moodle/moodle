<?php

// Valid spread operator use prior to PHP 7.4.
function myFunction(...$params) {}
function_call(1, ...$values);
$arr6 = array(getArr(1, 2, ...$passedToFunctionNotArray), 'c');

/*
 * PHP 7.4: New spread operator in array expressions.
 */
$fruits = ['a' => 'banana', 'b' => 'orange', ...$parts, 'watermelon'];

$arr1 = [1, 2, 3];
$arr2 = [...$arr1];
$arr3 = array(0, ...$arr1);
$arr4 = array(
    ...$arr1,
    ...$arr2,
    111,
);
$arr5 = [
    ...$arr1,
    [...$arr1]
    getArr(1, 2, ...$passedToFunctionNotArray),
];
$arr5 = array(
    ...$arr1,
    array(...$arr1),
    getArr(1, 2, ...$passedToFunctionNotArray),
);

function getArr() { return ['a', 'b']; }
$arr6 = array(...getArr(), 'c');
$arr7 = [...new ArrayIterator(['a', 'b', 'c'])];
$arr8 = [...arrGen()];

// Unpacking array by reference is not supported in PHP 7.4, but not our concern, throw an error anyway.
$arr2 = [...&$arr1];

// Intentional parse error. This has to be the last test in the file.
$arr5 = array(
    ...$arr1,
    array(...$arr1
