<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/mathslib.php');

/**
 * Unit tests of mathslib wrapper and underlying EvalMath library.
 *
 * @author Petr Skoda (skodak)
 * @version $Id$
 */
class mathsslib_test extends UnitTestCase {

    /**
     * Tests the basic formula evaluation
     */
    function test__basic() {
        $formula = new calc_formula('=1+2');
        $res = $formula->evaluate();
        $this->assertEqual($res, 3, '3+1 is: %s');
    }

    /**
     * Tests the formula params
     */
    function test__params() {
        $formula = new calc_formula('=a+b+c', array('a'=>10,'b'=>20,'c'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 60, '10+20+30 is: %s');
    }

    /**
     * Tests the changed params
     */
    function test__changing_params() {
        $formula = new calc_formula('=a+b+c', array('a'=>10,'b'=>20,'c'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 60, '10+20+30 is: %s');
        $formula->set_params(array('a'=>1,'b'=>2,'c'=>3));
        $res = $formula->evaluate();
        $this->assertEqual($res, 6, 'changed params 1+2+3 is: %s');
    }

    /**
     * Tests the spreadsheet emulation function in formula
     */
    function test__calc_function() {
        $formula = new calc_formula('=sum(a,b,c)', array('a'=>10,'b'=>20,'c'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 60, 'sum(a,b,c) is: %s');
    }

    /**
     * Tests the min and max functions
     */
    function test__minmax_function() {
        $formula = new calc_formula('=min(a,b,c)', array('a'=>10,'b'=>20,'c'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 10, 'minimum is: %s');
        $formula = new calc_formula('=max(a,b,c)', array('a'=>10,'b'=>20,'c'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 30, 'maximum is: %s');
    }

    /**
     * Tests special chars
     */
    function test__specialchars() {
        $formula = new calc_formula('=gi1 + gi2 + gi11', array('gi1'=>10,'gi2'=>20,'gi11'=>30));
        $res = $formula->evaluate();
        $this->assertEqual($res, 60, 'sum is: %s');
    }

}

?>