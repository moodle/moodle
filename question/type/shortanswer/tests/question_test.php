<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the short answer question definition class.
 *
 * @package    qtype
 * @subpackage shortanswer
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');


/**
 * Unit tests for the short answer question definition class.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanswer_question_test extends advanced_testcase {
    public function test_compare_string_with_wildcard() {
        // Test case sensitive literal matches.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog', 'Frog', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog', 'frog', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   Frog   ', 'Frog', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frogs', 'Frog', false));

        // Test case insensitive literal matches.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog', 'frog', true));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   FROG   ', 'Frog', true));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frogs', 'Frog', true));

        // Test case sensitive wildcard matches.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog', 'F*og', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Fog', 'F*og', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   Fat dog   ', 'F*og', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frogs', 'F*og', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Fg', 'F*og', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'frog', 'F*og', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   fat dog   ', 'F*og', false));

        // Test case insensitive wildcard matches.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog', 'F*og', true));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Fog', 'F*og', true));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   Fat dog   ', 'F*og', true));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frogs', 'F*og', true));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Fg', 'F*og', true));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'frog', 'F*og', true));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   fat dog   ', 'F*og', true));

        // Test match using regexp special chars.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '   *   ', '\*', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '*', '\*', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'Frog*toad', 'Frog\*toad', false));
        $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'a', '[a-z]', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '[a-z]', '[a-z]', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '\{}/', '\{}/', true));

        // See http://moodle.org/mod/forum/discuss.php?d=120557.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                'ITÁLIE', 'Itálie', true));

        if (function_exists('normalizer_normalize')) {
            // Test ambiguous unicode representations.
            $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                    'départ', 'DÉPART', true));
            $this->assertFalse((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                    'départ', 'DÉPART', false));
            $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                    'd'."\xC3\xA9".'part', 'd'."\x65\xCC\x81".'part', false));
            $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                    'd'."\xC3\xA9".'part', 'D'."\x45\xCC\x81".'PART', true));
        }
    }

    public function test_compare_0_with_wildcard() {
        // Test the classic PHP problem case with '0'.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '0', '0', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '0', '0*', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '0', '*0', false));
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '0', '*0*', false));
    }

    public function test_compare_string_with_wildcard_many_stars() {
        // Test the classic PHP problem case with '0'.
        $this->assertTrue((bool)qtype_shortanswer_question::compare_string_with_wildcard(
                '<em></em>', '***********************************<em>***********************************</em>', false));
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_question('shortanswer');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(array('answer' => '')));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => '0.0')));
        $this->assertTrue($question->is_complete_response(array('answer' => 'x')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_question('shortanswer');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(array('answer' => '')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0.0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'x')));
    }

    public function test_grading() {
        $question = test_question_maker::make_question('shortanswer');

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 'x')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 'frog')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 'toad')));
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_question('shortanswer');

        $this->assertEquals(array('answer' => 'frog'),
                $question->get_correct_response());
    }

    public function test_get_correct_response_escapedwildcards() {
        $question = test_question_maker::make_question('shortanswer', 'escapedwildcards');

        $this->assertEquals(array('answer' => 'x*y'), $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $sa = test_question_maker::make_question('shortanswer');
        $qsummary = $sa->get_question_summary();
        $this->assertEquals('Name an amphibian: __________', $qsummary);
    }

    public function test_summarise_response() {
        $sa = test_question_maker::make_question('shortanswer');
        $summary = $sa->summarise_response(array('answer' => 'dog'));
        $this->assertEquals('dog', $summary);
    }

    public function test_classify_response() {
        $sa = test_question_maker::make_question('shortanswer');
        $sa->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(13, 'frog', 1.0)),
                $sa->classify_response(array('answer' => 'frog')));
        $this->assertEquals(array(
                new question_classified_response(14, 'toad', 0.8)),
                $sa->classify_response(array('answer' => 'toad')));
        $this->assertEquals(array(
                new question_classified_response(15, 'cat', 0.0)),
                $sa->classify_response(array('answer' => 'cat')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $sa->classify_response(array('answer' => '')));
    }

    public function test_classify_response_no_star() {
        $sa = test_question_maker::make_question('shortanswer', 'frogonly');
        $sa->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(13, 'frog', 1.0)),
                $sa->classify_response(array('answer' => 'frog')));
        $this->assertEquals(array(
                new question_classified_response(0, 'toad', 0.0)),
                $sa->classify_response(array('answer' => 'toad')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $sa->classify_response(array('answer' => '')));
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering() {
        $this->resetAfterTest();

        $question = test_question_maker::make_question('shortanswer');
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertNull($options);
    }
}
