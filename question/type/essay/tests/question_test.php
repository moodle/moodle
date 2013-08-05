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
 * Unit tests for the essay question definition class.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_question_testcase extends advanced_testcase {
    public function test_get_question_summary() {
        $essay = test_question_maker::make_an_essay_question();
        $essay->questiontext = 'Hello <img src="http://example.com/globe.png" alt="world" />';
        $this->assertEquals('Hello [world]', $essay->get_question_summary());
    }

    public function test_summarise_response() {
        $longstring = str_repeat('0123456789', 50);
        $essay = test_question_maker::make_an_essay_question();
        $this->assertEquals($longstring, $essay->summarise_response(
                array('answer' => $longstring, 'answerformat' => FORMAT_HTML)));
    }

    public function test_is_same_response() {
        $essay = test_question_maker::make_an_essay_question();

        $essay->responsetemplate = '';

        $essay->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($essay->is_same_response(
                array(),
                array('answer' => '')));

        $this->assertTrue($essay->is_same_response(
                array('answer' => ''),
                array('answer' => '')));

        $this->assertTrue($essay->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertFalse($essay->is_same_response(
                array('answer' => 'Hello'),
                array()));

        $this->assertFalse($essay->is_same_response(
                array('answer' => 'Hello'),
                array('answer' => '')));

        $this->assertFalse($essay->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($essay->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($essay->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($essay->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_same_response_with_template() {
        $essay = test_question_maker::make_an_essay_question();

        $essay->responsetemplate = 'Once upon a time';

        $essay->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($essay->is_same_response(
                array(),
                array('answer' => 'Once upon a time')));

        $this->assertTrue($essay->is_same_response(
                array('answer' => ''),
                array('answer' => 'Once upon a time')));

        $this->assertTrue($essay->is_same_response(
                array('answer' => 'Once upon a time'),
                array('answer' => '')));

        $this->assertTrue($essay->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertTrue($essay->is_same_response(
                array('answer' => 'Once upon a time'),
                array()));

        $this->assertFalse($essay->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($essay->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($essay->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($essay->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_complete_response() {

        $essay = test_question_maker::make_an_essay_question();
        $essay->start_attempt(new question_attempt_step(), 1);

        // The empty string should be considered an empty response, as should a lack of a response.
        $this->assertFalse($essay->is_complete_response(array('answer' => '')));
        $this->assertFalse($essay->is_complete_response(array()));

        // Any nonempty string should be considered a complete response.
        $this->assertTrue($essay->is_complete_response(array('answer' => 'A student response.')));
        $this->assertTrue($essay->is_complete_response(array('answer' => '0 times.')));
        $this->assertTrue($essay->is_complete_response(array('answer' => '0')));
    }
}
