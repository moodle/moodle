<?php

/**
 * These magic methods should not be flagged. Introduced prior to PHP 5.
 */
class MyOkClass
{
    public function __call($name, $arguments) {}
    public function __set($name, $value) {}
    public function __sleep() {}
    public function __wakeup() {}
    public function __clone() {}
}

/**
 * These magic methods should all be flagged.
 */
class MyClass
{
    public function __construct() {}
    public function __destruct() {}
    public function __get($name) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public static function __set_state($properties) {}
    public function __toString() {}
    public static function __callStatic($name, $arguments) {}
    public function __invoke($x) {}
    public function __debugInfo() {}
}

interface MyInterface
{
    public function __get($name);
    public function __isset($name);
    public function __unset($name);
    public static function __set_state($properties);
    public function __toString();
    public static function __callStatic($name, $arguments);
    public function __invoke($x);
    public function __debugInfo();
}

/*
 * Test against false positives. No error when outside class scope.
 */
function __get($name) {}
function __isset($name) {}
function __unset($name) {}
function __set_state($properties) {}
function __toString() {}
function __callStatic($name, $arguments) {}
function __invoke($x) {}
function __debugInfo() {}

/*
 * Magic methods in anonymous classes.
 */
$a = new class
{
    public function __get($name) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public static function __set_state($properties) {}
    public function __toString() {}
    public static function __callStatic($name, $arguments) {}
    public function __invoke($x) {}
    public function __debugInfo() {}
}

/*
 * PHP 7.4: new (un)serialize magic methods.
 */
function __serialize() {} // OK, not in OO scope.
function __unserialize($data) {} // OK, not in OO scope.

class PHP74NewMagic {
    public function __serialize() {}
    public function __unserialize($data) {}
}
