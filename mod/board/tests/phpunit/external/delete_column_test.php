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

use mod_board\external\delete_column;
use mod_board\board;

/**
 * Test external method for deleting of columns.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\delete_column
 */
final class delete_column_test extends \advanced_testcase {
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
            'groupmode' => NOGROUPS,
        ]);

        $teacher1 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');

        $this->setUser($teacher1);

        $column4 = $generator->create_column(['boardid' => $board1->id, 'name' => 'Col X']);
        $column5 = $generator->create_column(['boardid' => $board1->id, 'name' => 'Col Z']);

        $result = delete_column::execute($column4->id);
        $result = delete_column::clean_returnvalue(delete_column::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertNotEmpty($result['historyid']);
        $this->assertFalse($DB->record_exists('board_columns', ['id' => $column4->id]));

        $result = delete_column::execute($column4->id);
        $result = delete_column::clean_returnvalue(delete_column::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertSame(0, $result['historyid']);
        $this->assertFalse($DB->record_exists('board_columns', ['id' => $column4->id]));

        $this->setUser($student1);

        try {
            delete_column::execute($column5->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\required_capability_exception::class, $ex);
            $this->assertSame(
                'Sorry, but you do not currently have permissions to do that (Manage columns and manage all posts.).',
                $ex->getMessage()
            );
        }
    }
}
