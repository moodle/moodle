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

namespace core_search\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the view_results external function.
 *
 * @package    core_search
 * @category   test
 * @copyright  2023 Juan Leyva (juan@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_search\external\view_results
 */
final class view_results_test extends \externallib_advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * test external api
     * @covers ::execute
     * @return void
     */
    public function test_external_view_results(): void {

        set_config('enableglobalsearch', true);

        $this->setAdminUser();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = view_results::execute('forum post', ['title' => 'My progress'], 1);
        $result = external_api::clean_returnvalue(view_results::execute_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);
        $sink->close();

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('core\event\search_results_viewed', $event);
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
        $this->assertEquals('forum post', $event->get_data()['other']['q']);
        $this->assertEquals('My progress', $event->get_data()['other']['title']);
        $this->assertEquals(1, $event->get_data()['other']['page']);
    }
}
