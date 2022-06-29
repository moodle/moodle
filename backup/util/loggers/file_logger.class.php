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
 * @subpackage backup-logger
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Logger implementation that sends indented messages (depth option) to one file
 *
 * TODO: Finish phpdocs
 */
class file_logger extends base_logger {

    protected $fullpath; // Full path to OS file where contents will be stored
    protected $fhandle;  // File handle where all write operations happen

    public function __construct($level, $showdate = false, $showlevel = false, $fullpath = null) {
        if (empty($fullpath)) {
            throw new base_logger_exception('missing_fullpath_parameter', $fullpath);
        }
        if (!is_writable(dirname($fullpath))) {
            throw new base_logger_exception('file_not_writable', $fullpath);
        }
        // Open the OS file for writing (append)
        $this->fullpath = $fullpath;
        if ($level > backup::LOG_NONE) { // Only create the file if we are going to log something
            if (! $this->fhandle = fopen($this->fullpath, 'a')) {
                throw new base_logger_exception('error_opening_file', $fullpath);
            }
        }
        parent::__construct($level, $showdate, $showlevel);
    }

    public function __destruct() {
        if (is_resource($this->fhandle)) {
            // Blindy close the file handler (no exceptions in destruct).
            @fclose($this->fhandle);
        }
    }

    public function __sleep() {
        if (is_resource($this->fhandle)) {
            // Blindy close the file handler before serialization.
            @fclose($this->fhandle);
            $this->fhandle = null;
        }
        return array('level', 'showdate', 'showlevel', 'next', 'fullpath');
    }

    public function __wakeup() {
        if ($this->level > backup::LOG_NONE) { // Only create the file if we are going to log something
            if (! $this->fhandle = fopen($this->fullpath, 'a')) {
                throw new base_logger_exception('error_opening_file', $this->fullpath);
            }
        }
    }

    /**
     * Close the logger resources (file handle) if still open.
     *
     * @since Moodle 3.1
     */
    public function close() {
        // Close the file handle if hasn't been closed already.
        if (is_resource($this->fhandle)) {
            fclose($this->fhandle);
            $this->fhandle = null;
        }
    }

// Protected API starts here

    protected function action($message, $level, $options = null) {
        $prefix = $this->get_prefix($level, $options);
        $depth = isset($options['depth']) ? $options['depth'] : 0;
        // Depending of the type (extension of the file), format differently
        if (substr($this->fullpath, -5) !== '.html') {
            $content = $prefix . str_repeat('  ', $depth) . $message . PHP_EOL;
        } else {
            $content = $prefix . str_repeat('&nbsp;&nbsp;', $depth) . htmlentities($message, ENT_QUOTES, 'UTF-8') . '<br/>' . PHP_EOL;
        }
        if (!is_resource($this->fhandle) || (false === fwrite($this->fhandle, $content))) {
            throw new base_logger_exception('error_writing_file', $this->fullpath);
        }
        return true;
    }
}
