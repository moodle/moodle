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
 * Unit tests for the essayautograde question definition class.
 *
 * @package    qtype_essayautograde
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/essayautograde/tests/helper.php');

/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_question_test extends advanced_testcase {
    public function test_get_question_summary() {
        $essayautograde = qtype_essayautograde_test_helper::make_an_essayautograde_question();
        $essayautograde->questiontext = 'Hello <img src="http://example.com/globe.png" alt="world" />';
        $this->assertEquals('Hello [world]', $essayautograde->get_question_summary());
    }

    public function test_summarise_response() {
        $longstring = str_repeat('0123456789', 50);
        $essayautograde = qtype_essayautograde_test_helper::make_an_essayautograde_question();
        $this->assertEquals($longstring, $essayautograde->summarise_response(
                array('answer' => $longstring, 'answerformat' => FORMAT_HTML)));
    }

    public function test_is_same_response() {
        $essayautograde = qtype_essayautograde_test_helper::make_an_essayautograde_question();

        $essayautograde->responsetemplate = '';
        $essayautograde->responsesample = '';

        $essayautograde->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($essayautograde->is_same_response(
                array(),
                array('answer' => '')));

        $this->assertTrue($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '')));

        $this->assertTrue($essayautograde->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 'Hello'),
                array()));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 'Hello'),
                array('answer' => '')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_same_response_with_template() {
        $this->same_response_with_editor('responsetemplate');
    }

    public function test_is_same_response_with_sample() {
        $this->same_response_with_editor('responsesample');
    }

    protected function same_response_with_editor($editorfieldname) {
        $essayautograde = qtype_essayautograde_test_helper::make_an_essayautograde_question();

        $essayautograde->$editorfieldname = $editorfieldname;

        $essayautograde->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($essayautograde->is_same_response(
                array(),
                array('answer' => 'Once upon a time')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 'Once upon a time')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 'Once upon a time'),
                array('answer' => '')));

        $this->assertTrue($essayautograde->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 'Once upon a time'),
                array()));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($essayautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_complete_response() {
        $this->resetAfterTest(true);

        // Create a new logged-in user, so we can test responses with attachments.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create sample attachments to use in testing.
        $helper = test_question_maker::get_test_helper('essayautograde');
        $attachments = array();
        for ($i = 0; $i < 4; ++$i) {
            $attachments[$i] = $helper->make_attachments_saver($i);
        }

        // Create the essayautograde question under test.
        $essayautograde = qtype_essayautograde_test_helper::make_an_essayautograde_question();
        $essayautograde->start_attempt(new question_attempt_step(), 1);

        // Test the "traditional" case, where we must recieve a response from the user.
        $essayautograde->responserequired = 1;
        $essayautograde->attachmentsrequired = 0;
        $essayautograde->responseformat = 'editor';

        // The empty string should be considered an incomplete response, as should a lack of a response.
        $this->assertFalse($essayautograde->is_complete_response(array('answer' => '')));
        $this->assertFalse($essayautograde->is_complete_response(array()));

        // Any nonempty string should be considered a complete response.
        $this->assertTrue($essayautograde->is_complete_response(array('answer' => 'A student response.')));
        $this->assertTrue($essayautograde->is_complete_response(array('answer' => '0 times.')));
        $this->assertTrue($essayautograde->is_complete_response(array('answer' => '0')));

        // Test the case where two files are required.
        $essayautograde->attachmentsrequired = 2;

        // Attaching less than two files should result in an incomplete response.
        $this->assertFalse($essayautograde->is_complete_response(array('answer' => 'A')));
        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[0])));
        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[1])));

        // Anything without response text should result in an incomplete response.
        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Attaching two or more files should result in a complete response.
        $this->assertTrue($essayautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[2])));
        $this->assertTrue($essayautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[3])));

        // Test the case in which two files are required, but the inline
        // response is optional.
        $essayautograde->responserequired = 0;

        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));

        $this->assertTrue($essayautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Test the case in which both the response and inline text are optional.
        $essayautograde->attachmentsrequired = 0;

        // Providing no answer and no attachment should result in an incomplete
        // response.
        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => '')));
        $this->assertFalse($essayautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[0])));

        // Providing an answer _or_ an attachment should result in a complete
        // response.
        $this->assertTrue($essayautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));
        $this->assertTrue($essayautograde->is_complete_response(
                array('answer' => 'Answer text.', 'attachments' => $attachments[0])));

        // Test the case in which we're in "no inline response" mode,
        // in which the response is not required (as it's not provided).
        $essayautograde->reponserequired = 0;
        $essayautograde->responseformat = 'noinline';
        $essayautograde->attachmensrequired = 1;

        $this->assertFalse($essayautograde->is_complete_response(
                array()));
        $this->assertFalse($essayautograde->is_complete_response(
                array('attachments' => $attachments[0])));

        // Providing an attachment should result in a complete response.
        $this->assertTrue($essayautograde->is_complete_response(
                array('attachments' => $attachments[1])));

        // Ensure that responserequired is ignored when we're in inline response mode.
        $essayautograde->reponserequired = 1;
        $this->assertTrue($essayautograde->is_complete_response(
                array('attachments' => $attachments[1])));

    }

}