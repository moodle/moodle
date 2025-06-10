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
 * @copyright  2021 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class pu_helpers {
    /**
     * Function to grab current list of files in a folder that's specified in settings.
     *
     * @return @array of files.
     */
    public static function get_system_file_list() {

        $settingspath = get_config('moodle', "block_pu_copy_file");
        $nonmoodlefilestemp = scandir($settingspath, SCANDIR_SORT_DESCENDING);
        $nonmoodlefiles = array();
        $counter = 0;
        foreach ($nonmoodlefilestemp as $fcheck) {
            if ($fcheck == '.' || $fcheck == '..') {
                continue;
            }

            $temp = array(
                "nonmood_filename" => $fcheck,
                "nonmood_modified" => userdate(filectime($settingspath.$fcheck)),
                "nonmood_modifiedstamp" => filectime($settingspath.$fcheck),
                "nonmood_hash" => md5($settingspath.$fcheck.filectime($settingspath.$fcheck)),
                "form_value" => $counter
            );
            $counter++;
            $nonmoodlefiles[] = $temp;
        }
        return $nonmoodlefiles;
    }

    public static function get_pu_file_list() {
        global $DB;
        $uploadedfiles = $DB->get_records('block_pu_file');
        $tabledata = array();
        foreach ($uploadedfiles as $ufile) {
            $temp = array(
                "puid" => $ufile->id,
                "fileid" => $ufile->fileid,
                "itemid" => $ufile->itemid,
                "pu_filename" => $ufile->filename,
                "pu_filecreated" => userdate($ufile->timecreated),
                "pu_filemodified" => userdate($ufile->timemodified)
            );
            $tabledata[] = $temp;
        }
        return $tabledata;
    }
}
