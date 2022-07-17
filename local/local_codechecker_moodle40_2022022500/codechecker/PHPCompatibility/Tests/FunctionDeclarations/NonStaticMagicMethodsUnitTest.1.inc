<?php

class Plain
{
    function __get($name) {}
    function __set($name, $value) {}
    function __isset($name) {}
    function __unset($name) {}
    function __call($name, $arguments) {}
    static function __callStatic($name, $arguments) {}
    function __sleep() {}
    function __toString() {}
    static function __set_state($properties) {}
}

class Normal
{
    public function getId() {}
    public function __get($name) {}
    public function __set($name, $value) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public function __call($name, $arguments) {}
    public static function __callStatic($name, $arguments) {}
    public function __sleep() {}
    public function __toString() {}
    public static function __set_state($properties) {}
}

class WrongVisibility
{
    private function __get($name) {}
    protected function __set($name, $value) {}
    private function __isset($name) {}
    protected function __unset($name) {}
    private function __call($name, $arguments) {}
    protected static function __callStatic($name, $arguments) {}
    private function __sleep() {}
    protected function __toString() {}
}

class WrongStatic
{
    static function __get($name) {}
    static function __set($name, $value) {}
    static function __isset($name) {}
    static function __unset($name) {}
    static function __call($name, $arguments) {}
    function __callStatic($name, $arguments) {}
    function __set_state($properties) {}
}

class AlternativePropertyOrder
{
    static public function __get($name) {} // Bad: static.
    static protected function __set($name, $value) {} // Bad: static & protected.
    static private function __isset($name) {} // Bad: static & private.
    static public function __callStatic($name, $arguments) {} // Ok.
}

class StackedStaticPrivate
{
    static
    private
    function
    __get($name) {}
}

interface PlainInterface
{
    function __get($name);
    function __set($name, $value);
    function __isset($name);
    function __unset($name);
    function __call($name, $arguments);
    static function __callStatic($name, $arguments);
    function __sleep();
    function __toString();
    static function __set_state($properties);
}

interface NormalInterface
{
    public function getId();
    public function __get($name);
    public function __set($name, $value);
    public function __isset($name);
    public function __unset($name);
    public function __call($name, $arguments);
    public static function __callStatic($name, $arguments);
    public function __sleep();
    public function __toString();
    public static function __set_state($properties);
}

interface WrongVisibilityInterface
{
    protected function __get($name);
    private function __set($name, $value);
    protected function __isset($name);
    private function __unset($name);
    protected function __call($name, $arguments);
    private static function __callStatic($name, $arguments);
    protected function __sleep();
    private function __toString();
}

interface WrongStaticInterface
{
    static function __get($name);
    static function __set($name, $value);
    static function __isset($name);
    static function __unset($name);
    static function __call($name, $arguments);
    function __callStatic($name, $arguments);
    function __set_state($properties);
}

// Anonymous classes.
$plain = new class
{
    function __get($name) {}
    function __set($name, $value) {}
    function __isset($name) {}
    function __unset($name) {}
    function __call($name, $arguments) {}
    static function __callStatic($name, $arguments) {}
    function __sleep() {}
    function __toString() {}
    static function __set_state($properties) {}
}

$normal = new class
{
    public function getId() {}
    public function __get($name) {}
    public function __set($name, $value) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public function __call($name, $arguments) {}
    public static function __callStatic($name, $arguments) {}
    public function __sleep() {}
    public function __toString() {}
    public static function __set_state($properties) {}
}

$wrongVisibility = new class
{
    private function __get($name) {}
    protected function __set($name, $value) {}
    private function __isset($name) {}
    protected function __unset($name) {}
    private function __call($name, $arguments) {}
    protected static function __callStatic($name, $arguments) {}
    private function __sleep() {}
    protected function __toString() {}
}

$wrongStatic = new class
{
    static function __get($name) {}
    static function __set($name, $value) {}
    static function __isset($name) {}
    static function __unset($name) {}
    static function __call($name, $arguments) {}
    function __callStatic($name, $arguments) {}
    function __set_state($properties) {}
}

// PHP 7.4: new __serialize(), unserialize().
$normal = new class
{
    public function __serialize() {}
    public function __unserialize($data) {}
}

class PHP74WrongVisibility
{
    protected function __serialize() {}
    private function __unserialize($data) {}
}

interface PHP74WrongStatic
{
    public static function __serialize();
    static public function __unserialize($data);
}

// More magic methods.
class MoreNormal
{
    public function __construct() {}
    public function __destruct() {}
    public function __clone() {}
    public function __debugInfo() {}
    public function __invoke() {}
}

class MoreWrongVisibility
{
    private function __destruct() {}
    protected function __debugInfo() {}
    private function __invoke() {}
    protected static function __set_state() {}
}

class MoreWrongStatic
{
    static function __construct() {}
    static function __destruct() {}
    static function __clone() {}
    static function __debugInfo() {}
    static function __invoke() {}
}
