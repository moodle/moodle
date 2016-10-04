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
 * Events tests.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
/**
 * Unit tests for the dashboard events.
 *
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard_events_testcase extends advanced_testcase {

    /** @var user cobject */
    protected $user;

    /**
     * Setup often used objects for the following tests.
     */
    protected function setup() {
        global $USER;

        $this->resetAfterTest();

        // The user we are going to test this on.
        $this->setAdminUser();
        $this->user = $USER;
    }

    /**
     * Test the dashboard viewed event.
     *
     * There is no external API for viewing the dashboard, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_dashboard_viewed() {

        $user = $this->user;
        // Trigger an event: dashboard viewed.
        $eventparams = array(
            'context' => $context = context_system::instance()
        );

        $event = \core\event\dashboard_viewed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboard_viewed', $event);
        $this->assertEquals($user->id, $event->userid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the dashboard reset event.
     *
     * We will reset the user dashboard to
     * trigger the event and ensure data is returned as expected.
     */
    public function test_dashboard_reset() {
        global $CFG;
        require_once($CFG->dirroot . '/my/lib.php');
        $user = $this->user;
        $sink = $this->redirectEvents();

        // Reset the dashboard.
        my_reset_page($user->id);

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboard_reset', $event);
        $this->assertEquals($user->id, $event->userid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the dashboards reset event.
     *
     * We will reset all user dashboards to
     * trigger the event and ensure data is returned as expected.
     */
    public function test_dashboards_reset() {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/my/lib.php');

        $sink = $this->redirectEvents();

        // Reset all dashbaords.
        my_reset_page_for_all_users();

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboards_reset', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertDebuggingNotCalled();
    }
}
