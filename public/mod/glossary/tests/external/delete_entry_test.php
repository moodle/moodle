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

namespace mod_glossary\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;
use externallib_advanced_testcase;

/**
 * External function test for delete_entry.
 *
 * @package    mod_glossary
 * @category   external
 * @covers     \mod_glossary\external\delete_entry
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class delete_entry_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of delete_entry().
     */
    public function test_delete_entry(): void {
        global $DB;
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $anotherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $this->setUser($student);
        $entry = $gg->create_content($glossary);

        // Test entry creator can delete.
        $result = delete_entry::execute($entry->id);
        $result = external_api::clean_returnvalue(delete_entry::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertEquals(0, $DB->count_records('glossary_entries', ['id' => $entry->id]));

        // Test admin can delete.
        $this->setAdminUser();
        $entry = $gg->create_content($glossary);
        $result = delete_entry::execute($entry->id);
        $result = external_api::clean_returnvalue(delete_entry::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertEquals(0, $DB->count_records('glossary_entries', ['id' => $entry->id]));

        $entry = $gg->create_content($glossary);
        // Test a different student is not able to delete.
        $this->setUser($anotherstudent);
        $this->expectExceptionMessage(get_string('nopermissiontodelentry', 'error'));
        delete_entry::execute($entry->id);
    }
}
