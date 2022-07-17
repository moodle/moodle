<?php

// Invalid, but not the concern of this sniff.
echo parent::class;

function test() {
    echo parent::class;
}

// Valid use of the parent keyword.
class ExtendedClass extends ParentClass
{
    public function test() {
        echo parent::class;
        $this->foo = parent::$foo;
        return parent::test();
    }
}

class PlainClass {
    public function create_anon_class() {
        $anon = new class() extends ParentClass {
            public function test() {
                echo parent::class;
                $this->foo = parent::$foo;
                return parent::test();
            }
        };
    }
}

// PHP 7.4: Deprecated parent in class without parent.
class ParentClass
{
    public function test() {
        echo parent::class;
        $this->foo = parent::$foo;
        return parent::test();
    }
}

class ImplementedClass implements SomeInterface
{
    public function test() {
        echo parent::class;
        $this->foo = parent::$foo;
        return parent::test();
    }
}

// Test correct handling of nested classes.
class NestingStuff extends Nested {
    public function create_anon_class() {
        return new class() {
            public function test() {
                echo parent::class;
                $this->foo = parent::$foo;
                return parent::test();
            }
        };
    }
}

// Intentional parse error. This has to be the last test in the file.
class SomeClass extends Something
    public function test() {
        return parent::test();
    }
