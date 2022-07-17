<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

/// Three slashes are incorrect.

//// four are also wrong. Not to talk about the missing upper and final dot

//And no-space, uhm, bad, bad!

// None of the following phpdocs should be causing problems.

/** This is an interface comment */
interface commented_interface {}

/** This is a class comment */
class commented_class {

    /** A const comment */
    const commented = true;
    /** A private comment */
    private $aprivate = true;
    /** A protected comment */
    protected $aprotected = true;
    /** A public comment */
    public $apublic = true;
    /** A static comment */
    static $astatic = true;
    /** A var comment, this is wrong! */
    var $avar = true;

    /** A function comment */
    function afunction() {}

    /** An abstract comment */
    abstract function afunction() {}

    /** A final comment */
    final function afunction() {}

    /** A define comment */
    define('ADEFINE', true);
}

/** A defined comment, this is wrong! */
defined('ADEFINED', true);

// Comment separators are allowed if pure (from 20 to 120 chars). All below are correct.

// --------------------

// ------------------------------------------------------------------------------------------------------------------------

// But not if mixed with text or punctuations or smaller than 20 chars or after code. All below are wrong.

// -------------------

// -------------------------------------------------------------------------------------------------------------------------

// ---------- nonono ----------

// -----------.......----------

// .----------------------------

// ----------------------------.

// .---------------------------.

echo 'hello'; // --------------------------

echo 'hello'; // A--------------.

# and, finally, some horrible perl comment, oh my. Missing uppers and ending too

// and yes, I'm missing correct start and end

// just checking multilines do work ok.
/// And the correct problems are detected, also this 3-slash line
// missig upper at the start and missing dot at the end

// Following CONTRIB-6025, let's accept phpdoc type hinting matching with begin of next line.
/** @var some_class $variable */
$variable = $giveme->some_class();

foreach ($somearray as $variable) {
    /** @var some_class $variable */
    $variable->do_something();
}

// Don't accept type hinting if it does not match the begin of next line.
/** @var some_class $variable */
lets_execute_it($variable->do_something());

/** @var some_class $variable */
$variables = $giveme->some_class();

// And also, CONTRIB-6105, consider assignments via list() like a viable use.
/** @var cm_info $cm */
list($course, $cm) = get_course_and_cm_from_cmid($cmid);

// But not this (non matching within the list().
/** @var cm_info $cm */
list($course, $something) = $cm->whatever($cmid);

// And also, CONTRIB-7165, consider assignments via foreach() like a viable use.
/** @var cm_info $cm */
foreach ($cms as $cm) {
    echo 'This is a test';
}

// But not this (non matching within the foreach().
/** @var cm_info $cm */
foreach ($cms as $something) {
    echo 'This is a test';
}

// Allow phpdoc before "return new class extends" expressions.
/** This is a phpdoc block */
return new class extends xxxx {}

// But don't allow it before other expressions.
/** This is a phpdoc block */
return new stdClass();
/** This is a phpdoc block */
return new class {}
/** This is a phpdoc block */
return class extends xxxx {}
/** This is a phpdoc block */
new class testphpdoc {}
/** This is a phpdoc block */
return new class implements something {}

// Allow @codeCoverageIgnore inline comments.
$something = 1; // @codeCoverageIgnore
$something = 1;// @codeCoverageIgnoreStart
$something = 1; // @codeCoverageIgnoreEnd
$something = 1;  // @codeCoverageIgnoreAnythingInvented
