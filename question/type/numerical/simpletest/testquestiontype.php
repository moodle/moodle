<?php  // $Id$
/**
 * Unit tests for (some of) question/type/numerical/questiontype.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');

class question_numerical_qtype_test extends UnitTestCase {
    var $tolerance = 0.00000001;        
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
        $answer = new stdClass;
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'relative';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.99, $this->tolerance);
        $this->assertWithinMargin($answer->max, 1.01, $this->tolerance);

        $answer = new stdClass;
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'relative';
        $answer->answer = 10.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 9.9, $this->tolerance);
        $this->assertWithinMargin($answer->max, 10.1, $this->tolerance);

        $answer = new stdClass;
        $answer->tolerance = 0.01;
        $answer->tolerancetype = 'nominal';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.99, $this->tolerance);
        $this->assertWithinMargin($answer->max, 1.01, $this->tolerance);

        $answer = new stdClass;
        $answer->tolerance = 2.0;
        $answer->tolerancetype = 'nominal';
        $answer->answer = 10.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 8, $this->tolerance);
        $this->assertWithinMargin($answer->max, 12, $this->tolerance);

        $answer = new stdClass; // Test default tolerance 0.
        $answer->tolerancetype = 'nominal';
        $answer->answer = 0.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0, $this->tolerance);
        $this->assertWithinMargin($answer->max, 0, $this->tolerance);

        $answer = new stdClass; // Test default type nominal.
        $answer->tolerance = 1.0;
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0, $this->tolerance);
        $this->assertWithinMargin($answer->max, 2, $this->tolerance);

        $answer = new stdClass;
        $answer->tolerance = 1.0;
        $answer->tolerancetype = 'geometric';
        $answer->answer = 1.0;
        $this->qtype->get_tolerance_interval($answer);
        $this->assertWithinMargin($answer->min, 0.5, $this->tolerance);
        $this->assertWithinMargin($answer->max, 2.0, $this->tolerance);
    }

    function test_apply_unit() {
        $units = array(
            (object) array('unit' => 'm', 'multiplier' => 1),
            (object) array('unit' => 'cm', 'multiplier' => 100),
            (object) array('unit' => 'mm', 'multiplier' => 1000),
            (object) array('unit' => 'inch', 'multiplier' => 1.0/0.0254)
        );
        
        $this->assertWithinMargin($this->qtype->apply_unit('1', $units), 1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('1.0', $units), 1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('-1e0', $units), -1, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('100m', $units), 100, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('1cm', $units), 0.01, $this->tolerance);
        $this->assertWithinMargin($this->qtype->apply_unit('12inch', $units), .3048, $this->tolerance);
        $this->assertIdentical($this->qtype->apply_unit('1km', $units), false);
        $this->assertWithinMargin($this->qtype->apply_unit('-100', array()), -100, $this->tolerance);
        $this->assertIdentical($this->qtype->apply_unit('1000 miles', array()), false);
    }

//    function test_backup() {
//    }
//
//    function test_restore() {
//    }
}

?>
