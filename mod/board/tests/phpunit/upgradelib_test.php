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

/**
 * Board upgrade function tests.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class upgradelib_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        require_once(__DIR__ . '/../../db/upgradelib.php');
    }

    /**
     * Test migration of image url to filename.
     *
     * @covers \mod_board_migrate_image_url_to_filename
     */
    public function test_mod_board_migrate_image_url_to_filename(): void {
        global $DB;

        $this->resetAfterTest();

        $fs = get_file_storage();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context = board::context_for_board($board);

        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $noteid1 = $DB->insert_record('board_notes', [
            'columnid' => $columns[0]->id,
            'ownerid' => $user->id,
            'userid' => $user->id,
            'content' => 'Content 1',
            'type' => board::MEDIATYPE_IMAGE,
            'url' => null,
            'timecreated' => time(),
            'sortorder' => 0,
        ]);
        $DB->set_field(
            'board_notes',
            'url',
            "/pluginfile.php/$context->id/mod_board/images/$noteid1/someThing.pnG",
            ['id' => $noteid1]
        );
        $noteid2 = $DB->insert_record('board_notes', [
            'columnid' => $columns[0]->id,
            'ownerid' => $user->id,
            'userid' => $user->id,
            'content' => 'Content 2',
            'type' => board::MEDIATYPE_URL,
            'url' => "https://www.example.com/pluginfile.php/$context->id/mod_board/background/0/otherthing.png",
            'timecreated' => time(),
            'sortorder' => 1,
        ]);
        $noteid3 = $DB->insert_record('board_notes', [
            'columnid' => $columns[0]->id,
            'ownerid' => $user->id,
            'userid' => $user->id,
            'content' => 'Content 3',
            'type' => board::MEDIATYPE_IMAGE,
            'url' => "/pluginfile.php/$context->id/mod_board/images/6666666/image.png",
            'timecreated' => time(),
            'sortorder' => 0,
        ]);
        $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'mod_board',
            'filearea' => 'images',
            'itemid' => '6666666',
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');

        mod_board_migrate_image_url_to_filename();

        $note1 = $DB->get_record('board_notes', ['id' => $noteid1]);
        $this->assertSame('someThing.pnG', $note1->filename);
        $this->assertSame(null, $note1->url);

        $note2 = $DB->get_record('board_notes', ['id' => $noteid2]);
        $this->assertSame(null, $note2->filename);
        $this->assertSame(
            "https://www.example.com/pluginfile.php/$context->id/mod_board/background/0/otherthing.png",
            $note2->url
        );

        $note3 = $DB->get_record('board_notes', ['id' => $noteid3]);
        $this->assertSame('image.png', $note3->filename);
        $this->assertSame(null, $note3->url);
        $file = $fs->get_file($context->id, 'mod_board', 'images', $note3->id, '/', 'image.png');
        $this->assertSame('image.png', $file->get_filename());
    }
}
