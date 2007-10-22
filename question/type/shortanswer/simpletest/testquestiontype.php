<?php
/**
 * Unit tests for (some of) question/type/shortanswer/questiontype.php.
 *
 * @copyright &copy; 2007 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/questiontype.php');

class question_shortanswer_qtype_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
        $this->qtype = new question_shortanswer_qtype();
    }
    
    function tearDown() {
        $this->qtype = null;   
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'shortanswer');
    }

    function test_compare_string_with_wildcard() {
        // Test case sensitive literal matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'Frog', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frog', 'frog', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Frog   ', 'Frog', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'Frog', false));

        // Test case insensitive literal matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'frog', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   FROG   ', 'Frog', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'Frog', true));

        // Test case sensitive wildcard matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'F*og', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Fog', 'F*og', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Fat dog   ', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Fg', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('frog', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('   fat dog   ', 'F*og', false));

        // Test case insensitive wildcard matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Fog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Fat dog   ', 'F*og', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'F*og', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Fg', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('frog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   fat dog   ', 'F*og', true));

        // Test match using regexp special chars.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   *   ', '\*', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('*', '\*', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog*toad', 'Frog\*toad', false));
        $this->assertfalse($this->qtype->compare_string_with_wildcard('a', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('[a-z]', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('\{}/', '\{}/', true));
    }

    function test_get_correct_responses() {
        $answer1 = new stdClass;
        $answer1->id = 17;
        $answer1->answer = "frog";
        $answer1->fraction = 1;
        $answer2 = new stdClass;
        $answer2->id = 23;
        $answer2->answer = "f*g";
        $answer2->fraction = 1;
        $answer3 = new stdClass;
        $answer3->id = 29;
        $answer3->answer = "12\*13";
        $answer3->fraction = 1;
        $answer4 = new stdClass;
        $answer4->id = 31;
        $answer4->answer = "*";
        $answer4->fraction = 0;
        $question = new stdClass;
        $question->options->answers = array(
            17 => $answer1,
            23 => $answer2,
            29 => $answer3,
            31 => $answer4
        );
        $state = new stdClass;
        $this->assertEqual($this->qtype->get_correct_responses($question, $state), array('' => 'frog'));
        $question->options->answers[17]->fraction = 0;
        $this->assertEqual($this->qtype->get_correct_responses($question, $state), array('' => 'f*g'));
        $question->options->answers[23]->fraction = 0;
        $this->assertEqual($this->qtype->get_correct_responses($question, $state), array('' => '12*13'));
        $question->options->answers[29]->fraction = 0;
        $this->assertNull($this->qtype->get_correct_responses($question, $state));
    }
}

?>
