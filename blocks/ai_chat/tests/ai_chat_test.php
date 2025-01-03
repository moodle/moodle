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
 * Test class for the block_ai_chat.
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Andreas Wagner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group      local_mbs
 * @group      mebis
 */

namespace block_ai_chat;

defined('MOODLE_INTERNAL') || die();

use core\di;
use core\hook\output\before_footer_html_generation;
use moodle_page;

global $CFG;
require_once($CFG->dirroot . '/lib/blocklib.php');
require_once($CFG->dirroot . '/course/edit_form.php');

/**
 * Test class for the block_ai_chat.
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Andreas Wagner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class ai_chat_test extends \advanced_testcase {

    /**
     * Create a page with regions.
     *
     * @param array $regions
     * @param object $context
     * @param object $course
     * @param string $pagetype
     * @return moodle_page
     * @throws \coding_exception
     */
    protected function get_moodle_page(
            array $regions,
            object $context,
            object $course,
            string $pagetype
    ): \moodle_page {

        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_course($course);
        $page->set_pagetype($pagetype);
        $page->set_url(new \moodle_url('/'));

        $page->blocks->add_regions($regions, false);
        $page->blocks->set_default_region($regions[0]);

        return $page;
    }

    /**
     * Tests the method get_addable_blocks
     *
     * @covers \block_ai_chat::applicable_formats
     */
    public function test_get_addable_blocks(): void {

        $this->setAdminUser();
        $this->resetAfterTest();

        // Create course containing a module.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $options = ['course' => $course->id];
        $forum = $this->getDataGenerator()->create_module('forum', $options);
        $contextmodule = \context_module::instance($forum->cmid);

        // Get course page and module page.
        $coursepage = $this->get_moodle_page(
                ['side-pre'], $coursecontext, $course, 'course-view'
        );

        $modulepage = $this->get_moodle_page(
                ['side-pre'], $contextmodule, $course, 'mod-forum-view'
        );

        // Block cannot be added manually on course pages.
        $coursepage->blocks->load_blocks(false);
        $coursepage->blocks->create_all_block_instances();
        $this->assertFalse(in_array('ai_chat', array_keys($coursepage->blocks->get_addable_blocks())));

        $modulepage->blocks->load_blocks(false);
        $modulepage->blocks->create_all_block_instances();
        $this->assertFalse(in_array('ai_chat', array_keys($modulepage->blocks->get_addable_blocks())));
    }

    /**
     * Test hook before_footer.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     *
     * @covers \hook_callbacks::handle_before_footer_html_generation
     */
    public function test_before_footer_html_generation_hook(): void {
        global $PAGE, $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Assert there is no general block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertEmpty($aiblockinstances);

        // Replace the version of the manager in the DI container with a phpunit one.
        \core\di::set(
                \core\hook\manager::class,
                \core\hook\manager::phpunit_get_instance([
                        'block_ai_chat' => $CFG->dirroot . '/blocks/ai_chat/db/hooks.php',
                ]),
        );

        // Prepare renderer.
        $PAGE->blocks->add_region('side-pre');
        $renderer = $PAGE->get_renderer('core', null, RENDERER_TARGET_GENERAL);

        // Dispatch.
        $hook = new before_footer_html_generation($renderer);
        di::get(\core\hook\manager::class)->dispatch($hook);

        // Assert there is no general block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertEmpty($aiblockinstances);

        // Set proper pagetype.
        set_config('showonpagetypes', 'vendor-bin-phpunit', 'block_ai_chat');
        // Dispatch.
        ob_start();
        di::get(\core\hook\manager::class)->dispatch($hook);
        $output = ob_get_clean();

        // Assert bock is printed.
        $this->assertStringContainsString('id="ai_chat_button"', $output);
        // Assert there is one general block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertCount(1, $aiblockinstances);
        $aiblockinstance = array_shift($aiblockinstances);

        $this->assertEquals(\context_system::instance()->id, $aiblockinstance->parentcontextid);
        $this->assertEquals(0, $aiblockinstance->showinsubcontexts);
        $this->assertEquals('', $aiblockinstance->pagetypepattern);

        // Dispatch, while global block exists.
        ob_start();
        di::get(\core\hook\manager::class)->dispatch($hook);
        $output = ob_get_clean();

        // Assert there is no additional block instance created.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertCount(1, $aiblockinstances);
    }

    /**
     * Test the course settings form hooks.
     *
     * @return void
     * @throws \coding_exception
     *
     * @covers \hook_callbacks::handle_after_form_definition
     * @covers \hook_callbacks::handle_after_form_submission
     * @covers \hook_callbacks::handle_after_form_definition_after_data
     */
    public function test_course_settings_form_hooks(): void {
        global $CFG, $DB, $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Replace the version of the manager in the DI container with a phpunit one.
        \core\di::set(
                \core\hook\manager::class,
                \core\hook\manager::phpunit_get_instance([
                        'block_ai_chat' => $CFG->dirroot . '/blocks/ai_chat/db/hooks.php',
                ]),
        );

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = \context_course::instance($course->id);

        $PAGE->set_url('/');
        $editoroptions = [
                'context' => $coursecontext,
        ];

        // Create an course settings edit form mock.
        $course = file_prepare_standard_editor($course, 'summary', $editoroptions,
                $coursecontext, 'course', 'summary', 0);
        $editform = new mock_course_edit_form(null, ['course' => $course, 'category' => $category,
                'editoroptions' => $editoroptions, 'returnto' => '0', 'returnurl' => '']);
        $mform = $editform->get_mform();

        // Tenants are not restricted per default, so addaichat form element should exist.
        $addaichatelement = $mform->getElement('addaichat');
        $this->assertNotEmpty($addaichatelement);

        // Trigger definition_after_data.
        $editform->render();

        // There is no other instance of an ai chat block, so lement should be unchecked.
        $this->assertEquals(false, $addaichatelement->getValue());

        // Assert there is no general block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertEmpty($aiblockinstances);

        // Update with addaichat flag set.
        $data = (object) $mform->exportValues();
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions,
                $coursecontext, 'course', 'summary', 0);
        $data->addaichat = 1;
        // Update course triggers handle_after_form_submission.
        update_course($data, $editoroptions);

        // Assert there is one course block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertCount(1, $aiblockinstances);
        $aiblockinstance = array_shift($aiblockinstances);

        $this->assertEquals($coursecontext->id, $aiblockinstance->parentcontextid);
        $this->assertEquals(1, $aiblockinstance->showinsubcontexts);
        $this->assertEquals('*', $aiblockinstance->pagetypepattern);

        // Assert that addaichat element is existing and is checked.
        $editform = new mock_course_edit_form(null, ['course' => $course, 'category' => $category,
                'editoroptions' => $editoroptions, 'returnto' => '0', 'returnurl' => '']);
        $mform = $editform->get_mform();

        // Tenants are not restricted per default, so addaichat form element should exist.
        $addaichatelement = $mform->getElement('addaichat');
        $this->assertNotEmpty($addaichatelement);

        // Trigger definition_after_data.
        $editform->render();

        // There is no other instance of an ai chat block, so element should be checked.
        $this->assertEquals(true, $addaichatelement->getValue());

        // Update with addaichat flag set.
        $data = (object) $mform->exportValues();
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $coursecontext, 'course', 'summary', 0);
        $data->addaichat = 0;
        // Update course triggers handle_after_form_submission.
        update_course($data, $editoroptions);

        // Assert there is no course block instance existing.
        $aiblockinstances = $DB->get_records('block_instances', ['blockname' => 'ai_chat']);
        $this->assertCount(0, $aiblockinstances);
    }
}

/**
 * Class to retrieve MoodleQuickform from course_edit_form.
 *
 * @package   block_ai_chat
 * @copyright 2024 Andreas Wagner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
class mock_course_edit_form extends \course_edit_form {

    /**
     * Get the protected MoodleQuickForm.
     *
     * @return MoodleQuickForm the form used.
     */
    public function get_mform(): \MoodleQuickForm {
        return $this->_form;
    }
}
