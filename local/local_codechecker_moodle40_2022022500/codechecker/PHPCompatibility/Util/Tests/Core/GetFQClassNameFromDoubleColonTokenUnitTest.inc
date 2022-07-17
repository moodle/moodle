<?php

/* Case 1 */
DateTime::CONSTANT;
/* Case 2 */
DateTime::$static_property;
/* Case 3 */
DateTime::static_function();
/* Case 4 */
\DateTime::static_function();
/* Case 5 */
namespace\DateTime::static_function();
/* Case 6 */
AnotherNS\DateTime::static_function();
/* Case 7 */
\FQNS\DateTime::static_function();
/* Case 8 */
$var = (DateTime::$static_property);
/* Case 9 */
$var = (5+AnotherNS\DateTime::$static_property);


namespace Testing {
	/* Case 10 */
	DateTime::CONSTANT;
	/* Case 11 */
	DateTime::$static_property;
	/* Case 12 */
	DateTime::static_function();

	class MyClass {
		function test {
			/* Case 13 */
			echo self::CONSTANT;
			/* Case 14 */
			echo parent::$static_property;
			/* Case 15 */
			static::test_function();
		}
	}
}


class MyClass {
	function test {
		/* Case 16 */
		echo self::CONSTANT;
		/* Case 17 */
		echo parent::$static_property;
		/* Case 18 */
		static::test_function();
	}
}

// Issue #205
class Foo {
    static public function bar($a) {
        echo __METHOD__ . '() called with $a = ' . $a;
    }
}
$theclass = 'Foo';
/* Case 19 */
$theclass::bar(42);
