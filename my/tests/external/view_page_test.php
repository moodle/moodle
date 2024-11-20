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

namespace core_my\external;

use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Test Class for external function core_my_view_page.
 *
 * @package   core_my
 * @category  external
 * @copyright 2023 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.3
 * @covers \core_my\external\view_page
 */
class view_page_test extends externallib_advanced_testcase {

    /**
     * Set up for every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Helper.
     *
     * @param string $page
     * @return array
     */
    protected function view_page(string $page): array {
        $result = view_page::execute($page);
        return \core_external\external_api::clean_returnvalue(view_page::execute_returns(), $result);
    }

    /**
     * Test for webservice my view page.
     */
    public function test_view_page(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        // Request to trigger the view event in my.
        $result = $this->view_page('my');
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Request to trigger the view event in dashboard.
        $result = $this->view_page('dashboard');
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        // Wrong page to trigger the event.
        $result = $this->view_page('test');
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);

        $events = $sink->get_events();
        // Check if the log still with two rows.
        $this->assertCount(2, $events);
        $this->assertInstanceOf('\core\event\mycourses_viewed', $events[0]);
        $this->assertInstanceOf('\core\event\dashboard_viewed', $events[1]);
        $sink->close();
    }
}
