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

namespace mod_board\phpunit\local;

use stdClass;
use mod_board\local\note;
use mod_board\board;

/**
 * Test note helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\local\note
 */
final class note_test extends \advanced_testcase {
    public function test_create(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);
        $this->setCurrentTimeStart();

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
        $this->assertNotEmpty($note1->historyid);
        $this->assertSame($column1->id, $note1->columnid);
        $this->assertSame($user1->id, $note1->ownerid);
        $this->assertSame($user1->id, $note1->userid);
        $this->assertSame(null, $note1->groupid);
        $this->assertSame('NC 1', $note1->content);
        $this->assertSame('NH 1', $note1->heading);
        $this->assertSame('0', $note1->type);
        $this->assertSame(null, $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertTimeCurrent($note1->timecreated);
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('0', $note1->deleted);

        $note2 = note::create($column1->id, $user1->id, $group->id, 'NH 2', '', ['type' => 0, 'info' => '', 'url' => '']);
        $this->assertNotEmpty($note2->historyid);
        $this->assertSame($column1->id, $note2->columnid);
        $this->assertSame($user1->id, $note2->ownerid);
        $this->assertSame($user1->id, $note2->userid);
        $this->assertSame($group->id, $note2->groupid);
        $this->assertSame('', $note2->content);
        $this->assertSame('NH 2', $note2->heading);
        $this->assertSame('0', $note2->type);
        $this->assertSame(null, $note2->info);
        $this->assertSame(null, $note2->url);
        $this->assertTimeCurrent($note2->timecreated);
        $this->assertSame('1', $note2->sortorder);
        $this->assertSame('0', $note2->deleted);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));

        $note3 = note::create(
            $column1->id,
            $user1->id,
            null,
            '',
            'NC 3',
            ['type' => board::MEDIATYPE_NONE, 'info' => '', 'url' => ''],
            $user2->id
        );
        $this->assertNotEmpty($note3->historyid);
        $this->assertSame($column1->id, $note3->columnid);
        $this->assertSame($user1->id, $note3->ownerid);
        $this->assertSame($user2->id, $note3->userid);
        $this->assertSame(null, $note3->groupid);
        $this->assertSame('NC 3', $note3->content);
        $this->assertSame(null, $note3->heading);
        $this->assertSame('0', $note3->type);
        $this->assertSame(null, $note3->info);
        $this->assertSame(null, $note3->url);
        $this->assertTimeCurrent($note3->timecreated);
        $this->assertSame('0', $note3->sortorder);
        $this->assertSame('0', $note3->deleted);

        $note4 = note::create(
            $column1->id,
            $user1->id,
            null,
            '',
            'NC 4',
            ['type' => board::MEDIATYPE_URL, 'info' => 'Some info', 'url' => 'https::/www.example.com/'],
            $user2->id
        );
        $this->assertNotEmpty($note4->historyid);
        $this->assertSame($column1->id, $note4->columnid);
        $this->assertSame($user1->id, $note4->ownerid);
        $this->assertSame($user2->id, $note4->userid);
        $this->assertSame(null, $note4->groupid);
        $this->assertSame('NC 4', $note4->content);
        $this->assertSame(null, $note4->heading);
        $this->assertSame('3', $note4->type);
        $this->assertSame('Some info', $note4->info);
        $this->assertSame('https::/www.example.com/', $note4->url);
        $this->assertTimeCurrent($note4->timecreated);
        $this->assertSame('1', $note4->sortorder);
        $this->assertSame('0', $note4->deleted);

        $this->setUser($user1);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        try {
            note::create($column1->id, 0, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $ex);
            $this->assertSame('Invalid parameter value detected (ownerid is required)', $ex->getMessage());
        }

        try {
            note::create($column1->id, $user2->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $ex);
            $this->assertSame(
                'Invalid parameter value detected (ownerid must match userid if single user mode disabled)',
                $ex->getMessage()
            );
        }

        $this->setUser(null);

        try {
            note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $ex);
            $this->assertSame('Invalid parameter value detected (Invalid userid)', $ex->getMessage());
        }

        $this->setUser($user1);

        try {
            note::create($column1->id, $user1->id, -1, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $ex);
            $this->assertSame('Invalid parameter value detected (Invalid groupid)', $ex->getMessage());
        }

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));

        try {
            note::create($column1->id, $user1->id, $group->id, 'NH 2', '', ['type' => 0, 'info' => '', 'url' => '']);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $ex);
            $this->assertSame('Invalid parameter value detected (groupid is not allowed in single user mode)', $ex->getMessage());
        }
    }

    public function test_update(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);

        $note1 = note::update($note1->id, 'NH X', 'NC X', ['type' => 0, 'info' => '', 'url' => '']);
        $this->assertNotEmpty($note1->historyid);
        $this->assertSame($column1->id, $note1->columnid);
        $this->assertSame($user1->id, $note1->ownerid);
        $this->assertSame($user1->id, $note1->userid);
        $this->assertSame(null, $note1->groupid);
        $this->assertSame('NC X', $note1->content);
        $this->assertSame('NH X', $note1->heading);
        $this->assertSame('0', $note1->type);
        $this->assertSame(null, $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('0', $note1->deleted);

        $note1 = note::update($note1->id, 'NH Y', '', ['type' => 0, 'info' => '', 'url' => '']);
        $this->assertNotEmpty($note1->historyid);
        $this->assertSame($column1->id, $note1->columnid);
        $this->assertSame($user1->id, $note1->ownerid);
        $this->assertSame($user1->id, $note1->userid);
        $this->assertSame(null, $note1->groupid);
        $this->assertSame('', $note1->content);
        $this->assertSame('NH Y', $note1->heading);
        $this->assertSame('0', $note1->type);
        $this->assertSame(null, $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('0', $note1->deleted);

        $attachment = [
            'type' => board::MEDIATYPE_URL,
            'info' => 'test info',
            'url' => 'https://www.example.com/',
        ];

        $note1 = note::update($note1->id, '', 'NC Z', $attachment);
        $this->assertNotEmpty($note1->historyid);
        $this->assertSame($column1->id, $note1->columnid);
        $this->assertSame($user1->id, $note1->ownerid);
        $this->assertSame($user1->id, $note1->userid);
        $this->assertSame(null, $note1->groupid);
        $this->assertSame('NC Z', $note1->content);
        $this->assertSame(null, $note1->heading);
        $this->assertSame('3', $note1->type);
        $this->assertSame($attachment['info'], $note1->info);
        $this->assertSame($attachment['url'], $note1->url);
        $this->assertSame(null, $note1->filename);
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('0', $note1->deleted);
    }

    public function test_delete(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
        $note2 = note::create($column1->id, $user1->id, null, 'NH 2', 'NC 2', ['type' => 0, 'info' => '', 'url' => '']);
        $note3 = note::create($column1->id, $user1->id, null, 'NH 3', 'NC 3', ['type' => 0, 'info' => '', 'url' => '']);
        $note4 = note::create($column1->id, $user1->id, null, 'NH 4', 'NC 4', ['type' => 0, 'info' => '', 'url' => '']);

        $hisotryid = note::delete($note2->id);
        $this->assertNotEmpty($hisotryid);

        [$note1, $note2, $note3, $note4]
            = array_values($DB->get_records('board_notes', ['columnid' => $column1->id], 'id ASC'));
        $this->assertSame('0', $note1->deleted);
        $this->assertSame('1', $note2->deleted);
        $this->assertSame('0', $note3->deleted);
        $this->assertSame('0', $note4->deleted);
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('1', $note2->sortorder);
        $this->assertSame('1', $note3->sortorder);
        $this->assertSame('2', $note4->sortorder);
    }

    public function test_move(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
        $note2 = note::create($column1->id, $user1->id, null, 'NH 2', 'NC 2', ['type' => 0, 'info' => '', 'url' => '']);
        $note3 = note::create($column1->id, $user1->id, null, 'NH 3', 'NC 3', ['type' => 0, 'info' => '', 'url' => '']);
        $note4 = note::create($column1->id, $user1->id, null, 'NH 4', 'NC 4', ['type' => 0, 'info' => '', 'url' => '']);

        $historyid = note::move($note3->id, $column1->id, 1);
        $this->assertNotEmpty($historyid);
        [$note1, $note2, $note3, $note4]
            = array_values($DB->get_records('board_notes', ['columnid' => $column1->id], 'id ASC'));
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('1', $note3->sortorder);
        $this->assertSame('2', $note2->sortorder);
        $this->assertSame('3', $note4->sortorder);

        $historyid = note::move($note3->id, $column1->id, 10);
        $this->assertNotEmpty($historyid);
        [$note1, $note2, $note3, $note4]
            = array_values($DB->get_records('board_notes', ['columnid' => $column1->id], 'id ASC'));
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('1', $note2->sortorder);
        $this->assertSame('2', $note4->sortorder);
        $this->assertSame('10', $note3->sortorder);

        $historyid = note::move($note2->id, $column2->id, 0);
        $this->assertNotEmpty($historyid);
        [$note1, $note3, $note4]
            = array_values($DB->get_records('board_notes', ['columnid' => $column1->id], 'id ASC'));
        $this->assertSame('0', $note1->sortorder);
        $this->assertSame('1', $note4->sortorder);
        $this->assertSame('9', $note3->sortorder);
        [$note2]
            = array_values($DB->get_records('board_notes', ['columnid' => $column2->id], 'id ASC'));
        $this->assertSame('0', $note2->sortorder);
    }

    public function test_can_rate(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board3 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 3',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board4 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board5 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => VISIBLEGROUPS,
            'addrating' => board::RATINGBYALL,
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
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $columns0 = array_values($DB->get_records('board_columns', ['boardid' => $board0->id], 'id ASC'));
        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $columns2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));
        $columns3 = array_values($DB->get_records('board_columns', ['boardid' => $board3->id], 'id ASC'));
        $columns4 = array_values($DB->get_records('board_columns', ['boardid' => $board4->id], 'id ASC'));
        $columns5 = array_values($DB->get_records('board_columns', ['boardid' => $board5->id], 'id ASC'));

        $note0x1 = $generator->create_note(['columnid' => $columns0[0]->id, 'userid' => $student1->id]);
        $note1x1 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note2x1 = $generator->create_note(['columnid' => $columns2[0]->id, 'userid' => $student1->id]);
        $note3x1 = $generator->create_note(['columnid' => $columns3[0]->id, 'userid' => $student1->id]);
        $note4x1 = $generator->create_note(['columnid' => $columns4[0]->id, 'userid' => $student1->id, 'groupid' => $group1->id]);
        $note5x1 = $generator->create_note(['columnid' => $columns5[0]->id, 'userid' => $student1->id, 'groupid' => $group1->id]);

        $note0x0 = $generator->create_note(['columnid' => $columns0[0]->id, 'userid' => $teacher0->id]);
        $note1x0 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $teacher0->id]);
        $note2x0 = $generator->create_note(['columnid' => $columns2[0]->id, 'userid' => $teacher0->id, 'ownerid' => $student1->id]);
        $note3x0 = $generator->create_note(['columnid' => $columns3[0]->id, 'userid' => $teacher0->id, 'ownerid' => $student1->id]);
        $note4x0 = $generator->create_note(['columnid' => $columns4[0]->id, 'userid' => $teacher0->id, 'groupid' => 0]);
        $note4x0a = $generator->create_note(['columnid' => $columns4[0]->id, 'userid' => $teacher0->id, 'groupid' => $group1->id]);
        $note4x0b = $generator->create_note(['columnid' => $columns4[0]->id, 'userid' => $teacher0->id, 'groupid' => $group2->id]);
        $note5x0 = $generator->create_note(['columnid' => $columns5[0]->id, 'userid' => $teacher0->id, 'groupid' => 0]);
        $note5x0a = $generator->create_note(['columnid' => $columns5[0]->id, 'userid' => $teacher0->id, 'groupid' => $group1->id]);
        $note5x0b = $generator->create_note(['columnid' => $columns5[0]->id, 'userid' => $teacher0->id, 'groupid' => $group2->id]);

        $this->setUser($student1);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertTrue(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertTrue(note::can_rate($note4x0a->id));
        $this->assertFalse(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertTrue(note::can_rate($note5x0a->id));
        $this->assertFalse(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertTrue(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertTrue(note::can_rate($note4x1->id));
        $this->assertTrue(note::can_rate($note5x1->id));

        $this->setUser($student2);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertFalse(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertFalse(note::can_rate($note4x0a->id));
        $this->assertTrue(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertFalse(note::can_rate($note5x0a->id));
        $this->assertTrue(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertFalse(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertFalse(note::can_rate($note4x1->id));
        $this->assertFalse(note::can_rate($note5x1->id));

        $this->setUser($teacher0);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertTrue(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertTrue(note::can_rate($note4x0->id));
        $this->assertTrue(note::can_rate($note4x0a->id));
        $this->assertTrue(note::can_rate($note4x0b->id));
        $this->assertTrue(note::can_rate($note5x0->id));
        $this->assertTrue(note::can_rate($note5x0a->id));
        $this->assertTrue(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertTrue(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertTrue(note::can_rate($note4x1->id));
        $this->assertTrue(note::can_rate($note5x1->id));

        $DB->set_field('board', 'addrating', board::RATINGBYSTUDENTS);

        $this->setUser($student1);
        $this->assertTrue(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertTrue(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertTrue(note::can_rate($note4x0a->id));
        $this->assertFalse(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertTrue(note::can_rate($note5x0a->id));
        $this->assertFalse(note::can_rate($note5x0b->id));
        $this->assertTrue(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertTrue(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertTrue(note::can_rate($note4x1->id));
        $this->assertTrue(note::can_rate($note5x1->id));

        $this->setUser($student2);
        $this->assertTrue(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertFalse(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertFalse(note::can_rate($note4x0a->id));
        $this->assertTrue(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertFalse(note::can_rate($note5x0a->id));
        $this->assertTrue(note::can_rate($note5x0b->id));
        $this->assertTrue(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertFalse(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertFalse(note::can_rate($note4x1->id));
        $this->assertFalse(note::can_rate($note5x1->id));

        $this->setUser($teacher0);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertFalse(note::can_rate($note1x0->id));
        $this->assertFalse(note::can_rate($note2x0->id));
        $this->assertFalse(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertFalse(note::can_rate($note4x0a->id));
        $this->assertFalse(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertFalse(note::can_rate($note5x0a->id));
        $this->assertFalse(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertFalse(note::can_rate($note1x1->id));
        $this->assertFalse(note::can_rate($note2x1->id));
        $this->assertFalse(note::can_rate($note3x1->id));
        $this->assertFalse(note::can_rate($note4x1->id));
        $this->assertFalse(note::can_rate($note5x1->id));

        $DB->set_field('board', 'addrating', board::RATINGBYTEACHERS);

        $this->setUser($student1);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertFalse(note::can_rate($note1x0->id));
        $this->assertFalse(note::can_rate($note2x0->id));
        $this->assertFalse(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertFalse(note::can_rate($note4x0a->id));
        $this->assertFalse(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertFalse(note::can_rate($note5x0a->id));
        $this->assertFalse(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertFalse(note::can_rate($note1x1->id));
        $this->assertFalse(note::can_rate($note2x1->id));
        $this->assertFalse(note::can_rate($note3x1->id));
        $this->assertFalse(note::can_rate($note4x1->id));
        $this->assertFalse(note::can_rate($note5x1->id));

        $this->setUser($student2);
        $this->assertFalse(note::can_rate($note0x0->id));
        $this->assertFalse(note::can_rate($note1x0->id));
        $this->assertFalse(note::can_rate($note2x0->id));
        $this->assertFalse(note::can_rate($note3x0->id));
        $this->assertFalse(note::can_rate($note4x0->id));
        $this->assertFalse(note::can_rate($note4x0a->id));
        $this->assertFalse(note::can_rate($note4x0b->id));
        $this->assertFalse(note::can_rate($note5x0->id));
        $this->assertFalse(note::can_rate($note5x0a->id));
        $this->assertFalse(note::can_rate($note5x0b->id));
        $this->assertFalse(note::can_rate($note0x1->id));
        $this->assertFalse(note::can_rate($note1x1->id));
        $this->assertFalse(note::can_rate($note2x1->id));
        $this->assertFalse(note::can_rate($note3x1->id));
        $this->assertFalse(note::can_rate($note4x1->id));
        $this->assertFalse(note::can_rate($note5x1->id));

        $this->setUser($teacher0);
        $this->assertTrue(note::can_rate($note0x0->id));
        $this->assertTrue(note::can_rate($note1x0->id));
        $this->assertTrue(note::can_rate($note2x0->id));
        $this->assertTrue(note::can_rate($note3x0->id));
        $this->assertTrue(note::can_rate($note4x0->id));
        $this->assertTrue(note::can_rate($note4x0a->id));
        $this->assertTrue(note::can_rate($note4x0b->id));
        $this->assertTrue(note::can_rate($note5x0->id));
        $this->assertTrue(note::can_rate($note5x0a->id));
        $this->assertTrue(note::can_rate($note5x0b->id));
        $this->assertTrue(note::can_rate($note0x1->id));
        $this->assertTrue(note::can_rate($note1x1->id));
        $this->assertTrue(note::can_rate($note2x1->id));
        $this->assertTrue(note::can_rate($note3x1->id));
        $this->assertTrue(note::can_rate($note4x1->id));
        $this->assertTrue(note::can_rate($note5x1->id));
    }

    public function test_rate(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

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

        $this->setUser($user1);

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
        $note2 = note::create($column1->id, $user1->id, null, 'NH 2', 'NC 2', ['type' => 0, 'info' => '', 'url' => '']);

        $this->setUser($user1);
        $historyid = note::rate($note1->id);
        $this->assertNotEmpty($historyid);
        $this->assertSame(1, note::get_rating($note1->id));

        $this->setUser($user2);
        $historyid = note::rate($note1->id);
        $this->assertNotEmpty($historyid);
        $this->assertSame(2, note::get_rating($note1->id));
        $historyid = note::rate($note1->id);
        $this->assertNotEmpty($historyid);
        $this->assertSame(1, note::get_rating($note1->id));
    }

    public function test_get_rating(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

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

        $this->setUser($user1);

        $note1 = note::create($column1->id, $user1->id, null, 'NH 1', 'NC 1', ['type' => 0, 'info' => '', 'url' => '']);
        $note2 = note::create($column1->id, $user1->id, null, 'NH 2', 'NC 2', ['type' => 0, 'info' => '', 'url' => '']);

        $this->setUser($user1);
        $this->assertSame(0, note::get_rating($note1->id));
        note::rate($note1->id);
        $this->assertSame(1, note::get_rating($note1->id));

        $this->setUser($user2);
        note::rate($note1->id);
        $this->assertSame(2, note::get_rating($note1->id));
    }

    public function test_update_attachment(): void {
        global $DB;
        $this->resetAfterTest();

        $fs = get_file_storage();
        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        $context = board::context_for_board($board);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $this->setUser($user);

        $note1 = note::create(
            $column1->id,
            $user->id,
            null,
            'NH 1',
            'NC 1',
            ['type' => board::MEDIATYPE_NONE, 'info' => '', 'url' => '']
        );

        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_YOUTUBE, 'info' => 'Some Video', 'url' => 'https://youtube.com/watch?v=1234567890A'],
            $context
        );
        $this->assertSame('1', $note1->type);
        $this->assertSame('Some Video', $note1->info);
        $this->assertSame('https://youtube.com/watch?v=1234567890A', $note1->url);
        $this->assertSame(null, $note1->filename);

        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_URL, 'info' => 'Some URL', 'url' => 'https://example.com/1'],
            $context
        );
        $this->assertSame('3', $note1->type);
        $this->assertSame('Some URL', $note1->info);
        $this->assertSame('https://example.com/1', $note1->url);
        $this->assertSame(null, $note1->filename);

        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_URL, 'info' => '', 'url' => 'https://example.com/1'],
            $context
        );
        $this->assertSame('3', $note1->type);
        $this->assertSame('', $note1->info);
        $this->assertSame('https://example.com/1', $note1->url);
        $this->assertSame(null, $note1->filename);

        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_URL, 'info' => '', 'url' => 'https://example.com/1'],
            $context
        );
        $this->assertSame('3', $note1->type);
        $this->assertSame('', $note1->info);
        $this->assertSame('https://example.com/1', $note1->url);
        $this->assertSame(null, $note1->filename);

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6661,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6661],
            $context
        );
        $this->assertSame('2', $note1->type);
        $this->assertSame('Some image', $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('image.png', $note1->filename);
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6662,
            'filepath' => '/',
            'filename' => 'image.jpg',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image 2', 'draftitemid' => 6662],
            $context
        );
        $this->assertSame('2', $note1->type);
        $this->assertSame('Some image 2', $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('image.jpg', $note1->filename);
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.jpg'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));

        set_config('acceptedfiletypeforgeneral', 'txt', 'mod_board');
        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6663,
            'filepath' => '/',
            'filename' => 'text.txt',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_FILE, 'info' => 'Some text', 'draftitemid' => 6663],
            $context
        );
        $this->assertSame('4', $note1->type);
        $this->assertSame('Some text', $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('text.txt', $note1->filename);
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'files', $note1->id, '/', 'text.txt'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.jpg'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6664,
            'filepath' => '/',
            'filename' => 'text2.txt',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_FILE, 'info' => '', 'draftitemid' => 6664],
            $context
        );
        $this->assertSame('4', $note1->type);
        $this->assertSame('', $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame('text2.txt', $note1->filename);
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'files', $note1->id, '/', 'text2.txt'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'files', $note1->id, '/', 'text.txt'));

        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_NONE],
            $context
        );
        $this->assertSame('0', $note1->type);
        $this->assertSame(null, $note1->info);
        $this->assertSame(null, $note1->url);
        $this->assertSame(null, $note1->filename);
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'files', $note1->id, '/', 'text2.txt'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'files', $note1->id, '/', 'text.txt'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.jpg'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));
    }

    public function test_delete_files(): void {
        global $DB;
        $this->resetAfterTest();

        $fs = get_file_storage();
        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        $context = board::context_for_board($board);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $this->setUser($user);

        $note1 = note::create(
            $column1->id,
            $user->id,
            null,
            'NH 1',
            'NC 1',
            ['type' => board::MEDIATYPE_NONE, 'info' => '', 'url' => '']
        );
        $note2 = note::create(
            $column1->id,
            $user->id,
            null,
            'NH 2',
            'NC 2',
            ['type' => board::MEDIATYPE_NONE, 'info' => '', 'url' => '']
        );
        $note3 = note::create(
            $column1->id,
            $user->id,
            null,
            'NH 2',
            'NC 2',
            ['type' => board::MEDIATYPE_NONE, 'info' => '', 'url' => '']
        );

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6661,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6661],
            $context
        );

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6662,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note2 = note::update_attachment(
            $note2->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6662],
            $context
        );

        set_config('acceptedfiletypeforgeneral', 'txt', 'mod_board');
        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6663,
            'filepath' => '/',
            'filename' => 'text.txt',
        ], 'xx');
        $note3 = note::update_attachment(
            $note3->id,
            ['type' => board::MEDIATYPE_FILE, 'info' => 'Some text', 'draftitemid' => 6663],
            $context
        );

        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note2->id, '/', 'image.png'));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'files', $note3->id, '/', 'text.txt'));

        note::delete_files($note1, $context);
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note2->id, '/', 'image.png'));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'files', $note3->id, '/', 'text.txt'));

        note::delete_files($note3, $context);
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note2->id, '/', 'image.png'));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'files', $note3->id, '/', 'text.txt'));
    }

    public function test_get_accepted_image_file_extensions(): void {
        $this->resetAfterTest();

        $result = note::get_accepted_image_file_extensions();
        $this->assertSame(['jpg', 'jpeg', 'png', 'gif'], $result);

        set_config('acceptedfiletypeforcontent', 'jpg', 'mod_board');
        $result = note::get_accepted_image_file_extensions();
        $this->assertSame(['jpg'], $result);
    }

    public function test_get_image_picker_options(): void {
        $result = note::get_image_picker_options();
        $expected = [
            'accepted_types' => ['.jpg', '.jpeg', '.png', '.gif'],
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => board::ACCEPTED_FILE_MAX_SIZE,
        ];
        $this->assertSame($expected, $result);
    }

    public function test_get_accepted_general_file_extensions(): void {
        $this->resetAfterTest();

        $result = note::get_accepted_general_file_extensions();
        $this->assertSame([], $result);

        set_config('acceptedfiletypeforgeneral', 'jpg, jpeg,,gif', 'mod_board');
        $result = note::get_accepted_general_file_extensions();
        $this->assertSame(['jpg', 'jpeg', 'gif'], $result);
    }

    public function test_get_general_picker_options(): void {
        $this->resetAfterTest();

        $result = note::get_general_picker_options();
        $this->assertSame([], $result);

        set_config('acceptedfiletypeforgeneral', 'jpg, jpeg,,gif', 'mod_board');
        $result = note::get_general_picker_options();
        $expected = [
            'accepted_types' => ['.jpg', '.jpeg', '.gif'],
            'maxfiles' => 1,
            'subdirs' => 0,
            'maxbytes' => board::ACCEPTED_FILE_MAX_SIZE,
        ];
        $this->assertSame($expected, $result);
    }

    public function test_is_draft_file_present(): void {
        $this->resetAfterTest();

        $fs = get_file_storage();

        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);

        $draftfile1 = $fs->create_file_from_string([
            'contextid' => $usercontext1->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6661,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $draftfile2 = $fs->create_file_from_string([
            'contextid' => $usercontext2->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6662,
            'filepath' => '/',
            'filename' => 'image.jpg',
        ], 'xxx');

        $this->setUser($user1);

        $this->assertTrue(note::is_draft_file_present(6661));
        $this->assertFalse(note::is_draft_file_present(6662));
        $this->assertFalse(note::is_draft_file_present(6663));

        $draftfile1->delete();
        $this->assertFalse(note::is_draft_file_present(6661));
        $this->assertFalse(note::is_draft_file_present(6662));
        $this->assertFalse(note::is_draft_file_present(6663));

        $this->setUser($user2);
        $this->assertTrue(note::is_draft_file_present(6662));
    }

    public function test_is_youtube_url(): void {
        $this->assertTrue(note::is_youtube_url('https://youtube.com/watch?v=1234567890A'));
        $this->assertTrue(note::is_youtube_url('http://youtube.com/watch?v=1234567890A'));
        $this->assertTrue(note::is_youtube_url('http://whatever.com/watch?v=1234567890A'));
        $this->assertFalse(note::is_youtube_url('https://youtube.com/watch?v=1234567890'));
        $this->assertFalse(note::is_youtube_url('https://youtube.com/watch?x=1234567890A'));
    }

    public function test_format_plain_text(): void {
        $this->assertSame(null, note::format_plain_text(null));
        $this->assertSame('', note::format_plain_text(''));
        $this->assertSame('', note::format_plain_text('   '));
        $this->assertSame('abc', note::format_plain_text('abc'));
        $this->assertSame("&lt; abc &gt; &apos; def ghi &amp; \r\n", note::format_plain_text("< abc > ' def ghi & \r\n"));
        $this->assertSame('&lt; abc &gt; &apos; def ghi &amp;', note::format_plain_text('&lt; abc &gt; &apos; def ghi &amp;'));
    }

    public function test_format_limited_markdown(): void {
        $content = 'Hello';
        $expected = '<p>Hello</p>
';
        $this->assertSame($expected, note::format_limited_markdown($content));

        // phpcs:disable moodle.WhiteSpace.WhiteSpaceInStrings.EndLine
        $content = '# heading 1  

# Heading 2

#not a heading
#still not a heading 

';
        // phpcs:enabled moodle.WhiteSpace.WhiteSpaceInStrings.EndLine
        $expected = '<h4 class="h5">heading 1  </h4>
<h4 class="h5">Heading 2</h4>
<p>#not a heading #still not a heading </p>
';
        $this->assertSame($expected, note::format_limited_markdown($content));

        $content = '
paragraph
- list
- another list
1. numbered
4. numbered
';
        $expected = '<p>paragraph</p>
<ul><li>list</li>
<li>another list</li></ul>
<ol><li>numbered</li>
<li>numbered</li></ol>
';
        $this->assertSame($expected, note::format_limited_markdown($content));

        $content = '# Heading
long *1*

Some *very* **nice** ***paragraph***
on two lines.

- *list*
- another
**list**

1. ***numbered***
4. numbered
list
';
        $expected = '<h4 class="h5">Heading long <em>1</em></h4>
<p>Some <em>very</em> <strong>nice</strong> <em><strong>paragraph</strong></em> on two lines.</p>
<ul><li><em>list</em></li>
<li>another <strong>list</strong></li></ul>
<ol><li><em><strong>numbered</strong></em></li>
<li>numbered list</li></ol>
';
        $this->assertSame($expected, note::format_limited_markdown($content));
    }

    public function test_format_for_display(): void {
        global $DB, $CFG;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');
        $fs = get_file_storage();

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context = board::context_for_board($board);
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        [$column1, $column2, $column3] =
            array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $this->setUser($user);

        $note = $generator->create_note(
            ['columnid' => $column1->id, 'userid' => $user->id, 'content' => "# My 'heading'\n\nAnd some text"]
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('My &apos;heading&apos;', $result->identifier);
        $this->assertSame("<h4 class=\"h5\">My 'heading'</h4>\n<p>And some text</p>\n", $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('0', $result->type);
        $this->assertSame(null, $result->info);
        $this->assertSame(null, $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $note = $generator->create_note(
            ['columnid' => $column1->id, 'userid' => $user->id, 'heading' => 'My &quot;heading&quot; only']
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('My &quot;heading&quot; only', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame('My &quot;heading&quot; only', $result->heading);
        $this->assertSame('0', $result->type);
        $this->assertSame(null, $result->info);
        $this->assertSame(null, $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $note = note::update($note->id, '', '', ['type' => board::MEDIATYPE_YOUTUBE,
            'info' => 'Some Video', 'url' => 'https://youtube.com/watch?v=1234567890A']);
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('Some Video', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('1', $result->type);
        $this->assertSame('Some Video', $result->info);
        $this->assertSame('https://youtube.com/watch?v=1234567890A', $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_YOUTUBE, 'info' => '', 'url' => 'https://youtube.com/watch?v=1234567890A'],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('https://youtube.com/watch?v=1234567890A', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('1', $result->type);
        $this->assertSame('https://youtube.com/watch?v=1234567890A', $result->info);
        $this->assertSame('https://youtube.com/watch?v=1234567890A', $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_URL, 'info' => 'Some URL', 'url' => 'https://example.com/1'],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('Some URL', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('3', $result->type);
        $this->assertSame('Some URL', $result->info);
        $this->assertSame('https://example.com/1', $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_URL, 'info' => '', 'url' => 'https://example.com/1'],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('https://example.com/1', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('3', $result->type);
        $this->assertSame('https://example.com/1', $result->info);
        $this->assertSame('https://example.com/1', $result->url);
        $this->assertSame(null, $result->filename);
        $this->assertSame(null, $result->rating);

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6661,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6661],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('Some image', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('2', $result->type);
        $this->assertSame('Some image', $result->info);
        $this->assertSame("$CFG->wwwroot/pluginfile.php/$context->id/mod_board/images/$note->id/image.png", $result->url);
        $this->assertSame('image.png', $result->filename);
        $this->assertSame(null, $result->rating);

        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6662,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => '', 'draftitemid' => 6662],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('image.png', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('2', $result->type);
        $this->assertSame('image.png', $result->info);
        $this->assertSame("$CFG->wwwroot/pluginfile.php/$context->id/mod_board/images/$note->id/image.png", $result->url);
        $this->assertSame('image.png', $result->filename);
        $this->assertSame(null, $result->rating);

        set_config('acceptedfiletypeforgeneral', 'txt', 'mod_board');
        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6663,
            'filepath' => '/',
            'filename' => 'text.txt',
        ], 'xx');
        $note = note::update_attachment(
            $note->id,
            ['type' => board::MEDIATYPE_FILE, 'draftitemid' => 6663],
            $context
        );
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame('text.txt', $result->identifier);
        $this->assertSame('', $result->content);
        $this->assertSame(null, $result->heading);
        $this->assertSame('4', $result->type);
        $this->assertSame('text.txt', $result->info);
        $this->assertSame("$CFG->wwwroot/pluginfile.php/$context->id/mod_board/files/$note->id/text.txt", $result->url);
        $this->assertSame('text.txt', $result->filename);
        $this->assertSame(null, $result->rating);

        $DB->set_field('board', 'addrating', board::RATINGBYALL, ['id' => $board->id]);
        $board = board::get_board($board->id);
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame(0, $result->rating);

        note::rate($note->id);
        $result = note::format_for_display($note, $column1, $board, $context);
        $this->assertSame(1, $result->rating);
    }

    public function test_get_export_info(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $user = $this->getDataGenerator()->create_user();

        $context = board::context_for_board($board);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $note = $generator->create_note(
            ['columnid' => $columns[0]->id, 'userid' => $user->id, 'content' => 'abc <div>xx</div><br>xyz']
        );

        $formatted = note::format_for_display($note, $columns[0], $board, $context);
        $expected = '<p>abc &lt;div&gt;xx&lt;/div&gt;&lt;br&gt;xyz</p>
';
        $this->assertSame($expected, note::get_export_info($formatted));

        $note = $generator->create_note(['columnid' => $columns[0]->id, 'userid' => $user->id,
            'heading' => 'Some header', 'content' => 'Some content',
            'type' => board::MEDIATYPE_URL, 'info' => 'Some URL', 'url' => 'https://www.example.com/']);

        $formatted = note::format_for_display($note, $columns[0], $board, $context);
        $expected = 'Some header<p>Some content</p>
Some URL (https://www.example.com/)';
        $this->assertSame($expected, note::get_export_info($formatted));
    }
}
