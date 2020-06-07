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
 * Abstract class defining the needed stuf for one backup task (a collection of steps)
 *
 * TODO: Finish phpdocs
 */
abstract class backup_task extends base_task {

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $plan = null) {
        if (!is_null($plan) && !($plan instanceof backup_plan)) {
            throw new backup_task_exception('wrong_backup_plan_specified');
        }
        parent::__construct($name, $plan);
    }

    public function get_backupid() {
        return $this->plan->get_backupid();
    }

    public function is_excluding_activities() {
        return $this->plan->is_excluding_activities();
    }

    /**
     * Get the user roles that should be kept in the destination course
     * for a course copy operation.
     *
     * @return array
     */
    public function get_kept_roles(): array {
        return $this->plan->get_kept_roles();
    }
}

/*
 * Exception class used by all the @backup_task stuff
 */
class backup_task_exception extends base_task_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
