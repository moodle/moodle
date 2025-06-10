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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class syllabusuploader_helpers {
    /**
     * Function to grab current list of files in a folder that's specified in settings.
     *
     * @return @array of files.
     */
    public static function upsert_system_folder() {

        // Get the path from the settings.
        $supath = get_config('moodle', "local_syllabusuploader_copy_file");

        // Make sure the folder is there.
        if (!is_dir($supath)) {
            mkdir($supath, 0777, true);
        }
    }

    public static function get_system_file_list($sort) {
        // Get the copy file path.
        $settingspath = get_config('moodle', 'local_syllabusuploader_copy_file');

        // Get Moodle root path.
        $moodlepath = get_config('moodle', 'wwwroot');

        // Get the configured public path.
        $publicpath = get_config('moodle', 'local_syllabusuploader_public_path');

        // Build the link prefix.
        $filelink = $moodlepath . $publicpath;

        // Grab a preliminary list of files.
        $nonmoodlefilestemp = scandir($settingspath, SCANDIR_SORT_ASCENDING);

        // Sort it.
        if ($sort != 'desc') {
            $sorthint = true;
        } else {
            rsort($nonmoodlefilestemp);
            $sorthint = false;
        }

        // Build this array for later.
        $nonmoodlefiles = array();

        // Set the counter.
        $counter = 0;

        // Loop through the files and do stuff.
        foreach ($nonmoodlefilestemp as $fcheck) {
            // Make sure we don't send non-syllabus files or folders.
            if ($fcheck == '.'
                || $fcheck == '..'
                || $fcheck == 'index.php'
                || is_dir($settingspath . '/' . $fcheck)) {
                continue;
            }

            // Build the temporary array.
            $temp = array(
                "sort" => $sorthint,
                "nonmood_filename" => $fcheck,
                "nonmood_fileurl" => $filelink . $fcheck,
                "nonmood_modified" => userdate(filectime($settingspath.$fcheck)),
                "nonmood_modifiedstamp" => filectime($settingspath.$fcheck),
                "nonmood_hash" => md5($settingspath.$fcheck.filectime($settingspath.$fcheck)),
                "form_value" => $counter
            );

            // Increment the counter.
            $counter++;

            // Build the non moodle files array.
            $nonmoodlefiles[] = $temp;
        }
        // Return the data.
        return $nonmoodlefiles;
    }

    public static function syllabusuploader_user($user) {
        // Set the context.
        $context = \context_system::instance();

        // Check the cap and set the accressrule accordingly.
        $permitted = has_capability('local/syllabusuploader:admin', $context);

        // Get the allowed users from config.
        $alloweduserlist = get_config('moodle', 'local_syllabusuploader_admins');

        // Make an array out of the list.
        $allowedusers = explode(',', $alloweduserlist);

        // Loop through them and see if the user requesting access is allowed.
        foreach ($allowedusers as $alloweduser) {

            // We're using emails.
            if ($alloweduser == $user->email && $permitted) {
                return true;
            }

            // We're using usernames.
            if ($alloweduser == $user->username && $permitted) {
                return true;
            }
        }

        // We did not find the user, boot them.
        return false;
    }

    public static function get_syllabusuploader_file_list() {
        global $DB;

        // Get the uploaded files.
        $uploadedfiles = $DB->get_records('local_syllabusuploader_file');

        // Set the initial table array.
        $tabledata = array();

        // Loop through the uploaded files.
        foreach ($uploadedfiles as $ufile) {

            // Build the temp array of files.
            $temp = array(
                "suid" => $ufile->id,
                "fileid" => $ufile->fileid,
                "itemid" => $ufile->itemid,
                "syllabusuploader_filename" => $ufile->filename,
                "syllabusuploader_filecreated" => userdate($ufile->timecreated),
                "syllabusuploader_filemodified" => userdate($ufile->timemodified)
            );

            // Build the table from the data.
            $tabledata[] = $temp;
        }

        // Return the table.
        return $tabledata;
    }
}
