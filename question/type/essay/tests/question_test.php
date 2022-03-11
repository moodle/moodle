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

namespace qtype_essay;

use question_attempt_step;
use question_display_options;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @package qtype_essay
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_test extends \advanced_testcase {
    public function test_get_question_summary() {
        $essay = \test_question_maker::make_an_essay_question();
        $essay->questiontext = 'Hello <img src="http://example.com/globe.png" alt="world" />';
        $this->assertEquals('Hello [world]', $essay->get_question_summary());
    }

    /**
     * Test summarise_response() when teachers view quiz attempts and then
     * review them to see what has been saved in the response history table.
     *
     * @dataProvider summarise_response_provider
     * @param int $responserequired
     * @param int $attachmentsrequired
     * @param string $answertext
     * @param int $attachmentuploaded
     * @param string $expected
     */
    public function test_summarise_response(int $responserequired, int $attachmentsrequired,
                                            string $answertext, int $attachmentuploaded, string $expected): void {
        $this->resetAfterTest();

        // If number of allowed attachments is set to 'Unlimited', generate 10 attachments for testing purpose.
        $numberofattachments = ($attachmentsrequired === -1) ? 10 : $attachmentsrequired;

        // Create sample attachments.
        $attachments = $this->create_user_and_sample_attachments($numberofattachments);

        // Create the essay question under test.
        $essay = \test_question_maker::make_an_essay_question();
        $essay->start_attempt(new question_attempt_step(), 1);

        $essay->responseformat = 'editor';
        $essay->responserequired = $responserequired;
        $essay->attachmentsrequired = $attachmentsrequired;

        // The space before the number of bytes from display_size is actually a non-breaking space.
        $expected = str_replace(' bytes', "\xc2\xa0bytes", $expected);

        $this->assertEquals($expected, $essay->summarise_response(
            ['answer' => $answertext, 'answerformat' => FORMAT_HTML,  'attachments' => $attachments[$attachmentuploaded]]));
    }

    /**
     * Data provider for summarise_response() test cases.
     *
     * @return array List of data sets (test cases)
     */
    public function summarise_response_provider(): array {
        return [
            'text input required, not attachments required'  =>
                [1, 0, 'This is the text input for this essay.', 0, 'This is the text input for this essay.'],
            'Text input required, one attachments required, one uploaded'  =>
                [1, 1, 'This is the text input for this essay.', 1, 'This is the text input for this essay.Attachments: 0 (1 bytes)'],
            'Text input is optional, four attachments required, one uploaded'  => [0, 4, '', 1, 'Attachments: 0 (1 bytes)'],
            'Text input is optional, four attachments required, two uploaded'  => [0, 4, '', 2, 'Attachments: 0 (1 bytes), 1 (1 bytes)'],
            'Text input is optional, four attachments required, three uploaded'  => [0, 4, '', 3, 'Attachments: 0 (1 bytes), 1 (1 bytes), 2 (1 bytes)'],
            'Text input is optional, four attachments required, four uploaded'  => [0, 4, 'I have attached 4 files.', 4,
                'I have attached 4 files.Attachments: 0 (1 bytes), 1 (1 bytes), 2 (1 bytes), 3 (1 bytes)'],
            'Text input is optional, unlimited attachments required, one uploaded'  => [0, -1, '', 1, 'Attachments: 0 (1 bytes)'],
            'Text input is optional, unlimited attachments required, five uploaded'  => [0, -1, 'I have attached 5 files.', 5,
                'I have attached 5 files.Attachments: 0 (1 bytes), 1 (1 bytes), 2 (1 bytes), 3 (1 bytes), 4 (1 bytes)'],
            'Text input is optional, unlimited attachments required, ten uploaded'  =>
                [0, -1, '', 10, 'Attachments: 0 (1 bytes), 1 (1 bytes), 2 (1 bytes), 3 (1 bytes), 4 (1 bytes), ' .
                    '5 (1 bytes), 6 (1 bytes), 7 (1 bytes), 8 (1 bytes), 9 (1 bytes)']
        ];
    }

    public function test_is_same_response() {
        $essay = \test_question_maker::make_an_essay_question();

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
        $essay = \test_question_maker::make_an_essay_question();

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
        $this->resetAfterTest(true);

        // Create sample attachments.
        $attachments = $this->create_user_and_sample_attachments();

        // Create the essay question under test.
        $essay = \test_question_maker::make_an_essay_question();
        $essay->start_attempt(new question_attempt_step(), 1);

        // Test the "traditional" case, where we must receive a response from the user.
        $essay->responserequired = 1;
        $essay->attachmentsrequired = 0;
        $essay->responseformat = 'editor';

        // The empty string should be considered an incomplete response, as should a lack of a response.
        $this->assertFalse($essay->is_complete_response(array('answer' => '')));
        $this->assertFalse($essay->is_complete_response(array()));

        // Any nonempty string should be considered a complete response.
        $this->assertTrue($essay->is_complete_response(array('answer' => 'A student response.')));
        $this->assertTrue($essay->is_complete_response(array('answer' => '0 times.')));
        $this->assertTrue($essay->is_complete_response(array('answer' => '0')));

        // Test case for minimum and/or maximum word limit.
        $response = [];
        $response['answer'] = 'In this essay, I will be testing a function called check_input_word_count().';

        $essay->minwordlimit = 50; // The answer is shorter than the required minimum word limit.
        $this->assertFalse($essay->is_complete_response($response));

        $essay->minwordlimit = 10; // The  word count  meets the required minimum word limit.
        $this->assertTrue($essay->is_complete_response($response));

        // The word count meets the required minimum  and maximum word limit.
        $essay->minwordlimit = 10;
        $essay->maxwordlimit = 15;
        $this->assertTrue($essay->is_complete_response($response));

        // Unset the minwordlimit/maxwordlimit variables to avoid the extra check in is_complete_response() for further tests.
        $essay->minwordlimit = null;
        $essay->maxwordlimit = null;

        // Test the case where two files are required.
        $essay->attachmentsrequired = 2;

        // Attaching less than two files should result in an incomplete response.
        $this->assertFalse($essay->is_complete_response(array('answer' => 'A')));
        $this->assertFalse($essay->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[0])));
        $this->assertFalse($essay->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[1])));

        // Anything without response text should result in an incomplete response.
        $this->assertFalse($essay->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Attaching two or more files should result in a complete response.
        $this->assertTrue($essay->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[2])));
        $this->assertTrue($essay->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[3])));

        // Test the case in which two files are required, but the inline
        // response is optional.
        $essay->responserequired = 0;

        $this->assertFalse($essay->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));

        $this->assertTrue($essay->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Test the case in which both the response and online text are optional.
        $essay->attachmentsrequired = 0;

        // Providing no answer and no attachment should result in an incomplete
        // response.
        $this->assertFalse($essay->is_complete_response(
                array('answer' => '')));
        $this->assertFalse($essay->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[0])));

        // Providing an answer _or_ an attachment should result in a complete
        // response.
        $this->assertTrue($essay->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));
        $this->assertTrue($essay->is_complete_response(
                array('answer' => 'Answer text.', 'attachments' => $attachments[0])));

        // Test the case in which we're in "no inline response" mode,
        // in which the response is not required (as it's not provided).
        $essay->reponserequired = 0;
        $essay->responseformat = 'noinline';
        $essay->attachmensrequired = 1;

        $this->assertFalse($essay->is_complete_response(
                array()));
        $this->assertFalse($essay->is_complete_response(
                array('attachments' => $attachments[0])));

        // Providing an attachment should result in a complete response.
        $this->assertTrue($essay->is_complete_response(
                array('attachments' => $attachments[1])));

        // Ensure that responserequired is ignored when we're in inline response mode.
        $essay->reponserequired = 1;
        $this->assertTrue($essay->is_complete_response(
                array('attachments' => $attachments[1])));
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering() {
        $this->resetAfterTest();

        $essay = \test_question_maker::make_an_essay_question();
        $essay->minwordlimit = 15;
        $essay->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($essay);
        $displayoptions = new question_display_options();

        $options = $essay->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertNotEmpty($options);
        $this->assertEquals('editor', $options['responseformat']);
        $this->assertEquals(1, $options['responserequired']);
        $this->assertEquals(15, $options['responsefieldlines']);
        $this->assertEquals(0, $options['attachments']);
        $this->assertEquals(0, $options['attachmentsrequired']);
        $this->assertNull($options['maxbytes']);
        $this->assertNull($options['filetypeslist']);
        $this->assertEquals('', $options['responsetemplate']);
        $this->assertEquals(FORMAT_MOODLE, $options['responsetemplateformat']);
        $this->assertEquals($essay->minwordlimit, $options['minwordlimit']);
        $this->assertNull($options['maxwordlimit']);
    }

    /**
     * Test get_validation_error when users submit their input text.
     *
     * (The tests are done with a fixed 14-word response.)
     *
     * @dataProvider get_min_max_wordlimit_test_cases()
     * @param  int $responserequired whether response required (yes = 1, no = 0)
     * @param  int $minwordlimit minimum word limit
     * @param  int $maxwordlimit maximum word limit
     * @param  string $expected error message | null
     */
    public function test_get_validation_error(int $responserequired,
                                              int $minwordlimit, int $maxwordlimit, string $expected): void {
        $question = \test_question_maker::make_an_essay_question();
        $response = ['answer' => 'One two three four five six seven eight nine ten eleven twelve thirteen fourteen.'];
        $question->responserequired = $responserequired;
        $question->minwordlimit = $minwordlimit;
        $question->maxwordlimit = $maxwordlimit;
        $actual = $question->get_validation_error($response);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for get_validation_error test.
     *
     * @return array the test cases.
     */
    public function get_min_max_wordlimit_test_cases(): array {
        return [
            'text input required, min/max word limit not set'  => [1, 0, 0, ''],
            'text input required, min/max word limit valid (within the boundaries)'  => [1, 10, 25, ''],
            'text input required, min word limit not reached'  => [1, 15, 25,
                get_string('minwordlimitboundary', 'qtype_essay', ['count' => 14, 'limit' => 15])],
            'text input required, max word limit is exceeded'  => [1, 5, 12,
                get_string('maxwordlimitboundary', 'qtype_essay', ['count' => 14, 'limit' => 12])],
            'text input not required, min/max word limit not set'  => [0, 5, 12, ''],
        ];
    }

    /**
     * Test get_word_count_message_for_review when users submit their input text.
     *
     * (The tests are done with a fixed 14-word response.)
     *
     * @dataProvider get_word_count_message_for_review_test_cases()
     * @param int|null $minwordlimit minimum word limit
     * @param int|null $maxwordlimit maximum word limit
     * @param string $expected error message | null
     */
    public function test_get_word_count_message_for_review(?int $minwordlimit, ?int $maxwordlimit, string $expected): void {
        $question = \test_question_maker::make_an_essay_question();
        $question->minwordlimit = $minwordlimit;
        $question->maxwordlimit = $maxwordlimit;

        $response = ['answer' => 'One two three four five six seven eight nine ten eleven twelve thirteen fourteen.'];
        $this->assertEquals($expected, $question->get_word_count_message_for_review($response));
    }

    /**
     * Data provider for test_get_word_count_message_for_review.
     *
     * @return array the test cases.
     */
    public function get_word_count_message_for_review_test_cases() {
        return [
            'No limit' =>
                    [null, null, ''],
            'min and max, answer within range' =>
                    [10, 25, get_string('wordcount', 'qtype_essay', 14)],
            'min and max, answer too short' =>
                    [15, 25, get_string('wordcounttoofew', 'qtype_essay', ['count' => 14, 'limit' => 15])],
            'min and max, answer too long' =>
                    [5, 12, get_string('wordcounttoomuch', 'qtype_essay', ['count' => 14, 'limit' => 12])],
            'min only, answer within range' =>
                    [14, null, get_string('wordcount', 'qtype_essay', 14)],
            'min only, answer too short' =>
                    [15, null, get_string('wordcounttoofew', 'qtype_essay', ['count' => 14, 'limit' => 15])],
            'max only, answer within range' =>
                    [null, 14, get_string('wordcount', 'qtype_essay', 14)],
            'max only, answer too short' =>
                    [null, 13, get_string('wordcounttoomuch', 'qtype_essay', ['count' => 14, 'limit' => 13])],
        ];
    }

    /**
     * Create sample attachemnts and retun generated attachments.
     * @param int $numberofattachments
     * @return array
     */
    private function create_user_and_sample_attachments($numberofattachments = 4) {
        // Create a new logged-in user, so we can test responses with attachments.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create sample attachments to use in testing.
        $helper = \test_question_maker::get_test_helper('essay');
        $attachments = [];
        for ($i = 0; $i < ($numberofattachments + 1); ++$i) {
            $attachments[$i] = $helper->make_attachments_saver($i);
        }
        return $attachments;
    }
}
