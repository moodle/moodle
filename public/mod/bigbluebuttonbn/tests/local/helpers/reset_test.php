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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
namespace mod_bigbluebuttonbn\local\helpers;

use core_tag_tag;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @coversDefaultClass \mod_bigbluebuttonbn\local\helpers\reset
 * @covers \mod_bigbluebuttonbn\local\helpers\reset
 */
final class reset_test extends \advanced_testcase {
    use testcase_helper_trait;
    /**
     * Reset course item test
     */
    public function test_reset_course_items(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->bigbluebuttonbn_recordings_enabled = false;
        $results = reset::reset_course_items();
        $this->assertEquals(["events" => 0, "tags" => 0, "logs" => 0], $results);
        $CFG->bigbluebuttonbn_recordings_enabled = true;
        $results = reset::reset_course_items();
        $this->assertEquals(["events" => 0, "tags" => 0, "logs" => 0, "recordings" => 0], $results);
    }

    /**
     * Reset get_status test
     */
    public function test_reset_getstatus(): void {
        $this->resetAfterTest();
        $result = reset::reset_getstatus('events');
        $this->assertEquals([
                'component' => 'BigBlueButton',
                'item' => 'Deleted events',
                'error' => false,
        ], $result);
    }

    /**
     * Reset event test
     */
    public function test_reset_events(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(
                null,
                ['openingtime' => time()]
        );
        $formdata = $this->get_form_data_from_instance($bbactivity);
        \mod_bigbluebuttonbn\local\helpers\mod_helper::process_post_save($formdata);
        $this->assertEquals(1, $DB->count_records(
                'event',
                ['modulename' => 'bigbluebuttonbn', 'courseid' => $this->get_course()->id]));
        reset::reset_events($this->get_course()->id);
        $this->assertEquals(0, $DB->count_records(
                'event',
                ['modulename' => 'bigbluebuttonbn', 'courseid' => $this->get_course()->id]));
    }

    /**
     * Reset tags test
     */
    public function test_reset_tags(): void {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(null,
                ['course' => $this->get_course()->id],
                ['visible' => true]
        );
        core_tag_tag::add_item_tag('mod_bigbluebuttonbn', 'bbitem', $bbactivity->id, $bbactivitycontext, 'newtag');
        $alltags = core_tag_tag::get_item_tags('mod_bigbluebuttonbn', 'bbitem', $bbactivity->id);
        $this->assertCount(1, $alltags);
        reset::reset_tags($this->get_course()->id);
        $alltags = core_tag_tag::get_item_tags('mod_bigbluebuttonbn', 'bbitem', $bbactivity->id);
        $this->assertCount(0, $alltags);
    }

    /**
     * Reset recordings test
     */
    public function test_reset_recordings(): void {
        $this->initialise_mock_server();
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(null,
            ['course' => $this->get_course()->id],
            ['visible' => true]
        );
        $instance = instance::get_from_instanceid($bbactivity->id);
        $this->create_recordings_for_instance($instance, [
            ['name' => 'Recording 1'],
            ['name' => 'Recording 2'],
        ]);
        $this->assertCount(2, $instance->get_recordings());
        reset::reset_recordings($this->get_course()->id);
        $this->assertCount(0, $instance->get_recordings());
    }
}
