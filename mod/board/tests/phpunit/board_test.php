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

namespace mod_board\phpunit;

use mod_board\board;
use mod_board\local\note;

/**
 * Board class test.
 *
 * @package    mod_board
 * @copyright  2020 onward: Brickfield Education Labs <https://www.brickfield.ie/>
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\board
 */
final class board_test extends \advanced_testcase {
    public function test_coursemodule_for_board(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $result = board::coursemodule_for_board($board);
        $this->assertEquals($result->instance, $board->id);
    }

    public function test_get_board(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $result = board::get_board($board->id);
        $this->assertEquals($board, $result);
        $cm = board::coursemodule_for_board($board);
        $this->assertSame($cm->id, $result->cmid);
        $context = \context_module::instance($result->cmid);

        $this->assertNull(board::get_board(-1));

        try {
            board::get_board(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_get_board_for_columnid(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $result = board::get_board_for_columnid($columns[0]->id);
        $this->assertEquals($board, $result);
        $cm = board::coursemodule_for_board($board);
        $this->assertSame($cm->id, $result->cmid);
        $context = \context_module::instance($result->cmid);

        $this->assertNull(board::get_board_for_columnid(-1));

        try {
            board::get_board_for_columnid(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_get_board_for_noteid(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $user = $this->getDataGenerator()->create_user();

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $note = $generator->create_note(['columnid' => $columns[0]->id, 'userid' => $user->id]);
        $board = board::get_board($board->id);

        $result = board::get_board_for_noteid($note->id);
        $this->assertEquals($board, $result);
        $cm = board::coursemodule_for_board($board);
        $this->assertSame($cm->id, $result->cmid);
        $context = \context_module::instance($result->cmid);

        $this->assertNull(board::get_board_for_noteid(-1));

        try {
            board::get_board_for_noteid(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_get_column(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $result = board::get_column($columns[0]->id);
        $this->assertEquals($columns[0], $result);

        $this->assertNull(board::get_column(-1));

        try {
            board::get_column(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_get_note(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $user = $this->getDataGenerator()->create_user();

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $note = $generator->create_note(['columnid' => $columns[0]->id, 'userid' => $user->id]);

        $result = board::get_note($note->id);
        $this->assertEquals($note, $result);

        $this->assertNull(board::get_note(-1));

        try {
            board::get_note(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_get_teplate(): void {
        $this->resetAfterTest();

        $template = \mod_board\local\template::create((object)[
            'name' => 'Some template',
            'columns' => "Col 1\nCol 2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
        ]);

        $result = board::get_template($template->id);
        $this->assertEquals($template, $result);

        $this->assertNull(board::get_note(-1));

        try {
            board::get_template(-1, MUST_EXIST);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_context_for_board(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $context = board::context_for_board($board->id);
        $this->assertEquals($board->cmid, $context->instanceid);

        $context = board::context_for_board($board);
        $this->assertEquals($board->cmid, $context->instanceid);

        try {
            board::context_for_board(-1);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_context_for_column(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $output = board::context_for_column($columns[0]->id);
        $this->assertEquals($board->cmid, $output->instanceid);

        $output = board::context_for_column($columns[0]);
        $this->assertEquals($board->cmid, $output->instanceid);

        try {
            board::context_for_column(-1);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\dml_missing_record_exception::class, $ex);
        }
    }

    public function test_require_access_for_group(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => VISIBLEGROUPS,
        ]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($teacher0);

        board::require_access_for_group($board0, $group1->id);
        board::require_access_for_group($board1, $group1->id);
        board::require_access_for_group($board2, $group1->id);

        $this->setUser($student1);
        board::require_access_for_group($board0, $group1->id);
        board::require_access_for_group($board1, $group1->id);
        board::require_access_for_group($board2, $group1->id);

        board::require_access_for_group($board0, $group2->id);
        try {
            board::require_access_for_group($board1, $group2->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\required_capability_exception::class, $ex);
            $this->assertSame(
                'Sorry, but you do not currently have permissions to do that (Access all groups).',
                $ex->getMessage()
            );
        }
        try {
            board::require_access_for_group($board2, $group2->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\required_capability_exception::class, $ex);
            $this->assertSame(
                'Sorry, but you do not currently have permissions to do that (Access all groups).',
                $ex->getMessage()
            );
        }
    }

    public function test_can_view_note(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $cm1 = get_coursemodule_from_instance('board', $board1->id, $course->id, false, MUST_EXIST);
        $context1 = \context_module::instance($cm1->id);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
        ]);
        $cm2 = get_coursemodule_from_instance('board', $board2->id, $course->id, false, MUST_EXIST);
        $context2 = \context_module::instance($cm2->id);
        $board3 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
        ]);
        $cm3 = get_coursemodule_from_instance('board', $board3->id, $course->id, false, MUST_EXIST);
        $context3 = \context_module::instance($cm3->id);
        $board4 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
        ]);
        $cm4 = get_coursemodule_from_instance('board', $board4->id, $course->id, false, MUST_EXIST);
        $context4 = \context_module::instance($cm4->id);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher1 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $columns2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));
        $columns3 = array_values($DB->get_records('board_columns', ['boardid' => $board3->id], 'id ASC'));
        $columns4 = array_values($DB->get_records('board_columns', ['boardid' => $board4->id], 'id ASC'));

        $this->setUser($student1);
        $note1x1 = note::create($columns1[0]->id, $student1->id, 0, 'b1s1h1', 'test', []);
        $note2x1 = note::create($columns2[0]->id, $student1->id, 0, 'b2s1h1', 'test', []);
        $note3x1 = note::create($columns3[0]->id, $student1->id, 0, 'b3s1h1', 'test', []);
        $note4x1 = note::create($columns4[0]->id, $student1->id, $group1->id, 'b4s1h1', 'test', []);

        $this->setUser($student2);
        $note1x2 = note::create($columns1[0]->id, $student2->id, 0, 'b1s2h1', 'test', []);
        $note2x2 = note::create($columns2[0]->id, $student2->id, 0, 'b2s2h1', 'test', []);
        $note3x2 = note::create($columns3[0]->id, $student2->id, 0, 'b3s2h1', 'test', []);
        $note4x2 = note::create($columns4[0]->id, $student2->id, $group2->id, 'b4s2h1', 'test', []);

        $this->setUser($teacher1);
        $note2x1xt = note::create($columns2[0]->id, $student1->id, 0, 'b2s1h1', 'teach', []);
        $note3x1xt = note::create($columns3[0]->id, $student1->id, 0, 'b3s1h1', 'teach', []);

        $this->setUser($student1->id);
        $this->assertSame($context1->id, board::can_view_note($note1x1)->id);
        $this->assertSame($context2->id, board::can_view_note($note2x1)->id);
        $this->assertSame($context3->id, board::can_view_note($note3x1)->id);
        $this->assertSame($context4->id, board::can_view_note($note4x1)->id);
        $this->assertSame($context1->id, board::can_view_note($note1x2)->id);
        $this->assertSame(null, board::can_view_note($note2x2));
        $this->assertSame($context3->id, board::can_view_note($note3x2)->id);
        $this->assertSame(null, board::can_view_note($note4x2));
        $this->assertSame($context2->id, board::can_view_note($note2x1xt)->id);
        $this->assertSame($context3->id, board::can_view_note($note3x1xt)->id);

        $this->setUser($student3->id);
        $this->assertSame($context1->id, board::can_view_note($note1x1)->id);
        $this->assertSame(null, board::can_view_note($note2x1));
        $this->assertSame($context3->id, board::can_view_note($note3x1)->id);
        $this->assertSame($context4->id, board::can_view_note($note4x1)->id);
        $this->assertSame($context1->id, board::can_view_note($note1x2)->id);
        $this->assertSame(null, board::can_view_note($note2x2));
        $this->assertSame($context3->id, board::can_view_note($note3x2)->id);
        $this->assertSame(null, board::can_view_note($note4x2));
        $this->assertSame(null, board::can_view_note($note2x1xt));
        $this->assertSame($context3->id, board::can_view_note($note3x1xt)->id);

        $this->setUser($teacher1->id);
        $this->assertSame($context1->id, board::can_view_note($note1x1)->id);
        $this->assertSame($context2->id, board::can_view_note($note2x1)->id);
        $this->assertSame($context3->id, board::can_view_note($note3x1)->id);
        $this->assertSame($context4->id, board::can_view_note($note4x1)->id);
        $this->assertSame($context1->id, board::can_view_note($note1x2)->id);
        $this->assertSame($context2->id, board::can_view_note($note2x2)->id);
        $this->assertSame($context3->id, board::can_view_note($note3x2)->id);
        $this->assertSame($context4->id, board::can_view_note($note4x2)->id);
        $this->assertSame($context2->id, board::can_view_note($note2x1xt)->id);
        $this->assertSame($context3->id, board::can_view_note($note3x1xt)->id);
    }

    public function test_clear_history(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $generator->create_column(['boardid' => $board->id]);

        $this->assertCount(1, $DB->get_records('board_history', []));

        board::clear_history();
        $this->assertCount(1, $DB->get_records('board_history', []));

        $DB->execute("
            UPDATE {board_history}
               SET timecreated = timecreated - 36001
        ");
        board::clear_history();
        $this->assertCount(0, $DB->get_records('board_history', []));
    }

    public function test_board_hide_headers(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $result = board::board_hide_headers($board);
        $this->assertFalse($result);

        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id, 'hideheaders' => 1]);
        $result = board::board_hide_headers($board);
        $this->assertTrue($result);

        $this->setAdminUser();
        $result = board::board_hide_headers($board);
        $this->assertFalse($result);
    }

    public function test_board_has_notes(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->assertFalse(board::board_has_notes($board1->id));
        $this->assertFalse(board::board_has_notes($board2->id));

        $note = $generator->create_note(['columnid' => $column1->id, 'userid' => $user->id]);
        $this->assertTrue(board::board_has_notes($board1->id));
        $this->assertFalse(board::board_has_notes($board2->id));
    }

    public function test_repositionan_array_element(): void {
        $a = [1 => 'a', 2 => 'b', 3 => 'c'];

        board::repositionan_array_element($a, 3, 0);
        $this->assertSame([0 => 'c', 1 => 'a', 2 => 'b'], $a);

        board::repositionan_array_element($a, 1, 10);
        $this->assertSame([0 => 'c', 1 => 'b', 2 => 'a'], $a);

        try {
            board::repositionan_array_element($a, 10, 1);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\invalid_parameter_exception::class, $ex);
            $this->assertSame(
                'Invalid parameter value detected (The 10 cannot be found in the given array.)',
                $ex->getMessage()
            );
        }
    }

    public function test_board_rating_enabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $result = board::board_rating_enabled($board);
        $this->assertFalse($result);

        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id, 'addrating' => 3]);
        $result = board::board_rating_enabled($board);
        $this->assertTrue($result);
    }

    public function test_board_is_editor(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id, 'addrating' => 3]);

        $result = board::board_is_editor($board);
        $this->assertFalse($result);

        $this->setAdminUser();
        $result = board::board_is_editor($board);
        $this->assertTrue($result);
    }

    public function test_board_users_can_edit(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'userscanedit' => 0,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'userscanedit' => 1,
        ]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->setUser($teacher0);
        $this->assertFalse(board::board_users_can_edit($board0));
        $this->assertTrue(board::board_users_can_edit($board1));

        $this->setUser($student1);
        $this->assertFalse(board::board_users_can_edit($board0));
        $this->assertTrue(board::board_users_can_edit($board1));

        $this->setUser($student4);
        $this->assertFalse(board::board_users_can_edit($board0));
        $this->assertFalse(board::board_users_can_edit($board1));
    }

    public function test_board_readonly(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id, 'addrating' => 3]);

        $result = board::board_readonly($board, 0);
        $this->assertFalse($result);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);
        $result = board::board_readonly($board, 0);
        $this->assertFalse($result);
    }

    public function test_get_column_colours(): void {
        $this->resetAfterTest();

        $result = board::get_column_colours();
        foreach ($result as $color) {
            $this->assertMatchesRegularExpression('/^[A-F0-9]+$/', $color);
        }
    }

    public function test_get_default_colours(): void {
        $this->resetAfterTest();

        $result = board::get_default_colours();
        foreach ($result as $color) {
            $this->assertMatchesRegularExpression('/^[A-F0-9]+$/', $color);
        }
    }

    public function test_get_users_for_board(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => VISIBLEGROUPS,
            'availability' => '{"op":"&","c":[{"type":"group"}],"showc":[true]}',
        ]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student', status:ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');
        $this->getDataGenerator()->enrol_user($student5->id, $course->id, 'student');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($teacher0);

        $result = board::get_users_for_board($board, 0);
        $this->assertCount(4, $result);
        $this->assertArrayHasKey($teacher0->id, $result);
        $this->assertArrayHasKey($student1->id, $result);
        $this->assertArrayHasKey($student2->id, $result);
        $this->assertArrayHasKey($student5->id, $result);

        $result = board::get_users_for_board($board, $group1->id);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey($student1->id, $result);

        $this->setUser($student1);

        $result = board::get_users_for_board($board, 0);
        $this->assertCount(4, $result);
        $this->assertArrayHasKey($teacher0->id, $result);
        $this->assertArrayHasKey($student1->id, $result);
        $this->assertArrayHasKey($student2->id, $result);
        $this->assertArrayHasKey($student5->id, $result);

        $result = board::get_users_for_board($board, $group1->id);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey($student1->id, $result);

        $this->setUser($teacher0);

        $result = board::get_users_for_board($board2, 0);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey($teacher0->id, $result);
        $this->assertArrayHasKey($student1->id, $result);
        $this->assertArrayHasKey($student2->id, $result);

        $this->setUser($student1);

        $result = board::get_users_for_board($board2, 0);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey($teacher0->id, $result);
        $this->assertArrayHasKey($student1->id, $result);
        $this->assertArrayHasKey($student2->id, $result);
    }

    public function test_get_existing_owners_for_board(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
        ]);
        $columns0 = array_values($DB->get_records('board_columns', ['boardid' => $board0->id], 'id ASC'));
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
        ]);
        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => VISIBLEGROUPS,
            'availability' => '{"op":"&","c":[{"type":"group"}],"showc":[true]}',
        ]);
        $columns2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group3 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();
        $student6 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student', status:ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');
        $this->getDataGenerator()->enrol_user($student5->id, $course->id, 'student');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($teacher0);

        $result = board::get_existing_owners_for_board($board1, 0, false);
        $this->assertCount(0, $result);
        $result = board::get_existing_owners_for_board($board2, 0, false);
        $this->assertCount(0, $result);

        $note1x1 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $teacher0->id]);
        $note1x2 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note1x3 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student2->id]);
        $note1x4 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student6->id]);
        $note2x1 = $generator->create_note(['columnid' => $columns2[0]->id, 'userid' => $student1->id]);
        $note2x2 = $generator->create_note(['columnid' => $columns2[0]->id, 'userid' => $student6->id]);

        \mod_board\local\comment::create($note1x1->id, 'c1');
        \mod_board\local\comment::create($note1x2->id, 'c2');

        $result = board::get_existing_owners_for_board($board1, 0, false);
        $this->assertCount(4, $result);
        $this->assertSame(fullname($teacher0), $result[$teacher0->id]);
        $this->assertSame(fullname($student1), $result[$student1->id]);
        $this->assertSame(fullname($student2), $result[$student2->id]);
        $this->assertSame(fullname($student6), $result[$student6->id]);

        $result = board::get_existing_owners_for_board($board1, 0, true);
        $this->assertCount(2, $result);
        $this->assertSame(fullname($teacher0), $result[$teacher0->id]);
        $this->assertSame(fullname($student1), $result[$student1->id]);

        $result = board::get_existing_owners_for_board($board1, $group1->id, false);
        $this->assertCount(1, $result);
        $this->assertSame(fullname($student1), $result[$student1->id]);

        $result = board::get_existing_owners_for_board($board1, $group1->id, true);
        $this->assertCount(1, $result);
        $this->assertSame(fullname($student1), $result[$student1->id]);

        $result = board::get_existing_owners_for_board($board2, 0, false);
        $this->assertCount(2, $result);
        $this->assertSame(fullname($student1), $result[$student1->id]);
        $this->assertSame(fullname($student6), $result[$student6->id]);

        try {
            board::get_existing_owners_for_board($board0, 0, false);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\coding_exception::class, $ex);
            $this->assertSame(
                // phpcs:ignore moodle.Files.LineLength.TooLong
                'Coding error detected, it must be fixed by a programmer: get_existing_owners_for_board can be used only in singleusemode',
                $ex->getMessage()
            );
        }
    }

    public function test_can_view_owner(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
        ]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student', status:ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($student1);

        $this->assertFalse(board::can_view_owner($board0, $student1->id));
        $this->assertFalse(board::can_view_owner($board0, $student2->id));
        $this->assertFalse(board::can_view_owner($board0, $student3->id));
        $this->assertFalse(board::can_view_owner($board0, $student4->id));
        $this->assertFalse(board::can_view_owner($board0, $teacher0->id));

        $this->assertTrue(board::can_view_owner($board1, $student1->id));
        $this->assertFalse(board::can_view_owner($board1, $student2->id));
        $this->assertFalse(board::can_view_owner($board1, $student3->id));
        $this->assertFalse(board::can_view_owner($board1, $student4->id));
        $this->assertFalse(board::can_view_owner($board1, $teacher0->id));

        $this->assertTrue(board::can_view_owner($board2, $student1->id));
        $this->assertTrue(board::can_view_owner($board2, $student2->id));
        $this->assertFalse(board::can_view_owner($board2, $student3->id));
        $this->assertFalse(board::can_view_owner($board2, $student4->id));
        $this->assertTrue(board::can_view_owner($board2, $teacher0->id));

        $this->setUser($teacher0);

        $this->assertTrue(board::can_view_owner($board0, $student1->id));
        $this->assertTrue(board::can_view_owner($board0, $student2->id));
        $this->assertTrue(board::can_view_owner($board0, $student3->id));
        $this->assertTrue(board::can_view_owner($board0, $student4->id));
        $this->assertTrue(board::can_view_owner($board0, $teacher0->id));

        $this->assertTrue(board::can_view_owner($board1, $student1->id));
        $this->assertTrue(board::can_view_owner($board1, $student2->id));
        $this->assertTrue(board::can_view_owner($board1, $student3->id));
        $this->assertTrue(board::can_view_owner($board1, $student4->id));
        $this->assertTrue(board::can_view_owner($board1, $teacher0->id));

        $this->assertTrue(board::can_view_owner($board2, $student1->id));
        $this->assertTrue(board::can_view_owner($board2, $student2->id));
        $this->assertTrue(board::can_view_owner($board2, $student3->id));
        $this->assertTrue(board::can_view_owner($board2, $student4->id));
        $this->assertTrue(board::can_view_owner($board2, $teacher0->id));
    }

    public function test_can_post(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 0',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
        ]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student', status:ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($student1);

        $this->assertTrue(board::can_post($board0, 0));
        $this->assertTrue(board::can_post($board0, $student1->id));
        $this->assertTrue(board::can_post($board1, $student1->id));
        $this->assertTrue(board::can_post($board2, $student1->id));
        $this->assertFalse(board::can_post($board1, $student2->id));
        $this->assertFalse(board::can_post($board2, $student2->id));

        $this->setUser($student4);

        $this->assertFalse(board::can_post($board0, 0));
        $this->assertFalse(board::can_post($board1, $student1->id));
        $this->assertFalse(board::can_post($board2, $student1->id));

        $this->setUser($teacher0);

        $this->assertTrue(board::can_post($board0, 0));
        $this->assertTrue(board::can_post($board0, $teacher0->id));
        $this->assertTrue(board::can_post($board1, $student1->id));
        $this->assertTrue(board::can_post($board2, $student1->id));
        $this->assertTrue(board::can_post($board1, $student2->id));
        $this->assertTrue(board::can_post($board2, $student2->id));

        $this->assertDebuggingNotCalled();
        $this->assertFalse(board::can_post($board0, $student1->id));
        $this->assertDebuggingCalled('ownerid should not be used when single user mode disabled');
    }

    public function test_get_accepted_background_file_extensions(): void {
        $this->resetAfterTest();

        $result = board::get_accepted_background_file_extensions();
        $this->assertSame(['jpg', 'jpeg', 'png', 'gif'], $result);

        set_config('acceptedfiletypeforbackground', 'svg,jpg', 'mod_board');
        $result = board::get_accepted_background_file_extensions();
        $this->assertSame(['svg', 'jpg'], $result);
    }

    public function test_get_background_picker_options(): void {
        $result = board::get_background_picker_options();
        $expected = [
            'accepted_types' => ['.jpg', '.jpeg', '.png', '.gif'],
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => 0,
        ];
        $this->assertSame($expected, $result);
    }
}
