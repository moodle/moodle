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
 * @package moodlecore
 * @subpackage backup-plan
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining the basis for one execution (backup/restore) step
 *
 * TODO: Finish phpdocs
 */
abstract class base_step implements executable, loggable {

    protected $name;      // One simple name for identification purposes
    protected $task;      // Task this is part of

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $task = null) {
        if (!is_null($task) && !($task instanceof base_task)) {
            throw new base_step_exception('wrong_base_task_specified');
        }
        $this->name = $name;
        $this->task = $task;
        if (!is_null($task)) { // Add the step to the task if specified
            $task->add_step($this);
        }
    }

    public function get_name() {
        return $this->name;
    }

    public function set_task($task) {
        if (! $task instanceof base_task) {
            throw new base_step_exception('wrong_base_task_specified');
        }
        $this->task = $task;
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // No need to destroy anything recursively here, direct reset
        $this->task = null;
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        backup_helper::log($message, $level, $a, $depth, $display, $this->get_logger());
    }

/// Protected API starts here

    protected function get_settings() {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        return $this->task->get_settings();
    }

    protected function get_setting($name) {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        return $this->task->get_setting($name);
    }

    protected function setting_exists($name) {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        return $this->task->setting_exists($name);
    }

    protected function get_setting_value($name) {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        return $this->task->get_setting_value($name);
    }

    protected function get_courseid() {
        if (is_null($this->task)) {
            throw new base_step_exception('not_specified_base_task');
        }
        return $this->task->get_courseid();
    }

    protected function get_basepath() {
        return $this->task->get_basepath();
    }

    protected function get_logger() {
        return $this->task->get_logger();
    }
}


/*
 * Exception class used by all the @base_step stuff
 */
class base_step_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
