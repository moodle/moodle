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
 * Privacy test for the event monitor
 *
 * @package    mod_assignment
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use \mod_assignment\privacy\provider;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use \core_privacy\tests\provider_testcase;

/**
 * Privacy test for the event monitor
 *
 * @package    mod_assignment
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assignment_privacy_testcase extends advanced_testcase {

    /**
     * @var int array   Array of test student ids associated for Course 1.
     */
    private $course1students = [];

    /**
     * @var int array   Array of test student ids associated for Course 2.
     */
    private $course2students = [];

    /**
     * @var int array   Array of test assignments associated for Course 1.
     */
    private $course1assignments = [];

    /**
     * @var int array   Array of test assignments associated for Course 2.
     */
    private $course2assignments = [];

    /**
     * Test for provider::get_contexts_for_userid().
     *
     * @throws coding_exception
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $this->resetAfterTest(true);
        $this->create_courses_and_assignments();

        // Get Teacher 1 to test get_contexts_for_userid().
        $teacher1 = $DB->get_record('user', ['username' => 'teacher1']);
        $contextids = provider::get_contexts_for_userid($teacher1->id);
        // Verify there should be 4 contexts, as Teacher 1 has submitted tests and marked Assignments in Course 1 and 2.
        $this->assertEquals(4, count($contextids->get_contextids()));

        // Get Teacher 2 to test get_contexts_for_userid().
        $teacher2 = $DB->get_record('user', ['username' => 'teacher2']);
        $contextids = provider::get_contexts_for_userid($teacher2->id);
        // Verify there should be 0 contexts, as teacher 2 has not marked any Assignments.
        $this->assertEquals(0, count($contextids->get_contextids()));

        // Get Student 1 to test get_contexts_for_userid().
        $student1 = $DB->get_record('user', ['username' => 'student1']);
        $contextids = provider::get_contexts_for_userid($student1->id);
        // Verify there should be 2 contexts, as student 1 added submissions for both Assignments in Course 1.
        $this->assertEquals(2, count($contextids->get_contextids()));

        // Get Student 2 to test get_contexts_for_userid().
        $student2 = $DB->get_record('user', ['username' => 'student2']);
        $contextids = provider::get_contexts_for_userid($student2->id);
        // Verify there should be 2 context, as student 2 added submissions for both Assignments in Course 2.
        $this->assertEquals(2, count($contextids->get_contextids()));
    }

    /**
     * Test for provider::export_user_data().
     *
     * @throws coding_exception
     */
    public function test_export_user_data_teacher() {
        global $DB;

        $this->resetAfterTest(true);
        $this->create_courses_and_assignments();

        // Test Teacher 1 export_data_for_user() - marking assignment submissions for both Course 1 and 2.
        $teacher1 = $DB->get_record('user', ['username' => 'teacher1']);

        $contextlist = provider::get_contexts_for_userid($teacher1->id);
        $approvedcontextlist = new approved_contextlist($teacher1, 'mod_assignment', $contextlist->get_contextids());

        // Verify Teacher 1 has four contexts.
        $this->assertCount(4, $contextlist->get_contextids());

        // Retrieve Assignment Submissions data for Teacher 1.
        provider::export_user_data($approvedcontextlist);

        $contexts = $contextlist->get_contexts();

        // Context 1 - Course 1's Assignment 1 -- (onlinetext).
        $context = context_module::instance($this->course1assignments['ass1']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        $subcontexts = [
            get_string('privacy:markedsubmissionspath', 'mod_assignment'),
            transform::user($teacher1->id)
        ];
        // Verify the test assignment submission from Teacher 1 exists.
        $submission = $writer->get_data($subcontexts);
        $this->assertEquals('<p>Course 1 - Ass 1: Teacher Test Submission</p>', $submission->data1);

        foreach ($this->course1students as $student) {
            $subcontexts = [
                get_string('privacy:markedsubmissionspath', 'mod_assignment'),
                transform::user($student->id)
            ];
            // Verify the student assignment submissions exists.
            $submission = $writer->get_data($subcontexts);
            $this->assertEquals("<p>Course 1 - Ass 1: " . $student->id . "</p>", $submission->data1);
        }

        // Context 2 - Course 1's Assignment 2 -- (single file upload).
        $context = context_module::instance($this->course1assignments['ass2']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        foreach ($this->course1students as $student) {
            $subcontexts = [
                get_string('privacy:markedsubmissionspath', 'mod_assignment'),
                transform::user($student->id)
            ];
            // Verify the student assignment submissions exists.
            $submission = $writer->get_data($subcontexts);
            $this->assertEquals("<p>Course 1 - Ass 2: " . $student->id . "</p>", $submission->data1);

            // Verify the student assignment submission file upload exists.
            $submissionfiles = $writer->get_files($subcontexts);
            $this->assertTrue(array_key_exists('Student' . $student->id . '-Course1-Ass2-(File 1 of 1)', $submissionfiles));
        }

        // Context 3 - Course 2's Assignment 1 -- (offline).
        $context = context_module::instance($this->course2assignments['ass1']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        foreach ($this->course2students as $student) {
            $subcontexts = [
                get_string('privacy:markedsubmissionspath', 'mod_assignment'),
                transform::user($student->id)
            ];
            // Verify the student assignment submissions exists.
            $submission = $writer->get_data($subcontexts);
            $this->assertEquals("<p>Course 2 - Ass 1: " . $student->id . "</p>", $submission->data1);
        }

        // Context 4 - Course 2's Assignment 2 -- (multiple file upload).
        $context = context_module::instance($this->course2assignments['ass2']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        foreach ($this->course2students as $student) {
            $subcontexts = [
                get_string('privacy:markedsubmissionspath', 'mod_assignment'),
                transform::user($student->id)
            ];
            // Verify the student assignment submissions exists.
            $submission = $writer->get_data($subcontexts);
            $this->assertEquals("<p>Course 2 - Ass 2: " . $student->id . "</p>", $submission->data1);

            // Verify the student assignment submission file upload exists.
            $submissionfiles = $writer->get_files($subcontexts);
            $this->assertTrue(array_key_exists('Student' . $student->id . '-Course2-Ass2-(File 1 of 2)', $submissionfiles));
            $this->assertTrue(array_key_exists('Student' . $student->id . '-Course2-Ass2-(File 2 of 2)', $submissionfiles));
        }
    }

    /**
     * Test for provider::export_user_data().
     *
     * @throws dml_exception
     */
    public function test_export_user_data_student() {
        global $DB;

        $this->resetAfterTest(true);
        $this->create_courses_and_assignments();

        // Test Student 1 export_data_for_user() - added assignment submissions for both assignments in Course 1.
        $student1 = $DB->get_record('user', ['username' => 'student1']);

        $contextlist = provider::get_contexts_for_userid($student1->id);
        $approvedcontextlist = new approved_contextlist($student1, 'mod_assignment', $contextlist->get_contextids());

        // Retrieve Assignment Submissions data for Student 1.
        provider::export_user_data($approvedcontextlist);
        $contexts = $contextlist->get_contexts();

        // Context 1 - Course 1's Assignment 1 -- (onlinetext).
        $context = context_module::instance($this->course1assignments['ass1']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        $subcontexts = [
            get_string('privacy:submissionpath', 'mod_assignment')
        ];

        // Verify the student assignment submissions exists.
        $submission = $writer->get_data($subcontexts);
        $this->assertEquals("<p>Course 1 - Ass 1: " . $student1->id . "</p>", $submission->data1);

        // Context 2 - Course 1's Assignment 2 -- (single file upload).
        $context = context_module::instance($this->course1assignments['ass2']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        $subcontexts = [
            get_string('privacy:submissionpath', 'mod_assignment')
        ];

        // Verify the student assignment submission exists.
        $submission = $writer->get_data($subcontexts);
        $this->assertEquals("<p>Course 1 - Ass 2: " . $student1->id . "</p>", $submission->data1);

        // Verify the student assignment submission file upload exists.
        $submissionfiles = $writer->get_files($subcontexts);
        $this->assertTrue(array_key_exists('Student' . $student1->id . '-Course1-Ass2-(File 1 of 1)', $submissionfiles));

        // Test Student 2 export_data_for_user() - added assignment submissions for both assignments in Course 2.
        $student2 = $DB->get_record('user', ['username' => 'student2']);

        $contextlist = provider::get_contexts_for_userid($student2->id);
        $approvedcontextlist = new approved_contextlist($student2, 'mod_assignment', $contextlist->get_contextids());

        // Retrieve Assignment Submissions data for Student 2.
        provider::export_user_data($approvedcontextlist);
        $contexts = $contextlist->get_contexts();

        // Context 1 - Course 2's Assignment 1 -- (offline).
        $context = context_module::instance($this->course2assignments['ass1']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        $subcontexts = [
            get_string('privacy:submissionpath', 'mod_assignment')
        ];

        // Verify the student assignment submissions exists.
        $submission = $writer->get_data($subcontexts);
        $this->assertEquals("<p>Course 2 - Ass 1: " . $student2->id . "</p>", $submission->data1);

        // Context 2 - Course 2's Assignment 2 -- (multiple file upload).
        $context = context_module::instance($this->course2assignments['ass2']->cmid);
        $this->assertContains($context, $contexts);

        $writer = writer::with_context($context);
        $subcontexts = [
            get_string('privacy:submissionpath', 'mod_assignment')
        ];

        // Verify the student assignment submission exists.
        $submission = $writer->get_data($subcontexts);
        $this->assertEquals("<p>Course 2 - Ass 2: " . $student2->id . "</p>", $submission->data1);

        // Verify the student assignment submission file upload exists.
        $submissionfiles = $writer->get_files($subcontexts);
        $this->assertTrue(array_key_exists('Student' . $student2->id . '-Course2-Ass2-(File 1 of 2)', $submissionfiles));
        $this->assertTrue(array_key_exists('Student' . $student2->id . '-Course2-Ass2-(File 2 of 2)', $submissionfiles));
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     *
     * @throws dml_exception
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest(true);
        $this->create_courses_and_assignments();

        // Test teacher1 delete_data_for_all_users_in_context().
        $teacher1 = $DB->get_record('user', ['username' => 'teacher1']);
        $contextlist = provider::get_contexts_for_userid($teacher1->id);

        foreach ($contextlist as $context) {
            provider::delete_data_for_all_users_in_context($context);

            // Verify assignment submission(s) were deleted for the context.
            $deleted = $this->get_assignment_submissions($context->id);
            $this->assertCount(0, $deleted);

            // Verify all the file submissions associated with the context for all users were deleted.
            $files = $DB->get_records('files', ['component' => 'mod_assignment', 'filearea' => 'submission', 'contextid' => $context->id]);
            $this->assertEquals(0, count($files));
        }
    }

    /**
     * Test for provider::delete_data_for_user().
     *
     * @throws dml_exception
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest(true);
        $this->create_courses_and_assignments();

        // Test Teacher 1 delete_data_for_user(), should only remove the 1 test submission added by Teacher 1.
        // Should not remove any assignment submission records marked by the teacher.
        $teacher1 = $DB->get_record('user', ['username' => 'teacher1']);
        $contextlist = provider::get_contexts_for_userid($teacher1->id);
        $approvedcontextlist = new approved_contextlist($teacher1, 'mod_assignment', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // Verify the submissions submitted by students still exists.
        $markedsubmissions = $DB->get_records('assignment_submissions', ['teacher' => $teacher1->id]);
        $this->assertCount(4, $markedsubmissions);

        // Test student 1 delete_data_for_user().
        $student1 = $DB->get_record('user', ['username' => 'student1']);
        $contextlist = provider::get_contexts_for_userid($student1->id);
        $approvedcontextlist = new approved_contextlist($student1, 'mod_assignment', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // Verify student 1's assignment submissions were deleted.
        $assignmentsubmissions = $DB->get_records('assignment_submissions', ['userid' => $student1->id]);
        $this->assertEquals(0, count($assignmentsubmissions));

        // Verify student 1's file submissions were deleted.
        foreach ($contextlist->get_contextids() as $contextid) {
            $files = $DB->get_records('files', ['component' => 'mod_assignment', 'filearea' => 'submission', 'contextid' => $contextid]);
            $this->assertEquals(0, count($files));
        }
    }

    // Start of helper functions.

    /**
     * Helper function to setup Course, users, and assignments for testing.
     */
    protected function create_courses_and_assignments() {
        // Create Courses, Users, and Assignments.
        $course1 = $this->getDataGenerator()->create_course(['shortname' => 'course1']);
        $course2 = $this->getDataGenerator()->create_course(['shortname' => 'course2']);

        $teacher1 = $this->getDataGenerator()->create_user(['username' => 'teacher1']);
        $teacher2 = $this->getDataGenerator()->create_user(['username' => 'teacher2']);

        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);

        $this->course1students = [
            $student1
        ];

        $this->course2students = [
            $student2
        ];

        $course1assignment1 = $this->getDataGenerator()->create_module('assignment',
            [
                'course' => $course1->id,
                'name' => 'Course 1 - Assignment 1 (onlinetext)',
                'assignmenttype' => 'onlinetext',
            ]
        );
        $course1assignment2 = $this->getDataGenerator()->create_module('assignment',
            [
                'course' => $course1->id,
                'name' => 'Course 1 - Assignment 2 (single file upload)',
                'assignmenttype' => 'uploadsingle',
            ]
        );

        $this->course1assignments = [
            'ass1' => $course1assignment1,
            'ass2' => $course1assignment2
        ];

        $course2assignment1 = $this->getDataGenerator()->create_module('assignment',
            [
                'course' => $course2->id,
                'name' => 'Course 2 - Assignment 1 (offline)',
                'assignmenttype' => 'offline',
            ]
        );
        $course2assignment2 = $this->getDataGenerator()->create_module('assignment',
            [
                'course' => $course2->id,
                'name' => 'Course 2 - Assignment 2 (multiple file upload)',
                'assignmenttype' => 'upload',
            ]
        );

        $this->course2assignments = [
            'ass1' => $course2assignment1,
            'ass2' => $course2assignment2
        ];

        // Teacher 1 add test assignment submission for Course 1 - Assignment 1.
        $this->add_assignment_submission(
            $course1assignment1,
            $teacher1,
            "Course 1 - Ass 1: Teacher Test Submission"
        );

        // Student 1 add assignment submissions for Course 1 - Assignment 1 and 2.
        $this->add_assignment_submission(
            $course1assignment1,
            $student1,
            "Course 1 - Ass 1: " . $student1->id
        );
        $this->add_file_assignment_submission(
            $course1assignment2,
            $student1,
            "Course 1 - Ass 2: " . $student1->id,
            'Student' . $student1->id . '-Course1-Ass2'
        );

        // Student 2 add assignment submissions for Course 2 - Assignment 1 and 2.
        $this->add_assignment_submission(
            $course2assignment1,
            $student2,
            "Course 2 - Ass 1: " . $student2->id
        );
        $this->add_file_assignment_submission(
            $course2assignment2,
            $student2,
            "Course 2 - Ass 2: " . $student2->id,
            'Student' . $student2->id . '-Course2-Ass2',
            2
        );

        // Teacher 1 to mark assignment submissions for Course 1's Assignment 1 and 2.
        $course1submissions = $this->get_course_assignment_submissions($course1->id);
        foreach ($course1submissions as $submission) {
            $this->mark_assignment_submission($submission->assignment, $submission->id, $teacher1, 49);
        }

        // Teacher 1 to mark assignment submissions for Course 2's Assignment 1 and 2.
        $course2submissions = $this->get_course_assignment_submissions($course2->id);
        foreach ($course2submissions as $submission) {
            $this->mark_assignment_submission($submission->assignment, $submission->id, $teacher1, 50);
        }
    }

    /**
     * Helper function to add an assignment submission for testing.
     *
     * @param object $assignment        Object containing assignment submission details to create for testing.
     * @param object $user              Object of the user making the assignment submission.
     * @param string $submissiondata    The onlintext string value of the assignment submission.
     * @throws dml_exception
     */
    protected function add_assignment_submission($assignment, $user, $submissiondata) {
        global $DB;

        $submission = (object) [
            'assignment' => $assignment->id,
            'userid' => $user->id,
            'timecreated' => date('U'),
            'data1' => '<p>' . $submissiondata . '</p>',
            'submissioncomment' => 'My submission by ' . $user->username
        ];

        return $DB->insert_record('assignment_submissions', $submission);
    }

    /**
     * Helper function to add an assignment submission with file submissions for testing.
     *
     * @param object $assignment        Object containing assignment submission details to create for testing.
     * @param object $user              Object of the user making the assignment submission.
     * @param string $submissiondata    The onlintext string value of the assignment submission.
     * @param string $filename          The filename of the file submission included with the assignment submission.
     * @param int $numfiles             The number of files included with the assignment submission.
     * @throws dml_exception
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    protected function add_file_assignment_submission($assignment, $user, $submissiondata, $filename, $numfiles = 1) {
        global $CFG, $DB;

        $submission = (object) [
            'assignment' => $assignment->id,
            'userid' => $user->id,
            'timecreated' => date('U'),
            'data1' => '<p>' . $submissiondata . '</p>',
            'numfiles' => $numfiles,
            'submissioncomment' => 'My submission by ' . $user->username
        ];

        $submissionid = $DB->insert_record('assignment_submissions', $submission);

        // Create a file submission with the test pdf.
        $this->setUser($user->id);
        $context = context_module::instance($assignment->cmid);

        $fs = get_file_storage();
        $sourcefile = $CFG->dirroot . '/mod/assign/feedback/editpdf/tests/fixtures/submission.pdf';

        for ($f = 1; $f <= $numfiles; $f++) {
            $pdfsubmission = (object)array(
                'contextid' => $context->id,
                'component' => 'mod_assignment',
                'filearea' => 'submission',
                'itemid' => $submissionid,
                'filepath' => '/',
                'filename' => $filename . "-(File $f of $numfiles)"
            );
            $fs->create_file_from_pathname($pdfsubmission, $sourcefile);
        }
    }

    /**
     * Helper function to retrieve the assignment submission records for a given course.
     *
     * @param int $courseid     The course ID to get assignment submissions by.
     * @return array            Array of assignment submission details.
     * @throws dml_exception
     */
    protected function get_course_assignment_submissions($courseid) {
        global $DB;

        $sql = "SELECT s.id,
                       s.assignment,
                       s.userid,
                       s.timecreated,
                       s.timemodified,
                       s.numfiles,
                       s.data1,
                       s.data2,
                       s.grade,
                       s.submissioncomment,
                       s.format,
                       s.teacher,
                       s.timemarked,
                       s.mailed
                  FROM {assignment} a
                  JOIN {assignment_submissions} s ON s.assignment = a.id
                 WHERE a.course = :courseid";
        $params = [
            'courseid' => $courseid
        ];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to update an assignment submission with grading details for a teacher.
     *
     * @param int $assignmentid     The assignment ID to update assignment submissions with marking/graded details.
     * @param int $submissionid     The assignment submission ID to update with marking/grading details.
     * @param int $teacher          The teacher user ID to making the marking/grading details.
     * @param int $gradedata        The grade value set for the marking/grading details.
     */
    protected function mark_assignment_submission($assignmentid, $submissionid, $teacher, $gradedata) {
        global $DB;

        $submission = (object) [
            'id' => $submissionid,
            'assignment' => $assignmentid,
            'grade' => $gradedata,
            'teacher' => $teacher->id,
            'timemarked' => date('U')
        ];

        return $DB->update_record('assignment_submissions', $submission);
    }

    /**
     * Helper function to retrieve the assignment records for a given context.
     *
     * @param int $contextid    The context module ID value to retrieve assignment IDs by.
     * @return array            Array of assignment IDs.
     * @throws dml_exception
     */
    protected function get_assignments($contextid) {
        global $DB;

        $sql = "SELECT a.id
                  FROM {assignment} a
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextmodule
                 WHERE ctx.id = :contextid";
        $params = [
            'modulename' => 'assignment',
            'contextmodule' => CONTEXT_MODULE,
            'contextid' => $contextid
        ];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to retrieve the assignment submission records for a given context.
     *
     * @param int $contextid    The context module ID value to retrieve assignment submission IDs by.
     * @return array            Array of assignment submission IDs.
     * @throws dml_exception
     */
    protected function get_assignment_submissions($contextid) {
        global $DB;

        $sql = "SELECT s.id
                  FROM {assignment_submissions} s
                  JOIN {assignment} a ON a.id = s.assignment
                  JOIN {course_modules} cm ON a.id = cm.instance
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextmodule
                 WHERE ctx.id = :contextid";
        $params = [
            'modulename' => 'assignment',
            'contextmodule' => CONTEXT_MODULE,
            'contextid' => $contextid
        ];

        return $DB->get_records_sql($sql, $params);
    }
}
