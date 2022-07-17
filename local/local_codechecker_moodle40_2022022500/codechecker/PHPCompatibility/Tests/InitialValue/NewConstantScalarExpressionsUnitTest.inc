<?php

/*
 * These should be ignored, not our target.
 */
$var_A = TWO + 1;
$var_B = ONE / self::THREE;
$var_C = 'The value of THREE is '.self::THREE;

/*
 * These were all fine in PHP < 5.6.
 */
const ONE = 1;
const ONF = 'string';
const ONG = -1.4;
const ONH = false;
const ONI = ONE;
const ONJ = ClassName::ONE;
const ONK = MyNS\ClassName::ONE;
const ONL = namespace\ClassName::ONE;
const ONN = __LINE__;

static $a = 1;
static $b = array( 'a', 'b' => 2 );
static $c = 'string';
static $d, $e = 0, $f = __FILE__;
static $g = ONG, $h = ClassName::ONE;

function foofoo() {
    static $a = 1;
    static $b = [ 'a', 'b' => 2 ];
    static $c = 'string';
    static $d;
    static $e, $f, $g;
    static $h, $i = ONI, $j = __FUNCTION__;
}

class Bar {
    const THREE = +123.54;
    const CANNOT_FETCH = -1;
    const ONE_THIRD = true;
    const SENTENCE = 'The value of THREE is ';
    const SENTENCF = "The value of THREE is ";
    const SENTENCG = <<<FOOBAR
Constant example
FOOBAR;
    const FOUR = __CLASS__;
    const FINE = \ReflectionMethod::IS_PUBLIC;

    public $prop_A = +15;
    public $prop_B = 'string';
    public $prop_C = 2.5;
    public $prop_D = FOUR;
    public $prop_E;
    public $prop_F = array(
        1 => __NAMESPACE__,
        2 => 1.3,
    );
    public $prop_G = [
        'key1' => true,
        'key2' => null,
    ];
    public $prop_H = <<<FOOBAR
Property example
FOOBAR;

    public function f($a = 123, $b = false, $c = __METHOD__, $d = ['a', 'b'], $e = array('a', 'b'), $f = 1.5 )
    {
        static $bar = <<<LABEL
Nothing in here...
LABEL;

        return $a;
    }
}

// Make sure that (nested) arrays are handled correctly.
class NestedArrays {
	protected $table_and_column_defs = array(
		array(
			'definition'      => array(
				'a' => array(
					'( a INT, b FLOAT )',
				),
			),
		),
	);

    protected $defaultAcl = [
        [
            'principal' => '{DAV:}authenticated',
            'protected' => true,
            'privilege' => '{DAV:}all',
        ],
    ];

	var $span_gamut = array(
		"parseSpan"    => -30,
		-20            =>  10,
	);

	public $defaultSecondarySettings = array(
		'options' => array(),
		'post_type' => 'post',
		'num' => -1,
		'order' => 'DESC',
	);

	public static $status_map = array(-1=>'auto-draft',
										 0=>'draft',
										 1=>'pending',
										 4=>'trash');

	function post_data_export( $prefix = '_aioseop', $query = array( 'posts_per_page' => - 1 ) ) {}
}

trait Something {
	protected $prop = __TRAIT__;
}

// PHP 5.6: Test throwing errors for all newly allowed operators.
const TWOA = 1 + 2;
const TWOB = 2 - 2;
const TWOC = 3 * 2;
const TWOD = 4 / 2;
const TWOE = 5 % 2;
const TWOF = ! 6;
const TWOG = ~ 7;
const TWOH = 8 | 2;
const TWOI = 9 & 2;
const TWOJ = 10 ^ 2;
const TWOK = 11 << 2;
const TWOL = 12 >> 2;
const TWOM = 13 . 2;
const TWON = 14 ?: 2;
const TWOO = 15 <= 2;
const TWOP = 16 >= 2;
const TWOQ = 17 == 2;
const TWOR = 18 != 2;
const TWOS = 19 < 2;
const TWOT = 20 > 2;
const TWOU = 21 === 2;
const TWOV = 22 !== 2;
const TWOW = 23 && 2;
const TWOX = 24 and 2;
const TWOY = 25 || 2;
const TWOZ = 26 or 2;
const TWO0 = 27 xor 2;
const TWO1 = 28 ** 2;
const TWO2 = 29 ?? 2;

// PHP 5.6: Grouping parenthesis are allowed.
const TWO3 = (24 and 2);

// PHP 5.6: Using constants in combination with operators is allowed.
const TWO4 = ONE * 2;
const BAZ  = GREETING." WORLD!";

// PHP 5.6: Static constant expressions in class constants, properties and function param defaults.
class FooClass {
    const THREE = TWO + 1;
    const ONE_THIRD = ONE / self::THREE;
    const SENTENCE = 'The value of THREE is '.self::THREE;

    public $foo = 1 + 1;
    public $bar = [
        1 + 1,
        1 << 2,
        Foo::BAZ => "foo "."bar"
    ];
    public $baseDir = __DIR__ . "/base";

    public function f($a = ONE + static::THREE) { // Using `static::` is still not allowed, but not our concern.
        return $a;
    }
}

// ... and in an anonymous class...
$a = new class {
    const THREE = TWO + 1;
    const ONE_THIRD = ONE / self::THREE;
    const SENTENCE = 'The value of THREE is '.self::THREE;

    public $foo = 1 + 1;
    public $bar = [
        1 + 1 => 'value',
        1 << 2 => 'value',
		// Note: Heredoc with variables is still not allowed, but not our concern.
        Foo::BAZ => "foo ".<<<EOT
some text with a $variable
EOT
    ];
    public $baseDir = 'Line: '.__LINE__;

    public function f($a = ONE + self::THREE) {
        return $a;
    }
};

// ... and in traits.
trait FooTrait {
    public $foo = 1 + 1;
    public $bar = [
        'a'.'b' => 'value',
        1 - 2,
        Foo::BAZ => "foo "."bar"
    ];
    public $baseDir = 'Trait: '.__TRAIT__;

    public function f($a = ONE + self::THREE) {
        return $a;
    }
}

// PHP 5.6: Static constant expressions in function param defaults.
function f($a = 5 * MINUTEINSECONDS, $b = [ 'a', 1 + 2 ]) {
    return $a;
}

$closure = function ($a = 30 / HALF, $b = array( 1, THREE, 'string'.'concat') ) {
    return $a;
};

function foo($a = (1 + 1), $b = 2 << 3, $c = ((BAR)?10:100), $d = 123, $e = array(), $f = 10 * 5) {}

$closure = function (
	$a = (1 + 1),
	$b = 2 << 3,
	$c = ((BAR)?10:100)
) {};

// PHP 5.6: Static constant expressions in static variable declarations.
static $e = 1 + 1;
static $f = [1 << 2];
static $g = 0x01 | BAR;
static $h = (24 and 2), $i = ONE * 2, $j = 'a' . 'b';

static $i = namespace ABC\DEF; // Parse error, but testing part of the code.

// PHP 5.6: Negative/Positive constants can now be used.
static $j = - namespace\Foo::BAR;
const ABC = + \ABC\Bar::BAR;
class ABC {
	public static $foo = - Foo::THREE;
}
const ABC = + FOOBAR;
const ABC = - M_PI;

class testThis {
    private $oneComplexValueDataSets = [
        [-9.8765,  +4.321,     null],
        [0,        M_PI,       null],
        [0,        -M_PI,      null], // Correctly identified as invalid in PHP < 5.6 :tada:.
    ];
}

// PHP 5.6: Array unions.
const ABC = array( 1 => 1, 2 => 2 ) + array( 3 => 3, 4 => 4 );
const ABC = [ 1 => 1, 2 => 2 ] + [ 3 => 3, 4 => 4 ];

// Still not allowed, but not our concern. These should throw errors for pre-5.6, but that's it.
static $var = $a;
static $callA = function($a) {};
static $callB = strpos( $a, 'a' );
static $interpolated = "value with $variable";
