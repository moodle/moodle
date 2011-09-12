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
 * Implementable class defining the needed stuf for one restore plan
 *
 * TODO: Finish phpdocs
 */
class restore_plan extends base_plan implements loggable {

    /**
     *
     * @var restore_controller
     */
    protected $controller; // The restore controller building/executing this plan
    protected $basepath;   // Fullpath to dir where backup is available
    protected $preloaded;  // When executing the plan, do we have preloaded (from checks) info
    protected $decoder;    // restore_decode_processor in charge of decoding all the interlinks
    protected $missingmodules; // to flag if restore has detected some missing module
    protected $excludingdactivities; // to flag if restore settings are excluding any activity

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($controller) {
        global $CFG;

        if (! $controller instanceof restore_controller) {
            throw new restore_plan_exception('wrong_restore_controller_specified');
        }
        $this->controller = $controller;
        $this->basepath   = $CFG->tempdir . '/backup/' . $controller->get_tempdir();
        $this->preloaded  = false;
        $this->decoder    = new restore_decode_processor($this->get_restoreid(), $this->get_info()->original_wwwroot, $CFG->wwwroot);
        $this->missingmodules = false;
        $this->excludingdactivities = false;

        parent::__construct('restore_plan');
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // No need to destroy anything recursively here, direct reset
        $this->controller = null;
        // Delegate to base plan the rest
        parent::destroy();
    }

    public function build() {
        restore_plan_builder::build_plan($this->controller); // We are moodle2 always, go straight to builder
        $this->built = true;
    }

    public function get_restoreid() {
        return $this->controller->get_restoreid();
    }

    public function get_courseid() {
        return $this->controller->get_courseid();
    }

    public function get_mode() {
        return $this->controller->get_mode();
    }

    public function get_basepath() {
        return $this->basepath;
    }

    public function get_logger() {
        return $this->controller->get_logger();
    }

    public function get_info() {
        return $this->controller->get_info();
    }

    public function get_target() {
        return $this->controller->get_target();
    }

    public function get_userid() {
        return $this->controller->get_userid();
    }

    public function get_decoder() {
        return $this->decoder;
    }

    public function is_samesite() {
        return $this->controller->is_samesite();
    }

    public function is_missing_modules() {
        return $this->missingmodules;
    }

    public function is_excluding_activities() {
        return $this->excludingdactivities;
    }

    public function set_preloaded_information() {
        $this->preloaded = true;
    }

    public function get_preloaded_information() {
        return $this->preloaded;
    }

    public function get_tempdir() {
        return $this->controller->get_tempdir();
    }

    public function set_missing_modules() {
        $this->missingmodules = true;
    }

    public function set_excluding_activities() {
        $this->excludingdactivities = true;
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        backup_helper::log($message, $level, $a, $depth, $display, $this->get_logger());
    }

    /**
     * Function responsible for executing the tasks of any plan
     */
    public function execute() {
        if ($this->controller->get_status() != backup::STATUS_AWAITING) {
            throw new restore_controller_exception('restore_not_executable_awaiting_required', $this->controller->get_status());
        }
        $this->controller->set_status(backup::STATUS_EXECUTING);
        parent::execute();
        $this->controller->set_status(backup::STATUS_FINISHED_OK);
    }

    /**
     * Execute the after_restore methods of all the executed tasks in the plan
     */
    public function execute_after_restore() {
        // Simply iterate over each task in the plan and delegate to them the execution
        foreach ($this->tasks as $task) {
            $task->execute_after_restore();
        }
    }
}

/*
 * Exception class used by all the @restore_plan stuff
 */
class restore_plan_exception extends base_plan_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
