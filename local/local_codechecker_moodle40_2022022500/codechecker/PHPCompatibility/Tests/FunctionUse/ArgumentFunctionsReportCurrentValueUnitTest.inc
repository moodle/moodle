<?php
/*
 * Same behaviour across PHP versions as the passed argument(s) are not touched before the function(s) are called.
 */
function foo($x) {
    func_get_arg(0);
    func_get_args();
    debug_backtrace();
    ++$x;
}

$d = function($a, $b, ...$c) {
    func_get_arg(0);
    func_get_args();
}

/*
 * Prevent false positives.
 */
function foo($a) {
	$a = 'foo';
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // 'args' is ignored, so not affected.
    debug_backtrace()[0]['class']; // Explicitly requesting a non-args index from the backtrace array.
}


function foo($a) {
	$a   = 'changed!';
	$obj = new ABC();
	echo $obj->
		func_get_arg(0); // OK, not the PHP native function call.
	echo ABC::func_get_args(); // OK, not the PHP native function call.
	echo \myNS\debug_print_backtrace(); // OK, not the PHP native function call.
	const DEBUG_BACKTRACE = 'abc';
}

debug_backtrace(); // Not called within the scope of a function, so 'args' is not set/affected.

$e = function() {
	$abc = 'abc';
    var_dump(func_get_args()); // OK, no named args, so no risk.
}

function something( $a, $b ) {
	$a = 'abc';
    func_get_arg(2); // OK, requesting a non-named additional argument.
}

// No scope opener/closer.
class ABC {
	abstract private function def();
}

interface ABC {
	public function def();
}

// Making sure the sniff does not get confused over nested structures.
function foo($a, $b, ...$c) {
	$d = function($args) {
		$a = 'abc';
	    $b = \func_get_args();
	}

	function local_function($b = 123) {
		-- /* comment */ $c;
	    func_get_arg(0);
	}

	class ABC {
		public function some_method() {
		    debug_print_backtrace();
		}
	}

	func_get_args(); // OK, nothing changed in the scope we're interested in.
}

/*
 * PHP 7.0+ changed behaviour.
 */
function bar($x) {
    $x++;
    func_get_arg(0); // Error.
    Func_Get_Args(); // Error.
    \DEBUG_BACKTRACE(); // Error.
}

$d = function($a, $b, ...$c) {
    func_get_arg(
		2
	); // OK.
	$b = array_map('strtolower', $b);
    func_get_arg( 0 ); // OK - $a was not touched
    func_get_arg(1); // Error.
    func_get_arg(2); // OK - $c was not touched (yet).
    func_get_args(); // Error.
	$d = array_map('strtolower', $c);
    func_get_arg( 0 ); // OK - $a was not touched
    func_get_arg(1); // Error.
    func_get_arg(2); // Warning.
    func_get_args(); // Error.
	$c[0] = 'something else';
    func_get_arg(0); // OK - $a was not touched.
    func_get_arg(1); // Error.
    func_get_arg(2); // Error.
    debug_backtrace(); // Error.
    debug_print_backtrace( $index ); // Error.
    debug_backtrace ( )  [ 1 ]; // Error. Index which will be used is unknown.
    debug_backtrace ( )  [ 2 ] [ 'args' ]; // Error.
}

function foo($a, $b, ...$c) {
	$a[1]['a'][2] = 'abc';

	function local_function( $b = 123 ) {
		$c = 'something';
	}

	func_get_arg(0); // Error.
	func_get_arg(1); // OK. Changed in a different scope.
	func_get_arg($var); // Error.
	$c = func_get_arg(2); // OK. Assignment operator is right associative.
}


function foo($x) {
    array_sort($x);
    \ func_get_args ( ); // Warning, $x may (in this case: will) have been changed by reference.
}

function foo($x) {
    $y = function_call($x);
    debug_backtrace(); // Warning, $x may have been changed by reference.
}

/*
 * Some more tests based on real life code.
 */
	public function add_node( $args ) {
		// Shim for old method signature.
		if ( func_num_args() >= 3 && is_string( func_get_arg( 0 ) ) ) { // OK.
			$args = array_merge( array( 'parent' => func_get_arg( 0 ) ), func_get_arg( 2 ) ); // OK.
		}
	}

function author_can( $post, $capability ) {
	if ( $post === 'abc' ) {
		return false;
	}

	$args = array_slice( func_get_args(), 2 ); // OK, not accessing a named parameter.
}

	public function feedback( $string, $other ) {
		if ( isset( $this->upgrader->strings[ $string ] ) ) {
			$string = $this->upgrader->strings[ $string ];
		}

		if ( strpos( $string, '%' ) !== false ) {
			$args = array_slice( func_get_args(), 0 ); // Error.
			$args = array_slice( func_get_args(), 1 ); // OK, param changed is not one of the ones targetted by array_slice.
			$args = array_splice(
				func_get_args(),
				2
			); // OK, not accessing a named parameter.
		}
	}

// Parse error on purpose (missing closing scope brace). This has to be the last test in the file.
function foo($x) {
    $x = function_call($x);
    func_get_args(); // Ignored.
