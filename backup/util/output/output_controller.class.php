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
 * @subpackage backup-output
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * This class decides, based in environment/backup controller settings about
 * the best way to send information to output, independently of the process
 * and the loggers. Instantiated/configured by @backup_controller constructor
 *
 * Mainly used by backup_helper::log() (that receives all the log requests from
 * the rest of backup objects) to split messages both to loggers and to output.
 *
 * This class adopts the singleton pattern to be able to provide some persistency
 * and global access.
 */
class output_controller {

    private static $instance; // The unique instance of output_controller available along the request
    private $list;            // progress_trace object we are going to use for output
    private $active;          // To be able to stop output completely or active it again

    private function __construct() { // Private constructor
        if (defined('STDOUT')) { // text mode
            $this->list = new text_progress_trace();
        } else {
            $this->list = new html_list_progress_trace();
        }
        $this->active = false; // Somebody has to active me before outputing anything
    }

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new output_controller();
        }
        return self::$instance;
    }

    public function set_active($active) {
        if ($this->active && (bool)$active == false) { // Stopping, call finished()
            $this->list->finished();
        }
        $this->active = (bool)$active;
    }

    public function output($message, $langfile, $a, $depth) {
        if ($this->active) {
            $stringkey = preg_replace('/\s/', '', $message); // String key is message without whitespace
            $message = get_string($stringkey, $langfile, $a);
            $this->list->output($message, $depth);
        }
    }
}
