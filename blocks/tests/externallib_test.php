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

namespace core_block;

use core_block_external;
use externallib_advanced_testcase;

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
class externallib_test extends externallib_advanced_testcase {

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

        $page = new \moodle_page();
        $page->set_context(\context_course::instance($course->id));
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
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

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

        $page = new \moodle_page();
        $page->set_context(\context_course::instance(SITEID));
        $page->set_pagelayout('frontpage');
        $page->set_pagetype('site-index');
        $page->blocks->load_blocks();
        $newblock = 'calendar_upcoming';
        $page->blocks->add_block_at_end_of_default_region($newblock);
        $this->setUser($user);

        // Check for the new block.
        $result = core_block_external::get_course_blocks(SITEID);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

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

        $CFG->defaultblocks_override = 'search_forums,course_list:calendar_upcoming,recent_activity';

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $this->setUser($user);

        // Try default blocks.
        $result = core_block_external::get_course_blocks($course->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect 4 default blocks.
        $this->assertCount(4, $result['blocks']);

        $expectedblocks = array('navigation', 'settings', 'search_forums', 'course_list',
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
        $coursecontext = \context_course::instance($course->id);

        // Create a HTML block.
        $title = 'Some course info';
        $body = 'Some course info<br /><p>Some contents</p>';
        $bodyformat = FORMAT_MOODLE;
        $page = new \moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $newblock = 'html';
        $page->blocks->add_block_at_end_of_default_region($newblock);

        $this->setUser($user);
        // Re-create the page.
        $page = new \moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance('html', $block->instance);
        $nonscalar = [
            'something' => true,
        ];
        $configdata = (object) [
            'title' => $title,
            'text' => [
                'itemid' => 0,
                'text' => $body,
                'format' => $bodyformat,
            ],
            'nonscalar' => $nonscalar
        ];
        $block->instance_config_save((object) $configdata);
        $filename = 'img.png';
        $filerecord = array(
            'contextid' => \context_block::instance($block->instance->id)->id,
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
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Expect the new block.
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals($title, $result['blocks'][0]['contents']['title']);
        $this->assertEquals($body, $result['blocks'][0]['contents']['content']);
        $this->assertEquals(FORMAT_HTML, $result['blocks'][0]['contents']['contentformat']);    // Format change for external.
        $this->assertEquals('', $result['blocks'][0]['contents']['footer']);
        $this->assertCount(1, $result['blocks'][0]['contents']['files']);
        $this->assertEquals($newblock, $result['blocks'][0]['name']);
        $configcounts = 0;
        foreach ($result['blocks'][0]['configs'] as $config) {
            if ($config['type'] = 'plugin' && $config['name'] == 'allowcssclasses' && $config['value'] == json_encode('0')) {
                $configcounts++;
            } else if ($config['type'] = 'instance' && $config['name'] == 'text' && $config['value'] == json_encode($body)) {
                $configcounts++;
            } else if ($config['type'] = 'instance' && $config['name'] == 'title' && $config['value'] == json_encode($title)) {
                $configcounts++;
            } else if ($config['type'] = 'instance' && $config['name'] == 'format' && $config['value'] == json_encode('0')) {
                $configcounts++;
            } else if ($config['type'] = 'instance' && $config['name'] == 'nonscalar' &&
                    $config['value'] == json_encode($nonscalar)) {
                $configcounts++;
            }
        }
        $this->assertEquals(5, $configcounts);
    }

    /**
     * Test get_course_blocks contents with mathjax.
     */
    public function test_get_course_blocks_contents_with_mathjax() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Enable MathJax filter in content and headings.
        $this->configure_filters([
            ['name' => 'mathjaxloader', 'state' => TEXTFILTER_ON, 'move' => -1, 'applytostrings' => true],
        ]);

        // Create a few stuff to test with.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $coursecontext = \context_course::instance($course->id);

        // Create a HTML block.
        $title = 'My block $$(a+b)=2$$';
        $body = 'My block contents $$(a+b)=2$$';
        $bodyformat = FORMAT_MOODLE;
        $page = new \moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $newblock = 'html';
        $page->blocks->add_block_at_end_of_default_region($newblock);

        $this->setUser($user);
        // Re-create the page.
        $page = new \moodle_page();
        $page->set_context($coursecontext);
        $page->set_pagelayout('course');
        $course->format = course_get_format($course)->get_format();
        $page->set_pagetype('course-view-' . $course->format);
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance('html', $block->instance);
        $nonscalar = [
            'something' => true,
        ];
        $configdata = (object) [
            'title' => $title,
            'text' => [
                'itemid' => 0,
                'text' => $body,
                'format' => $bodyformat,
            ],
            'nonscalar' => $nonscalar
        ];
        $block->instance_config_save((object) $configdata);

        // Check for the new block.
        $result = core_block_external::get_course_blocks($course->id, true);
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        // Format the original data.
        $sitecontext = \context_system::instance();
        $title = \core_external\util::format_string($title, $coursecontext->id);
        list($body, $bodyformat) = \core_external\util::format_text($body, $bodyformat, $coursecontext, 'block_html', 'content');

        // Check that the block data is formatted.
        $this->assertCount(1, $result['blocks']);
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">',
                $result['blocks'][0]['contents']['title']);
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">',
                $result['blocks'][0]['contents']['content']);
        $this->assertEquals($title, $result['blocks'][0]['contents']['title']);
        $this->assertEquals($body, $result['blocks'][0]['contents']['content']);
    }

    /**
     * Test user get default dashboard blocks.
     */
    public function test_get_dashboard_blocks_default_dashboard() {
        global $PAGE, $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        // Force a setting change to check the returned blocks settings.
        set_config('displaycategories', 0, 'block_myoverview');

        $systempage = $DB->get_record('my_pages', array('userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => true));
        // Get the expected default blocks.
        $alldefaultblocksordered = $DB->get_records_menu(
            'block_instances',
            array('pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id),
            'defaultregion, defaultweight ASC',
            'id, blockname'
        );

        $this->setUser($user);

        // Check for the default blocks.
        $result = core_block_external::get_dashboard_blocks($user->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all default blocks defined in blocks_add_default_system_blocks().
        $this->assertCount(count($alldefaultblocksordered), $result['blocks']);
        $returnedblocks = array();
        foreach ($result['blocks'] as $block) {
            // Check all the returned blocks are in the expected blocks array.
            $this->assertContains($block['name'], $alldefaultblocksordered);
            $returnedblocks[] = $block['name'];
            // Check the configuration returned for this default block.
            if ($block['name'] == 'myoverview') {
                // Convert config to associative array to avoid DB sorting randomness.
                $config = array_column($block['configs'], null, 'name');
                $this->assertArrayHasKey('displaycategories', $config);
                $this->assertEquals(json_encode('0'), $config['displaycategories']['value']);
                $this->assertEquals('plugin', $config['displaycategories']['type']);
            }
        }

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

        $systempage = $DB->get_record('my_pages', array('userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => true));
        // Get the expected default blocks.
        $alldefaultblocks = $DB->get_records_menu(
            'block_instances', array('pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id),
            '',
            'id, blockname'
        );

        // Now, add a sticky block.
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_pagetype('my-index');
        $page->set_url(new \moodle_url('/'));
        $page->blocks->add_region('side-pre');
        $page->blocks->load_blocks();
        $page->blocks->add_block('myprofile', 'side-pre', 0, true, '*');

        $this->setUser($user);

        // Check for the default blocks plus the sticky.
        $result = core_block_external::get_dashboard_blocks($user->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all default blocks defined in blocks_add_default_system_blocks() plus sticky one.
        $this->assertCount(count($alldefaultblocks) + 1, $result['blocks']);
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

        $systempage = $DB->get_record('my_pages', array('userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => true));
        // Get the expected default blocks.
        $alldefaultblocks = $DB->get_records_menu(
            'block_instances',
            array('pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id),
            '',
            'id, blockname'
        );

        // Add a custom block.
        $page = new \moodle_page();
        $page->set_context(\context_user::instance($user->id));
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
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all default blocks defined in blocks_add_default_system_blocks() plus the one we added.
        $this->assertCount(count($alldefaultblocks) + 1, $result['blocks']);
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

    /**
     * Test user get default dashboard blocks for my courses page.
     */
    public function test_get_dashboard_blocks_my_courses() {
        global $PAGE, $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        // Force a setting change to check the returned blocks settings.
        set_config('displaycategories', 0, 'block_myoverview');

        $systempage = $DB->get_record('my_pages', ['userid' => null, 'name' => MY_PAGE_COURSES, 'private' => false]);
        // Get the expected default blocks.
        $alldefaultblocksordered = $DB->get_records_menu(
            'block_instances',
            ['pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id],
            'defaultregion, defaultweight ASC',
            'id, blockname'
        );

        $this->setUser($user);

        // Check for the default blocks.
        $result = core_block_external::get_dashboard_blocks($user->id, false, MY_PAGE_COURSES);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \core_external\external_api::clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);
        // Expect all default blocks defined in blocks_add_default_system_blocks().
        $this->assertCount(count($alldefaultblocksordered), $result['blocks']);
        $returnedblocks = [];
        foreach ($result['blocks'] as $block) {
            // Check all the returned blocks are in the expected blocks array.
            $this->assertContains($block['name'], $alldefaultblocksordered);
            $returnedblocks[] = $block['name'];
            // Check the configuration returned for this default block.
            if ($block['name'] == 'myoverview') {
                // Convert config to associative array to avoid DB sorting randomness.
                $config = array_column($block['configs'], null, 'name');
                $this->assertArrayHasKey('displaycategories', $config);
                $this->assertEquals(json_encode('0'), $config['displaycategories']['value']);
                $this->assertEquals('plugin', $config['displaycategories']['type']);
            }
        }

        // Check that we received the blocks in the expected order.
        $this->assertEquals(array_values($alldefaultblocksordered), $returnedblocks);
    }

    /**
     * Test user passing the wrong page type and getting an exception.
     */
    public function test_get_dashboard_blocks_incorrect_page() {
        global $PAGE;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');    // Need this because some internal API calls require the $PAGE url to be set.

        $this->setUser($user);

        $this->expectException('moodle_exception');
        // Check for the default blocks with a fake page, no need to assign as it'll throw.
        core_block_external::get_dashboard_blocks($user->id, false, 'fakepage');

    }
}
