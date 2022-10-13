<?php

class CrossVersionValid
{
    public function __toString() {
        return $this->foo;
    }
}

// Irrelevant, not the magic method.
function __toString($param) {
    return $foo;
}

// PHP 5.3: The __toString() magic method can no longer accept arguments.
interface MyInterface {
    public function __toString($param);
}

abstract class AbstractClass {
    abstract public function __toString($param);
}

class MyClass {
    public function __toString($param) {
        return $this->foo;
    }
}

trait MyTrait {
    public function __toString($param) {
        return $this->foo;
    }
}

$anon = new class() {
    public function __toString($param) {
        return $this->foo;
    }
};
