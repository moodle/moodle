<?php

/* Case 1 */
$var = false;

/* Case 2 */
function something($var = false)
{
	/* Case 3 */
	$var = false;
}

class MyClass {
	/* Case 4 */
	public $var = true;
	/* Case 5 */
	protected $var = true;
	/* Case 6 */
	private $var = true;
	/* Case 7 */
	var $var = true;

	/* Case 8 */
	public function something($var = false)
	{
		/* Case 9 */
		$var = false;
	}
}

$a = new class {
	/* Case 10 */
	public $var = true;
	/* Case 11 */
	protected $var = true;
	/* Case 12 */
	private $var = true;
	/* Case 13 */
	var $var = true;

	/* Case 14 */
	public function something($var = false)
	{
		/* Case 15 */
		$var = false;
	}
}

interface MyInterface {
	// Properties are not allowed in interfaces.
	/* Case 16 */
	public $var = false;
	/* Case 17 */
	protected $var = false;
	/* Case 18 */
	private $var = false;
	/* Case 19 */
	var $var = false;

	/* Case 20 */
	public function something($var = false);
}

trait MyTrait {
	/* Case 21 */
	public $var = true;
	/* Case 22 */
	protected $var = true;
	/* Case 23 */
	private $var = true;
	/* Case 24 */
	var $var = true;

	/* Case 25 */
	function something($var = false)
	{
		/* Case 26 */
		$var = false;
	}
}

// Multi-property declarations.
class MyClass {
	/* Case 27 */
	public $varA = true,
		/* Case 28 */
		$varB = false,
		/* Case 29 */
		$varC = 'string';

	/* Case 30 */
	private $varD = true,
		/* Case 31 */
		$varE = false,
		/* Case 32 */
		$varF = 'string';
}

$a = ( $foo == $bar ? new stdClass() :
	new class() {
		/* Case 33 */
		public $var = true;

		/* Case 34 */
		public function something($var = false) {}
	}
);

function_call( 'param', new class {
	/* Case 35 */
	public $year = 2017;

	/* Case 36 */
	public function __construct( $open, $post_id ) {}
}, 10, 2 );
