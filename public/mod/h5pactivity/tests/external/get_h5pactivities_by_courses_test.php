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

namespace mod_h5pactivity\external;

use core_external\external_api;
use context_module;

/**
 * External function test for get_h5pactivities_by_courses.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_h5pactivities_by_courses_test extends \core_external\tests\externallib_testcase {
    /**
     * Test test_get_h5pactivities_by_courses user student.
     */
    public function test_get_h5pactivities_by_courses(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create 2 courses.
        // Course 1 -> 2 activities with H5P files package without deploy.
        // Course 2 -> 1 activity with H5P file package deployed.
        $course1 = $this->getDataGenerator()->create_course();
        $params = [
            'course' => $course1->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/filltheblanks.h5p',
            'introformat' => 1
        ];
        $activities[] = $this->getDataGenerator()->create_module('h5pactivity', $params);
        // Add filename and contextid to make easier the asserts.
        $activities[0]->filename = 'filltheblanks.h5p';
        $context = context_module::instance($activities[0]->cmid);
        $activities[0]->contextid = $context->id;

        $params = [
            'course' => $course1->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/greeting-card.h5p',
            'introformat' => 1
        ];
        $activities[] = $this->getDataGenerator()->create_module('h5pactivity', $params);
        // Add filename and contextid to make easier the asserts.
        $activities[1]->filename = 'greeting-card.h5p';
        $context = context_module::instance($activities[1]->cmid);
        $activities[1]->contextid = $context->id;

        $course2 = $this->getDataGenerator()->create_course();
        $params = [
            'course' => $course2->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/guess-the-answer.h5p',
            'introformat' => 1
        ];
        $activities[] = $this->getDataGenerator()->create_module('h5pactivity', $params);
        $activities[2]->filename = 'guess-the-answer.h5p';
        $context = context_module::instance($activities[2]->cmid);
        $activities[2]->contextid = $context->id;

        // Create a fake deploy H5P file.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $deployedfile = $generator->create_export_file($activities[2]->filename, $context->id, 'mod_h5pactivity', 'package');

        // Create a user and enrol as student in both courses.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $maninstance1 = $DB->get_record('enrol', ['courseid' => $course1->id, 'enrol' => 'manual'], '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', ['courseid' => $course2->id, 'enrol' => 'manual'], '*', MUST_EXIST);
        $manual = enrol_get_plugin('manual');
        $manual->enrol_user($maninstance1, $user->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $user->id, $studentrole->id);

        // Set admin settings.
        set_config('enablesavestate', 1, 'mod_h5pactivity');
        set_config('savestatefreq', 120, 'mod_h5pactivity');

        // Check the activities returned by the first course.
        $this->setUser($user);
        $courseids = [$course1->id];
        $result = get_h5pactivities_by_courses::execute($courseids);
        $result = external_api::clean_returnvalue(get_h5pactivities_by_courses::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['h5pactivities']);
        $this->assert_activities($activities, $result);
        $this->assertNotContains('deployedfile', $result['h5pactivities'][0]);
        $this->assertNotContains('deployedfile', $result['h5pactivities'][1]);
        $this->assertEquals(1, $result['h5pglobalsettings']['enablesavestate']);
        $this->assertEquals(120, $result['h5pglobalsettings']['savestatefreq']);

        // Call the external function without passing course id.
        // Expected result, all the courses, course1 and course2.
        $result = get_h5pactivities_by_courses::execute([]);
        $result = external_api::clean_returnvalue(get_h5pactivities_by_courses::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['h5pactivities']);
        // We need to sort the $result by id.
        // Because we are not sure how it is ordered with more than one course.
        array_multisort(array_map(function($element) {
            return $element['id'];
        }, $result['h5pactivities']), SORT_ASC, $result['h5pactivities']);
        $this->assert_activities($activities, $result);
        $this->assertNotContains('deployedfile', $result['h5pactivities'][0]);
        $this->assertNotContains('deployedfile', $result['h5pactivities'][1]);
        // Only the activity from the second course has been deployed.
        $this->assertEquals($deployedfile['filename'], $result['h5pactivities'][2]['deployedfile']['filename']);
        $this->assertEquals($deployedfile['filepath'], $result['h5pactivities'][2]['deployedfile']['filepath']);
        $this->assertEquals($deployedfile['filesize'], $result['h5pactivities'][2]['deployedfile']['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $result['h5pactivities'][2]['deployedfile']['timemodified']);
        $this->assertEquals($deployedfile['mimetype'], $result['h5pactivities'][2]['deployedfile']['mimetype']);
        $this->assertEquals($deployedfile['fileurl'], $result['h5pactivities'][2]['deployedfile']['fileurl']);
        $this->assertEquals(1, $result['h5pglobalsettings']['enablesavestate']);
        $this->assertEquals(120, $result['h5pglobalsettings']['savestatefreq']);

        // Unenrol user from second course.
        $manual->unenrol_user($maninstance2, $user->id);
        // Remove the last activity from the array.
        array_pop($activities);

        // Disable save state.
        set_config('enablesavestate', 0, 'mod_h5pactivity');

        // Call the external function without passing course id.
        $result = get_h5pactivities_by_courses::execute([]);
        $result = external_api::clean_returnvalue(get_h5pactivities_by_courses::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['h5pactivities']);
        $this->assert_activities($activities, $result);
        $this->assertEquals(0, $result['h5pglobalsettings']['enablesavestate']);
        $this->assertNotContains('savestatefreq', $result['h5pglobalsettings']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = get_h5pactivities_by_courses::execute([$course2->id]);
        $result = external_api::clean_returnvalue(get_h5pactivities_by_courses::execute_returns(), $result);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);
    }

    /**
     * Create a scenario to use into the tests.
     *
     * @param  array $activities list of H5P activities.
     * @param  array $result list of H5P activities by WS.
     * @return void
     */
    protected function assert_activities(array $activities, array $result): void {

        $total = count($result['h5pactivities']);
        for ($i = 0; $i < $total; $i++) {
            $this->assertEquals($activities[$i]->id, $result['h5pactivities'][$i]['id']);
            $this->assertEquals($activities[$i]->course, $result['h5pactivities'][$i]['course']);
            $this->assertEquals($activities[$i]->name, $result['h5pactivities'][$i]['name']);
            $this->assertEquals($activities[$i]->timecreated, $result['h5pactivities'][$i]['timecreated']);
            $this->assertEquals($activities[$i]->timemodified, $result['h5pactivities'][$i]['timemodified']);
            $this->assertEquals($activities[$i]->intro, $result['h5pactivities'][$i]['intro']);
            $this->assertEquals($activities[$i]->introformat, $result['h5pactivities'][$i]['introformat']);
            $this->assertEquals([], $result['h5pactivities'][$i]['introfiles']);
            $this->assertEquals($activities[$i]->grade, $result['h5pactivities'][$i]['grade']);
            $this->assertEquals($activities[$i]->displayoptions, $result['h5pactivities'][$i]['displayoptions']);
            $this->assertEquals($activities[$i]->enabletracking, $result['h5pactivities'][$i]['enabletracking']);
            $this->assertEquals($activities[$i]->grademethod, $result['h5pactivities'][$i]['grademethod']);
            $this->assertEquals($activities[$i]->cmid, $result['h5pactivities'][$i]['coursemodule']);
            $this->assertEquals($activities[$i]->contextid, $result['h5pactivities'][$i]['context']);
            $this->assertEquals($activities[$i]->filename, $result['h5pactivities'][$i]['package'][0]['filename']);
        }
    }
}
