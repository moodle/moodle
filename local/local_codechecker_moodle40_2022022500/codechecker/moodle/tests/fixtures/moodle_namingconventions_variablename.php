<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

$bad_variable_1 = 0;
$badVariable2 = 0;
$_badvariable3 = 0;
$REALLY_badvar4 = 0;
$goodvariable = 1;
$ACCESSLIB_PRIVATE = null;

class foo {
    public $bad_variable = null;
    public $badVariable = null;
    public $REALLY_badvar = null;
    public $goodvariable = null;
    private $_goodvariable = null;
    var $badvarusage = null;
}

$result = "String: $badPlaceholder1 is a badPlaceholder1";
$result = "String: $bad_placeholder2";
$result = "String: $REALLY_badplaceholder3";
$result = "String: $goodplaceholder1";
