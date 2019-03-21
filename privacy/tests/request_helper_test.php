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
 * Unit Tests for the request helper.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\helper;
use \core_privacy\local\request\writer;

/**
 * Tests for the \core_privacy API's request helper functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\helper
 */
class request_helper_test extends advanced_testcase {
    /**
     * Test that basic module data is returned.
     *
     * @covers ::get_context_data
     */
    public function test_get_context_data_context_module() {
        $this->resetAfterTest();

        // Setup.
        $course = $this->getDataGenerator()->create_course();
        $user = \core_user::get_user_by_username('admin');

        $forum = $this->getDataGenerator()->create_module('forum', [
                'course' => $course->id,
            ]);
        $context = context_module::instance($forum->cmid);
        $modinfo = get_fast_modinfo($course->id);
        $cm = $modinfo->cms[$context->instanceid];

        // Fetch the data.
        $result = helper::get_context_data($context, $user);
        $this->assertInstanceOf('stdClass', $result);

        // Check that the name matches.
        $this->assertEquals($cm->get_formatted_name(), $result->name);

        // This plugin supports the intro. Check that it is included and correct.
        $formattedintro = format_text($forum->intro, $forum->introformat, [
                'noclean' => true,
                'para' => false,
                'context' => $context,
                'overflowdiv' => true,
            ]);
        $this->assertEquals($formattedintro, $result->intro);

        // This function should only fetch data. It does not export it.
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that basic block data is returned.
     *
     * @covers ::get_context_data
     */
    public function test_get_context_data_context_block() {
        $this->resetAfterTest();

        // Setup.
        $block = $this->getDataGenerator()->create_block('online_users');
        $context = context_block::instance($block->id);
        $user = \core_user::get_user_by_username('admin');

        // Fetch the data.
        $data = helper::get_context_data($context, $user);
        $this->assertEquals(get_string('pluginname', 'block_online_users'), $data->blocktype);

         // This function should only fetch data. It does not export it.
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that a course moudle with completion tracking enabled has the completion data returned.
     *
     * @covers ::get_context_data
     */
    public function test_get_context_data_context_module_completion() {
        $this->resetAfterTest();

        // Create a module and set completion.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'completion' => 1]);
        $context = context_module::instance($assign->cmid);
        $cm = get_coursemodule_from_id('assign', $assign->cmid);

        // Fetch context data.
        $contextdata = helper::get_context_data($context, $user);

        // Completion state is zero.
        // Check non completion for a user.
        $this->assertEquals(0, $contextdata->completion->state);

        // Complete the activity as a user.
        $completioninfo = new completion_info($course);
        $completioninfo->update_state($cm, COMPLETION_COMPLETE, $user->id);

        // Check that completion is now exported.
        $contextdata = helper::get_context_data($context, $user);
        $this->assertEquals(1, $contextdata->completion->state);

        // This function should only fetch data. It does not export it.
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that when there are no files to export for a course module context, nothing is exported.
     *
     * @covers ::export_context_files
     */
    public function test_export_context_files_context_module_no_files() {
        $this->resetAfterTest();

        // Setup.
        $course = $this->getDataGenerator()->create_course();
        $user = \core_user::get_user_by_username('admin');

        $forum = $this->getDataGenerator()->create_module('forum', [
                'course' => $course->id,
            ]);
        $context = context_module::instance($forum->cmid);
        $modinfo = get_fast_modinfo($course->id);
        $cm = $modinfo->cms[$context->instanceid];

        // Fetch the data.
        helper::export_context_files($context, $user);

        // This function should only fetch data. It does not export it.
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that when there are no files to export for a course context, nothing is exported.
     *
     * @covers ::export_context_files
     */
    public function test_export_context_files_context_course_no_files() {
        $this->resetAfterTest();

        // Setup.
        $course = $this->getDataGenerator()->create_course();
        $user = \core_user::get_user_by_username('admin');
        $context = context_course::instance($course->id);

        // Fetch the data.
        helper::export_context_files($context, $user);

        // This function should only fetch data. It does not export it.
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that when there are files to export for a course context, the files are exported.
     *
     * @covers ::export_context_files
     */
    public function test_export_context_files_context_course_intro_files() {
        $this->resetAfterTest();

        // Setup.
        $course = $this->getDataGenerator()->create_course();
        $user = \core_user::get_user_by_username('admin');
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $context = context_module::instance($assign->cmid);

        // File details.
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'mod_assign',
            'filearea'  => 'intro',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'logo.png',
        );

        $content = file_get_contents(__DIR__ . '/fixtures/logo.png');

        // Store the file.
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $content);

        // Fetch the data.
        helper::export_context_files($context, $user);

        // This should have resulted in the file being exported.
        $this->assertTrue(writer::with_context($context)->has_any_data());
    }

}
