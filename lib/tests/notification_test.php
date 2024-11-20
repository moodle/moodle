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

namespace core;

/**
 * Unit tests for core\notification.
 *
 * @package   core
 * @category  test
 * @copyright 2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_test extends \advanced_testcase {

    /**
     * Setup required for all notification tests.
     *
     * This includes emptying the list of notifications on the session, resetting any session which exists, and setting
     * up a new \moodle_page object.
     */
    public function setUp(): void {
        global $PAGE, $SESSION;

        parent::setUp();
        $PAGE = new \moodle_page();
        \core\session\manager::init_empty_session();
        $SESSION->notifications = [];
    }

    /**
     * Tear down required for all notification tests.
     *
     * This includes emptying the list of notifications on the session, resetting any session which exists, and setting
     * up a new \moodle_page object.
     */
    public function tearDown(): void {
        global $PAGE, $SESSION;

        $PAGE = null;
        \core\session\manager::init_empty_session();
        $SESSION->notifications = [];
        parent::tearDown();
    }

    /**
     * Test the way in which notifications are added to the session in different stages of the page load.
     */
    public function test_add_during_output_stages(): void {
        global $PAGE, $SESSION;

        \core\notification::add('Example before header', \core\notification::INFO);
        $this->assertCount(1, $SESSION->notifications);

        $PAGE->set_state(\moodle_page::STATE_PRINTING_HEADER);
        \core\notification::add('Example during header', \core\notification::INFO);
        $this->assertCount(2, $SESSION->notifications);

        $PAGE->set_state(\moodle_page::STATE_IN_BODY);
        \core\notification::add('Example in body', \core\notification::INFO);
        $this->expectOutputRegex('/Example in body/');
        $this->assertCount(2, $SESSION->notifications);

        $PAGE->set_state(\moodle_page::STATE_DONE);
        \core\notification::add('Example after page', \core\notification::INFO);
        $this->assertCount(2, $SESSION->notifications);
        $this->expectOutputRegex('/Example after page/');

        \core\session\manager::write_close();
        \core\notification::add('Example after write_close', \core\notification::INFO);
        $this->assertCount(2, $SESSION->notifications);
        $this->expectOutputRegex('/Example after write_close/');

        // Simulate shutdown handler which calls fetch.
        $this->assertCount(2, \core\notification::fetch());
    }

    /**
     * Test fetching of notifications from the session.
     */
    public function test_fetch(): void {
        // Initially there won't be any notifications.
        $this->assertCount(0, \core\notification::fetch());

        // Adding a notification should make one available to fetch.
        \core\notification::success('Notification created');
        $this->assertCount(1, \core\notification::fetch());
        $this->assertCount(0, \core\notification::fetch());
    }

    /**
     * Test that session notifications are persisted across session clears.
     */
    public function test_session_persistance(): void {
        global $PAGE, $SESSION;

        // Initially there won't be any notifications.
        $this->assertCount(0, $SESSION->notifications);

        // Adding a notification should make one available to fetch.
        \core\notification::success('Notification created');
        $this->assertCount(1, $SESSION->notifications);

        // Re-creating the session will not empty the notification bag.
        \core\session\manager::init_empty_session();
        $this->assertCount(1, $SESSION->notifications);
    }
}
