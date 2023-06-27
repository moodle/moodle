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
 * Custom lang importer.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\local;

use tool_customlang\local\mlang\phpparser;
use tool_customlang\local\mlang\logstatus;
use tool_customlang\local\mlang\langstring;
use core\output\notification;
use stored_file;
use coding_exception;
use moodle_exception;
use core_component;
use stdClass;

/**
 * Class containing tha custom lang importer
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class importer {

    /** @var int imports will only create new customizations */
    public const IMPORTNEW = 1;
    /** @var int imports will only update the current customizations */
    public const IMPORTUPDATE = 2;
    /** @var int imports all strings */
    public const IMPORTALL = 3;

    /**
     * @var string the language name
     */
    protected $lng;

    /**
     * @var int the importation mode (new, update, all)
     */
    protected $importmode;

    /**
     * @var string request folder path
     */
    private $folder;

    /**
     * @var array import log messages
     */
    private $log;

    /**
     * Constructor for the importer class.
     *
     * @param string $lng the current language to import.
     * @param int $importmode the import method (IMPORTALL, IMPORTNEW, IMPORTUPDATE).
     */
    public function __construct(string $lng, int $importmode = self::IMPORTALL) {
        $this->lng = $lng;
        $this->importmode = $importmode;
        $this->log = [];
    }

    /**
     * Returns the last parse log.
     *
     * @return logstatus[] mlang logstatus with the messages
     */
    public function get_log(): array {
        return $this->log;
    }

    /**
     * Import customlang files.
     *
     * @param stored_file[] $files array of files to import
     */
    public function import(array $files): void {
        // Create a temporal folder to store the files.
        $this->folder = make_request_directory(false);

        $langfiles = $this->deploy_files($files);

        $this->process_files($langfiles);
    }

    /**
     * Deploy all files into a request folder.
     *
     * @param stored_file[] $files array of files to deploy
     * @return string[] of file paths
     */
    private function deploy_files(array $files): array {
        $result = [];
        // Desploy all files.
        foreach ($files as $file) {
            if ($file->get_mimetype() == 'application/zip') {
                $result = array_merge($result, $this->unzip_file($file));
            } else {
                $path = $this->folder.'/'.$file->get_filename();
                $file->copy_content_to($path);
                $result = array_merge($result, [$path]);
            }
        }
        return $result;
    }

    /**
     * Unzip a file into the request folder.
     *
     * @param stored_file $file the zip file to unzip
     * @return string[] of zip content paths
     */
    private function unzip_file(stored_file $file): array {
        $fp = get_file_packer('application/zip');
        $zipcontents = $fp->extract_to_pathname($file, $this->folder);
        if (!$zipcontents) {
            throw new moodle_exception("Error Unzipping file", 1);
        }
        $result = [];
        foreach ($zipcontents as $contentname => $success) {
            if ($success) {
                $result[] = $this->folder.'/'.$contentname;
            }
        }
        return $result;
    }

    /**
     * Import strings from a list of langfiles.
     *
     * @param string[] $langfiles an array with file paths
     */
    private function process_files(array $langfiles): void {
        $parser = phpparser::get_instance();
        foreach ($langfiles as $filepath) {
            $component = $this->component_from_filepath($filepath);
            if ($component) {
                $strings = $parser->parse(file_get_contents($filepath));
                $this->import_strings($strings, $component);
            }
        }
    }

    /**
     * Try to get the component from a filepath.
     *
     * @param string $filepath the filepath
     * @return stdCalss|null the DB record of that component
     */
    private function component_from_filepath(string $filepath) {
        global $DB;

        // Get component from filename.
        $pathparts = pathinfo($filepath);
        if (empty($pathparts['filename'])) {
            throw new coding_exception("Cannot get filename from $filepath", 1);
        }
        $filename = $pathparts['filename'];

        $normalized = core_component::normalize_component($filename);
        if (count($normalized) == 1 || empty($normalized[1])) {
            $componentname = $normalized[0];
        } else {
            $componentname = implode('_', $normalized);
        }

        $result = $DB->get_record('tool_customlang_components', ['name' => $componentname]);

        if (!$result) {
            $this->log[] = new logstatus('notice_missingcomponent', notification::NOTIFY_ERROR, null, $componentname);
            return null;
        }
        return $result;
    }

    /**
     * Import an array of strings into the customlang tables.
     *
     * @param langstring[] $strings the langstring to set
     * @param stdClass $component the target component
     */
    private function import_strings(array $strings, stdClass $component): void {
        global $DB;

        foreach ($strings as $newstring) {
            // Check current DB entry.
            $customlang = $DB->get_record('tool_customlang', [
                'componentid' => $component->id,
                'stringid' => $newstring->id,
                'lang' => $this->lng,
            ]);
            if (!$customlang) {
                $customlang = null;
            }

            if ($this->can_save_string($customlang, $newstring, $component)) {
                $customlang->local = $newstring->text;
                $customlang->timecustomized = $newstring->timemodified;
                $customlang->outdated = 0;
                $customlang->modified = 1;
                $DB->update_record('tool_customlang', $customlang);
            }
        }
    }

    /**
     * Determine if a specific string can be saved based on the current importmode.
     *
     * @param stdClass $customlang customlang original record
     * @param langstring $newstring the new strign to store
     * @param stdClass $component the component target
     * @return bool if the string can be stored
     */
    private function can_save_string(?stdClass $customlang, langstring $newstring, stdClass $component): bool {
        $result = false;
        $message = 'notice_success';
        if (empty($customlang)) {
            $message = 'notice_inexitentstring';
            $this->log[] = new logstatus($message, notification::NOTIFY_ERROR, null, $component->name, $newstring);
            return $result;
        }

        switch ($this->importmode) {
            case self::IMPORTNEW:
                $result = empty($customlang->local);
                $warningmessage = 'notice_ignoreupdate';
                break;
            case self::IMPORTUPDATE:
                $result = !empty($customlang->local);
                $warningmessage = 'notice_ignorenew';
                break;
            case self::IMPORTALL:
                $result = true;
                break;
        }
        if ($result) {
            $errorlevel = notification::NOTIFY_SUCCESS;
        } else {
            $errorlevel = notification::NOTIFY_ERROR;
            $message = $warningmessage;
        }
        $this->log[] = new logstatus($message, $errorlevel, null, $component->name, $newstring);

        return $result;
    }
}
