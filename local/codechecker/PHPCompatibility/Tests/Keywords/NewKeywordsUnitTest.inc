<?php

$dir = __DIR__;

try {
    // something
} catch (Exception $e) {
    // something else
} finally {
    // finally something
}

class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}

namespace Foobar;

$namespace = __NAMESPACE__;

trait FoobarTrait {
    public function foobar() {
        $name = __TRAIT__;
    }
}

function gen_one_to_three() {
    for ($i = 1; $i <= 3; $i++) {
        // Note that $i is preserved between yields.
        yield $i;
    }
}

const TEST = 'Hello';

class testing {
    const TEST = 'Hello';
    const ok = 'a';

    public function something() {
        const TEST = 'This is not a class constant';
    }
}

interface testing {
    const TEST = 'Hello';
    const ok = 'a';

    public function something() {
        const TEST = 'This is not an interface constant';
    }
}

$a = new class {
    const TEST = 'Hello';
    const ok = 'a';

    public function something() {
        const TEST = 'This is not a class constant';
    }
}


function myTest(callable $callableMethod) {}

goto end;

end:
echo 'something';

function testYieldFrom() {
    yield from [3, 4];
    yield
		from [3, 4]; // This is yield from, but tokenized as two T_YIELD_FROM tokens.
    yield /*something*/ from [3, 4]; // Test against false positive.
}

// Normal Heredoc is ok.
$str = <<<EOD
Example of string
spanning multiple lines
using nowdoc syntax.
EOD;

// PHP 5.3 Nowdoc.
$str = <<<'LABEL'
Example of string
spanning multiple lines
using nowdoc syntax.
LABEL;

// PHP 5.3 quoted heredoc.
$str = <<<"LABEL"
Example of string
spanning multiple lines
using nowdoc syntax.
LABEL;

/*
 * Test case-insensitive matching of the PHPCS cross-version compat layer.
 */
TRAIT MyFoobarTrait {}

try {
} FINALLY {
}

/*
 * Check against false positives/correct PHPCS cross-version compat layer.
 * The fact that these keywords are reserved, is not our concern. That is handled by the forbiddenNames sniff.
 */

// "namespace" is a property (allowed use, though confusing), not the keyword.
$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $id ) ) );

// yield used as a class constant.
echo MyClass::yield;

echo __dir__; // Magic constants are also case-insensitive.

__halt_compiler();

bla();
const ok = 'a';
