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
 * Unit tests for the core_notes implementation of the privacy API.
 *
 * @package    core_notes
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . "/notes/lib.php");

use \core_notes\privacy\provider;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\approved_contextlist;

/**
 * Unit tests for the core_notes implementation of the privacy API.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_notes_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('enablenotes', true);

        $teacher1 = $this->getDataGenerator()->create_user();
        $this->setUser($teacher1);
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Create Courses, then enrol a teacher and a student.
        $nocourses = 5;
        $courses = [];
        $coursecontextids = [];
        for ($c = 1; $c <= $nocourses; $c++) {
            $course = $this->getDataGenerator()->create_course();
            $coursecontext = context_course::instance($course->id);

            role_assign($teacherrole->id, $teacher1->id, $coursecontext->id);
            role_assign($studentrole->id, $student->id, $coursecontext->id);

            // Only create private user notes (i.e. NOTES_STATE_DRAFT) for student in Course 1, 2, 3 written by the teacher.
            if ($c <= 3) {
                $this->help_create_user_note(
                    $student->id,
                    NOTES_STATE_DRAFT,
                    $course->id,
                    "Test private user note about the student in Course $c by the teacher"
                );
            }

            $courses[$c] = $course;
            $coursecontextids[] = $coursecontext->id;
        }

        // Test Teacher 1's contexts equals 3 because only 3 user notes were added for Course 1, 2, and 3.
        // Course 4 and 5 does not have any notes associated with it, so the contexts should not be returned.
        $contexts = provider::get_contexts_for_userid($teacher1->id);
        $this->assertCount(3, $contexts->get_contextids());

        // Test the Student's contexts is 0 because the notes written by the teacher are private.
        $contexts = provider::get_contexts_for_userid($student->id);
        $this->assertCount(0, $contexts->get_contextids());

        // Add a public user note (i.e. NOTES_STATE_PUBLIC) written by the Teacher about the Student in Course 4.
        $course = $courses[4];
        $this->help_create_user_note(
            $student->id,
            NOTES_STATE_PUBLIC,
            $course->id,
            "Test public user note about the student in Course 4 by the teacher"
        );

        // Test Teacher 1's contexts equals 4 after adding a public note about a student in Course 4.
        $contexts = provider::get_contexts_for_userid($teacher1->id);
        $this->assertCount(4, $contexts->get_contextids());

        // Test the Student's contexts is 1 for Course 4 because there is a public note written by the teacher.
        $contexts = provider::get_contexts_for_userid($student->id);
        $this->assertCount(1, $contexts->get_contextids());

        // Add a site-wide user note (i.e. NOTES_STATE_SITE) written by the Teacher 1 about the Student in Course 3.
        $course = $courses[3];
        $this->help_create_user_note(
            $student->id,
            NOTES_STATE_SITE,
            $course->id,
            "Test site-wide user note about the student in Course 3 by the teacher"
        );

        // Test the Student's contexts is 2 for Courses 3, 4 because there is a public and site-wide note written by the Teacher.
        $contexts = provider::get_contexts_for_userid($student->id);
        $this->assertCount(2, $contexts->get_contextids());

        // Add a site-wide user note for the Teacher 1 by another Teacher 2 in Course 5.
        $teacher2 = $this->getDataGenerator()->create_user();
        $this->setUser($teacher2);

        $course = $courses[5];
        $this->help_create_user_note(
            $teacher1->id,
            NOTES_STATE_SITE,
            $course->id,
            "Test site-wide user note about the teacher in Course 5 by another teacher"
        );

        // Test Teacher 1's contexts equals 5 after adding the note from another teacher.
        $contextlist = provider::get_contexts_for_userid($teacher1->id);
        $this->assertCount(5, $contextlist->get_contextids());

        // Test Teacher 1's contexts match the contexts of the Courses associated with notes created.
        $this->assertEmpty(array_diff($coursecontextids, $contextlist->get_contextids()));
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_user_data() {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('enablenotes', true);

        $teacher1 = $this->getDataGenerator()->create_user();
        $this->setUser($teacher1);
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $nocourses = 5;
        $nostudents = 2;
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $courses = [];
        $coursecontextids = [];
        for ($c = 1; $c <= $nocourses; $c++) {
            // Create a Course, then enrol a teacher and enrol 2 students.
            $course = $this->getDataGenerator()->create_course();
            $coursecontext = context_course::instance($course->id);

            role_assign($teacherrole->id, $teacher1->id, $coursecontext->id);

            // Only create public user notes (i.e. NOTES_STATE_PUBLIC) for students in Course 1, 2, 3 written by the teacher.
            if ($c <= 3) {
                for ($s = 0; $s < $nostudents; $s++) {
                    $student = $this->getDataGenerator()->create_user();
                    role_assign($studentrole->id, $student->id, $coursecontext->id);

                    // Create test public user note data written for students by the teacher.
                    $this->help_create_user_note(
                        $student->id,
                        NOTES_STATE_PUBLIC,
                        $course->id,
                        "Test public user note for student $s in Course $c by the teacher"
                    );
                }
                // Store the Course context for those which have test notes added for verification.
                $coursecontextids[] = $coursecontext->id;
            }

            $courses[$c] = $course;
        }

        // Add a site-wide user note for Teacher 1 by another Teacher 2 in Course 4.
        $teacher2 = $this->getDataGenerator()->create_user();
        $this->setUser($teacher2);

        $course = $courses[4];
        $this->help_create_user_note(
            $teacher1->id,
            NOTES_STATE_SITE,
            $course->id,
            "Test site-wide user note about the teacher in Course 4 by another teacher"
        );
        // Store the Course context for those which have test notes added for verification.
        $coursecontextids[] = context_course::instance($course->id)->id;

        // Add a private user note for Teacher 1 by another Teacher 2 in Course 5.
        $course = $courses[5];
        $this->help_create_user_note(
            $teacher1->id,
            NOTES_STATE_DRAFT,
            $course->id,
            "Test private user note about the teacher in Course 5 by another teacher"
        );

        // Test the number of contexts returned matches the Course contexts created with notes.
        $contextlist = provider::get_contexts_for_userid($teacher1->id);
        $this->assertEmpty(array_diff($coursecontextids, $contextlist->get_contextids()));

        $approvedcontextlist = new approved_contextlist($teacher1, 'core_notes', $contextlist->get_contextids());

        // Retrieve User notes created by the teacher.
        provider::export_user_data($approvedcontextlist);

        // Test the core_notes data is exported at the Course context level and has content.
        foreach ($contextlist as $context) {
            $this->assertEquals(CONTEXT_COURSE, $context->contextlevel);

            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('enablenotes', true);

        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $nocourses = 2;
        $nostudents = 5;
        $nonotes = 7;
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $n = 0;
        for ($c = 0; $c < $nocourses; $c++) {
            // Create a Course, then enrol a teacher and enrol 2 students.
            $course = $this->getDataGenerator()->create_course();
            $coursecontext = context_course::instance($course->id);

            role_assign($teacherrole->id, $teacher->id, $coursecontext->id);

            for ($s = 0; $s < $nostudents; $s++) {
                if ($n < $nonotes) {
                    $student = $this->getDataGenerator()->create_user();
                    role_assign($studentrole->id, $student->id, $coursecontext->id);

                    // Create test note data.
                    $this->help_create_user_note(
                        $student->id,
                        NOTES_STATE_PUBLIC,
                        $course->id,
                        "Test user note for student $s in Course $c"
                    );
                }
                $n++;
            }
        }

        // Test the number of contexts returned equals the number of Courses created with user notes for its students.
        $contextlist = provider::get_contexts_for_userid($teacher->id);
        $this->assertCount($nocourses, $contextlist->get_contextids());

        // Test the created user note records in mdl_post table matches the test number of user notes specified.
        $notes = $DB->get_records('post', ['module' => 'notes', 'usermodified' => $teacher->id]);
        $this->assertCount($nonotes, $notes);

        // Delete all user note records in mdl_post table by the specified Course context.
        foreach ($contextlist->get_contexts() as $context) {
            provider::delete_data_for_all_users_in_context($context);
        }

        // Test the core_note records in mdl_post table is equals zero.
        $notes = $DB->get_records('post', ['module' => 'notes', 'usermodified' => $teacher->id]);
        $this->assertCount(0, $notes);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('enablenotes', true);

        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        $nocourses = 2;
        $nostudents = 5;
        $nonotes = 7;
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $n = 0;
        for ($c = 0; $c < $nocourses; $c++) {
            // Create a Course, then enrol a teacher and enrol 2 students.
            $course = $this->getDataGenerator()->create_course();
            $coursecontext = context_course::instance($course->id);

            role_assign($teacherrole->id, $teacher->id, $coursecontext->id);

            for ($s = 0; $s < $nostudents; $s++) {
                if ($n < $nonotes) {
                    $student = $this->getDataGenerator()->create_user();
                    role_assign($studentrole->id, $student->id, $coursecontext->id);

                    // Create test note data.
                    $this->help_create_user_note(
                        $student->id,
                        NOTES_STATE_PUBLIC,
                        $course->id,
                        "Test user note for student $s in Course $c"
                    );
                }
                $n++;
            }
        }

        // Test the number of contexts returned equals the number of Courses created with user notes for its students.
        $contextlist = provider::get_contexts_for_userid($teacher->id);
        $this->assertCount($nocourses, $contextlist->get_contextids());

        // Test the created user note records in mdl_post table matches the test number of user notes specified.
        $notes = $DB->get_records('post', ['module' => 'notes', 'usermodified' => $teacher->id]);
        $this->assertCount($nonotes, $notes);

        // Delete all user note records in mdl_post table created by the specified teacher.
        $approvedcontextlist = new approved_contextlist($teacher, 'core_notes', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // Test the core_note records in mdl_post table is equals zero.
        $notes = $DB->get_records('post', ['module' => 'notes', 'usermodified' => $teacher->id]);
        $this->assertCount(0, $notes);
    }

    /**
     * Helper function to create user notes for testing.
     *
     * @param int       $userid   The ID of the User associated with the note.
     * @param string    $state    The publish status
     * @param int       $courseid The ID of the Course associated with the note.
     * @param string    $content  The note content.
     */
    protected function help_create_user_note($userid, $state, $courseid, $content) {
        $note = (object) [
            'userid' => $userid,
            'publishstate' => $state,
            'courseid' => $courseid,
            'content' => $content,
        ];
        note_save($note);
    }
}
