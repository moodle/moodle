<?php

// PHP 7.1 Forbidden variable names in closure use statements.
$f = function () use ($_SERVER) {};
$f = function () use ($_REQUEST) {};
$f = function () use ($GLOBALS) {};
$f = function () use ($this) {};
$f = function ($param) use ($param) {};
$f = function ($param) use (&$param) {};
$f = function ($a, $b, $c, $d, $e) use ($c) {};
$f = function ($a, $b, $c, $d, $e) use ($b, $d) {};

/*
 * Check against false positives.
 */

// Empty use statement.
$f = function () use () {};
$f = function () use ( /*nothing here*/ ) {};

// Variable names are case-sensitive.
$f = function () use ($_server) {};
$f = function () use ($THIS) {};
$f = function ($param) use ($Param) {};

// Not the same variable name.
$f = function ($par, $parameter) use ($param) {};

// Use statements not used with a closure.
namespace Something;
use function some\namespace\fn_a as fn_a;
use const some\namespace\ConstA;
use some\namespace\{ClassA, ClassB, ClassC as C};

class ezcReflectionMethod extends ReflectionMethod {
    use ezcReflectionReturnInfo { sayHello as private myPrivateHello; };
    /* ... */
}
