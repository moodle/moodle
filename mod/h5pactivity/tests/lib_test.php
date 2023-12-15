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


namespace mod_h5pactivity;

use advanced_testcase;
use mod_h5pactivity\local\manager;

/**
 * Unit tests for (some of) mod/h5pactivity/lib.php.
 *
 * @package    mod_h5pactivity
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends advanced_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/mod/h5pactivity/lib.php");
    }

    /**
     * Test that h5pactivity_delete_instance removes data.
     *
     * @covers ::h5pactivity_delete_instance
     */
    public function test_h5pactivity_delete_instance() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $this->setUser($user);

        /** @var \mod_h5pactivity_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        /** @var \core_h5p_generator $h5pgenerator */
        $h5pgenerator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Add an attempt to the H5P activity.
        $attemptinfo = [
            'userid' => $user->id,
            'h5pactivityid' => $activity->id,
            'attempt' => 1,
            'interactiontype' => 'compound',
            'rawscore' => 2,
            'maxscore' => 2,
            'duration' => 1,
            'completion' => 1,
            'success' => 0,
        ];
        $generator->create_attempt($attemptinfo);

        // Add also a xAPI state to the H5P activity.
        $filerecord = [
            'contextid' => \context_module::instance($activity->cmid)->id,
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];
        $h5pgenerator->generate_h5p_data(false, $filerecord);

        // Check the H5P activity exists and the attempt has been created.
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check nothing happens when given activity id doesn't exist.
        h5pactivity_delete_instance($activity->id + 1);
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check the H5P instance and its associated data is removed.
        h5pactivity_delete_instance($activity->id);
        $this->assertEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(1, $DB->count_records('grade_items'));
        $this->assertEquals(1, $DB->count_records('grade_grades'));
        $this->assertEquals(0, $DB->count_records('xapi_states'));
    }

    /**
     * Test that assign_print_recent_activity shows ungraded submitted assignments.
     *
     * @covers ::h5pactivity_print_recent_activity
     */
    public function test_print_recent_activity() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course, 'enabletracking' => 1, 'grademethod' => manager::GRADEHIGHESTATTEMPT]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        $user = $student;
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);
        $this->setUser($student);
        $this->expectOutputRegex('/submitted:/');
        h5pactivity_print_recent_activity($course, true, time() - 3600);
    }

    /**
     * Test that h5pactivity_print_recent_activity does not display any warnings when a custom fullname has been configured.
     *
     * @covers ::h5pactivity_print_recent_activity
     */
    public function test_print_recent_activity_fullname() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course, 'enabletracking' => 1, 'grademethod' => manager::GRADEHIGHESTATTEMPT]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        $user = $student;
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);

        $this->setUser($teacher);

        $this->expectOutputRegex('/submitted:/');
        set_config('fullnamedisplay', 'firstname, lastnamephonetic');
        h5pactivity_print_recent_activity($course, false, time() - 3600);
    }

    /**
     * Test that h5pactivity_get_recent_mod_activity fetches the h5pactivity correctly.
     *
     * @covers ::h5pactivity_get_recent_mod_activity
     */
    public function test_h5pactivity_get_recent_mod_activity() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course, 'enabletracking' => 1, 'grademethod' => manager::GRADEHIGHESTATTEMPT]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        $user = $student;
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $generator->create_content($activity, $params);

        $index = 1;
        $activities = [
            $index => (object) [
                'type' => 'h5pactivity',
                'cmid' => $cm->id,
            ],
        ];

        $this->setUser($teacher);
        h5pactivity_get_recent_mod_activity($activities, $index, time() - HOURSECS, $course->id, $cm->id);

        $activity = $activities[1];
        $this->assertEquals("h5pactivity", $activity->type);
        $this->assertEquals($student->id, $activity->user->id);
    }

    /**
     * Test that h5pactivity_get_recent_mod_activity fetches activity correctly.
     *
     * @covers ::h5pactivity_fetch_recent_activity
     */
    public function test_h5pactivity_fetch_recent_activity() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Create users and groups.
        $students = array();
        $groups = array();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        for ($i = 1; $i < 6; $i++) {
            $students[$i] = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $groups[$i] = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        }
        $groups[$i] = $this->getDataGenerator()->create_group(array('courseid' => $course->id, 'participation' => 0));

        // Update the course set the groupmode SEPARATEGROUPS and forced.
        update_course((object)array('id' => $course->id, 'groupmode' => SEPARATEGROUPS, 'groupmodeforce' => true));

        // Student 1 is in groups 1 and 3.
        groups_add_member($groups[1], $students[1]);
        groups_add_member($groups[3], $students[1]);

        // Student 2 is in groups 1 and 2.
        groups_add_member($groups[1], $students[2]);
        groups_add_member($groups[2], $students[2]);

        // Student 3 is only in group 3.
        groups_add_member($groups[3], $students[3]);

        // Student 4 is only in group 5 (non-participation).
        groups_add_member($groups[6], $students[4]);

        // Student 5 is not in any groups.

        // Grader is only in group 3.
        groups_add_member($groups[3], $teacher);

        $timestart = time() - 1;
        // Create h5pactivity.
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course->id, 'enabletracking' => 1, 'grademethod' => manager::GRADEHIGHESTATTEMPT,
                'groupmode' => SEPARATEGROUPS]);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $cmcontext = \context_module::instance($activity->cmid);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, $cmcontext, true);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();

        // Create attempts.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');
        foreach ($students as $student) {
            $params = ['cmid' => $cm->id, 'userid' => $student->id];
            $generator->create_content($activity, $params);
        }

        // Get all attempts.
        $dbparams = [$timestart, $course->id, 'h5pactivity'];
        $userfieldsapi = \core_user\fields::for_userpic();
        $namefields = $userfieldsapi->get_sql('u', false, '', 'userid', false)->selects;;

        $sql = "SELECT h5pa.id, h5pa.timemodified, cm.id as cmid, $namefields
                  FROM {h5pactivity_attempts} h5pa
                  JOIN {h5pactivity} h5p      ON h5p.id = h5pa.h5pactivityid
                  JOIN {course_modules} cm ON cm.instance = h5p.id
                  JOIN {modules} md        ON md.id = cm.module
                  JOIN {user} u            ON u.id = h5pa.userid
                 WHERE h5pa.timemodified > ?
                   AND h5p.course = ?
                   AND md.name = ?
              ORDER BY h5pa.timemodified ASC";

        $submissions = $DB->get_records_sql($sql, $dbparams);
        $this->assertCount(count($students), $submissions);

        // Fetch activity for student (only his own).
        $this->setUser($students[1]);
        $recentactivity = h5pactivity_fetch_recent_activity($submissions, $course->id);
        $this->assertCount(1, $recentactivity);
        $this->assertEquals($students[1]->id, $recentactivity[$students[1]->id]->userid);

        // Fetch users group info for grader.
        $this->setUser($teacher);
        $recentactivity = h5pactivity_fetch_recent_activity($submissions, $course->id);
        $this->assertCount(2, $recentactivity);
        // Grader, Student 1 and 3 are in Group 3.
        $this->assertEquals($students[1]->id, $recentactivity[$students[1]->id]->userid);
        $this->assertEquals($students[3]->id, $recentactivity[$students[3]->id]->userid);

        // Grader is in Group 2.
        groups_remove_member($groups[3], $teacher);
        groups_add_member($groups[2], $teacher);
        get_fast_modinfo($course->id, 0, true);
        $recentactivity = h5pactivity_fetch_recent_activity($submissions, $course->id);
        $this->assertCount(1, $recentactivity);
        // Grader, Student 2 are in Group 2.
        $this->assertEquals($students[2]->id, $recentactivity[$students[2]->id]->userid);

        // Grader is in Group 1.
        groups_remove_member($groups[2], $teacher);
        groups_add_member($groups[1], $teacher);
        get_fast_modinfo($course->id, 0, true);
        $recentactivity = h5pactivity_fetch_recent_activity($submissions, $course->id);
        $this->assertCount(2, $recentactivity);
        // Grader, Student 1 and 2 are in Group 1.
        $this->assertEquals($students[1]->id, $recentactivity[$students[1]->id]->userid);
        $this->assertEquals($students[2]->id, $recentactivity[$students[2]->id]->userid);

        // Grader is in no group.
        groups_remove_member($groups[1], $teacher);
        get_fast_modinfo($course->id, 0, true);
        $recentactivity = h5pactivity_fetch_recent_activity($submissions, $course->id);
        // Student 4 and Student 5 have submissions, but they are not in a participation group, so they do not show up in recent
        // activity for separate groups mode.
        $this->assertCount(0, $recentactivity);
    }

    /**
     * Test that h5pactivity_reset_userdata reset user data.
     *
     * @covers ::h5pactivity_reset_userdata
     */
    public function test_h5pactivity_reset_userdata() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $this->setUser($user);

        /** @var \mod_h5pactivity_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        /** @var \core_h5p_generator $h5pgenerator */
        $h5pgenerator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Add an attempt to the H5P activity.
        $attemptinfo = [
            'userid' => $user->id,
            'h5pactivityid' => $activity->id,
            'attempt' => 1,
            'interactiontype' => 'compound',
            'rawscore' => 2,
            'maxscore' => 2,
            'duration' => 1,
            'completion' => 1,
            'success' => 0,
        ];
        $generator->create_attempt($attemptinfo);

        // Add also a xAPI state to the H5P activity.
        $filerecord = [
            'contextid' => \context_module::instance($activity->cmid)->id,
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];
        $h5pgenerator->generate_h5p_data(false, $filerecord);

        // Check the H5P activity exists and the attempt has been created with the expected data.
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check nothing happens when reset_h5pactivity is not set.
        $data = new \stdClass();
        h5pactivity_reset_userdata($data);
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check nothing happens when reset_h5pactivity is not set.
        $data = (object) [
            'courseid' => $course->id,
        ];
        h5pactivity_reset_userdata($data);
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check nothing happens when the given course doesn't exist.
        $data = (object) [
            'reset_h5pactivity' => true,
            'courseid' => $course->id + 1,
        ];
        h5pactivity_reset_userdata($data);
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(2, $DB->count_records('grade_grades'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));
        $this->assertEquals(1, $DB->count_records('xapi_states'));

        // Check the H5P instance and its associated data is reset.
        $data = (object) [
            'reset_h5pactivity' => true,
            'courseid' => $course->id,
        ];
        h5pactivity_reset_userdata($data);
        $this->assertNotEmpty($DB->get_record('h5pactivity', ['id' => $activity->id]));
        $this->assertEquals(2, $DB->count_records('grade_items'));
        $this->assertEquals(1, $DB->count_records('grade_grades'));
        $this->assertEquals(0, $DB->count_records('xapi_states'));
    }
}
