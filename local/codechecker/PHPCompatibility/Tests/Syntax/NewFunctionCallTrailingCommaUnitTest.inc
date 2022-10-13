<?php

/*
 * Allowed pre-PHP 7.3.
 */
isset($foo, $bar, $baz);
unset($foo, $bar, $baz);
myFunction($foo, $bar);
$myClosure($foo, $bar);

/*
 * PHP 7.3 trailing comma's in function calls + isset + unset.
 */
// Isset & unset.
unset($foo, $bar,);
var_dump(isset($foo, $bar,));

unset(
    $foo,
    $bar,
    $baz,
);

// Function calls, including calls to methods and closures.
echo $twig->render(
    'index.html',
    compact('title', 'body', 'comments',), // x2.
);

$newArray = \array_merge(
    $arrayOne,
    $arrayTwo,
    ['foo', 'bar'],
);

var_dump($whatIsInThere, $probablyABugInThisOne, $oneMoreToCheck,);

$text = SomeNameSpace\PartTwo\PartThree\functionName($en, 'comma', 'Jane',);

$foo = new Foo( 'constructor', 'bar', );

$foo->bar(
  'method',
  'bar',
);

$foo( 'invoke','bar' , );

MyNamespace\Foo::bar('method','bar',);

$bar = function(...$args) {};
$bar('arg1', 'arg2',);

/*
 * Still not allowed.
 */
// Trailing comma in function declaration.
function bar($a, $b,) {} // Parse error, but not our concern.
$closure = function ($a, $b,) {} // Parse error, but not our concern.

// Free-standing comma.
foo(,); // Parse error, but throw an error anyway.

// Multiple trailing comma's.
foo('function', 'bar',,); // Parse error, but throw an error anyway.

// Leading comma.
foo(, 'function', 'bar'); // Parse error, but not our concern.

// List with trailing comma.
list($drink, $color, $power, ) = $info; // Parse error, but not our concern.
