<?php

trait PlainTrait
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
    function __serialize() {}
    function __unserialize($data) {}
}

trait NormalTrait
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
    public function __serialize() {}
    public function __unserialize($data) {}
}

trait WrongVisibilityTrait
{
    private function __get($name) {}
    protected function __set($name, $value) {}
    private function __isset($name) {}
    protected function __unset($name) {}
    private function __call($name, $arguments) {}
    protected static function __callStatic($name, $arguments) {}
    private function __sleep() {}
    protected function __toString() {}
    private function __serialize() {}
    protected function __unserialize($data) {}
}

trait WrongStaticTrait
{
    static function __get($name) {}
    static function __set($name, $value) {}
    static function __isset($name) {}
    static function __unset($name) {}
    static function __call($name, $arguments) {}
    function __callStatic($name, $arguments) {}
    function __set_state($properties) {}
    public static function __serialize() {}
    static public function __unserialize($data) {}
}

