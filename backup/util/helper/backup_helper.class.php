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
        if (!check_dir_exists($CFG->dataroot . '/temp/backup/' . $backupid, true, true)) {
            throw new backup_helper_exception('cannot_create_backup_temp_dir');
        }
    }

    /**
     * Given one backupid, ensure its temp dir is completelly empty
     */
    static public function clear_backup_dir($backupid) {
        global $CFG;
        if (!self::delete_dir_contents($CFG->dataroot . '/temp/backup/' . $backupid)) {
            throw new backup_helper_exception('cannot_empty_backup_temp_dir');
        }
    }

    /**
     * Given one fullpath to directory, delete its contents recursively
     * Copied originally from somewhere in the net.
     * TODO: Modernise this
     */
    static public function delete_dir_contents($dir, $excludeddir='') {
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
        chmod($dir, 0777);

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
            chmod($dir_files[$i], 0777);
            if (((unlink($dir_files[$i]))) == false) {
                return false;
            }
        }

        // Empty sub directories and then remove the directory
        for ($i=0; $i<count($dir_subdirs); $i++) {
            chmod($dir_subdirs[$i], 0777);
            if (self::delete_dir_contents($dir_subdirs[$i]) == false) {
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
     * Delete all the temp dirs older than the time specified
     */
    static public function delete_old_backup_dirs($deletefrom) {
        global $CFG;

        $status = true;
        // Get files and directories in the temp backup dir witout descend
        $list = get_directory_list($CFG->dataroot . '/temp/backup', '', false, true, true);
        foreach ($list as $file) {
            $file_path = $CFG->dataroot . '/temp/backup/' . $file;
            $moddate = filemtime($file_path);
            if ($status && $moddate < $deletefrom) {
                //If directory, recurse
                if (is_dir($file_path)) {
                    $status = self::delete_dir_contents($file_path);
                    //There is nothing, delete the directory itself
                    if ($status) {
                        $status = rmdir($file_path);
                    }
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
}

/*
 * Exception class used by all the @helper stuff
 */
class backup_helper_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
