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

require_once($CFG->dirroot . '/mod/board/backup/moodle2/backup_board_stepslib.php');

/**
 * Main backup class.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_board_activity_task extends backup_activity_task {
    /**
     * Define my settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Defines the steps.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_board_activity_structure_step('board_structure', 'board.xml'));
    }

    /**
     * Encodes the content links.
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of boards.
        $search = "/(" . $base . "\/mod\/board\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@BOARDINDEX*$2@$', $content);

        // Link to board view by moduleid.
        $search = "/(" . $base . "\/mod\/board\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@BOARDVIEWBYID*$2@$', $content);

        return $content;
    }

    /**
     * Get the file areas.
     * @return string[]
     */
    public function get_fileareas() {
        return ['images', 'files', 'background'];
    }
}
