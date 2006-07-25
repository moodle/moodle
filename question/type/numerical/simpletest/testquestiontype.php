<?php
/**
 * Unit tests for (some of) question/type/numerical/questiontype.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

/** */
require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');

class question_numerical_qtype_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
        $this->qtype = new question_numerical_qtype();
    }
    
    function tearDown() {
        $this->qtype = null;   
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'numerical');
    }

//    function test_get_question_options() {
//    }
//
//    function test_get_numerical_units() {
//    }
//
//    function test_get_default_numerical_unit() {
//    }
//
//    function test_save_question_options() {
//    }
//
//    function test_save_numerical_units() {
//    }
//
//    function test_delete_question() {
//    }
//
//    function test_compare_responses() {
//    }
//
//    function test_test_response() {
//    }
//
//    function test_check_response(){
//    }
//
//    function test_grade_responses() {
//    }
//
//    function test_get_correct_responses() {
//    }
//
//    function test_get_all_responses() {
//    }

    function test_get_tolerance_interval() {
    }

    function test_apply_unit() {
    }

//    function test_backup() {
//    }
//
//    function test_restore() {
//    }
}

?>
