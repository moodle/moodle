<?php

class StaticAllThings {
	const BAR = 'xyz';

    public static $foo = 123;

    public static function test() {
        return 'abc';
    }
}

// OK in PHP < 5.3.
echo StaticAllThings::BAR;
echo StaticAllThings :: $foo;
echo StaticAllThings::test();

// Not OK in PHP < 5.3.
$object = new StaticAllThings();
echo $object::BAR;
echo $object :: $foo;
echo $object::test();

$a = 'StaticAllThings';
echo $a::BAR;
echo $a::$foo;
echo $a :: test();

$b = array(
    'abc' => 'StaticAllThings',
);
echo $b['abc']::BAR;
echo $b['abc']
     :: $foo;
echo $b['abc']::test();

// PHP 7.0+
$c = new stdClass();
$c->name = 'StaticAllThings';

echo $c->name::BAR;
echo $c->name::$foo;
echo $c->name::test();

class TestKeyword extends StaticAllThings {
    public static $bar = 456;

    public function testIt() {
        // OK in PHP < 5.3.
        echo TestKeyword::$bar;
        echo StaticAllThings::$foo;

        echo self::$bar;
        echo parent::$foo;
        // Not OK, but this is because of late static binding not being supported in PHP 5.2,
        // which is reported by another sniff, so no need to report here.
        echo static::$foo;

        // Not OK in PHP < 5.3.
        $name = __CLASS__;
        echo $name::$bar;
        echo $name::$foo;
    }
}
