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
 * Generator tests.
 *
 * @package    core_notes
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_notes;

/**
 * Generator tests class.
 *
 * @package    core_notes
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {

    /** Test create_instance method */
    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');

        $this->assertFalse($DB->record_exists('post', array('courseid' => $course->id)));
        $note = $gen->create_instance(array('courseid' => $course->id, 'userid' => $user->id));
        $this->assertEquals(1, $DB->count_records('post', array('courseid' => $course->id, 'userid' => $user->id)));
        $this->assertTrue($DB->record_exists('post', array('id' => $note->id)));

        $params = array('courseid' => $course->id, 'userid' => $user->id, 'publishstate' => NOTES_STATE_DRAFT);
        $note = $gen->create_instance($params);
        $this->assertEquals(2, $DB->count_records('post', array('courseid' => $course->id, 'userid' => $user->id)));
        $this->assertEquals(NOTES_STATE_DRAFT, $DB->get_field_select('post', 'publishstate', 'id = :id',
                array('id' => $note->id)));
    }

    /** Test Exceptions thrown by create_instance method */
    public function test_create_instance_exceptions(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');

        // Test not setting userid.
        try {
            $gen->create_instance(array('courseid' => 2));
            $this->fail('A note should not be allowed to be created without associcated userid');
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Module generator requires $record->userid', $e->getMessage());
        }

        // Test not setting courseid.
        try {
            $gen->create_instance(array('userid' => 2));
            $this->fail('A note should not be allowed to be created without associcated courseid');
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Module generator requires $record->courseid', $e->getMessage());
        }
    }

}

