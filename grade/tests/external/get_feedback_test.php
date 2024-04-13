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

namespace core_grades\external;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for the core_grades\external\get_feedback webservice.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2023 Kevin Percy <kevin.percy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.2
 * @covers \core_grades\external\get_feedback
 */
class get_feedback_test extends \externallib_advanced_testcase {

    /**
     * Test get_feedback.
     *
     * @dataProvider get_feedback_provider
     * @param string|null $feedback The feedback text added for the grade item.
     * @param array $expected The expected feedback data.
     * @return void
     */
    public function test_get_feedback(?string $feedback, array $expected): void {

        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user(['firstname' => 'John', 'lastname' => 'Doe',
            'email' => 'johndoe@example.com']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $gradeitem = $this->getDataGenerator()->create_grade_item(['itemname' => 'Grade item 1',
            'courseid' => $course->id]);

        $gradegradedata = [
            'itemid' => $gradeitem->id,
            'userid' => $user->id,
        ];

        if ($feedback) {
            $gradegradedata['feedback'] = $feedback;
        }

        $this->getDataGenerator()->create_grade_grade($gradegradedata);
        $this->setAdminUser();

        $feedbackdata = get_feedback::execute($course->id, $user->id, $gradeitem->id);

        $this->assertEquals($expected['feedbacktext'], $feedbackdata['feedbacktext']);
        $this->assertEquals($expected['title'], $feedbackdata['title']);
        $this->assertEquals($expected['fullname'], $feedbackdata['fullname']);
        $this->assertEquals($expected['additionalfield'], $feedbackdata['additionalfield']);
    }

    /**
     * Data provider for test_get_feedback().
     *
     * @return array
     */
    public static function get_feedback_provider(): array {
        return [
            'Return when feedback is set.' => [
                'Test feedback',
                [
                    'feedbacktext' => 'Test feedback',
                    'title' => 'Grade item 1',
                    'fullname' => 'John Doe',
                    'additionalfield' => 'johndoe@example.com'
                ]
            ],
            'Return when feedback is not set.' => [
                null,
                [
                    'feedbacktext' => null,
                    'title' => 'Grade item 1',
                    'fullname' => 'John Doe',
                    'additionalfield' => 'johndoe@example.com'
                ]
            ]
        ];
    }

    /**
     * Test get_feedback with invalid requests.
     *
     * @dataProvider get_feedback_invalid_request_provider
     * @param string $loggeduserrole The role of the logged user.
     * @param bool $feedbacknotincourse Whether to request a feedback for a grade item which is not a part of the course.
     * @param array $expectedexception The expected exception.
     * @return void
     */
    public function test_get_feedback_invalid_request(string $loggeduserrole, bool $feedbacknotincourse,
            array $expectedexception = []): void {

        $this->resetAfterTest(true);
        // Create a course with a user and a grade item.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $gradeitem = $this->getDataGenerator()->create_grade_item(['courseid' => $course->id]);
        // Add feedback for the grade item in course.
        $gradegradedata = [
            'itemid' => $gradeitem->id,
            'userid' => $user->id,
            'feedback' => 'Test feedback',
        ];

        $this->getDataGenerator()->create_grade_grade($gradegradedata);
        // Set the current user as specified.
        if ($loggeduserrole === 'user') {
            $this->setUser($user);
        } else if ($loggeduserrole === 'guest') {
            $this->setGuestUser();
        } else {
            $this->setAdminUser();
        }

        if ($feedbacknotincourse) { // Create a new course which will be later used in the feedback request call.
            $course = $this->getDataGenerator()->create_course();
        }

        $this->expectException($expectedexception['exceptionclass']);

        if (!empty($expectedexception['exceptionmessage'])) {
            $this->expectExceptionMessage($expectedexception['exceptionmessage']);
        }

        get_feedback::execute($course->id, $user->id, $gradeitem->id);
    }

    /**
     * Data provider for test_get_feedback_invalid_request().
     *
     * @return array
     */
    public static function get_feedback_invalid_request_provider(): array {
        return [
            'Logged user does not have permissions to view feedback.' => [
                'user',
                false,
                ['exceptionclass' => \required_capability_exception::class]
            ],
            'Guest user cannot view feedback.' => [
                'guest',
                false,
                ['exceptionclass' => \require_login_exception::class]
            ],
            'Request feedback for a grade item which is not a part of the course.' => [
                'admin',
                true,
                [
                    'exceptionclass' => \invalid_parameter_exception::class,
                    'exceptionmessage' => 'Course ID and item ID mismatch',
                ]
            ]
        ];
    }
}
