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
 * Events test.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\event;

use advanced_testcase;
use moodle_url;
use coding_exception;
use context_module;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P activity events test cases.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report_viewed_test extends advanced_testcase {

    /**
     * Test report_viewed event.
     *
     * @dataProvider report_viewed_data
     * @param bool $usea if a (instanceid) will be used in the event
     * @param bool $useattemptid if attemptid will be used in the event
     * @param bool $useuserid if user id will be used in the event
     * @param bool $exception if exception is expected
     */
    public function test_report_viewed(bool $usea, bool $useattemptid, bool $useuserid, bool $exception): void {

        $this->resetAfterTest();

        // Must be a non-guest user to create h5pactivities.
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course->id]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // Create a user with 1 attempt.
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $params = ['cmid' => $activity->cmid, 'userid' => $user->id];
        $attempt = $generator->create_content($activity, $params);

        $other = [];
        $urlparams = [];
        if ($usea) {
            $other['instanceid'] = $activity->id;
            $urlparams['a'] = $activity->id;
        }
        if ($useuserid) {
            $other['userid'] = $user->id;
            $urlparams['userid'] = $user->id;
        }
        if ($useattemptid) {
            $other['attemptid'] = $attempt->id;
            $urlparams['attemptid'] = $attempt->id;
        }
        $params = [
            'context' => context_module::instance($activity->cmid),
            'objectid' => $activity->id,
            'other' => $other,
        ];

        if ($exception) {
            $this->expectException(coding_exception::class);
        }

        $event = report_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_h5pactivity\event\report_viewed', $event);
        $this->assertEquals(context_module::instance($activity->cmid), $event->get_context());
        $this->assertEquals($activity->id, $event->objectid);

        $eventurl = $event->get_url();
        $url = new moodle_url('/mod/h5pactivity/report.php', $urlparams);
        $this->assertTrue($eventurl->compare($url));
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public static function report_viewed_data(): array {
        return [
            // Exception cases.
            'Event withour other data (exception)' => [
                false, false, false, true
            ],
            'Event with only userid (exception)' => [
                false, false, true, true
            ],
            'Event with only attemptid (exception)' => [
                false, true, false, true
            ],
            'Event with attemptid and userid (exception)' => [
                false, true, true, true
            ],
            // Correct cases.
            'Event with instance id' => [
                true, false, false, false
            ],
            'Event with instance id and attempt id' => [
                true, false, true, false
            ],
            'Event with instance id and userid' => [
                true, true, false, false
            ],
            'Event with instance id, user id and attemptid' => [
                true, true, true, false
            ],
        ];
    }
}
