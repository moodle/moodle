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

namespace mod_board\phpunit\external;

use mod_board\external\delete_note;
use mod_board\board;

/**
 * Test external method for deleting of notes.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\delete_note
 */
final class delete_note_test extends \advanced_testcase {
    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $note1 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note2 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note3 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $teacher0->id]);

        $this->setUser($student1);

        $result = delete_note::execute($note1->id);
        $result = delete_note::clean_returnvalue(delete_note::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertNotEmpty($result['historyid']);

        $result = delete_note::execute($note1->id);
        $result = delete_note::clean_returnvalue(delete_note::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertSame(0, $result['historyid']);

        $this->setUser($student2);

        try {
            delete_note::execute($note2->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\required_capability_exception::class, $ex);
            $this->assertSame(
                'Sorry, but you do not currently have permissions to do that (Manage columns and manage all posts.).',
                $ex->getMessage()
            );
        }

        $this->setUser($teacher0);
        $result = delete_note::execute($note2->id);
        $result = delete_note::clean_returnvalue(delete_note::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertNotEmpty($result['historyid']);

        $this->setUser($student4);

        try {
            delete_note::execute($note3->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Activity is hidden)', $ex->getMessage());
        }

        $this->setUser($student5);

        try {
            delete_note::execute($note3->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Not enrolled)', $ex->getMessage());
        }
    }
}
