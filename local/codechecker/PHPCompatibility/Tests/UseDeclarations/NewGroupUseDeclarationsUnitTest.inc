<?php

// Pre PHP 7 code
use ArrayObject;
use some\namespace\ClassA;
use some\namespace\ClassC as C;

use function some\namespace\fn_a;
use function My\Full\functionName as func;

use const some\namespace\ConstA;

use My\Full\Classname as Another, My\Full\NSname;

use A, B {
   B::smallTalk insteadof A;
   A::bigTalk insteadof B;
}
use HelloWorld { sayHello as protected; }
use HelloWorld { sayHello as private myPrivateHello; }

// PHP 7+ code
use some\namespace\{ClassA, ClassB, ClassC as C};
use function some\namespace\{fn_a, fn_b, fn_c};
use const some\namespace\{ConstA, ConstB, ConstC};
use Foo\Bar\{
    Foo,
    Bar,
    Baz as C
};

// PHP 7.2+ code.
use some\namespace\{ ClassA, ClassB, ClassC as C, };
use function some\namespace\{ fn_a, fn_b, fn_c, };
use const some\namespace\{ConstA, ConstB, ConstC,};
use Foo\Bar\{
    Foo,
    Bar,
    Baz as C,
};
