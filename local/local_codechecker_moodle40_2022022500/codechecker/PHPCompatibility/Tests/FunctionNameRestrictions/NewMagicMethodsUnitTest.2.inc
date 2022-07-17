<?php

trait MyTrait
{
    public function __get($name) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public static function __set_state($properties) {}
    public function __toString() {}
    public static function __callStatic($name, $arguments) {}
    public function __invoke($x) {}
    public function __debugInfo() {}
    public function __serialize() {}
    public function __unserialize($data) {}
    public function __construct() {}
    public function __destruct() {}
}
