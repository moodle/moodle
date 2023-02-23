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
 * Implementable class defining the needed stuf for one backup plan
 *
 * TODO: Finish phpdocs
 */
class backup_plan extends base_plan implements loggable {

    protected $controller; // The backup controller building/executing this plan
    protected $basepath;   // Fullpath to dir where backup is created
    protected $excludingdactivities;

    /**
     * The role ids to keep in a copy operation.
     * @var array
     */
    protected $keptroles = array();

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($controller) {
        if (! $controller instanceof backup_controller) {
            throw new backup_plan_exception('wrong_backup_controller_specified');
        }
        $backuptempdir    = make_backup_temp_directory('');
        $this->controller = $controller;
        $this->basepath   = $backuptempdir . '/' . $controller->get_backupid();
        $this->excludingdactivities = false;
        parent::__construct('backup_plan');
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
        backup_factory::build_plan($this->controller); // Dispatch to correct format
        $this->built = true;
    }

    public function get_backupid() {
        return $this->controller->get_backupid();
    }

    public function get_type() {
        return $this->controller->get_type();
    }

    public function get_mode() {
        return $this->controller->get_mode();
    }

    public function get_courseid() {
        return $this->controller->get_courseid();
    }

    public function get_basepath() {
        return $this->basepath;
    }

    public function get_logger() {
        return $this->controller->get_logger();
    }

    /**
     * Gets the progress reporter, which can be used to report progress within
     * the backup or restore process.
     *
     * @return \core\progress\base Progress reporting object
     */
    public function get_progress() {
        return $this->controller->get_progress();
    }

    public function is_excluding_activities() {
        return $this->excludingdactivities;
    }

    public function set_excluding_activities() {
        $this->excludingdactivities = true;
    }

    /**
     * Sets the user roles that should be kept in the destination course
     * for a course copy operation.
     *
     * @param array $roleids
     */
    public function set_kept_roles(array $roleids): void {
        $this->keptroles = $roleids;
    }

    /**
     * Get the user roles that should be kept in the destination course
     * for a course copy operation.
     *
     * @return array
     */
    public function get_kept_roles(): array {
        return $this->keptroles;
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        backup_helper::log($message, $level, $a, $depth, $display, $this->get_logger());
    }

    /**
     * Function responsible for executing the tasks of any plan
     */
    public function execute() {
        if ($this->controller->get_status() != backup::STATUS_AWAITING) {
            throw new backup_controller_exception('backup_not_executable_awaiting_required', $this->controller->get_status());
        }
        $this->controller->set_status(backup::STATUS_EXECUTING);
        parent::execute();
        $this->controller->set_status(backup::STATUS_FINISHED_OK);

        if ($this->controller->get_type() === backup::TYPE_1COURSE) {
            // Trigger a course_backup_created event.
            $otherarray = array('format' => $this->controller->get_format(),
                                'mode' => $this->controller->get_mode(),
                                'interactive' => $this->controller->get_interactive(),
                                'type' => $this->controller->get_type(),
                                'backupid' => $this->controller->get_backupid()
            );
            $event = \core\event\course_backup_created::create(array(
                'objectid' => $this->get_courseid(),
                'context' => context_course::instance($this->get_courseid()),
                'other' => $otherarray
            ));
            $event->trigger();
        }
    }
}

/*
 * Exception class used by all the @backup_plan stuff
 */
class backup_plan_exception extends base_plan_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
