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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use mod_h5pactivity\local\manager;
use core_external\external_api;
use externallib_advanced_testcase;

/**
 * External function test for log_report_viewed.
 *
 * @package    mod_h5pactivity
 * @category   external
 * @covers     \mod_h5pactivity\external\log_report_viewed
 * @since      Moodle 3.11
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class log_report_viewed_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of log_report_viewed.
     *
     * @dataProvider execute_data
     * @param int $enabletracking the activity tracking enable
     * @param int $reviewmode the activity review mode
     * @param string $loginuser the user which calls the webservice
     * @param string|null $participant the user to log the data
     */
    public function test_execute(int $enabletracking, int $reviewmode, string $loginuser, ?string $participant): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Enrol users: 1 teacher, 1 student.
        $users = [
            'editingteacher' => $this->getDataGenerator()->create_and_enrol($course, 'editingteacher'),
            'student' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        // Add h5p activity.
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course, 'enabletracking' => $enabletracking, 'reviewmode' => $reviewmode]);

        // Create attempt for h5p activity.
        $attempts = [];
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');
        $user = $users['student'];
        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();
        $params = ['cmid' => $cm->id, 'userid' => $user->id];
        $attempts['student'] = $generator->create_content($activity, $params);

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        // Execute external method.
        $this->setUser($users[$loginuser]);
        $attemptid = $attempts[$participant]->id ?? 0;
        $result = log_report_viewed::execute($activity->id, $user->id, $attemptid);
        $result = external_api::clean_returnvalue(
            log_report_viewed::execute_returns(),
            $result
        );

        // Validate general structure.
        $this->assertArrayHasKey('status', $result);

        $events = $sink->get_events();
        $event = end($events);

        // Check the event details are correct.
        $this->assertInstanceOf('mod_h5pactivity\event\report_viewed', $event);
        $this->assertEquals(\context_module::instance($cm->id), $event->get_context());

        $this->assertEquals($cm->instance, $event->other['instanceid']);
        $this->assertEquals($user->id, $event->other['userid']);
        $this->assertEquals($attemptid, $event->other['attemptid']);
    }

    /**
     * Data provider for the test_execute tests.
     *
     * @return  array
     */
    public static function execute_data(): array {
        return [
            'Student reviewing own attempt' => [
                1, manager::REVIEWCOMPLETION, 'student', 'student'
            ],
            'Teacher reviewing student attempts' => [
                1, manager::REVIEWCOMPLETION, 'editingteacher', 'student'
            ],
        ];
    }
}
