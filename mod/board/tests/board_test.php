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

namespace mod_board;

use mod_board\board;
use cm_info;
use mod_board\completion\custom_completion;

/**
 * Class board_test.
 *
 * @package    mod_board
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group mod_boards
 */
class board_test extends \advanced_testcase {

    public function test_coursemodule_for_board() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));

        $result = board::coursemodule_for_board($board);
        $this->assertEquals($result->instance, $board->id);
    }

    public function test_get_board() {
        $this->resetAfterTest();
        $board = self::add_board(2);
        $output = board::get_board($board->id);

        $this->assertEquals($board->id, $output->id);
    }

    public function test_get_column() {
        $this->resetAfterTest();
        $board = self::add_board(2);
        $column = self::add_column($board->id);
        $output = board::get_column($column->id);

        $this->assertEquals($column->id, $output->id);
    }

    public function test_get_note() {
        $this->resetAfterTest();
        $board = self::add_board(2);
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $output = board::get_note($note->id);

        $this->assertEquals($note->id, $output->id);
    }

    public function test_get_note_rating() {
        $this->resetAfterTest();
        $board = self::add_board(2);
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        self::add_note_rating($note->id, 2);
        $output = board::get_note_rating($note->id);

        $this->assertEquals(1, $output);

        self::add_note_rating($note->id, 3);
        $output = board::get_note_rating($note->id);

        $this->assertEquals(2, $output);
    }

    public function test_context_for_board() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $output = board::context_for_board($board->id);

        $this->assertEquals($board->cmid, $output->instanceid);
    }

    public function test_context_for_column() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $output = board::context_for_column($column->id);

        $this->assertEquals($board->cmid, $output->instanceid);
    }

    public function test_clear_history() {
        global $DB;
        $this->resetAfterTest();
        $board = self::add_board(1);
        $record = array(
            'id' => 1,
            'boardid' => $board->id,
            'groupid' => 1,
            'action' => 'action',
            'userid' => 1,
            'content' => 'content',
            'timecreated' => 0
        );
        $DB->insert_record('board_history', $record);

        $record = $DB->get_record('board_history', array('boardid' => $board->id));
        $this->assertEquals($record->content, 'content');

        board::clear_history();
        $record = $DB->get_record('board_history', array('boardid' => $board->id));
        $this->assertFalse($record);
    }

    public function test_board_hide_headers() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));

        $result = board::board_hide_headers($board->id);
        $this->assertFalse($result);

        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'hideheaders' => 1));
        $result = board::board_hide_headers($board->id);
        $this->assertTrue($result);

        $this->setAdminUser();
        $result = board::board_hide_headers($board->id);
        $this->assertFalse($result);
    }

    public function test_board_get() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);

        $result = board::board_get($board->id);
        $this->assertEquals($result[$column->id]->name, 'New Heading');
    }

    public function test_board_history() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $record = array(
            'id' => 1,
            'boardid' => $board->id,
            'groupid' => 1,
            'action' => 'action',
            'userid' => 1,
            'content' => 'content',
            'timecreated' => 101010101010
        );

        $DB->insert_record('board_history', $record);
        $record = $DB->get_record('board_history', array('action' => 'action'));

        $result = board::board_history($board->id, 0, 1);
        $this->assertEquals($result[$record->id]->boardid, $board->id);
    }

    public function test_board_add_column() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $result = board::board_add_column($board->id, 'Test column');

        $column = $DB->get_record('board_columns', array('name' => 'Test column'));

        $this->assertIsArray($result);
        $this->assertEquals($result['id'], $column->id);
    }

    public function test_board_update_column() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $result = board::board_update_column($column->id, 'Test column');

        $this->assertIsArray($result);
        $this->assertTrue($result['status']);
    }

    public function test_board_delete_column() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $result = board::board_delete_column($column->id);

        $this->assertIsArray($result);
        $this->assertTrue($result['status']);
    }

    public function test_get_note_file() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);

        $result = board::get_note_file($note->id);
        $this->assertNull($result);

        $note = self::add_note_file($column->id, 'www.google.com');
        $result = board::get_note_file($note->id);
        $this->assertFalse($result);

        $attachment = array(
            'type' => 2,
            'info' => '',
            'url' => '',
            'filename' => 'testimage.png',
            'filecontents' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABpAAAAQaCAIAhEUgAABpAAAAQaCAIAAADL9awBAAAACXBIWXMAA'
        );

        $note = board::board_add_note($column->id, 2, 'heading', 'content', $attachment);
        $result = board::get_note_file($note['note']->id);
        $this->assertEmpty($result);
    }

    public function test_board_note_update_attachment() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);

        $attachment = array(
            'type' => 2,
            'info' => 'test info',
            'url' => 'test url',
            'filename' => 'testimage.png',
            'filecontents' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABpAAAAQaCAIAhEUgAABpAAAAQaCAIAAADL9awBAAAACXBIWXMAASAS'
        );

        $result = board::board_note_update_attachment($note->id, $attachment);
        $this->assertEquals($result['info'], $attachment['info']);
        $this->assertEquals($result['url'], $attachment['url']);
    }

    public function test_board_add_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $attachment = [
            'type' => 0,
            'info' => '',
            'url' => '',
        ];
        $result = board::board_add_note($column->id, 2, 'Test heading', 'Test content', $attachment);

        $this->assertIsArray($result);
    }

    public function test_board_update_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $attachment = [
            'type' => 0,
            'info' => '',
            'url' => '',
        ];
        $result = board::board_update_note($note->id, 2, 'update heading', 'update content', $attachment);

        $this->assertIsArray($result);
    }

    public function test_board_delete_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $result = board::board_delete_note($note->id);

        $this->assertIsArray($result);
        $this->assertTrue($result['status']);
    }

    public function test_board_move_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $column2 = self::add_column($board->id, 'New column');
        $result = board::board_move_note($note->id, 0, $column2->id, 0);

        $this->assertIsArray($result);
        $this->assertTrue($result['status']);
    }

    public function test_board_can_rate_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'addrating' => 3));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $result = board::board_can_rate_note($note->id);

        $this->assertTrue($result['canrate']);
    }

    public function test_board_rating_enabled() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id));

        $result = board::board_rating_enabled($board->id);
        $this->assertFalse($result);

        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'addrating' => 3));
        $result = board::board_rating_enabled($board->id);
        $this->assertTrue($result);
    }

     // Undefined variable 'rate', it never gets defined if a valid note is passed in.
    public function test_board_rate_note() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'addrating' => 3));
        $column = self::add_column($board->id);
        $note = self::add_note($column->id);
        $result = board::board_rate_note($note->id);

        $this->assertIsArray($result);
        $this->assertTrue($result['status']);
    }

    public function test_board_is_editor() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'addrating' => 3));

        $result = board::board_is_editor($board->id);
        $this->assertFalse($result);

        $this->setAdminUser();
        $result = board::board_is_editor($board->id);
        $this->assertTrue($result);
    }

    public function test_board_readonly() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', array('course' => $course->id, 'addrating' => 3));

        $result = board::board_readonly($board->id);
        $this->assertFalse($result);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user->id, 'groupid' => $group->id));
        $result = board::board_readonly($board->id);
        $this->assertFalse($result);
    }

    /**
     * Test updating activity completion when submitting 2 notes.
     */
    public function test_board_completion() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id, 'completionnotes' => 2, 'completion' => COMPLETION_TRACKING_AUTOMATIC]);
        $column = self::add_column($board->id);
        $attachment = [
            'type' => 0,
            'info' => '',
            'url' => '',
        ];

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);
        $result = board::board_add_note($column->id, $student->id, 'Test heading', 'Test content', $attachment);

        $cm = get_coursemodule_from_instance('board', $board->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);
        $customcompletion = new custom_completion($cm, (int)$student->id);

        $this->assertEquals(COMPLETION_INCOMPLETE, $customcompletion->get_state('completionnotes'));

        $result = board::board_add_note($column->id, $student->id, 'Test heading 2', 'Test content 2', $attachment);
        $this->assertEquals(COMPLETION_COMPLETE, $customcompletion->get_state('completionnotes'));
    }

    /**
     * Add board helper function.
     * @param int $courseid
     * @return false|mixed|\stdClass
     */
    private static function add_board(int $courseid) {
        global $DB;

        $record = [
            'id' => 1,
            'course' => $courseid,
            'name' => 'test',
            'timemodified' => 0,
            'intro' => '',
            'historyid' => 3,
            'background_color' => null,
            'addrating' => 3,
            'hideheaders' => 0,
            'sortby' => 2,
            'postby' => 0
        ];

        $DB->insert_record('board', $record);
        return $DB->get_record('board', array('name' => 'test'));
    }

    /**
     * Add column helper function.
     * @param int $boardid
     * @param string $name
     * @return false|mixed|\stdClass
     */
    private static function add_column(int $boardid, string $name = 'New Heading') {
        global $DB;

        $record = array(
            'id' => 1,
            'boardid' => $boardid,
            'name' => $name,
        );

        $DB->insert_record('board_columns', $record);
        return $DB->get_record('board_columns', array('name' => $name));
    }

    /**
     * Add note helper function.
     * @param int $columnid
     * @return false|mixed|\stdClass
     */
    private static function add_note(int $columnid) {
        global $DB;

        $record = array(
            'id' => 1,
            'columnid' => $columnid,
            'userid' => '2',
            'groupid' => null,
            'content' => 'Test content',
            'heading' => 'Test heading',
        );

        $DB->insert_record('board_notes', $record);
        return $DB->get_record('board_notes', array('columnid' => $columnid));
    }

    /**
     * Add note file helper function.
     * @param int $columnid
     * @param string $url
     * @return false|mixed|\stdClass
     */
    private static function add_note_file(int $columnid, string $url) {
        global $DB;

        $record = array(
            'id' => 1,
            'columnid' => $columnid,
            'userid' => '2',
            'groupid' => null,
            'content' => 'Test content',
            'heading' => 'Test heading',
            'url' => $url,
        );

        $DB->insert_record('board_notes', $record);
        return $DB->get_record('board_notes', array('url' => $url));
    }

    /**
     * Add note rating helper function.
     * @param int $noteid
     * @param int $userid
     * @return false|mixed|\stdClass
     */
    private static function add_note_rating(int $noteid, int $userid) {
        global $DB;

        $record = array(
            'id' => 1,
            'noteid' => $noteid,
            'userid' => $userid,
        );

        $DB->insert_record('board_note_ratings', $record);
        return $DB->get_record('board_note_ratings', array('userid' => $userid));
    }
}
