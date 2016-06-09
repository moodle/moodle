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
 * Unit tests for (some of) mod/assign/locallib.php.
 *
 * @package    mod_assign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../locallib.php');

class mod_assign_locallib_participants extends advanced_testcase {
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

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(['course' => $course->id, 'blindmarking' => 1]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        // Allocate IDs now.
        // We're testing whether the IDs are correct after allocation.
        assign::allocate_unique_ids($assign->get_instance()->id);

        $participants = $assign->list_participants(null, false);

        // There should be exactly two participants and they should be the students.
        $this->assertCount(2, $participants);
        foreach ($participants as $participant) {
            $this->assertArrayHasKey($participant->id, $students);
        }

        $keys = array_keys($participants);

        // Create a grading table, and query the DB This should have the same order.
        $table = new assign_grading_table($assign, 10, '', 0, false);
        $table->setup();
        $table->query_db(10);
        $this->assertEquals($keys, array_keys($table->rawdata));

        // Submit a file for the second student.
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        static::helper_add_submission($assign, $participants[$keys[1]], $data, 'onlinetext');

        // Assign has a private cache. The easiest way to clear this is to create a new instance.
        $assign = new assign($context, $cm, $course);

        $newparticipants = $assign->list_participants(null, false);

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
        static::helper_add_submission($assign, $participants[$keys[0]], $data, 'onlinetext');
        $assign = new assign($context, $cm, $course);
        $newparticipants = $assign->list_participants(null, false);

        // The users should still be listed in order of the first submission
        $this->assertEquals($participants[$keys[1]]->id, $newparticipants[$newkeys[0]]->id);
        $this->assertEquals($participants[$keys[0]]->id, $newparticipants[$newkeys[1]]->id);

        // The updated grading table should have the same order as the updated participant list.
        $table->query_db(10);
        $this->assertEquals($newkeys, array_keys($table->rawdata));
    }

    public function helper_add_submission($assign, $user, $data, $type) {
        global $USER;

        $previoususer = $USER;

        $this->setUser($user);
        $submission = $assign->get_user_submission($user->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;

        $rc = new ReflectionClass('assign');
        $rcm = $rc->getMethod('update_submission');
        $rcm->setAccessible(true);
        $rcm->invokeArgs($assign, [$submission, $user->id, true, false]);

        $plugin = $assign->get_submission_plugin_by_type($type);
        $plugin->save($submission, $data);

        $this->setUser($previoususer);
    }
}
