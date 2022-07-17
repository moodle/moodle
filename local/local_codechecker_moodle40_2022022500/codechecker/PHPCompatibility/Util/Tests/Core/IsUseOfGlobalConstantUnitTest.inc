<?php

/*
 * Make sure that global constants are correctly identified.
 *
 * The below should *NOT* be recognized as global constant usage.
 */

/* Case 1 */
namespace PHP_VERSION_ID {}

/* Case 2 */
namespace MY\OTHER\PHP_VERSION_ID\NS {}

/* Case 3 */
use PHP_VERSION_ID;

/* Case 4 */
use Something, PHP_VERSION_ID, SomethingElse;

/* Case 5 */
class PHP_VERSION_ID {
    /* Case 6 */
    const PHP_VERSION_ID = 'something';
    /* Case 7 */
    private function PHP_VERSION_ID() {}
}

/* Case 8 */
class ABC extends PHP_VERSION_ID {}

/* Case 9 */
class DEF implements PHP_VERSION_ID {}

/* Case 10 */
interface PHP_VERSION_ID {}

/* Case 11 */
trait PHP_VERSION_ID {}

/* Case 12 */
$a = new PHP_VERSION_ID;

/* Case 13 */
$a = new PHP_VERSION_ID();

/* Case 14 */
function PHP_VERSION_ID() {}

/* Case 15 */
echo PHP_VERSION_ID();

/* Case 16 */
echo My\UsedAsNamespace\PHP_VERSION_ID\something;

/* Case 17 */
My\UsedAsNamespace\PHP_VERSION_ID\something::something_else();

/* Case 18 */
if ( $abc instanceof PHP_VERSION_ID ) {}

/* Case 19 */
goto PHP_VERSION_ID;

/* Case 20 */
echo \mynamespace\PHP_VERSION_ID;

/* Case 21 */
echo My_Class::PHP_VERSION_ID;

/* Case 22 */
echo $this->PHP_VERSION_ID;

/* Case 23 */
use const SomeNamespace\PHP_VERSION_ID as SSP; // PHP 5.6+

/* Case 24 */
use const ABC as PHP_VERSION_ID;

/* Case 25 */
use const SomeNamespace\{PHP_VERSION_ID, TEMPLATEPATH}; // PHP 7.0+

class Talker {
    /* Case 26 */
    use A, PHP_VERSION_ID, C {
        /* Case 27 */
        PHP_VERSION_ID::smallTalk insteadof A;
        /* Case 28 */
        A::bigTalk insteadof PHP_VERSION_ID;
    }
}

class MyClass2 {
    use HelloWorld {
        /* Case 29 */
        sayHello as private PHP_VERSION_ID;
        /* Case 30 */
        sayGoodbye as protected PHP_VERSION_ID;
        /* Case 31 */
        sayHowdy as public PHP_VERSION_ID;
    }
}

/*
 * Make sure that global constants are correctly identified.
 *
 * The below should be recognized as global constant usage.
 */

/* Case A1 */
echo PHP_VERSION_ID;

/* Case A2 */
echo \PHP_VERSION_ID; // Global constant.

/* Case A3 */
$folder = basename( PHP_VERSION_ID );

/* Case A4 */
include PHP_VERSION_ID . '/js/myfile.js';

/* Case A5 */
use const PHP_VERSION_ID as SSP;

/* Case A6 */
switch( PHP_VERSION_ID ) {
    /* Case A7 */
    case PHP_VERSION_ID:
        break;
}

/* Case A8 */
const PHP_VERSION_ID = 'something';

/* Case A9 */
$array[PHP_VERSION_ID] = 'something';

/* Case A10 */
const ABC = '123',
      DEF = '456',
      PHP_VERSION_ID = 'something',
	  GHI = 789;
