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
 * Abstract class defining the basis for one execution (backup/restore) task
 *
 * TODO: Finish phpdocs
 */
abstract class base_task implements checksumable, executable, loggable {

    /** @var string */
    protected $name;      // One simple name for identification purposes
    /** @var backup_plan|restore_plan */
    protected $plan;      // Plan this is part of
    /** @var base_setting[] */
    protected $settings;  // One array of base_setting elements to define this task
    /** @var base_step[] */
    protected $steps;     // One array of base_step elements
    /** @var bool */
    protected $built;     // Flag to know if one task has been built
    /** @var bool */
    protected $executed;  // Flag to know if one task has been executed

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $plan = null) {
        if (!is_null($plan) && !($plan instanceof base_plan)) {
            throw new base_task_exception('wrong_base_plan_specified');
        }
        $this->name = $name;
        $this->plan = $plan;
        $this->settings = array();
        $this->steps    = array();
        $this->built    = false;
        $this->executed = false;
        if (!is_null($plan)) { // Add the task to the plan if specified
            $plan->add_task($this);
        }
    }

    public function get_name() {
        return $this->name;
    }

    public function get_steps() {
        return $this->steps;
    }

    public function get_settings() {
        return $this->settings;
    }

    /**
     * Returns the weight of this task, an approximation of the amount of time
     * it will take. By default this value is 1. It can be increased for longer
     * tasks.
     *
     * @return int Weight
     */
    public function get_weight() {
        return 1;
    }

    public function get_setting($name) {
        // First look in task settings
        $result = null;
        foreach ($this->settings as $key => $setting) {
            if ($setting->get_name() == $name) {
                if ($result != null) {
                    throw new base_task_exception('multiple_settings_by_name_found', $name);
                } else {
                    $result = $setting;
                }
            }
        }
        if ($result) {
            return $result;
        } else {
            // Fallback to plan settings
            return $this->plan->get_setting($name);
        }
    }

    public function setting_exists($name) {
        return $this->plan->setting_exists($name);
    }

    public function get_setting_value($name) {
        return $this->get_setting($name)->get_value();
    }

    public function get_courseid() {
        return $this->plan->get_courseid();
    }

    public function get_basepath() {
        return $this->plan->get_basepath();
    }

    public function get_taskbasepath() {
        return $this->get_basepath();
    }

    public function get_logger() {
        return $this->plan->get_logger();
    }

    /**
     * Gets the progress reporter, which can be used to report progress within
     * the backup or restore process.
     *
     * @return \core\progress\base Progress reporting object
     */
    public function get_progress() {
        return $this->plan->get_progress();
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        backup_helper::log($message, $level, $a, $depth, $display, $this->get_logger());
    }

    public function add_step($step) {
        if (! $step instanceof base_step) {
            throw new base_task_exception('wrong_base_step_specified');
        }
        // link the step with the task
        $step->set_task($this);
        $this->steps[] = $step;
    }

    public function set_plan($plan) {
        if (! $plan instanceof base_plan) {
            throw new base_task_exception('wrong_base_plan_specified');
        }
        $this->plan = $plan;
        $this->define_settings(); // Settings are defined when plan & task are linked
    }

    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    abstract public function build();

    /**
     * Function responsible for executing the steps of any task
     * (setting the $executed property to  true)
     */
    public function execute() {
        if (!$this->built) {
            throw new base_task_exception('base_task_not_built', $this->name);
        }
        if ($this->executed) {
            throw new base_task_exception('base_task_already_executed', $this->name);
        }

        // Starts progress based on the weight of this task and number of steps.
        $progress = $this->get_progress();
        $progress->start_progress($this->get_name(), count($this->steps), $this->get_weight());
        $done = 0;

        // Execute all steps.
        foreach ($this->steps as $step) {
            $result = $step->execute();
            // If step returns array, it will be forwarded to plan
            // (TODO: shouldn't be array but proper result object)
            if (is_array($result) and !empty($result)) {
                $this->add_result($result);
            }
            $done++;
            $progress->progress($done);
        }
        // Mark as executed if any step has been executed
        if (!empty($this->steps)) {
            $this->executed = true;
        }

        // Finish progress for this task.
        $progress->end_progress();
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // Before reseting anything, call destroy recursively
        foreach ($this->steps as $step) {
            $step->destroy();
        }
        foreach ($this->settings as $setting) {
            $setting->destroy();
        }
        // Everything has been destroyed recursively, now we can reset safely
        $this->steps = array();
        $this->settings = array();
        $this->plan = null;
    }

    public function is_checksum_correct($checksum) {
        return $this->calculate_checksum() === $checksum;
    }

    public function calculate_checksum() {
        // Let's do it using name and settings and steps
        return md5($this->name . '-' .
                   backup_general_helper::array_checksum_recursive($this->settings) .
                   backup_general_helper::array_checksum_recursive($this->steps));
    }

    /**
     * Add the given info to the current plan's results.
     *
     * @see base_plan::add_result()
     * @param array $result associative array describing a result of a task/step
     */
    public function add_result($result) {
        if (!is_null($this->plan)) {
            $this->plan->add_result($result);
        } else {
            debugging('Attempting to add a result of a task not binded with a plan', DEBUG_DEVELOPER);
        }
    }

    /**
     * Return the current plan's results
     *
     * @return array|null
     */
    public function get_results() {
        if (!is_null($this->plan)) {
            return $this->plan->get_results();
        } else {
            debugging('Attempting to get results of a task not binded with a plan', DEBUG_DEVELOPER);
            return null;
        }
    }

// Protected API starts here

    /**
     * This function is invoked on activity creation in order to add all the settings
     * that are associated with one task. The function will, directly, inject the settings
     * in the task.
     */
    abstract protected function define_settings();

    protected function add_setting($setting) {
        if (! $setting instanceof base_setting) {
            throw new base_setting_exception('wrong_base_setting_specified');
        }
        $this->settings[] = $setting;
    }
}

/*
 * Exception class used by all the @base_task stuff
 */
class base_task_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
