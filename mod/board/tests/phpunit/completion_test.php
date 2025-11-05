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

use mod_board\local\note;
use cm_info;
use mod_board\completion\custom_completion;

/**
 * Board completion tests.
 *
 * @package    mod_board
 * @copyright  2020 onward: Brickfield Education Labs <https://www.brickfield.ie/>
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class completion_test extends \advanced_testcase {
    /**
     * Test updating activity completion when submitting 2 notes.
     */
    public function test_activity_completion(): void {
        global $CFG, $DB;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $board = $this->getDataGenerator()->create_module(
            'board',
            ['course' => $course->id, 'completionnotes' => 2, 'completion' => COMPLETION_TRACKING_AUTOMATIC]
        );
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $column = $columns[0];

        $attachment = [
            'type' => 0,
            'info' => '',
            'url' => '',
        ];

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);
        note::create($column->id, $student->id, 0, 'Test heading', 'Test content', $attachment);

        $cm = get_coursemodule_from_instance('board', $board->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);
        $customcompletion = new custom_completion($cm, (int)$student->id);

        $this->assertEquals(COMPLETION_INCOMPLETE, $customcompletion->get_state('completionnotes'));

        note::create($column->id, $student->id, 0, 'Test heading 2', 'Test content 2', $attachment);
        $this->assertEquals(COMPLETION_COMPLETE, $customcompletion->get_state('completionnotes'));
    }
}
