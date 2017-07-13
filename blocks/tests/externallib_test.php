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
 * External block functions unit tests
 *
 * @package    core_block
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External block functions unit tests
 *
 * @package    core_block
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class core_block_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test get_course_blocks
     */
    public function test_get_course_blocks() {
        global $DB, $FULLME;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $page = new moodle_page();
        $page->set_context(context_course::instance($course->id));
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $newblock = 'calendar_upcoming';
        $page->blocks->add_block_at_end_of_default_region($newblock);
        $this->setUser($user);

        // Check for the new block.
        $result = core_block_external::get_course_blocks($course->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect the new block.
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals($newblock, $result['blocks'][0]['name']);
    }

    /**
     * Test get_course_blocks on site home
     */
    public function test_get_course_blocks_site_home() {
        global $DB, $FULLME;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $page = new moodle_page();
        $page->set_context(context_course::instance(SITEID));
        $page->set_pagelayout('frontpage');
        $page->set_pagetype('site-index');
        $page->blocks->load_blocks();
        $newblock = 'calendar_upcoming';
        $page->blocks->add_block_at_end_of_default_region($newblock);
        $this->setUser($user);

        // Check for the new block.
        $result = core_block_external::get_course_blocks(SITEID);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect the new block.
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals($newblock, $result['blocks'][0]['name']);
    }

    /**
     * Test get_course_blocks
     */
    public function test_get_course_blocks_overrides() {
        global $DB, $CFG, $FULLME;

        $this->resetAfterTest(true);

        $CFG->defaultblocks_override = 'participants,search_forums,course_list:calendar_upcoming,recent_activity';

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $this->setUser($user);

        // Try default blocks.
        $result = core_block_external::get_course_blocks($course->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect 5 default blocks.
        $this->assertCount(5, $result['blocks']);

        $expectedblocks = array('navigation', 'settings', 'participants', 'search_forums', 'course_list',
                                'calendar_upcoming', 'recent_activity');
        foreach ($result['blocks'] as $block) {
            if (!in_array($block['name'], $expectedblocks)) {
                $this->fail("Unexpected block found: " . $block['name']);
            }
        }

    }

}
