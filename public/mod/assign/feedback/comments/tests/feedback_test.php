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

namespace assignfeedback_comments;

use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Unit tests for assignfeedback_comments
 *
 * @package    assignfeedback_comments
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class feedback_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Test the is_feedback_modified() method for the comments feedback.
     */
    public function test_is_feedback_modified(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'assignfeedback_comments_enabled' => 1,
            ]);

        // Create an online text submission.
        $this->add_submission($student, $assign);

        $this->setUser($teacher);

        // Create formdata.
        $grade = $assign->get_user_grade($student->id, true);
        $data = (object) [
            'assignfeedbackcomments_editor' => [
                'text' => '<p>first comment for this test</p>',
                'format' => 1,
            ]
        ];

        // This is the first time that we are submitting feedback, so it is modified.
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertTrue($plugin->is_feedback_modified($grade, $data));

        // Save the feedback.
        $plugin->save($grade, $data);

        // Try again with the same data.
        $this->assertFalse($plugin->is_feedback_modified($grade, $data));

        // Change the data.
        $data->assignfeedbackcomments_editor = [
                'text' => '<p>Altered comment for this test</p>',
                'format' => 1,
            ];
        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
    }
}
