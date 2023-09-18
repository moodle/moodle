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
 * Base class for unit tests for mod_assign.
 *
 * @package    mod_assign
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use mod_assign\privacy\provider;

/**
 * Unit tests for mod/assign/classes/privacy/
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Convenience method for creating a submission.
     *
     * @param  assign  $assign The assign object
     * @param  stdClass  $user The user object
     * @param  string  $submissiontext Submission text
     * @param  integer $attemptnumber The attempt number
     * @return object A submission object.
     */
    protected function create_submission($assign, $user, $submissiontext, $attemptnumber = 0) {
        $submission = $assign->get_user_submission($user->id, true, $attemptnumber);
        $submission->onlinetext_editor = ['text' => $submissiontext,
                                         'format' => FORMAT_MOODLE];

        $this->setUser($user);
        $notices = [];
        $assign->save_submission($submission, $notices);
        return $submission;
    }

    /**
     * Convenience function to create an instance of an assignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return assign The assign class.
     */
    protected function create_instance($params = array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        return new \assign($context, $cm, $params['course']);
    }

    /**
     * Test that getting the contexts for a user works.
     */
    public function test_get_contexts_for_userid() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, 'student');
        // Need a second user to create content in other assignments.
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');

        // Create multiple assignments.
        // Assignment with a text submission.
        $assign1 = $this->create_instance(['course' => $course1]);
        // Assignment two in a different course that the user is not enrolled in.
        $assign2 = $this->create_instance(['course' => $course2]);
        // Assignment three has an entry in the override table.
        $assign3 = $this->create_instance(['course' => $course3, 'cutoffdate' => time()]);
        // Assignment four - blind marking.
        $assign4 = $this->create_instance(['course' => $course1, 'blindmarking' => 1]);
        // Assignment five - user flags.
        $assign5 = $this->create_instance(['course' => $course3]);

        // Override has to be manually inserted into the DB.
        $overridedata = new \stdClass();
        $overridedata->assignid = $assign3->get_instance()->id;
        $overridedata->userid = $user1->id;
        $overridedata->duedate = time();
        $DB->insert_record('assign_overrides', $overridedata);
        // Assign unique id for blind marking in assignment four for user 1.
        \assign::get_uniqueid_for_user_static($assign4->get_instance()->id, $user1->id);
        // Create an entry in the user flags table.
        $assign5->get_user_flags($user1->id, true);

        // The user will be in these contexts.
        $usercontextids = [
            $assign1->get_context()->id,
            $assign3->get_context()->id,
            $assign4->get_context()->id,
            $assign5->get_context()->id,
        ];

        $submission = new \stdClass();
        $submission->assignment = $assign1->get_instance()->id;
        $submission->userid = $user1->id;
        $submission->timecreated = time();
        $submission->onlinetext_editor = ['text' => 'Submission text',
                                         'format' => FORMAT_MOODLE];

        $this->setUser($user1);
        $notices = [];
        $assign1->save_submission($submission, $notices);

        // Create a submission for the second assignment.
        $submission->assignment = $assign2->get_instance()->id;
        $submission->userid = $user2->id;
        $this->setUser($user2);
        $assign2->save_submission($submission, $notices);

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertEquals(count($usercontextids), count($contextlist->get_contextids()));
        // There should be no difference between the contexts.
        $this->assertEmpty(array_diff($usercontextids, $contextlist->get_contextids()));
    }

    /**
     * Test returning a list of user IDs related to a context (assign).
     */
    public function test_get_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Only made a comment on a submission.
        $user1 = $this->getDataGenerator()->create_user();
        // User 2 only has information about an activity override.
        $user2 = $this->getDataGenerator()->create_user();
        // User 3 made a submission.
        $user3 = $this->getDataGenerator()->create_user();
        // User 4 makes a submission and it is marked by the teacher.
        $user4 = $this->getDataGenerator()->create_user();
        // Grading and providing feedback as a teacher.
        $user5 = $this->getDataGenerator()->create_user();
        // This user has no entries and should not show up.
        $user6 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user5->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($user6->id, $course->id, 'student');

        $assign1 = $this->create_instance(['course' => $course,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true]);
        $assign2 = $this->create_instance(['course' => $course]);

        $context = $assign1->get_context();

        // Jam an entry in the comments table for user 1.
        $comment = (object) [
            'contextid' => $context->id,
            'component' => 'assignsubmission_comments',
            'commentarea' => 'submission_comments',
            'itemid' => 5,
            'content' => 'A comment by user 1',
            'format' => 0,
            'userid' => $user1->id,
            'timecreated' => time()
        ];
        $DB->insert_record('comments', $comment);

        $this->setUser($user5); // Set the user to the teacher.

        $overridedata = new \stdClass();
        $overridedata->assignid = $assign1->get_instance()->id;
        $overridedata->userid = $user2->id;
        $overridedata->duedate = time();
        $overridedata->allowsubmissionsfromdate = time();
        $overridedata->cutoffdate = time();
        $DB->insert_record('assign_overrides', $overridedata);

        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign1, $user3, $submissiontext);

        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign1, $user4, $submissiontext);

        $this->setUser($user5);

        $grade = '72.00';
        $teachercommenttext = 'This is better. Thanks.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign1->save_grade($user4->id, $data);

        $userlist = new \core_privacy\local\request\userlist($context, 'assign');
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $this->assertTrue(in_array($user1->id, $userids));
        $this->assertTrue(in_array($user2->id, $userids));
        $this->assertTrue(in_array($user3->id, $userids));
        $this->assertTrue(in_array($user4->id, $userids));
        $this->assertTrue(in_array($user5->id, $userids));
        $this->assertFalse(in_array($user6->id, $userids));
    }

    /**
     * Test that a student with multiple submissions and grades is returned with the correct data.
     */
    public function test_export_user_data_student() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $assign = $this->create_instance([
                'course' => $course,
                'name' => 'Assign 1',
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                'maxattempts' => 3,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true
            ]);

        $context = $assign->get_context();
        // Create some submissions (multiple attempts) for a student.
        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign, $user, $submissiontext);

        $this->setUser($teacher);

        $overridedata = new \stdClass();
        $overridedata->assignid = $assign->get_instance()->id;
        $overridedata->userid = $user->id;
        $overridedata->duedate = time();
        $overridedata->allowsubmissionsfromdate = time();
        $overridedata->cutoffdate = time();
        $DB->insert_record('assign_overrides', $overridedata);

        $grade1 = '67.00';
        $teachercommenttext = 'Please try again.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade1;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user->id, $data);

        $submissiontext2 = 'My second submission';
        $submission = $this->create_submission($assign, $user, $submissiontext2, 1);

        $this->setUser($teacher);

        $grade2 = '72.00';
        $teachercommenttext2 = 'This is better. Thanks.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade2;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext2, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user->id, $data);

        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have some text submitted.
        // Add the course context as well to make sure there is no error.
        $approvedlist = new approved_contextlist($user, 'mod_assign', [$context->id, $coursecontext->id]);
        provider::export_user_data($approvedlist);

        // Check that we have general details about the assignment.
        $this->assertEquals('Assign 1', $writer->get_data()->name);
        // Check Submissions.
        $this->assertEquals($submissiontext, $writer->get_data(['attempt 1', 'Submission Text'])->text);
        $this->assertEquals($submissiontext2, $writer->get_data(['attempt 2', 'Submission Text'])->text);
        $this->assertEquals(1, $writer->get_data(['attempt 1', 'submission'])->attemptnumber);
        $this->assertEquals(2, $writer->get_data(['attempt 2', 'submission'])->attemptnumber);
        // Check grades.
        $this->assertEquals((float)$grade1, $writer->get_data(['attempt 1', 'grade'])->grade);
        $this->assertEquals((float)$grade2, $writer->get_data(['attempt 2', 'grade'])->grade);
        // Check feedback.
        $this->assertStringContainsString($teachercommenttext, $writer->get_data(['attempt 1', 'Feedback comments'])->commenttext);
        $this->assertStringContainsString($teachercommenttext2, $writer->get_data(['attempt 2', 'Feedback comments'])->commenttext);

        // Check override data was exported correctly.
        $overrideexport = $writer->get_data(['Overrides']);
        $this->assertEquals(\core_privacy\local\request\transform::datetime($overridedata->duedate),
                $overrideexport->duedate);
        $this->assertEquals(\core_privacy\local\request\transform::datetime($overridedata->cutoffdate),
                $overrideexport->cutoffdate);
        $this->assertEquals(\core_privacy\local\request\transform::datetime($overridedata->allowsubmissionsfromdate),
                $overrideexport->allowsubmissionsfromdate);
    }

    /**
     * Tests the data returned for a teacher.
     */
    public function test_export_user_data_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $assign = $this->create_instance([
                'course' => $course,
                'name' => 'Assign 1',
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                'maxattempts' => 3,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true
            ]);

        $context = $assign->get_context();

        // Create and grade some submissions from the students.
        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign, $user1, $submissiontext);

        $this->setUser($teacher);

        $grade1 = '54.00';
        $teachercommenttext = 'Comment on user 1 attempt 1.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade1;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user1->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext2 = 'My first submission for user 2';
        $submission = $this->create_submission($assign, $user2, $submissiontext2);

        $this->setUser($teacher);

        $grade2 = '56.00';
        $teachercommenttext2 = 'Comment on user 2 first attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade2;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext2, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext3 = 'My second submission for user 2';
        $submission = $this->create_submission($assign, $user2, $submissiontext3, 1);

        $this->setUser($teacher);

        $grade3 = '83.00';
        $teachercommenttext3 = 'Comment on user 2 another attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade3;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext3, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Set up some flags.
        $duedate = time();
        $flagdata = $assign->get_user_flags($teacher->id, true);
        $flagdata->mailed = 1;
        $flagdata->extensionduedate = $duedate;
        $assign->update_user_flags($flagdata);

        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have some text submitted.
        $approvedlist = new approved_contextlist($teacher, 'mod_assign', [$context->id, $coursecontext->id]);
        provider::export_user_data($approvedlist);

        // Check flag metadata.
        $metadata = $writer->get_all_metadata();
        $this->assertEquals(\core_privacy\local\request\transform::yesno(1), $metadata['mailed']->value);
        $this->assertEquals(\core_privacy\local\request\transform::datetime($duedate), $metadata['extensionduedate']->value);

        // Check for student grades given.
        $student1grade = $writer->get_data(['studentsubmissions', $user1->id, 'attempt 1', 'grade']);
        $this->assertEquals((float)$grade1, $student1grade->grade);
        $student2grade1 = $writer->get_data(['studentsubmissions', $user2->id, 'attempt 1', 'grade']);
        $this->assertEquals((float)$grade2, $student2grade1->grade);
        $student2grade2 = $writer->get_data(['studentsubmissions', $user2->id, 'attempt 2', 'grade']);
        $this->assertEquals((float)$grade3, $student2grade2->grade);
        // Check for feedback given to students.
        $this->assertStringContainsString($teachercommenttext, $writer->get_data(['studentsubmissions', $user1->id, 'attempt 1',
                'Feedback comments'])->commenttext);
        $this->assertStringContainsString($teachercommenttext2, $writer->get_data(['studentsubmissions', $user2->id, 'attempt 1',
                'Feedback comments'])->commenttext);
        $this->assertStringContainsString($teachercommenttext3, $writer->get_data(['studentsubmissions', $user2->id, 'attempt 2',
                'Feedback comments'])->commenttext);
    }

    /**
     * A test for deleting all user data for a given context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $assign = $this->create_instance([
                'course' => $course,
                'name' => 'Assign 1',
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                'maxattempts' => 3,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true
            ]);

        $context = $assign->get_context();

        // Create and grade some submissions from the students.
        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign, $user1, $submissiontext);

        $this->setUser($teacher);

        // Overrides for both students.
        $overridedata = new \stdClass();
        $overridedata->assignid = $assign->get_instance()->id;
        $overridedata->userid = $user1->id;
        $overridedata->duedate = time();
        $DB->insert_record('assign_overrides', $overridedata);
        $overridedata->userid = $user2->id;
        $DB->insert_record('assign_overrides', $overridedata);
        assign_update_events($assign);

        $grade1 = '54.00';
        $teachercommenttext = 'Comment on user 1 attempt 1.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade1;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user1->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext2 = 'My first submission for user 2';
        $submission = $this->create_submission($assign, $user2, $submissiontext2);

        $this->setUser($teacher);

        $grade2 = '56.00';
        $teachercommenttext2 = 'Comment on user 2 first attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade2;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext2, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext3 = 'My second submission for user 2';
        $submission = $this->create_submission($assign, $user2, $submissiontext3, 1);

        $this->setUser($teacher);

        $grade3 = '83.00';
        $teachercommenttext3 = 'Comment on user 2 another attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade3;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext3, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Delete all user data for this assignment.
        provider::delete_data_for_all_users_in_context($context);

        // Check all relevant tables.
        $records = $DB->get_records('assign_submission');
        $this->assertEmpty($records);
        $records = $DB->get_records('assign_grades');
        $this->assertEmpty($records);
        $records = $DB->get_records('assignsubmission_onlinetext');
        $this->assertEmpty($records);
        $records = $DB->get_records('assignfeedback_comments');
        $this->assertEmpty($records);

        // Check that overrides and the calendar events are deleted.
        $records = $DB->get_records('event');
        $this->assertEmpty($records);
        $records = $DB->get_records('assign_overrides');
        $this->assertEmpty($records);
    }

    /**
     * A test for deleting all user data for one user.
     */
    public function test_delete_data_for_user() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $coursecontext = \context_course::instance($course->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $assign = $this->create_instance([
                'course' => $course,
                'name' => 'Assign 1',
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                'maxattempts' => 3,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true
            ]);

        $context = $assign->get_context();

        // Create and grade some submissions from the students.
        $submissiontext = 'My first submission';
        $submission1 = $this->create_submission($assign, $user1, $submissiontext);

        $this->setUser($teacher);

        // Overrides for both students.
        $overridedata = new \stdClass();
        $overridedata->assignid = $assign->get_instance()->id;
        $overridedata->userid = $user1->id;
        $overridedata->duedate = time();
        $DB->insert_record('assign_overrides', $overridedata);
        $overridedata->userid = $user2->id;
        $DB->insert_record('assign_overrides', $overridedata);
        assign_update_events($assign);

        $grade1 = '54.00';
        $teachercommenttext = 'Comment on user 1 attempt 1.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade1;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user1->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext2 = 'My first submission for user 2';
        $submission2 = $this->create_submission($assign, $user2, $submissiontext2);

        $this->setUser($teacher);

        $grade2 = '56.00';
        $teachercommenttext2 = 'Comment on user 2 first attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 0;
        $data->grade = $grade2;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext2, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Create and grade some submissions from the students.
        $submissiontext3 = 'My second submission for user 2';
        $submission3 = $this->create_submission($assign, $user2, $submissiontext3, 1);

        $this->setUser($teacher);

        $grade3 = '83.00';
        $teachercommenttext3 = 'Comment on user 2 another attempt.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade3;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext3, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign->save_grade($user2->id, $data);

        // Delete user 2's data.
        $approvedlist = new approved_contextlist($user2, 'mod_assign', [$context->id, $coursecontext->id]);
        provider::delete_data_for_user($approvedlist);

        // Check all relevant tables.
        $records = $DB->get_records('assign_submission');
        foreach ($records as $record) {
            $this->assertEquals($user1->id, $record->userid);
            $this->assertNotEquals($user2->id, $record->userid);
        }
        $records = $DB->get_records('assign_grades');
        foreach ($records as $record) {
            $this->assertEquals($user1->id, $record->userid);
            $this->assertNotEquals($user2->id, $record->userid);
        }
        $records = $DB->get_records('assignsubmission_onlinetext');
        $this->assertCount(1, $records);
        $record = array_shift($records);
        // The only submission is for user 1.
        $this->assertEquals($submission1->id, $record->submission);
        $records = $DB->get_records('assignfeedback_comments');
        $this->assertCount(1, $records);
        $record = array_shift($records);
        // The only record is the feedback comment for user 1.
        $this->assertEquals($teachercommenttext, $record->commenttext);

        // Check calendar events as well as assign overrides.
        $records = $DB->get_records('event');
        $this->assertCount(1, $records);
        $record = array_shift($records);
        // The remaining event should be for user 1.
        $this->assertEquals($user1->id, $record->userid);
        // Now for assign_overrides
        $records = $DB->get_records('assign_overrides');
        $this->assertCount(1, $records);
        $record = array_shift($records);
        // The remaining event should be for user 1.
        $this->assertEquals($user1->id, $record->userid);
    }

    /**
     * A test for deleting all user data for a bunch of users.
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Only made a comment on a submission.
        $user1 = $this->getDataGenerator()->create_user();
        // User 2 only has information about an activity override.
        $user2 = $this->getDataGenerator()->create_user();
        // User 3 made a submission.
        $user3 = $this->getDataGenerator()->create_user();
        // User 4 makes a submission and it is marked by the teacher.
        $user4 = $this->getDataGenerator()->create_user();
        // Grading and providing feedback as a teacher.
        $user5 = $this->getDataGenerator()->create_user();
        // This user has entries in assignment 2 and should not have their data deleted.
        $user6 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user5->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($user6->id, $course->id, 'student');

        $assign1 = $this->create_instance(['course' => $course,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true]);
        $assign2 = $this->create_instance(['course' => $course,
                'assignsubmission_onlinetext_enabled' => true,
                'assignfeedback_comments_enabled' => true]);

        $context = $assign1->get_context();

        // Jam an entry in the comments table for user 1.
        $comment = (object) [
            'contextid' => $context->id,
            'component' => 'assignsubmission_comments',
            'commentarea' => 'submission_comments',
            'itemid' => 5,
            'content' => 'A comment by user 1',
            'format' => 0,
            'userid' => $user1->id,
            'timecreated' => time()
        ];
        $DB->insert_record('comments', $comment);

        $this->setUser($user5); // Set the user to the teacher.

        $overridedata = new \stdClass();
        $overridedata->assignid = $assign1->get_instance()->id;
        $overridedata->userid = $user2->id;
        $overridedata->duedate = time();
        $overridedata->allowsubmissionsfromdate = time();
        $overridedata->cutoffdate = time();
        $DB->insert_record('assign_overrides', $overridedata);

        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign1, $user3, $submissiontext);

        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign1, $user4, $submissiontext);

        $submissiontext = 'My first submission';
        $submission = $this->create_submission($assign2, $user6, $submissiontext);

        $this->setUser($user5);

        $grade = '72.00';
        $teachercommenttext = 'This is better. Thanks.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign1->save_grade($user4->id, $data);

        $this->setUser($user5);

        $grade = '81.00';
        $teachercommenttext = 'This is nice.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade;
        $data->assignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $assign2->save_grade($user6->id, $data);

        // Check data is in place.
        $data = $DB->get_records('assign_submission');
        // We should have one entry for user 3 and two entries each for user 4 and 6.
        $this->assertCount(5, $data);
        $usercounts = [
            $user3->id => 0,
            $user4->id => 0,
            $user6->id => 0
        ];
        foreach ($data as $datum) {
            $usercounts[$datum->userid]++;
        }
        $this->assertEquals(1, $usercounts[$user3->id]);
        $this->assertEquals(2, $usercounts[$user4->id]);
        $this->assertEquals(2, $usercounts[$user6->id]);

        $data = $DB->get_records('assign_grades');
        // Two entries in assign_grades, one for each grade given.
        $this->assertCount(2, $data);

        $data = $DB->get_records('assign_overrides');
        $this->assertCount(1, $data);

        $data = $DB->get_records('comments');
        $this->assertCount(1, $data);

        $userlist = new \core_privacy\local\request\approved_userlist($context, 'assign', [$user1->id, $user2->id]);
        provider::delete_data_for_users($userlist);

        $data = $DB->get_records('assign_overrides');
        $this->assertEmpty($data);

        $data = $DB->get_records('comments');
        $this->assertEmpty($data);

        $data = $DB->get_records('assign_submission');
        // No change here.
        $this->assertCount(5, $data);

        $userlist = new \core_privacy\local\request\approved_userlist($context, 'assign', [$user3->id, $user5->id]);
        provider::delete_data_for_users($userlist);

        $data = $DB->get_records('assign_submission');
        // Only the record for user 3 has been deleted.
        $this->assertCount(4, $data);

        $data = $DB->get_records('assign_grades');
        // Grades should be unchanged.
        $this->assertCount(2, $data);
    }
}
