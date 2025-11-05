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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/board/backup/moodle2/restore_board_stepslib.php'); // Because it exists (must).

/**
 * Restore activity class.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_board_activity_task extends restore_activity_task {
    /**
     * Define any settings.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define the steps.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_board_activity_structure_step('board_structure', 'board.xml'));
    }

    /**
     * Define the content decode.
     * @return array
     */
    public static function define_decode_contents() {
        $contents = [];

        $contents[] = new restore_decode_content('board', ['intro'], 'board');

        // NOTE: url may contain links to internal pages, decode the field the same way as mod_url does externalurl.
        $contents[] = new restore_decode_content('board_notes', ['content', 'url'], 'board_note');

        return $contents;
    }

    /**
     * Define the decode rules.
     * @return array
     */
    public static function define_decode_rules() {
        $rules = [];
        $rules[] = new restore_decode_rule('BOARDVIEWBYID', '/mod/board/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('BOARDINDEX', '/mod/board/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Get the file areas.
     * @return string[]
     */
    public function get_fileareas() {
        return ['images', 'files', 'backgrond'];
    }
}
