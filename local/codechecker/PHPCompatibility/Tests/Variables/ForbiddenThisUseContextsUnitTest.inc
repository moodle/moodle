<?php

/*
 * Disable using $this as parameter.
 */
function noParams() {}
function paramNotThis($that) {}

// This was already an error prior to PHP 7.1, so not our concern.
class ThisAsMethodParam {
    function methodParam($this) {
    }
}

// These should be flagged. New fatal error: Cannot use $this as parameter.
function thisAsGlobalFunctionParam($this) {}
$a = function($paramA, $paramB, $this, $paramC, $this) {}; // Error x 2, throws error as of PHP 7.0.7.

class NestedClosures {
    function thisInNestedClosure() {
		$a = function($this) {};
    }
}

/*
 * Disable using $this as global variable.
 *
 * New fatal error: Cannot use $this as global variable.
 */
global $this;

function thisInGlobalStatement() {
    global $that, $theOther, $this, $somethingElse;
}

class GlobalInMethod {
    function thisInGlobalStatement() {
        global $that,
	        $theOther,
			$this,
			$somethingElse;
    }
}

/*
 * Disable using $this as catch variable.
 *
 * New fatal error: Cannot re-assign $this.
 */
try {
   // Some code...
} catch (ExceptionTypeA | ExceptionTypeB $that) { // OK.
   // Code
} catch (ExceptionType1 | ExceptionType2 $this) {
   // Code to handle the exception.
} catch (\Exception $this) {
   // ...
}

/*
 * Disable using $this as foreach value variable.
 */
foreach ($a as $something) {}

class ForeachInMethod {
    public function testThis($a) {
        foreach($a as $k => $this->item) {}
    }
}

// This was already an error prior to PHP 7.1, so not our concern.
foreach ($a as $this => $that) {}

// These should be flagged. New fatal error: Cannot re-assign $this.
foreach ($a as $this) {}
foreach ($a as $that => $this) {}

/*
 * Disable ability to unset() $this.
 */
class UnsetInMethod {
	public function foo() {
		unset($this->property); // OK.
		
		// This throws an "Attempt to unset static property" error across all versions, so not our concern.
		unset($this::$property);
	}
}

class MyBar implements ArrayAccess {
    public function remove($name) {
        unset($this[$name]); // OK.
    }
}

// These should be flagged. New fatal error: Cannot unset $this.
unset($this);
unset($that, $theOther, $this, $somethingElse, $this); // Error x 2.
unset(
	$that,
	$this->something,
	$this,
);

/*
 * Using $this when not in object context now throws a fatal error.
 */
abstract class AbstractClass {
	abstract function bar();

    function foo() {
        var_dump($this);
    }
}

$a = function () {
    var_dump($this);
};

function thisInGlobalFunction() {
    if (isset($this)) {}
}

class ThisInStaticMethod {
    static function foo() {
        if (empty($this)) {};
    }
}

class Nested {
    public function getAnonymousClass() {
		$a = function() {
			var_dump($this);
		};

        return new class() {
            public function nestedFunction() {
                var_dump($this);
            }
        };
    }
}

// These should be flagged.  New fatal error: using $this when not in object context.
function usingThisNotInObjectContext() {
    var_dump($this);
}

class StaticMethodsClass {
    static function usingThisNotInObjectContext() {
        var_dump($this);
    }
}


/*
 * ====================================================================
 * THE BELOW CHANGES ARE NOT COVERED BY THE SNIFF.
 * Just here in case someone does decide to work on them and to protect against false positives.
 */

/*
 * Disable ability to re-assign $this indirectly through $$.
 */
$a = "this";
$$a = 42; // throw new Error("Cannot re-assign $this")

function_call( $$a );  // This is (still) fine.
$b = $$a; // This is (still) fine.

/*
 * Disable ability to re-assign $this indirectly through reference.
 */
class ReassignThisThroughReference {
  function foo(){
    $a =& $this;
    $a = 42;
    var_dump($this); // prints object(C)#1 (0) {}, php-7.0 printed int(42)
  }
}
$x = new ReassignThisThroughReference;
$x->foo();

/*
 * Disable ability to re-assign $this indirectly through extract() and parse_str().
 */
class ReassignThisIndirectly {
  function foo(){
    extract(["this"=>42]);  // throw new Error("Cannot re-assign $this")
    var_dump($this);
  }
}
$x = new ReassignThisIndirectly;
$x->foo();

/*
 * Always show true $this value in magic method __call().
 * In PHP 7.0 and below $this in static magic method __call() had value NULL. However it was possible to access properties and call object methods.
 */
class MagicMethodCall {
  static function __call($name, $args) {
    var_dump($this); // prints object(C)#1 (0) {}, php-7.0 printed NULL
    $this->test();   // prints "ops"
  }
  function test() {
    echo "ops\n";
  }
}
$x = new MagicMethodCall;
$x->foo();
