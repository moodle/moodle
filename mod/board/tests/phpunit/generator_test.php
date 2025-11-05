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
use mod_board\local\template;

/**
 * Board generator tests.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board_generator
 */
final class generator_test extends \advanced_testcase {
    public function test_plugin_generator(): void {
        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');
        $this->assertInstanceOf(\mod_board_generator::class, $generator);
        $this->assertTrue(method_exists($generator, 'create_instance'));
        $this->assertTrue(method_exists($generator, 'create_column'));
    }

    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);

        $this->setCurrentTimeStart();
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);
        $this->assertSame($course->id, $board->course);
        $this->assertSame('Board 1', $board->name);
        $this->assertSame('0', $board->hidename);
        $this->assertTimeCurrent($board->timemodified);
        $this->assertSame('Test board 1', $board->intro);
        $this->assertSame(FORMAT_MOODLE, $board->introformat);
        $this->assertSame('0', $board->historyid);
        $this->assertSame('', $board->background_color);
        $this->assertSame('0', $board->addrating);
        $this->assertSame('0', $board->hideheaders);
        $this->assertSame((string)board::SORTBYNONE, $board->sortby);
        $this->assertSame('0', $board->postby);
        $this->assertSame('0', $board->userscanedit);
        $this->assertSame((string)board::SINGLEUSER_DISABLED, $board->singleusermode);
        $this->assertSame('0', $board->enableblanktarget);
        $this->assertSame('0', $board->completionnotes);
        $this->assertSame('0', $board->embed);

        $this->setCurrentTimeStart();
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'name' => 'Board X',
            'hidename' => 1,
            'intro' => 'Some intro',
            'introformat' => FORMAT_HTML,
            'background_color' => '#fff',
            'addrating' => 1,
            'hideheaders' => 1,
            'sortby' => board::SORTBYDATE,
            'postby' => 12345,
            'userscanedit' => 1,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'enableblanktarget' => 1,
            'completionnotes' => 1,
        ]);
        $this->assertSame($course->id, $board->course);
        $this->assertSame('Board X', $board->name);
        $this->assertSame('1', $board->hidename);
        $this->assertTimeCurrent($board->timemodified);
        $this->assertSame('Some intro', $board->intro);
        $this->assertSame(FORMAT_HTML, $board->introformat);
        $this->assertSame('0', $board->historyid);
        $this->assertSame('#fff', $board->background_color);
        $this->assertSame('1', $board->addrating);
        $this->assertSame('1', $board->hideheaders);
        $this->assertSame((string)board::SORTBYDATE, $board->sortby);
        $this->assertSame('12345', $board->postby);
        $this->assertSame('1', $board->userscanedit);
        $this->assertSame((string)board::SINGLEUSER_PRIVATE, $board->singleusermode);
        $this->assertSame('1', $board->enableblanktarget);
        $this->assertSame('1', $board->completionnotes);
        $this->assertSame('0', $board->embed);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertSame('Heading', $columns[0]->name);
        $this->assertSame('Heading', $columns[1]->name);
        $this->assertSame('Heading', $columns[2]->name);
        $this->assertCount(3, $columns);

        $template = template::create((object)[
            'name' => 'Some template',
            'columns' => "Col 1\nCol 2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
        ]);
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'templateid' => $template->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
        ]);
        $this->assertSame((string)board::SINGLEUSER_PRIVATE, $board->singleusermode);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertSame('Col 1', $columns[0]->name);
        $this->assertSame('Col 2', $columns[1]->name);
        $this->assertCount(2, $columns);

        $template = template::create((object)[
            'name' => 'Some template',
            'columns' => '',
        ]);
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'templateid' => $template->id,
        ]);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertCount(0, $columns);
    }

    public function test_create_column(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);

        $column4 = $generator->create_column(['boardid' => $board->id]);
        $this->assertSame($board->id, $column4->boardid);
        $this->assertSame('Column 4', $column4->name);
        $this->assertSame('0', $column4->locked);
        $this->assertSame('4', $column4->sortorder);

        $column5 = $generator->create_column(['boardid' => $board->id, 'name' => 'Col X']);
        $this->assertSame($board->id, $column5->boardid);
        $this->assertSame('Col X', $column5->name);
        $this->assertSame('0', $column5->locked);
        $this->assertSame('5', $column5->sortorder);
    }

    public function test_create_note(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);

        $this->setCurrentTimeStart();
        $note = $generator->create_note(['columnid' => $column1->id, 'heading' => 'Head 1', 'content' => 'CCC']);
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user1->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('CCC', $note->content);
        $this->assertSame('Head 1', $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('0', $note->sortorder);
        $this->assertSame('0', $note->deleted);

        $this->setCurrentTimeStart();
        $note = $generator->create_note(
            ['columnid' => $column1->id, 'heading' => 'Head 2', 'groupid' => $group->id, 'userid' => $user2->id]
        );
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user2->id, $note->ownerid);
        $this->assertSame($user2->id, $note->userid);
        $this->assertSame($group->id, $note->groupid);
        $this->assertSame('', $note->content);
        $this->assertSame('Head 2', $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('1', $note->sortorder);
        $this->assertSame('0', $note->deleted);

        $this->setUser(null);

        $board2 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
        ]);
        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));

        $this->setCurrentTimeStart();
        $note = $generator->create_note(
            ['columnid' => $column1->id, 'content' => 'XXX', 'userid' => $user1->id, 'ownerid' => $user2->id]
        );
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user2->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('XXX', $note->content);
        $this->assertSame(null, $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('0', $note->sortorder);
        $this->assertSame('0', $note->deleted);

        $this->setCurrentTimeStart();
        $note = $generator->create_note(['columnid' => $column1->id, 'userid' => $user1->id]);
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user1->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('', $note->content);
        $this->assertSame('Some note', $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('1', $note->sortorder);
        $this->assertSame('0', $note->deleted);

        $this->setCurrentTimeStart();
        $note = $generator->create_note(['column' => 1, 'boardid' => $board2->id, 'userid' => $user1->id]);
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user1->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('', $note->content);
        $this->assertSame('Some note', $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('2', $note->sortorder);
        $this->assertSame('0', $note->deleted);

        $note = $generator->create_note(['column' => 1, 'boardid' => $board2->id, 'userid' => $user1->id, 'deleted' => 1]);
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user1->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('', $note->content);
        $this->assertSame('Some note', $note->heading);
        $this->assertSame('0', $note->type);
        $this->assertSame(null, $note->info);
        $this->assertSame(null, $note->url);
        $this->assertSame('3', $note->sortorder);
        $this->assertSame('1', $note->deleted);

        $this->setCurrentTimeStart();
        $note = $generator->create_note(['column' => 1, 'boardid' => $board2->id, 'userid' => $user1->id,
            'type' => board::MEDIATYPE_URL, 'info' => 'Some URL', 'url' => 'https://www.example.com/']);
        $this->assertSame($column1->id, $note->columnid);
        $this->assertSame($user1->id, $note->ownerid);
        $this->assertSame($user1->id, $note->userid);
        $this->assertSame(null, $note->groupid);
        $this->assertSame('', $note->content);
        $this->assertSame('Some note', $note->heading);
        $this->assertSame('3', $note->type);
        $this->assertSame('Some URL', $note->info);
        $this->assertSame('https://www.example.com/', $note->url);
        $this->assertTimeCurrent($note->timecreated);
        $this->assertSame('3', $note->sortorder);
        $this->assertSame('0', $note->deleted);
    }

    public function test_create_comment(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);
        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $this->setUser($user1);

        $note1 = $generator->create_note(['columnid' => $column1->id, 'heading' => 'Head 1', 'content' => 'CCC']);

        $this->setUser($user1);

        $comment1 = $generator->create_comment(['noteid' => $note1->id]);
        $this->assertSame($note1->id, $comment1->noteid);
        $this->assertSame('Comment 1', $comment1->content);
        $this->assertSame($user1->id, $comment1->userid);
        $this->assertSame('0', $comment1->deleted);

        $comment2 = $generator->create_comment(['noteid' => $note1->id, 'content' => 'Other comment', 'userid' => $user2->id]);
        $this->assertSame($note1->id, $comment2->noteid);
        $this->assertSame('Other comment', $comment2->content);
        $this->assertSame($user2->id, $comment2->userid);
        $this->assertSame('0', $comment2->deleted);

        $comment3 = $generator->create_comment(['noteid' => $note1->id, 'deleted' => 1]);
        $this->assertSame($note1->id, $comment3->noteid);
        $this->assertSame('Comment 3', $comment3->content);
        $this->assertSame($user1->id, $comment3->userid);
        $this->assertSame('1', $comment3->deleted);
    }

    public function test_create_template(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $syscontext = \context_system::instance();
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = \context_coursecat::instance($category->id);

        $this->setCurrentTimeStart();
        $template = $generator->create_template();
        $this->assertSame('Template 1', $template->name);
        $this->assertSame((string)$syscontext->id, $template->contextid);
        $this->assertSame('', $template->description);
        $this->assertSame('', $template->columns);
        $this->assertSame('[]', $template->jsonsettings);
        $this->assertTimeCurrent($template->timecreated);

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'contextid' => $categorycontext->id,
            'columns' => "Col 1\r\nCol2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);
        $this->assertSame('My template', $template->name);
        $this->assertSame((string)$categorycontext->id, $template->contextid);
        $this->assertSame('Fancy <em>template</em>', $template->description);
        $this->assertSame("Col 1\nCol2", $template->columns);
        $this->assertSame('{"sortby":"3","singleusermode":"1"}', $template->jsonsettings);
    }
}
