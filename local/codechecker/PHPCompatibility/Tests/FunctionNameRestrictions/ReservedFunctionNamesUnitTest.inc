<?php

/* Test for magic functions */
class Magic_Test {
    function __construct() {}
    function __destruct() {}
    function __call() {}
    function __callStatic() {}
    function __get() {}
    function __set() {}
    function __isset() {}
    function __unset() {}
    function __sleep() {}
    function __wakeup() {}
    function __toString() {}
    function __set_state() {}
    function __clone() {}
    function __invoke() {}
    function __debugInfo() {}
    function __autoload() {}
    function __myFunction() {}
    function __my_function() {}
}

function __construct() {}
function __destruct() {}
function __call() {}
function __callStatic() {}
function __get() {}
function __set() {}
function __isset() {}
function __unset() {}
function __sleep() {}
function __wakeup() {}
function __toString() {}
function __set_state() {}
function __clone() {}
function __invoke() {}
function __debugInfo() {}
function __autoload() {}
function __myFunction() {}
function __my_function() {}

interface Foo
{
    function __call() {}
}

class Magic_Case_Test {
    function __Construct() {}
    function __isSet() {}
    function __tostring() {}
}
function __autoLoad() {}

class Foo extends \SoapClient
{
    public function __soapCall() {
        // body
    }
}

function _singleUnderscore() {} // Ok.

class single {
    public function _singleUnderscore() {} // Ok.
}

function ___tripleUnderscore() {} // Ok.

class triple {
    public function ___tripleUnderscore() {} // Ok.
}

/* Magic methods in anonymous classes. */
$a = new class {
    function __construct() {}
    function __destruct() {}
    function __call() {}
    function __callStatic() {}
    function __get() {}
    function __set() {}
    function __isset() {}
    function __unset() {}
    function __sleep() {}
    function __wakeup() {}
    function __toString() {}
    function __set_state() {}
    function __clone() {}
    function __invoke() {}
    function __debugInfo() {}
    function __autoload() {}
    function __myFunction() {}
    function __my_function() {}
}

// Closures shouldn't trigger any errors.
$b = function ($a) {};

class ClassContainingClosure {
    public function methodContainingClosure() {
        $a = function($c) {};
    }
}

class Nested {
    public function __getAnonymousClass() {
        return new class() {
            public function __nested() {
                echo 'In method nested!';
            }
        };
    }
}

/**
 * Function description.
 *
 * @since 1.2.3
 * @deprecated 2.3.4
 *
 * @return void
 */
function __deprecatedFunction() {}

class Deprecated {
    /**
     * Function description.
     *
     * @since 1.2.3
     * @deprecated 2.3.4
     *
     * @return void
     */
    public static function __deprecatedMethod() {}
}
