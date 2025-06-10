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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/blocks/pu/lib.php');

class pufile {

    public function get_file_list() {
        global $CFG;

        $nonmoodlefiles = pu_helpers::get_system_file_list();
        $tabledata = pu_helpers::get_pu_file_list();

        $renderdata = array(
            "pu_data" => $tabledata,
            "pu_url" => $CFG->wwwroot,
            "currentpath" => $settingspath,
            "non_mood_files" => $nonmoodlefiles
        );

        return $renderdata;
    }

    public function check_file_exists($params = null) {
        $mfileid = isset($params->mfileid) ? $params->mfileid : null;
        $pufileid = isset($params->pufileid) ? $params->pufileid : null;
        $fpath = get_config('moodle', "block_pu_copy_file");

        // If file exists then report back to deny.
        if ($mfileid) {
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($mfileid);
            $fname = $file->get_filename();

            if (file_exists($fpath . $fname)) {
                return array(
                    "success" => false,
                    "msg" => "Sorry, this file already exists in that location."
                );
            } else {
                $file->copy_content_to($fpath. $fname);
                return array(
                    "success" => true,
                    "msg" => "The file was successfully copied over."
                );
            }
        }
    }
}
