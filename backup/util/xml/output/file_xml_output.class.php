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
 * @subpackage backup-xml
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class implements one @xml_output able to send contents to one OS file
 *
 * Buffering enabled by default (can be disabled)
 *
 * TODO: Finish phpdocs
 */
class file_xml_output extends xml_output {

    protected $fullpath; // Full path to OS file where contents will be stored
    protected $fhandle;  // File handle where all write operations happen

    public function __construct($fullpath, $usebuffer = true) {
        $this->fullpath = $fullpath;
        parent::__construct($usebuffer);
    }

// Private API starts here

    protected function init() {
        if (!file_exists(dirname($this->fullpath))) {
            throw new xml_output_exception('directory_not_exists', dirname($this->fullpath));
        }
        if (file_exists($this->fullpath)) {
            throw new xml_output_exception('file_already_exists', $this->fullpath);
        }
        if (!is_writable(dirname($this->fullpath))) {
            throw new xml_output_exception('directory_not_writable', dirname($this->fullpath));
        }
        // Open the OS file for writing
        if (! $this->fhandle = fopen($this->fullpath, 'w')) {
            throw new xml_output_exception('error_opening_file');
        }
    }

    protected function finish() {
        if (false === fclose($this->fhandle)) {
            throw new xml_output_exception('error_closing_file');
        }
    }

    protected function send($content) {
        if (false === fwrite($this->fhandle, $content)) {
            throw new xml_output_exception('error_writing_file');
        }
    }
}
