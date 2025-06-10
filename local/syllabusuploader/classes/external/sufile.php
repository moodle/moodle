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
 * @copyright  2023 onwards Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/local/syllabusuploader/lib.php');

class sufile {

    public function get_file_list() {
        global $CFG;

        // Set up the page params.
        $pageparams = [
            'sort' => optional_param('dir', array(), PARAM_TEXT)
        ];

        // Get this ready.
        $sort = isset($pageparams['sort']) ? $pageparams['sort'] : 'asc';

        // We only really care about descending sorts as the default is ascending.
        if ($sort != 'desc') {
            $sorthint = true;
        } else {
            $sorthint = false;
        }

        // Get the settings path.
        $settingspath = get_config('moodle', "local_syllabusuploader_copy_file");

        // Get the non Moodle files.
        $nonmoodlefiles = \syllabusuploader_helpers::get_system_file_list($sort);

        // Get the Moodle data.
        $tabledata = \syllabusuploader_helpers::get_syllabusuploader_file_list();

        // Build the array to send to the renderer.
        $renderdata = array(
            "syllabusuploader_data" => $tabledata,
            "syllabusuploader_url" => $CFG->wwwroot,
            "currentpath" => $settingspath,
            "non_mood_files" => $nonmoodlefiles
        );

        // Return the data.
        return $renderdata;
    }

    public function check_file_exists($params = null) {
        // Get the Moodle file id.
        $mfileid = isset($params->mfileid) ? $params->mfileid : null;

        // Get the syllabulsuploader file id.
        $sufileid = isset($params->sufileid) ? $params->sufileid : null;

        // Get the public path.
        $fpath = get_config('moodle', 'local_syllabusuploader_copy_file');

        // If the Moodle file exists then report back to deny.
        if ($mfileid) {
            // Get the Moodle file storage.
            $fs = get_file_storage();

            // Get the Moodle file.
            $file = $fs->get_file_by_id($mfileid);

            // Get the Moodle filename.
            $fname = $file->get_filename();

            // Check to see if we have the file.
            if (file_exists($fpath . $fname)) {

                // Let the user know.
                return array(
                    "success" => false,
                    "msg" => "Sorry, this file already exists in that location."
                );

            } else {
                // File is new, copy the file.
                $file->copy_content_to($fpath. $fname);
                // TODO: Check if the file copied and let the user know.
                return array(
                    "success" => true,
                    "msg" => "The file was successfully copied over."
                );
            }
        }
    }
}
