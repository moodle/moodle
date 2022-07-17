<?php

// OK.
function gen_one_to_three() {
    for ($i = 1; $i <= 3; $i++) {
        yield $i;
    }
}

function gen_one_to_three() {
	$a = array();
    for ($i = 1; $i <= 3; $i++) {
        $a[] = 'yield'.$i;
    }
    return $a;
}

$gen = function {
    $foo = yield myAsyncFoo();
    $bar = yield myAsyncBar($foo);
    yield "return" => $bar + 42;
};


// PHP 7.0+
function foo() {
    yield 0;
    yield 1;

    return 42;
}

$generator = function () {
    yield 1;
    return yield from bar();
}

function gen() {
    return $foo;
    yield;
}

// Issue #724 - nested generator functions.
  class test {
      final public function trigger()
      {
          $result = (
              function() {
                  yield 1;
                  yield 2;
              }
          );
          return $result(); // OK.
      }
    }

  class test {
      final public function trigger()
      {
          $result = (
              function() {
                  yield 1;
                  yield 2;
                  return 3; // Not OK.
              }
          );
          return $result();
      }
    }

// Make sure the correct scoped conditions are found.
function xrange($start, $limit, $step = 1) {
    if ($start < $limit) {
        if ($step <= 0) {
            throw new LogicException('Step must be +ve');
        }

        for ($i = $start; $i <= $limit; $i += $step) {
            yield $i;
        }
    }
    
    return 100; // Not OK.
}

function nestedConditions( $a, $b ) {
	try {
		switch( $a ) {
			case 'A':
				if ( $b > $a ) {
					for($a; $a < $b; $a++) {
						yield $a;
					}
				}
				break;
			case 'B':
				yield 2;
				break;
		}
	} finally {
		return 10; // Not OK.
	}
}

function nestedClosureReturn() {
	$a = function() {
		return 10; // OK.
	};
	
	$a();

	yield 20;
}

