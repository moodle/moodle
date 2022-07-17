<?php

// Array type declaration - PHP 5.1+
function foo(array $a) {}
function foo( array   $a ) {} // Test extra spacing.

// Callable type declaration - PHP 5.4+
function foo(callable $a) {}

// Scalar type declarations - PHP 7.0+
function foo(bool $a) {}
function foo(int $a) {}
function foo(float $a) {}
function foo(string $a) {}

// Iterable type declaration - PHP 7.1+
function foo(iterable $a) {}

// (Very likely) Invalid type declarations - will be treated as class/interface names.
function foo(boolean $a) {}
function foo(integer $a) {}

class MyOtherClass extends MyClass {
    function foo(parent $a) {} // Correction: Valid as of PHP 5.2+.
    function bar(static $a) {}
}

// Class/interface type declaration - PHP 5.0+
function foo(stdClass $a) {}
function foo(DateInterval ...$a) {} // Combined with splat, PHP 5.6+.

// Class/interface type declaration with self keyword - PHP 5.2+
class MyClass {
    function foo(self $a) {}
}

function foo(self $a) {} // Invalid - not within a class.

namespace test {
    class foo {
        function foo(self $a) {}
    }

    function foo(self $a) {} // Invalid - not within a class.
}

// Ok: No function parameters or no type hints.
function foo() {}
function foo( $a, $b ) {}

// Type hints in closures.
function (callable $a) {}
function(int $a) {}

// Deal with nullable type hints.
function foo(?callable $a) {}
function foo(?int $a) {}

// Object type declaration - PHP 7.2+
function foo(object $a) {}

// Use of "parent" outside class scope.
function foo(parent $a) {} // Invalid - not within a class.

namespace test {
    function foo(parent $a) {} // Invalid - not within a class.
}
