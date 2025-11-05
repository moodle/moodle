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
 * Testcase class for the tool_ally\componentsupport\assign_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\files_in_use;
use tool_ally\webservice\course_files;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\assign_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files_in_use_test extends abstract_testcase {
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
     * @var stdClass
     */
    private $assign;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->assign = $gen->create_module('assign',
            [
                'course' => $this->course->id,
                'introformat' => FORMAT_HTML,
                'intro' => 'Text in intro'
            ]
        );
    }

    /**
     * Test if file in use detection is working.
     */
    public function test_check_file_in_use() {
        global $DB;

        set_config('excludeunused', 1, 'tool_ally');
        $context = \context_module::instance($this->assign->cmid);
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_ally');

        list($usedfile, $unusedfile) = $this->setup_check_files($context, 'mod_assign', 'intro', 0);

        // Update the intro with the link.
        $link = $generator->create_pluginfile_link_for_file($usedfile);
        $DB->set_field('assign', 'intro', $link, ['id' => $this->assign->id]);

        $this->assertCount(0, $DB->get_records('tool_ally_file_in_use'));

        // Make sure it is seen as in use.
        $this->assertTrue(files_in_use::check_file_in_use($usedfile));

        // Check that the record looks like we expect.
        $record = $DB->get_record('tool_ally_file_in_use', ['fileid' => $usedfile->get_id()]);
        $this->assertEquals(1, $record->inuse);
        $this->assertEquals(0, $record->needsupdate);
        $this->assertEquals($context->id, $record->contextid);
        $this->assertEquals($this->course->id, $record->courseid);

        // See that there is no existing record (because we haven't checked for its status yet).
        $record = $DB->get_record('tool_ally_file_in_use', ['fileid' => $unusedfile->get_id()]);
        $this->assertFalse($record);

        // Check, make sure file shows as not in use.
        $this->assertFalse(files_in_use::check_file_in_use($unusedfile));

        $record = $DB->get_record('tool_ally_file_in_use', ['fileid' => $unusedfile->get_id()]);
        $this->assertEquals(0, $record->inuse);
        $this->assertEquals(0, $record->needsupdate);
        $this->assertEquals($context->id, $record->contextid);
        $this->assertEquals($this->course->id, $record->courseid);

        // Last thing, check that 'always used' files are not stored.
        $this->assertCount(2, $DB->get_records('tool_ally_file_in_use'));
        list($attachfile1, $attachfile2) = $this->setup_check_files($context, 'mod_assign', 'introattachment', 0);

        $this->assertTrue(files_in_use::check_file_in_use($attachfile1));
        $this->assertTrue(files_in_use::check_file_in_use($attachfile2));

        // Confirm no new records are in the DB.
        $this->assertCount(2, $DB->get_records('tool_ally_file_in_use'));
    }
}
