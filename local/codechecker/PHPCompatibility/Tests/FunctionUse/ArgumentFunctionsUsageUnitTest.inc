<?php

/*
 * Test pre-PHP 5.3: these functions could not be used in parameter lists.
 */
function Foo() {
	echo Foo(func_get_args());
	$closure = function( \func_get_args() ) {}; // Parse error, but not our concern.
}

$closure = function ( $param ) {
	echo $this->Foo(func_get_args());
};

class ABC {
	public function foo() {
		echo MyNS\Bar(func_get_arg(1));
		if ( func_num_args() > 0 &&  self::baz( \func_num_args() ) ) {}
		$b = new MyClass(func_get_args());
	}
}

// Test against false positives.
function Foo() {
    $a = func_get_args();
    echo func_get_arg(1);
    if ( func_num_args() > 0 ) {}

	$d = Baz(Bar::func_get_args());
	$e = Baz($object->func_get_args());
	$f = Baz(\Bar\func_get_args());
	$g = Baz( new Func_Get_Args());

	if ( !function_exists('func_get_args')) {
		function func_get_args() {}
	}
}


/*
 * Test PHP 5.0+: these functions can only be used within a user-defined function.
 */
$a = func_get_args();
echo Bar(func_get_arg(1));
if ( \func_num_args() > 0 &&  baz( func_num_args() ) ) {} // x 2 (+ 1 x use in param list) .

// Test against false positives.
$d = Baz(Bar::func_get_args());
$e = Baz($object->func_get_args());
$f = Baz(\Bar\func_get_args());
