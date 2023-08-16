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

namespace mod_qbassign;

use mod_qbassign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../locallib.php');
require_once($CFG->dirroot . '/mod/qbassign/tests/generator.php');

/**
 * Unit tests for (some of) mod/qbassign/locallib.php.
 *
 * @package    mod_qbassign
 * @category   test
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_participants_test extends \advanced_testcase {
    use mod_qbassign_test_generator;

    public function test_list_participants_blind_marking() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $roles = $DB->get_records('role', null, '', 'shortname, id');
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher->id,
                $course->id,
                $roles['teacher']->id);

        $this->setUser($teacher);

        // Enrol two students.
        $students = [];
        for ($i = 0; $i < 2; $i++) {
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id,
                    $course->id,
                    $roles['student']->id);
            $students[$student->id] = $student;
        }

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassign');
        $instance = $generator->create_instance(['course' => $course->id, 'blindmarking' => 1]);
        $cm = get_coursemodule_from_instance('qbassign', $instance->id);
        $context = \context_module::instance($cm->id);
        $qbassign = new \qbassign($context, $cm, $course);

        // Allocate IDs now.
        // We're testing whether the IDs are correct after allocation.
        \qbassign::allocate_unique_ids($qbassign->get_instance()->id);

        $participants = $qbassign->list_participants(null, false);

        // There should be exactly two participants and they should be the students.
        $this->assertCount(2, $participants);
        foreach ($participants as $participant) {
            $this->assertArrayHasKey($participant->id, $students);
        }

        $keys = array_keys($participants);

        // Create a grading table, and query the DB This should have the same order.
        $table = new \qbassign_grading_table($qbassign, 10, '', 0, false);
        $table->setup();
        $table->query_db(10);
        $this->assertEquals($keys, array_keys($table->rawdata));

        // Submit a file for the second student.
        $data = new \stdClass();
        $data->onlinetex_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        static::helper_add_submission($qbassign, $participants[$keys[1]], $data, 'onlinetex');

        // qbassign has a private cache. The easiest way to clear this is to create a new instance.
        $qbassign = new \qbassign($context, $cm, $course);

        $newparticipants = $qbassign->list_participants(null, false);

        // There should be exactly two participants and they should be the students.
        $this->assertCount(2, $newparticipants);
        foreach ($newparticipants as $participant) {
            $this->assertArrayHasKey($participant->id, $students);
        }
        $newkeys = array_keys($newparticipants);

        // The user who submitted first should now be listed first.
        $this->assertEquals($participants[$keys[1]]->id, $newparticipants[$newkeys[0]]->id);
        $this->assertEquals($participants[$keys[0]]->id, $newparticipants[$newkeys[1]]->id);

        // Submit for the other student.
        static::helper_add_submission($qbassign, $participants[$keys[0]], $data, 'onlinetex');
        $qbassign = new \qbassign($context, $cm, $course);
        $newparticipants = $qbassign->list_participants(null, false);

        // The users should still be listed in order of the first submission
        $this->assertEquals($participants[$keys[1]]->id, $newparticipants[$newkeys[0]]->id);
        $this->assertEquals($participants[$keys[0]]->id, $newparticipants[$newkeys[1]]->id);

        // The updated grading table should have the same order as the updated participant list.
        $table->query_db(10);
        $this->assertEquals($newkeys, array_keys($table->rawdata));
    }

    /**
     * Tests that users who have a submission, but can no longer submit are listed.
     */
    public function test_list_participants_can_no_longer_submit() {
        global $DB;
        $this->resetAfterTest(true);
        // Create a role that will prevent users submitting.
        $role = self::getDataGenerator()->create_role();
        qbassign_capability('mod/qbassign:submit', CAP_PROHIBIT, $role, \context_system::instance());
        // Create the test data.
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $qbassign = $this->create_instance($course);
        self::getDataGenerator()->create_and_enrol($course, 'teacher');
        $student1 = self::getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = self::getDataGenerator()->create_and_enrol($course, 'student');
        $cannotsubmit1 = self::getDataGenerator()->create_and_enrol($course, 'student');
        $cannotsubmit2 = self::getDataGenerator()->create_and_enrol($course, 'student');
        // Create submissions for some users.
        $this->add_submission($student1, $qbassign);
        $this->submit_for_grading($student1, $qbassign);
        $this->add_submission($cannotsubmit1, $qbassign);
        $this->submit_for_grading($cannotsubmit1, $qbassign);
        // Remove the capability to submit from some users.
        role_qbassign($role, $cannotsubmit1->id, $coursecontext);
        role_qbassign($role, $cannotsubmit2->id, $coursecontext);
        // Everything is setup for the test now.
        $participants = $qbassign->list_participants(null, true);
        self::assertCount(3, $participants);
        self::assertArrayHasKey($student1->id, $participants);
        self::assertArrayHasKey($student2->id, $participants);
        self::assertArrayHasKey($cannotsubmit1->id, $participants);
    }

    public function helper_add_submission($qbassign, $user, $data, $type) {
        global $USER;

        $previoususer = $USER;

        $this->setUser($user);
        $submission = $qbassign->get_user_submission($user->id, true);
        $submission->status = qbassign_SUBMISSION_STATUS_SUBMITTED;

        $rc = new \ReflectionClass('qbassign');
        $rcm = $rc->getMethod('update_submission');
        $rcm->setAccessible(true);
        $rcm->invokeArgs($qbassign, [$submission, $user->id, true, false]);

        $plugin = $qbassign->get_submission_plugin_by_type($type);
        $plugin->save($submission, $data);

        $this->setUser($previoususer);
    }
}
