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

/**
 * @package    local_syllabusuploader
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_syllabusuploader\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

defined('MOODLE_INTERNAL') || die;
global $CFG;

require_once($CFG->dirroot . '/local/syllabusuploader/lib.php');

require_login();

class files_view implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): array {
        global $USER, $CFG;

        // Set the bool for access permission.
        $allowed = \syllabusuploader_helpers::syllabusuploader_user($USER);

        // Check to see if the user is admin.
        if (!$allowed) {
            return array();
        }

        // Set up the page params.
        $pageparams = [
            'sort' => optional_param('dir', array(), PARAM_TEXT)
        ];

        // Get this ready.
        $sort = isset($pageparams['sort']) ? $pageparams['sort'] : 'asc';

        // We really only care about descending sorts as asc is the default.
        if ($sort != 'desc') {
            $sorthint = true;
        } else {
            $sorthint = false;
        }

        // Get the public path..
        $settingspath = get_config('moodle', "local_syllabusuploader_copy_file");

        // Make sure the folder exists.
        \syllabusuploader_helpers::upsert_system_folder();

        // Build the array of non-moodle files.
        $nonmoodlefiles = \syllabusuploader_helpers::get_system_file_list($sort);

        // Build the Moodle files array.
        $tabledata = \syllabusuploader_helpers::get_syllabusuploader_file_list();

        // Prepare the array for the renderer.
        $renderdata = array(
            "sort" => $sorthint,
            "syllabusuploader_data" => $tabledata,
            "syllabusuploader_url" => $CFG->wwwroot,
            "currentpath" => $settingspath,
            "non_mood_files" => $nonmoodlefiles

        );
        // Return the data.
        return $renderdata;
    }
}
