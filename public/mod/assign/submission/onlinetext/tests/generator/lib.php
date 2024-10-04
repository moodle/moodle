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

require_once("{$CFG->dirroot}/mod/assign/tests/generator/assignsubmission_subplugin_generator.php");

/**
 * Online Text assignment submission subplugin data generator.
 *
 * @package assignsubmission_onlinetext
 * @category test
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignsubmission_onlinetext_generator extends assignsubmission_subplugin_generator {
    /**
     * Add submission data in the correct format for a call to `assign::save_submission()` from a table containing
     * submission data for a single activity.
     *
     * Data should be added to the $submission object passed into the function.
     *
     * @param stdClass $submission The submission record to be modified
     * @param assign $assign The assignment being submitted to
     * @param array $data The data received
     */
    public function add_submission_data(stdClass $submission, assign $assign, array $data): void {
        if (array_key_exists('onlinetext', $data)) {
            $submission->onlinetext_editor = [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => $data['onlinetext'],
                'format' => FORMAT_HTML,
            ];
        }
    }
}
