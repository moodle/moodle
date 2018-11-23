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
require_once($CFG->dirroot . '/my/lib.php');

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

    /**
     * Test get_course_blocks contents
     */
    public function test_get_course_blocks_contents() {
        global $DB, $FULLME;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $coursecontext = context_course::instance($course->id);

        // Create a HTML block.
        $title = 'Some course info';
        $body = 'Some course info<br /><p>Some contents</p>';
        $bodyformat = FORMAT_MOODLE;
        $page = new moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $newblock = 'html';
        $page->blocks->add_block_at_end_of_default_region($newblock);

        $this->setUser($user);
        // Re-create the page.
        $page = new moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance('html', $block->instance);
        $configdata = (object) [
            'title' => $title,
            'text' => [
                'itemid' => 0,
                'text' => $body,
                'format' => $bodyformat,
            ],
        ];
        $block->instance_config_save((object) $configdata);
        $filename = 'img.png';
        $filerecord = array(
            'contextid' => context_block::instance($block->instance->id)->id,
            'component' => 'block_html',
            'filearea' => 'content',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
        );
        // Create an area to upload the file.
        $fs = get_file_storage();
        // Create a file from the string that we made earlier.
        $file = $fs->create_file_from_string($filerecord, 'some fake content (should be an image).');

        // Check for the new block.
        $result = core_block_external::get_course_blocks($course->id, true);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect the new block.
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals($title, $result['blocks'][0]['contents']['title']);
        $this->assertEquals($body, $result['blocks'][0]['contents']['content']);
        $this->assertEquals(FORMAT_HTML, $result['blocks'][0]['contents']['contentformat']);    // Format change for external.
        $this->assertEquals('', $result['blocks'][0]['contents']['footer']);
        $this->assertCount(1, $result['blocks'][0]['contents']['files']);
        $this->assertEquals($newblock, $result['blocks'][0]['name']);
    }

    /**
     * Test user get default dashboard blocks.
     */
    public function test_get_dashboard_blocks_default_dashboard() {
        global $PAGE, $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        // Get the expected default blocks.
        $alldefaultblocksordered = $DB->get_records_menu('block_instances',
            array('pagetypepattern' => 'my-index'), 'defaultregion, defaultweight ASC', 'id, blockname');

        $this->setUser($user);

        // Check for the default blocks.
        $result = core_block_external::get_dashboard_blocks($user->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all blogs except learning plans one (no learning plans to show).
        $this->assertCount(count($alldefaultblocksordered) - 1, $result['blocks']);
        $returnedblocks = array();
        foreach ($result['blocks'] as $block) {
            // Check all the returned blocks are in the expected blocks array.
            $this->assertContains($block['name'], $alldefaultblocksordered);
            $returnedblocks[] = $block['name'];
        }
        // Remove lp block.
        array_shift($alldefaultblocksordered);
        // Check that we received the blocks in the expected order.
        $this->assertEquals(array_values($alldefaultblocksordered), $returnedblocks);
    }

    /**
     * Test user get default dashboard blocks including a sticky block.
     */
    public function test_get_dashboard_blocks_default_dashboard_including_sticky_block() {
        global $PAGE, $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        // Get the expected default blocks.
        $alldefaultblocks = $DB->get_records_menu('block_instances', array('pagetypepattern' => 'my-index'), '', 'id, blockname');

        // Now, add a sticky block.
        $page = new moodle_page();
        $page->set_context(context_system::instance());
        $page->set_pagetype('my-index');
        $page->set_url(new moodle_url('/'));
        $page->blocks->add_region('side-pre');
        $page->blocks->load_blocks();
        $page->blocks->add_block('myprofile', 'side-pre', 0, true, '*');

        $this->setUser($user);

        // Check for the default blocks plus the sticky.
        $result = core_block_external::get_dashboard_blocks($user->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all blogs plus sticky one except learning plans one (no learning plans to show).
        $this->assertCount(count($alldefaultblocks), $result['blocks']);
        $found = false;
        foreach ($result['blocks'] as $block) {
            if ($block['name'] == 'myprofile') {
                $this->assertEquals('side-pre', $block['region']);
                $found = true;
                continue;
            }
            // Check that the block is in the expected blocks array.
            $this->assertContains($block['name'], $alldefaultblocks);
        }
        $this->assertTrue($found);
    }

    /**
     * Test admin get user's custom dashboard blocks.
     */
    public function test_get_dashboard_blocks_custom_user_dashboard() {
        global $PAGE, $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        // Get the expected default blocks.
        $alldefaultblocks = $DB->get_records_menu('block_instances', array('pagetypepattern' => 'my-index'), '', 'id, blockname');

        // Add a custom block.
        $page = new moodle_page();
        $page->set_context(context_user::instance($user->id));
        $page->set_pagelayout('mydashboard');
        $page->set_pagetype('my-index');
        $page->blocks->add_region('content');
        $currentpage = my_get_page($user->id, MY_PAGE_PRIVATE);
        $page->set_subpage($currentpage->id);
        $page->blocks->load_blocks();
        $page->blocks->add_block('myprofile', 'content', 0, false);

        $this->setAdminUser();

        // Check for the new block as admin for a user.
        $result = core_block_external::get_dashboard_blocks($user->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all default blogs plys the one we added except learning plans one (no learning plans to show).
        $this->assertCount(count($alldefaultblocks), $result['blocks']);
        $found = false;
        foreach ($result['blocks'] as $block) {
            if ($block['name'] == 'myprofile') {
                $this->assertEquals('content', $block['region']);
                $found = true;
                continue;
            }
            // Check that the block is in the expected blocks array.
            $this->assertContains($block['name'], $alldefaultblocks);
        }
        $this->assertTrue($found);
    }

    /**
     * Test user tries to get other user blocks not having permission.
     */
    public function test_get_dashboard_blocks_other_user_missing_permissions() {
        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);

        $this->expectException('moodle_exception');
        core_block_external::get_dashboard_blocks($user2->id);
    }
}
