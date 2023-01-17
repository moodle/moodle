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
 * Abstract class defining the needed stuf for one restore task (a collection of steps)
 *
 * TODO: Finish phpdocs
 */
abstract class restore_task extends base_task {

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $plan = null) {
        if (!is_null($plan) && !($plan instanceof restore_plan)) {
            throw new restore_task_exception('wrong_restore_plan_specified');
        }
        parent::__construct($name, $plan);
    }

    public function get_restoreid() {
        return $this->plan->get_restoreid();
    }

    public function get_info() {
        return $this->plan->get_info();
    }

    public function get_target() {
        return $this->plan->get_target();
    }

    public function get_userid() {
        return $this->plan->get_userid();
    }

    public function get_decoder() {
        return $this->plan->get_decoder();
    }

    public function is_samesite() {
        return $this->plan->is_samesite();
    }

    public function is_missing_modules() {
        return $this->plan->is_missing_modules();
    }

    public function is_excluding_activities() {
        return $this->plan->is_excluding_activities();
    }

    public function set_preloaded_information() {
        $this->plan->set_preloaded_information();
    }

    public function get_preloaded_information() {
        return $this->plan->get_preloaded_information();
    }

    public function get_tempdir() {
        return $this->plan->get_tempdir();
    }

    public function get_old_courseid() {
        return $this->plan->get_info()->original_course_id;
    }

    public function get_old_contextid() {
        return $this->plan->get_info()->original_course_contextid;
    }

    public function get_old_system_contextid() {
        return $this->plan->get_info()->original_system_contextid;
    }

    /**
     * Given a commment area, return the itemname that contains the itemid mappings
     *
     * By default, both are the same (commentarea = itemname), so return it. If some
     * plugins use a different approach, this method can be overriden in its task.
     *
     * @param string $commentarea area defined for this comment
     * @return string itemname that contains the related itemid mapping
     */
    public function get_comment_mapping_itemname($commentarea) {
        return $commentarea;
    }

    /**
     * If the task has been executed, launch its after_restore()
     * method if available
     */
    public function execute_after_restore() {
        if ($this->executed) {
            foreach ($this->steps as $step) {
                if (method_exists($step, 'launch_after_restore_methods')) {
                    $step->launch_after_restore_methods();
                }
            }
        }
        if ($this->executed && method_exists($this, 'after_restore')) {
            $this->after_restore();
        }
    }
}

/*
 * Exception class used by all the @restore_task stuff
 */
class restore_task_exception extends base_task_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
