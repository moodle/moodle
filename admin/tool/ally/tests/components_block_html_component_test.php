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
 * Testcase class for the tool_ally\componentsupport\block_html_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_content;
use tool_ally\models\component;
use tool_ally\componentsupport\block_html_component;
use tool_ally\webservice\course_content;
use tool_ally\testing\traits\component_assertions;
use block_html\search_content_test;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\block_html_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_block_html_component_test extends abstract_testcase {
    use component_assertions;

    /**
     * @var stdClass
     */
    private $admin;

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var context_course
     */
    private $coursecontext;

    /**
     * @var block_html_component
     */
    private $component;

    public function setUp(): void {
        global $CFG;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->admin = get_admin();
        $this->setAdminUser();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        require_once($CFG->dirroot.'/blocks/html/tests/search_content_test.php');
        $this->component = local_content::component_instance('block_html');
    }

    private function add_block( array $data = null) : \block_html {
        global $USER;

        $sctc = new search_content_test();

        $block = \phpunit_util::call_internal_method($sctc, 'create_block',
            ['course' => $this->course], get_class($sctc));

        // Change block settings to add some text and a file.
        $itemid = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $fs->create_file_from_string(['component' => 'user', 'filearea' => 'draft',
            'contextid' => $usercontext->id, 'itemid' => $itemid, 'filepath' => '/',
            'filename' => 'file.txt'], 'File content');

        if ($data === null) {
            $data = [
                'title' => 'Block title',
                'text' => [
                    'text' => '<div>Block html</div>',
                    'itemid' => $itemid,
                    'format' => FORMAT_HTML
                ]
            ];
        } else if (isset($data['text']) && empty($data['text']['itemid'])) {
            $data['text']['itemid'] = $itemid;
        }
        $block->instance_config_save((object) $data);
        $page = \phpunit_util::call_internal_method($sctc, 'construct_page',
            ['course' => $this->course], get_class($sctc));
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        return end($blocks);
    }

    public function test_list_content() {
        $this->setAdminUser();
        $block = $this->add_block();
        $id = $block->context->instanceid;
        $contentitems = course_content::service([$this->course->id]);
        $component = new component(
            $id, 'block_html', 'block_instances', 'configdata',
            $this->course->id, 0, FORMAT_HTML, $block->title);
        $this->assert_component_is_in_array($component, $contentitems);

    }

    public function test_get_all_html_content_items() {
        $block = $this->add_block();
        $contentitems = $this->component->get_all_html_content($block->context->instanceid);

        $this->assert_content_items_contain_item($contentitems,
            $block->context->instanceid, 'block_html', 'block_instances', 'configdata');
    }

    public function test_get_all_html_content() {
        $sctc = new search_content_test();

        // Create an empty unconfigured block.
        // Ensure this does not trigger an error and that content has empty format and text.
        $htmlblock = \phpunit_util::call_internal_method($sctc, 'create_block',
            ['course' => $this->course], get_class($sctc));
        $block = $htmlblock->instance;
        $contents = $this->component->get_all_html_content($block->id);
        $this->assertCount(1, $contents);
        $content = reset($contents);
        $this->assertEmpty($content->title);
        $this->assertEmpty($content->content);
        $this->assertEmpty($content->contentformat);

        // Update the block so that it is now configured.
        $page = $htmlblock->page;
        $itemid = file_get_unused_draft_itemid();
        $expectedtitle = 'Block title';
        $expectedtext = '<div>Block html</div>';
        $expectedformat = FORMAT_HTML;
        $data = [
            'title' => $expectedtitle,
            'text' => [
                'text' => $expectedtext,
                'itemid' => $itemid,
                'format' => $expectedformat
            ]
        ];

        // Reget the block.
        $htmlblock->instance_config_save((object) $data);
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks)->instance;
        $contents = $this->component->get_all_html_content($block->id);
        $this->assertCount(1, $contents);
        $content = reset($contents);
        $this->assertEquals($expectedtitle, $content->title);
        $this->assertEquals($expectedtext, $content->content);
        $this->assertEquals($expectedformat, $content->contentformat);
    }

    public function test_get_course_html_content_items() {
        $sctc = new search_content_test();

        // Create an empty unconfigured block.
        // Ensure this does not trigger an error and that content has empty format and text.
        $htmlblock = \phpunit_util::call_internal_method($sctc, 'create_block',
            ['course' => $this->course], get_class($sctc));
        $contents = $this->component->get_course_html_content_items($this->course->id);

        $this->assertCount(1, $contents);
        $content = reset($contents);
        $this->assertEquals('block_html', $content->component);
        $this->assertEmpty($content->title);
        $this->assertEmpty($content->contentformat);

        // Update the block so that it is now configured.
        $page = $htmlblock->page;
        $itemid = file_get_unused_draft_itemid();
        $expectedtitle = 'Block title';
        $expectedtext = '<div>Block html</div>';
        $expectedformat = FORMAT_HTML;
        $data = [
            'title' => $expectedtitle,
            'text' => [
                'text' => $expectedtext,
                'itemid' => $itemid,
                'format' => $expectedformat
            ]
        ];

        // Reget the block.
        $htmlblock->instance_config_save((object) $data);
        $contents = $this->component->get_course_html_content_items($this->course->id);
        $this->assertCount(1, $contents);
        $content = reset($contents);
        $this->assertEquals('block_html', $content->component);
        $this->assertEquals($expectedtitle, $content->title);
        $this->assertEquals($expectedformat, $content->contentformat);
    }

    /**
     * Test if file in use detection is working with this module.
     */
    public function test_file_in_use() {
        global $USER;

        set_config('excludeunused', 1, 'tool_ally');

        $this->setAdminUser();

        $htmlblock = $this->add_block();

        $context = $htmlblock->context;
        // Setup some files we are going to use.
        $usercontext = \context_user::instance($USER->id);
        $itemid = file_get_unused_draft_itemid();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_ally');

        // Because of how block saving works, we are going to add the files to user drafts.
        $record = [
                'filename'  => 'unused.txt',
                'contextid' => $usercontext->id,
                'component' => 'user',
                'filearea'  => 'draft',
                'itemid'    => $itemid
        ];

        $htmlunusedfile = $generator->create_file($record);
        $record['filename'] = 'used1.txt';
        $htmlusedfile1 = $generator->create_file($record);
        $record['filename'] = 'used2.txt';
        $htmlusedfile2 = $generator->create_file($record);

        $fileids = $this->get_file_ids_in_context($context);
        $this->assertCount(0, $fileids);

        $url = \moodle_url::make_pluginfile_url($context->id, 'block_html', 'content',
                null, $htmlusedfile2->get_filepath(), $htmlusedfile2->get_filename());

        // Now update the content the two used links, in the two different formats.
        $data = [
                'title' => $htmlblock->get_title(),
                'text' => [
                        'text' => $generator->create_pluginfile_link_for_file($htmlusedfile1) .
                                '<a href="' . $url->out() . '">Link</a>',
                        'itemid' => $itemid,
                        'format' => FORMAT_HTML
                ]
        ];
        $htmlblock->instance_config_save((object) $data);

        // Make sure it matches the expected file.
        $fileids = $this->get_file_ids_in_context($context);
        // Because of the user draft issue above, we are going to convert from file ids to filenames.
        $fs = get_file_storage();
        $filenames = [];
        foreach ($fileids as $fileid) {
            $file = $fs->get_file_by_id($fileid);
            $filenames[] = $file->get_filename();

        }
        $this->assertCount(2, $filenames);

        $this->assertContains('used1.txt', $filenames);
        $this->assertContains('used2.txt', $filenames);
        $this->assertNotContains('unused.txt', $filenames);
    }
}
