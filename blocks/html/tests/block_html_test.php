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

namespace block_html;

/**
 * Unit test for block_html class.
 *
 * @package   block_html
 * @copyright 2022 Open LMS (https://www.openlms.net/)
 * @author    Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \block_html
 */
class block_html_test extends \advanced_testcase {
    /**
     * Tests instance files copying.
     * @covers ::instance_copy
     */
    public function test_instance_copy(): void {
        global $USER;
        $this->resetAfterTest();

        $this->setAdminUser();
        $fs = get_file_storage();

        $course = $this->getDataGenerator()->create_course();
        $block1 = $this->create_block($course);
        $itemid = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $fs->create_file_from_string(['component' => 'user', 'filearea' => 'draft',
            'contextid' => $usercontext->id, 'itemid' => $itemid, 'filepath' => '/',
            'filename' => 'file.txt'], 'File content');
        $data = (object)['title' => 'Block title', 'text' => ['text' => 'Block text',
            'itemid' => $itemid, 'format' => FORMAT_HTML]];
        $block1->instance_config_save($data);
        $this->assertTrue($fs->file_exists($block1->context->id, 'block_html', 'content', 0, '/', 'file.txt'));

        $block2 = $this->create_block($course);
        $this->assertFalse($fs->file_exists($block2->context->id, 'block_html', 'content', 0, '/', 'file.txt'));

        $this->setUser(null);
        $block2->instance_copy($block1->instance->id);
        $this->assertTrue($fs->file_exists($block2->context->id, 'block_html', 'content', 0, '/', 'file.txt'));
    }

    /**
     * Constructs a page object for the test course.
     *
     * @param \stdClass $course Moodle course object
     * @return \moodle_page Page object representing course view
     */
    protected static function construct_page($course): \moodle_page {
        $context = \context_course::instance($course->id);
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_course($course);
        $page->set_pagelayout('standard');
        $page->set_pagetype('course-view');
        $page->blocks->load_blocks();
        return $page;
    }

    /**
     * Creates an HTML block on a course.
     *
     * @param \stdClass $course Course object
     * @return \block_html Block instance object
     */
    protected function create_block($course): \block_html {
        $page = self::construct_page($course);
        $page->blocks->add_block_at_end_of_default_region('html');

        // Load the block.
        $page = self::construct_page($course);
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        return $block;
    }
}
