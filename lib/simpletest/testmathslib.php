<?php

/* $Id$ */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/mathslib.php');

class mathsslib_test extends UnitTestCase {

    /**
     * Tests the basic formula execition
     */
    function test__basic() {
        $calc = new calc_formula('=1+2');
        $res = $calc->evaluate();
        $this->assertEqual($res, 3, '3+1 is: %s');
    }

    /**
     * Tests the formula params
     */
    function test__params() {
        $calc = new calc_formula('=a+b+c', array('a'=>10,'b'=>20,'c'=>30));
        $res = $calc->evaluate();
        $this->assertEqual($res, 60, '10+20+30 is: %s');
    }

    /**
     * Tests the formula params
     */
    function test__calc_function() {
        $calc = new calc_formula('=sum(a,b,c)', array('a'=>10,'b'=>20,'c'=>30));
        $res = $calc->evaluate();
        $this->assertEqual($res, 60, 'sum(a,b,c) is: %s');
    }

    /**
     * Tests the formula changed params
     */
    function test__changing_params() {
        $calc = new calc_formula('=a+b+c', array('a'=>10,'b'=>20,'c'=>30));
        $res = $calc->evaluate();
        $this->assertEqual($res, 60, '10+20+30 is: %s');
        $calc->set_params(array('a'=>1,'b'=>2,'c'=>3));
        $res = $calc->evaluate();
        $this->assertEqual($res, 6, '1+2+3 is: %s');
    }

}

?>