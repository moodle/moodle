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
 * @package    moodlecore
 * @subpackage backup-helper
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base abstract class for all the helper classes providing various operations
 *
 * TODO: Finish phpdocs
 */
abstract class backup_helper {

    /**
     * Given one backupid, create all the needed dirs to have one backup temp dir available
     */
    static public function check_and_create_backup_dir($backupid) {
        global $CFG;
        if (!check_dir_exists($CFG->tempdir . '/backup/' . $backupid, true, true)) {
            throw new backup_helper_exception('cannot_create_backup_temp_dir');
        }
    }

    /**
     * Given one backupid, ensure its temp dir is completely empty
     *
     * If supplied, progress object should be ready to receive indeterminate
     * progress reports.
     *
     * @param string $backupid Backup id
     * @param \core\progress\base $progress Optional progress reporting object
     */
    static public function clear_backup_dir($backupid, \core\progress\base $progress = null) {
        global $CFG;
        if (!self::delete_dir_contents($CFG->tempdir . '/backup/' . $backupid, '', $progress)) {
            throw new backup_helper_exception('cannot_empty_backup_temp_dir');
        }
        return true;
    }

    /**
     * Given one backupid, delete completely its temp dir
     *
     * If supplied, progress object should be ready to receive indeterminate
     * progress reports.
     *
     * @param string $backupid Backup id
     * @param \core\progress\base $progress Optional progress reporting object
     */
     static public function delete_backup_dir($backupid, \core\progress\base $progress = null) {
         global $CFG;
         self::clear_backup_dir($backupid, $progress);
         return rmdir($CFG->tempdir . '/backup/' . $backupid);
     }

     /**
     * Given one fullpath to directory, delete its contents recursively
     * Copied originally from somewhere in the net.
     * TODO: Modernise this
     *
     * If supplied, progress object should be ready to receive indeterminate
     * progress reports.
     *
     * @param string $dir Directory to delete
     * @param string $excludedir Exclude this directory
     * @param \core\progress\base $progress Optional progress reporting object
     */
    static public function delete_dir_contents($dir, $excludeddir='', \core\progress\base $progress = null) {
        global $CFG;

        if ($progress) {
            $progress->progress();
        }

        if (!is_dir($dir)) {
            // if we've been given a directory that doesn't exist yet, return true.
            // this happens when we're trying to clear out a course that has only just
            // been created.
            return true;
        }
        $slash = "/";

        // Create arrays to store files and directories
        $dir_files      = array();
        $dir_subdirs    = array();

        // Make sure we can delete it
        chmod($dir, $CFG->directorypermissions);

        if ((($handle = opendir($dir))) == false) {
            // The directory could not be opened
            return false;
        }

        // Loop through all directory entries, and construct two temporary arrays containing files and sub directories
        while (false !== ($entry = readdir($handle))) {
            if (is_dir($dir. $slash .$entry) && $entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_subdirs[] = $dir. $slash .$entry;

            } else if ($entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_files[] = $dir. $slash .$entry;
            }
        }

        // Delete all files in the curent directory return false and halt if a file cannot be removed
        for ($i=0; $i<count($dir_files); $i++) {
            chmod($dir_files[$i], $CFG->directorypermissions);
            if (((unlink($dir_files[$i]))) == false) {
                return false;
            }
        }

        // Empty sub directories and then remove the directory
        for ($i=0; $i<count($dir_subdirs); $i++) {
            chmod($dir_subdirs[$i], $CFG->directorypermissions);
            if (self::delete_dir_contents($dir_subdirs[$i], '', $progress) == false) {
                return false;
            } else {
                if (remove_dir($dir_subdirs[$i]) == false) {
                    return false;
                }
            }
        }

        // Close directory
        closedir($handle);

        // Success, every thing is gone return true
        return true;
    }

    /**
     * Delete all the temp dirs older than the time specified.
     *
     * If supplied, progress object should be ready to receive indeterminate
     * progress reports.
     *
     * @param int $deletefrom Time to delete from
     * @param \core\progress\base $progress Optional progress reporting object
     */
    static public function delete_old_backup_dirs($deletefrom, \core\progress\base $progress = null) {
        global $CFG;

        $status = true;
        // Get files and directories in the temp backup dir witout descend
        $list = get_directory_list($CFG->tempdir . '/backup', '', false, true, true);
        foreach ($list as $file) {
            $file_path = $CFG->tempdir . '/backup/' . $file;
            $moddate = filemtime($file_path);
            if ($status && $moddate < $deletefrom) {
                //If directory, recurse
                if (is_dir($file_path)) {
                    // $file is really the backupid
                    $status = self::delete_backup_dir($file, $progress);
                //If file
                } else {
                    unlink($file_path);
                }
            }
        }
        if (!$status) {
            throw new backup_helper_exception('problem_deleting_old_backup_temp_dirs');
        }
    }

    /**
     * This function will be invoked by any log() method in backup/restore, acting
     * as a simple forwarder to the standard loggers but also, if the $display
     * parameter is true, supporting translation via get_string() and sending to
     * standard output.
     */
    static public function log($message, $level, $a, $depth, $display, $logger) {
        // Send to standard loggers
        $logmessage = $message;
        $options = empty($depth) ? array() : array('depth' => $depth);
        if (!empty($a)) {
            $logmessage = $logmessage . ' ' . implode(', ', (array)$a);
        }
        $logger->process($logmessage, $level, $options);

        // If $display specified, send translated string to output_controller
        if ($display) {
            output_controller::get_instance()->output($message, 'backup', $a, $depth);
        }
    }

    /**
     * Given one backupid and the (FS) final generated file, perform its final storage
     * into Moodle file storage. For stored files it returns the complete file_info object
     *
     * Note: the $filepath is deleted if the backup file is created successfully
     *
     * If you specify the progress monitor, this will start a new progress section
     * to track progress in processing (in case this task takes a long time).
     *
     * @param int $backupid
     * @param string $filepath zip file containing the backup
     * @param \core\progress\base $progress Optional progress monitor
     * @return stored_file if created, null otherwise
     *
     * @throws moodle_exception in case of any problems
     */
    static public function store_backup_file($backupid, $filepath, \core\progress\base $progress = null) {
        global $CFG;

        // First of all, get some information from the backup_controller to help us decide
        list($dinfo, $cinfo, $sinfo) = backup_controller_dbops::get_moodle_backup_information(
                $backupid, $progress);

        // Extract useful information to decide
        $hasusers  = (bool)$sinfo['users']->value;     // Backup has users
        $isannon   = (bool)$sinfo['anonymize']->value; // Backup is anonymised
        $filename  = $sinfo['filename']->value;        // Backup filename
        $backupmode= $dinfo[0]->mode;                  // Backup mode backup::MODE_GENERAL/IMPORT/HUB
        $backuptype= $dinfo[0]->type;                  // Backup type backup::TYPE_1ACTIVITY/SECTION/COURSE
        $userid    = $dinfo[0]->userid;                // User->id executing the backup
        $id        = $dinfo[0]->id;                    // Id of activity/section/course (depends of type)
        $courseid  = $dinfo[0]->courseid;              // Id of the course
        $format    = $dinfo[0]->format;                // Type of backup file

        // Quick hack. If for any reason, filename is blank, fix it here.
        // TODO: This hack will be out once MDL-22142 - P26 gets fixed
        if (empty($filename)) {
            $filename = backup_plan_dbops::get_default_backup_filename('moodle2', $backuptype, $id, $hasusers, $isannon);
        }

        // Backups of type IMPORT aren't stored ever
        if ($backupmode == backup::MODE_IMPORT) {
            return null;
        }

        if (!is_readable($filepath)) {
            // we have a problem if zip file does not exist
            throw new coding_exception('backup_helper::store_backup_file() expects valid $filepath parameter');

        }

        // Calculate file storage options of id being backup
        $ctxid     = 0;
        $filearea  = '';
        $component = '';
        $itemid    = 0;
        switch ($backuptype) {
            case backup::TYPE_1ACTIVITY:
                $ctxid     = context_module::instance($id)->id;
                $component = 'backup';
                $filearea  = 'activity';
                $itemid    = 0;
                break;
            case backup::TYPE_1SECTION:
                $ctxid     = context_course::instance($courseid)->id;
                $component = 'backup';
                $filearea  = 'section';
                $itemid    = $id;
                break;
            case backup::TYPE_1COURSE:
                $ctxid     = context_course::instance($courseid)->id;
                $component = 'backup';
                $filearea  = 'course';
                $itemid    = 0;
                break;
        }

        if ($backupmode == backup::MODE_AUTOMATED) {
            // Automated backups have there own special area!
            $filearea  = 'automated';

            // If we're keeping the backup only in a chosen path, just move it there now
            // this saves copying from filepool to here later and filling trashdir.
            $config = get_config('backup');
            $dir = $config->backup_auto_destination;
            if ($config->backup_auto_storage == 1 and $dir and is_dir($dir) and is_writable($dir)) {
                $filedest = $dir.'/'.backup_plan_dbops::get_default_backup_filename($format, $backuptype, $courseid, $hasusers, $isannon, !$config->backup_shortname);
                // first try to move the file, if it is not possible copy and delete instead
                if (@rename($filepath, $filedest)) {
                    return null;
                }
                umask($CFG->umaskpermissions);
                if (copy($filepath, $filedest)) {
                    @chmod($filedest, $CFG->filepermissions); // may fail because the permissions may not make sense outside of dataroot
                    unlink($filepath);
                    return null;
                } else {
                    $bc = backup_controller::load_controller($backupid);
                    $bc->log('Attempt to copy backup file to the specified directory using filesystem failed - ',
                            backup::LOG_WARNING, $dir);
                    $bc->destroy();
                }
                // bad luck, try to deal with the file the old way - keep backup in file area if we can not copy to ext system
            }
        }

        // Backups of type HUB (by definition never have user info)
        // are sent to user's "user_tohub" file area. The upload process
        // will be responsible for cleaning that filearea once finished
        if ($backupmode == backup::MODE_HUB) {
            $ctxid     = context_user::instance($userid)->id;
            $component = 'user';
            $filearea  = 'tohub';
            $itemid    = 0;
        }

        // Backups without user info or with the anonymise functionality
        // enabled are sent to user's "user_backup"
        // file area. Maintenance of such area is responsibility of
        // the user via corresponding file manager frontend
        if ($backupmode == backup::MODE_GENERAL && (!$hasusers || $isannon)) {
            $ctxid     = context_user::instance($userid)->id;
            $component = 'user';
            $filearea  = 'backup';
            $itemid    = 0;
        }

        // Let's send the file to file storage, everything already defined
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
        $file = $fs->create_file_from_pathname($fr, $filepath);
        unlink($filepath);
        return $file;
    }

    /**
     * This function simply marks one param to be considered as straight sql
     * param, so it won't be searched in the structure tree nor converted at
     * all. Useful for better integration of definition of sources in structure
     * and DB stuff
     */
    public static function is_sqlparam($value) {
        return array('sqlparam' => $value);
    }

    /**
     * This function returns one array of itemnames that are being handled by
     * inforef.xml files. Used both by backup and restore
     */
    public static function get_inforef_itemnames() {
        return array('user', 'grouping', 'group', 'role', 'file', 'scale', 'outcome', 'grade_item', 'question_category');
    }
}

/*
 * Exception class used by all the @helper stuff
 */
class backup_helper_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
