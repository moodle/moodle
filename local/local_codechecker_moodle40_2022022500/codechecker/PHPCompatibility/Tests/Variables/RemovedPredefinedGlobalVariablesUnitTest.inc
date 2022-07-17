<?php

echo $HTTP_RAW_POST_DATA; // Bad.

echo $http_raw_post_data; // Ok - variable names are case-sensitive.
echo $HTTP_Raw_Post_Data; // Ok - variable names are case-sensitive.

// Bad.
$HTTP_POST_VARS;
$HTTP_GET_VARS;
$HTTP_ENV_VARS;
$HTTP_SERVER_VARS;
$HTTP_COOKIE_VARS;
$HTTP_SESSION_VARS;
$HTTP_POST_FILES;

// Issue #268
class TestClass {
    // OK.
    private $HTTP_POST_VARS;
    protected $HTTP_GET_VARS;
    public $HTTP_ENV_VARS;
    var $HTTP_SERVER_VARS;
    private $HTTP_COOKIE_VARS;
    protected $HTTP_SESSION_VARS;
    public $HTTP_POST_FILES;
    var $HTTP_RAW_POST_DATA;

    function testing() {
         // Bad.
        $HTTP_POST_VARS;
        $HTTP_GET_VARS;
        $HTTP_ENV_VARS;
        $HTTP_SERVER_VARS;
        $HTTP_COOKIE_VARS;
        $HTTP_SESSION_VARS;
        $HTTP_POST_FILES;
        echo $HTTP_RAW_POST_DATA;

        // Ok.
        self::$HTTP_POST_VARS;
        self::$HTTP_GET_VARS;
        static::$HTTP_ENV_VARS;
        static::$HTTP_SERVER_VARS;
        $this->HTTP_COOKIE_VARS;
        $this->HTTP_SESSION_VARS;
        $this->HTTP_POST_FILES;
        static::$HTTP_RAW_POST_DATA;

        // Bad.
        self::{$HTTP_GET_VARS};
        self::{$HTTP_ENV_VARS};
        self::{$HTTP_RAW_POST_DATA};
    }
}

// Anonymous classes: Issue #333
$a = new class {
	// OK.
    private $HTTP_POST_VARS;
    protected $HTTP_GET_VARS;
    public static $HTTP_ENV_VARS;
    var $HTTP_SERVER_VARS;
    private $HTTP_COOKIE_VARS;
    protected static $HTTP_SESSION_VARS;
    public $HTTP_POST_FILES;
    var static $HTTP_RAW_POST_DATA;

    function testing() {
		 // Bad.
        $HTTP_POST_VARS;
        $HTTP_GET_VARS;
        $HTTP_ENV_VARS;
        $HTTP_SERVER_VARS;
        $HTTP_COOKIE_VARS;
        $HTTP_SESSION_VARS;
        $HTTP_POST_FILES;
        echo $HTTP_RAW_POST_DATA;

		// Ok.
        self::$HTTP_POST_VARS;
        self::$HTTP_GET_VARS;
        static::$HTTP_ENV_VARS;
        static::$HTTP_SERVER_VARS;
        $this->HTTP_COOKIE_VARS;
        $this->HTTP_SESSION_VARS;
        $this->HTTP_POST_FILES;
        static::$HTTP_RAW_POST_DATA;

		// Bad.
        self::{$HTTP_POST_VARS};
        self::{$HTTP_SERVER_VARS};
        self::{$HTTP_SESSION_VARS};
    }
}

/*
 * Note: the order of the below code is important for the unit tests!
 */
// Do something which causes an error.
echo $php_errormsg; // Bad.

// Test jumping over classes and such.
class ABC {
	function abc() {
		$php_errormsg = error_get_last(); // OK.
	}
}

if (isset($php_errormsg)) { // Bad.
	trigger_error($php_errormsg); // Bad.
}

echo $php_errormsg['message']; // OK, array.

function something( $php_errormsg ) // OK, param.
{
	echo $php_errormsg; // OK, param shadowing.
}

$a = function ( $php_errormsg ) // OK, param.
{
	echo $php_errormsg; // OK, param shadowing.
};

$f = function () use (&$php_errormsg) { // Bad.
	echo $php_errormsg; // Bad.
};

$php_errormsg = error_get_last(); // OK.

echo $php_errormsg; // OK - uses the value from the above assignment.
if (isset($php_errormsg)) { // OK - uses the value from the above assignment.
	trigger_error($php_errormsg); // OK - uses the value from the above assignment.
}


function something_else()
{
	global $php_errormsg; // Bad.
	echo $php_errormsg; // Bad.

	$php_errormsg = error_get_last(); // OK.

	echo $php_errormsg; // OK, uses the value from the above assignment.
	if (isset($php_errormsg)) { // OK, uses the value from the above assignment.
		trigger_error($php_errormsg); // OK, uses the value from the above assignment.
	}

	$f = function () use (&$php_errormsg) { // OK, uses the value from the above assignment.
		echo $php_errormsg; // OK, but for now gives false positive.
	};

	$a = function ()
	{
		echo $php_errormsg; // Bad.
	};
}

$a = class {};

// Test jumping over traits and such.
trait ABC {
	function abc() {
		$php_errormsg = error_get_last(); // OK.
	}
}

echo $php_errormsg; // OK - uses the value from the assignment on line 37.
