<?php

/*
 * This is fine pre-PHP 5.4.
 */
$a = new Bar;
$a = new Bar();
$a = (new Bar());

// Deprecated new by reference but not our concern.
$a = &new Bar();
$a = (&new Bar());

$b = $a->foo();

function_call( new Bar(), $b->something, $c->method(), $d['a']);

$container->register('foo', 'FooClass')->addArgument(new \stdClass())->setPublic(true);

$c = clone $a;
$d = (clone $a);
$date1 = clone ($_date1 <= $_date2 ? $_date1 : $_date2);

// Still not supported:
$X = new foo->setX(10)->getX();
var_dump(new foo()->bar());
var_dump(new foo()->baz()->x);
var_dump(new foo()->baz()->baz()->bar());
var_dump(new foo()->xyz());

try {
	$X = new foo->Inexistent(3);
} catch (Exception $e) {
	var_dump($e->getMessage());
}


/*
 * PHP 5.4: class member access on instantiation.
 */
(new foo())->bar();
(new $foo())->bar;
(new foo)[0];

$a = (new Foo( array(1, array(4, 5), 3) ))[1][0]; // Should give two errors.

var_dump((new Bar)->y);
$closure = function() {return (new $x)->y;};
$foo = (new $bar->y)->x);

return clone (new bar)->z;
$clone = clone (new bar)->getZ();

$X = (new foo)->setX(10)->getX();

var_dump(
	(new foo())
		-> bar()
);
var_dump((new foo())->baz()->x);
var_dump((new foo())->baz()->baz()->bar());
var_dump((new foo())->xyz());

try {
	$X = (new foo)->Inexistent(3);
} catch (Exception $e) {
	var_dump($e->getMessage());
}

        $container->setDefinition('child', (new ChildDefinition('parent'))->setArguments(array(
            1,
            'index_0' => 2,
            'foo' => 3,
        )));

        $factory->setFactory(array((new Definition('Baz\Qux'))->setFactory(array(null, 'getInstance')), 'create'));

        $def->setInstanceofConditionals(array(
            parent::class => (new ChildDefinition(''))->addMethodCall('foo', array('bar')),
        ));

        $def = (new ChildDefinition('parent'))->setClass(self::class);

        $container->register('foo', 'Class1')
            ->setPublic(true)
            ->addArgument((new Definition('Class2'))
                ->addArgument(new Definition('Class2'))
            )
        ;

        return $this->services['service_from_anonymous_factory'] = (new \Bar\FooClass())->getInstance();

/*
 * As of PHP 7.0, the new object can be assigned to a variable within the parentheses.
 */
$b = ($a = new foo())->bar();

/*
 * PHP 7.0: class member access on object cloning.
 */
$b = (clone $foo)->bar();

echo (clone $iterable)[20];

$date1 = (clone ($_date1 <= $_date2 ? $_date1 : $_date2))->format('Y');

/*
 * PHP 7.0: class member access on instantiation using curly braces.
 * This "silently" started working in PHP 7.0. See: https://3v4l.org/KsEgH
 */
(new foo){0};
$a = (new Foo( array(1, array(4, 5), 3) )){1}{0}; // Should give two errors.

echo (clone $iterable){20};

// Mixing access with square brackets and curly braces.
$a = (new Foo( array(1, array(4, 5), 3) )){1}[0]; // Should give two errors (depending on supported PHP version).
$a = (clone $iterable)[1]{0}; // Should give two errors (depending on supported PHP version).
