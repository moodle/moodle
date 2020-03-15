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
 * Quarantine file
 *
 * @package    core_antivirus
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\antivirus;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/filelib.php');

/**
 * Quarantine file
 *
 * @package    core_antivirus
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quarantine {

    /** Default quarantine folder */
    const DEFAULT_QUARANTINE_FOLDER = 'antivirus_quarantine';

    /** Zip infected file  */
    const FILE_ZIP_INFECTED = '_infected_file.zip';

    /** Zip all infected file */
    const FILE_ZIP_ALL_INFECTED = '_all_infected_files.zip';

    /** Incidence details file */
    const FILE_HTML_DETAILS = '_details.html';

    /** Incidence details file */
    const DEFAULT_QUARANTINE_TIME = DAYSECS * 28;

    /** Date format in filename */
    const FILE_NAME_DATE_FORMAT = '%Y%m%d%H%M%S';

    /**
     * Move the infected file to the quarantine folder
     *
     * @param string $file infected file
     * @param string $filename infected file name
     * @param string $incidencedetails incidence details
     * @param string $notice notice details
     * @throws \dml_exception
     */
    public static function quarantine_file($file, $filename, $incidencedetails, $notice) {
        if (!self::is_allowed_quarantine()) {
            return;
        }
        // Generate file names.
        $date = userdate(time(), self::FILE_NAME_DATE_FORMAT) . "_" . rand();
        $zipfilepath = self::get_quarantine_folder() . $date . self::FILE_ZIP_INFECTED;
        $detailsfilename = $date . self::FILE_HTML_DETAILS;

        // Create Zip file.
        $ziparchive = new \zip_archive();
        if ($ziparchive->open($zipfilepath, \file_archive::CREATE)) {
            $ziparchive->add_file_from_string($detailsfilename, format_text($incidencedetails, FORMAT_MOODLE));
            $ziparchive->add_file_from_pathname($filename, $file);
            $ziparchive->close();
        }
        $zipfile = basename($zipfilepath);
        self::create_infected_file_record($filename, $zipfile, $notice);
        return $zipfile;
    }

    /**
     * Move the infected file to the quarantine folder
     *
     * @param string $data data which is infected
     * @param string $filename infected file name
     * @param string $incidencedetails incidence details
     * @param string $notice notice details
     * @throws \dml_exception
     */
    public static function quarantine_data($data, $filename, $incidencedetails, $notice) {
        if (!self::is_allowed_quarantine()) {
            return;
        }
        // Generate file names.
        $date = userdate(time(), self::FILE_NAME_DATE_FORMAT) . "_" . rand();
        $zipfilepath = self::get_quarantine_folder() . $date . self::FILE_ZIP_INFECTED;
        $detailsfilename = $date . self::FILE_HTML_DETAILS;

        // Create Zip file.
        $ziparchive = new \zip_archive();
        if ($ziparchive->open($zipfilepath, \file_archive::CREATE)) {
            $ziparchive->add_file_from_string($detailsfilename, format_text($incidencedetails, FORMAT_MOODLE));
            $ziparchive->add_file_from_string($filename, $data);
            $ziparchive->close();
        }
        $zipfile = basename($zipfilepath);
        self::create_infected_file_record($filename, $zipfile, $notice);
        return $zipfile;
    }

    /**
     * Check if the virus quarantine is allowed
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_allowed_quarantine() {
        return !empty(get_config("antivirus", "enablequarantine"));
    }

    /**
     * Get quarantine folder
     *
     * @return string path of quarantine folder
     */
    public static function get_quarantine_folder() {
        global $CFG;
        $quarantinefolder = $CFG->dataroot . DIRECTORY_SEPARATOR . self::DEFAULT_QUARANTINE_FOLDER;
        if (!file_exists($quarantinefolder)) {
            make_upload_directory(self::DEFAULT_QUARANTINE_FOLDER);
        }
        return $quarantinefolder . DIRECTORY_SEPARATOR;
    }

    /**
     * Download quarantined file
     *
     * @param string $filename name of file to be downloaded
     */
    public static function download_quarantined_file($filename) {
        $file = self::get_quarantine_folder() . $filename;
//        send_file($file, $filename);
    }

    /**
     * Delete quarantined file
     *
     * @param string $filename name of file to be deleted
     */
    public static function delete_quarantined_file($filename) {
        self::delete_infected_file_record($filename);
        $file = self::get_quarantine_folder() . $filename;
        if (file_exists($file)) {
//            unlink($file);
        }
    }

    /**
     * Download all quarantined files
     *
     */
    public static function download_all_quarantined_files() {
        $files = new \DirectoryIterator(self::get_quarantine_folder());
        // Add all infected file to a zip file.
        $date = userdate(time(), self::FILE_NAME_DATE_FORMAT);
        $zipfilename = $date . self::FILE_ZIP_ALL_INFECTED;
        $zipfilepath = self::get_quarantine_folder() . DIRECTORY_SEPARATOR . $zipfilename;
        $tempfilestocleanup = [];
        $ziparchive = new \zip_archive();
        if ($ziparchive->open($zipfilepath, \file_archive::CREATE)) {
            foreach ($files as $file) {
                if (!$file->isDot()) {
                    $filename = $file->getFilename();
                    $filepath = $file->getPathname();
                    $ziparchive->add_file_from_pathname($filename, $filepath);
                }
            }
            $ziparchive->close();
        }
        // Clean up temp files.
        foreach ($tempfilestocleanup as $tempfile) {
            if (file_exists($tempfile)) {
                unlink($tempfile);
            }
        }
//        send_temp_file($zipfilepath, $zipfilename);
    }

    /**
     * Return array of quarantined files
     *
     * @return array list of quarantined files
     */
    public static function get_quarantined_files() {
        $files = new \DirectoryIterator(self::get_quarantine_folder());
        $filestosort = [];
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (!$file->isDot() && strpos($filename, self::FILE_ZIP_INFECTED) !== false) {
                $filestosort[$filename] = $file->getPathname();
            }
        }
        krsort($filestosort, SORT_NATURAL);
        return $filestosort;
    }

    /**
     * Clean up quarantine folder
     *
     * @param int $timetocleanup time to clean up
     */
    public static function clean_up_quarantine_folder($timetocleanup) {
        $files = new \DirectoryIterator(self::get_quarantine_folder());
        // Clean up the folder.
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (!$file->isDot() && strpos($filename, self::FILE_ZIP_INFECTED) !== false) {
                $modifiedtime = $file->getMTime();
                if ($modifiedtime <= $timetocleanup) {
                    unlink($file->getPathname());
                    self::delete_infected_file_record($filename);
                }
            }
        }
    }

    /**
     * Create an infected file record
     *
     * @param string $filename original file name
     * @param string $zipfile quarantined file name
     * @param string $reason failure reason
     * @throws \dml_exception
     */
    private static function create_infected_file_record($filename, $zipfile, $reason) {
        global $DB, $USER;
        $record = new \stdClass();
        $record->filename = $filename;
        $record->quarantinedfile = $zipfile;
        $record->author = fullname($USER);
        $record->reason = $reason;
        $record->timecreated = time();
        $DB->insert_record('infected_files', $record);
    }

    /**
     * Delete an infected_file_record
     *
     * @param string $zipfile quarantined file name
     * @throws \dml_exception
     */
    private static function delete_infected_file_record($zipfile) {
        global $DB;
        $DB->delete_records('infected_files', ['quarantinedfile' => $zipfile]);
    }
}
