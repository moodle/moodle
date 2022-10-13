<?php

class LateStatic {
    private $bar;

    public function test() {
        self::foo(); // Ok.
        static::foo(); // Late static binding.
        echo static::$bar; // Late static binding.
    }

    public static function foo() {} // Ok.
}

static function testing() { // Ok.
    static $var; //Ok.
}

static::testing(); // Bad. Outside class scope.
