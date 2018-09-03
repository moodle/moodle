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
 * Provides {@link imscc11_export_converter} class
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/converter/convertlib.php');

class imscc11_export_converter extends base_converter {
    static public function get_deps() {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/settings/setting_dependency.class.php');
        return array(
                'users'   => setting_dependency::DISABLED_VALUE,
                'filters' => setting_dependency::DISABLED_VALUE,
                'blocks'  => setting_dependency::DISABLED_VALUE
        );

    }
    protected function execute() {

    }
    public static function description() {

        return array(
                'from'  => backup::FORMAT_MOODLE,
                'to'    => backup::FORMAT_IMSCC11,
                'cost'  => 10
        );
    }

}


class imscc11_store_backup_file extends backup_execution_step {

    protected function define_execution() {

        // Get basepath
        $basepath = $this->get_basepath();

        // Calculate the zip fullpath (in OS temp area it's always backup.imscc)
        $zipfile = $basepath . '/backup.imscc';

        // Perform storage and return it (TODO: shouldn't be array but proper result object)
        // Let's send the file to file storage, everything already defined
        // First of all, get some information from the backup_controller to help us decide
        list($dinfo, $cinfo, $sinfo) = backup_controller_dbops::get_moodle_backup_information($this->get_backupid());

        // Extract useful information to decide
        $file      = $sinfo['filename']->value;
        $filename  = basename($file,'.'.pathinfo($file, PATHINFO_EXTENSION)).'.imscc';        // Backup filename
        $userid    = $dinfo[0]->userid;                // User->id executing the backup
        $id        = $dinfo[0]->id;                    // Id of activity/section/course (depends of type)
        $courseid  = $dinfo[0]->courseid;              // Id of the course

        $ctxid     = context_user::instance($userid)->id;
        $component = 'user';
        $filearea  = 'backup';
        $itemid    = 0;
        $fs = get_file_storage();
        $fr = array(
                    'contextid'   => $ctxid,
                    'component'   => $component,
                    'filearea'    => $filearea,
                    'itemid'      => $itemid,
                    'filepath'    => '/',
                    'filename'    => $filename,
                    'userid'      => $userid,
                    'timecreated' => time(),
                    'timemodified'=> time());
        // If file already exists, delete if before
        // creating it again. This is BC behaviour - copy()
        // overwrites by default
        if ($fs->file_exists($fr['contextid'], $fr['component'], $fr['filearea'], $fr['itemid'], $fr['filepath'], $fr['filename'])) {
            $pathnamehash = $fs->get_pathname_hash($fr['contextid'], $fr['component'], $fr['filearea'], $fr['itemid'], $fr['filepath'], $fr['filename']);
            $sf = $fs->get_file_by_hash($pathnamehash);
            $sf->delete();
        }

        return array('backup_destination' => $fs->create_file_from_pathname($fr, $zipfile));
    }
}

class imscc11_zip_contents extends backup_execution_step {

    protected function define_execution() {

        // Get basepath
        $basepath = $this->get_basepath();

        // Get the list of files in directory
        $filestemp = get_directory_list($basepath, '', false, true, true);
        $files = array();
        foreach ($filestemp as $file) {
            // Add zip paths and fs paths to all them
            $files[$file] = $basepath . '/' . $file;
        }

        // Calculate the zip fullpath (in OS temp area it's always backup.mbz)
        $zipfile = $basepath . '/backup.imscc';

        // Get the zip packer
        $zippacker = get_file_packer('application/zip');

        // Zip files
        $zippacker->archive_to_pathname($files, $zipfile);
    }
}

class imscc11_backup_convert extends backup_execution_step {

    protected function define_execution() {
        global $CFG;
        // Get basepath
        $basepath = $this->get_basepath();

        require_once($CFG->dirroot . '/backup/cc/cc_includes.php');

        $tempdir = $CFG->backuptempdir . '/' . uniqid('', true);

        if (mkdir($tempdir, $CFG->directorypermissions, true)) {

            cc_convert_moodle2::convert($basepath, $tempdir);
            //Switch the directories
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($basepath);
            } else {
                if (!rename($basepath, $basepath  . '_moodle2_source')) {
                    throw new backup_task_exception('failed_rename_source_tempdir');
                }
            }

            if (!rename($tempdir, $basepath)) {
                throw new backup_task_exception('failed_move_converted_into_place');
            }

        }
    }
}



