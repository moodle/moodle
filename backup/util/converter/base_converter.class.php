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
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base Abstract Converter
 *
 * @throws backup_exception|Exception|null
 */
abstract class base_converter {

    protected $id;
    protected $tempdir;
    protected $convertdir;

    // do we want absolute path instead of tempdir?
    // Do we need to create a new tempdir to convert into?  EG: target...
    public function __construct($tempdir) {
        $this->tempdir    = $tempdir;
        $this->convertdir = $this->tempdir.'_'.$this->get_name();
        $this->id         = convert_helper::generate_id($this->convertdir);
        $this->init();
    }

    public function init() {
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return array_shift(explode('_', get_class($this)));
    }

    public function get_convertdir() {
        global $CFG;

        return "$CFG->dataroot/temp/backup/$this->convertdir";
    }

    public function get_tempdir() {
        global $CFG;

        return "$CFG->dataroot/temp/backup/$this->tempdir";
    }

    public function delete_convertdir() {
        fulldelete($this->get_convertdir());
    }

    public function create_convertdir() {
        $this->delete_convertdir();
        if (!check_dir_exists($this->get_convertdir())) {
            throw new backup_exception('failedtomakeconvertdir'); // @todo Define this string
        }
    }

    public function replace_tempdir() {
        fulldelete($this->get_tempdir());

        if (!rename($this->get_convertdir(), $this->get_tempdir())) {
            throw new backup_exception('failedmoveconvertedintoplace'); // @todo Define this string
        }
    }

    /**
     * @abstract
     * @return boolean
     */
    abstract public function can_convert();

    // Kicks things off
    public function convert() {

        $e = NULL;

        try {
            $this->create_convertdir();
            $this->execute();
            $this->replace_tempdir();
        } catch (Exception $e) {
        }
        // Do cleanup...
        $this->destroy();

        if ($e instanceof Exception) {
            throw $e;
        }
    }

    abstract public function execute();

    public function destroy() {
        $this->delete_convertdir();
    }
}
