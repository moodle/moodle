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

use mod_board\external\move_column;
use mod_board\board;

/**
 * Test external method for moving of columns.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\move_column
 */
final class move_column_test extends \advanced_testcase {
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

        $result = move_column::execute($column4->id, 1);
        $result = move_column::clean_returnvalue(move_column::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertNotEmpty($result['historyid']);
        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $this->assertSame('1', $column1->sortorder);
        $this->assertSame('2', $column4->sortorder);
        $this->assertSame('3', $column2->sortorder);
        $this->assertSame('4', $column3->sortorder);

        $this->setUser($student1);

        try {
            move_column::execute($column4->id, 2);
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
