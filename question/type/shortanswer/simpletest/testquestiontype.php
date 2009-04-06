<?php  // $Id$
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
        $this->assertFalse($this->qtype->compare_string_with_wildcard('a', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('[a-z]', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('\{}/', '\{}/', true));

        // See http://moodle.org/mod/forum/discuss.php?d=120557
        $this->assertTrue($this->qtype->compare_string_with_wildcard('ITÁLIE', 'Itálie', true));
    }

    function test_check_response() {
        $answer1 = new stdClass;
        $answer1->id = 17;
        $answer1->answer = "celine";
        $answer1->fraction = 1;
        $answer2 = new stdClass;
        $answer2->id = 23;
        $answer2->answer = "c*line";
        $answer2->fraction = 0.8;
        $answer3 = new stdClass;
        $answer3->id = 23;
        $answer3->answer = "*line";
        $answer3->fraction = 0.7;
        $answer4 = new stdClass;
        $answer4->id = 29;
        $answer4->answer = "12\*13";
        $answer4->fraction = 0.5;

        $question = new stdClass;
        $question->options->answers = array(
            17 => $answer1,
            23 => $answer2,
            29 => $answer3,
            31 => $answer4
        );
        $question->options->usecase = true;

        $state = new stdClass;

        $state->responses = array('' => 'celine');
        $this->assertEqual($this->qtype->check_response($question, $state), 17);

        $state->responses = array('' => 'caline');
        $this->assertEqual($this->qtype->check_response($question, $state), 23);

        $state->responses = array('' => 'aline');
        $this->assertEqual($this->qtype->check_response($question, $state), 29);

        $state->responses = array('' => 'frog');
        $this->assertFalse($this->qtype->check_response($question, $state));

        $state->responses = array('' => '12*13');
        $this->assertEqual($this->qtype->check_response($question, $state), 31);

        $question->options->usecase = false;

        $answer1->answer = "Fred's";
        $question->options->answers[17] = $answer1;

        $state->responses = array('' => 'frog');
        $this->assertFalse($this->qtype->check_response($question, $state));

        $state->responses = array('' => "fred\'s");
        $this->assertEqual($this->qtype->check_response($question, $state), 17);

        $state->responses = array('' => '12*13');
        $this->assertEqual($this->qtype->check_response($question, $state), 31);

        $state->responses = array('' => 'caLINe');
        $this->assertEqual($this->qtype->check_response($question, $state), 23);

        $state->responses = array('' => 'ALIne');
        $this->assertEqual($this->qtype->check_response($question, $state), 29);
    }

    function test_compare_responses() {
        $question = new stdClass;
        $question->options->usecase = false;

        $state = new stdClass;
        $teststate = new stdClass;
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => '');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state = new stdClass;
        $teststate->responses = array('' => '');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => '');
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog');
        $teststate->responses = array('' => 'frog');
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog');
        $teststate->responses = array('' => 'Frog');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => "\'");
        $teststate->responses = array('' => "\'");
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog*toad');
        $teststate->responses = array('' => 'frog*TOAD');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog*');
        $teststate->responses = array('' => 'frogs');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frogs');
        $teststate->responses = array('' => 'frog*');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $question->options->usecase = true;

        $state->responses = array('' => '');
        $teststate->responses = array('' => '');
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog');
        $teststate->responses = array('' => 'frog');
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog');
        $teststate->responses = array('' => 'Frog');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => "\'");
        $teststate->responses = array('' => "\'");
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog*toad');
        $teststate->responses = array('' => 'frog*toad');
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog*');
        $teststate->responses = array('' => 'frogs');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frogs');
        $teststate->responses = array('' => 'frog*');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));
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
