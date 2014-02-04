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
 * Abstract class defining the basis for one execution (backup/restore) plan
 *
 * TODO: Finish phpdocs
 */
abstract class base_plan implements checksumable, executable {

    protected $name;      // One simple name for identification purposes
    protected $settings;  // One array of (accumulated from tasks) base_setting elements
    protected $tasks;     // One array of base_task elements
    protected $results;   // One array of results received from tasks

    protected $built;     // Flag to know if one plan has been built

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name) {
        $this->name = $name;
        $this->settings = array();
        $this->tasks    = array();
        $this->results  = array();
        $this->built = false;
    }

    public function get_name() {
        return $this->name;
    }

    public function add_task($task) {
        if (! $task instanceof base_task) {
            throw new base_plan_exception('wrong_base_task_specified');
        }
        $this->tasks[] = $task;
        // link the task with the plan
        $task->set_plan($this);
        // Append task settings to plan array, if not present, for comodity
        foreach ($task->get_settings() as $key => $setting) {
            // Check if there is already a setting for this name.
            $name = $setting->get_name();
            if (!isset($this->settings[$name])) {
                // There is no setting, so add it.
                $this->settings[$name] = $setting;
            } else if ($this->settings[$name] != $setting) {
                // If the setting already exists AND it is not the same setting,
                // then throw an error. (I.e. you're allowed to add the same
                // setting twice, but cannot add two different ones with same
                // name.)
                throw new base_plan_exception('multiple_settings_by_name_found', $name);
            }
        }
    }

    public function get_tasks() {
        return $this->tasks;
    }

    /**
     * Add the passed info to the plan results
     *
     * At the moment we expect an associative array structure to be merged into
     * the current results. In the future, some sort of base_result class may
     * be introduced.
     *
     * @param array $result associative array describing a result of a task/step
     */
    public function add_result($result) {
        if (!is_array($result)) {
            throw new coding_exception('Associative array is expected as a parameter of add_result()');
        }
        $this->results = array_merge($this->results, $result);
    }

    /**
     * Return the results collected via {@link self::add_result()} method
     *
     * @return array
     */
    public function get_results() {
        return $this->results;
    }

    public function get_settings() {
        return $this->settings;
    }

    /**
     * return one setting by name, useful to request root/course settings
     * that are, by definition, unique by name.
     *
     * @param string $name name of the setting
     * @throws base_plan_exception if setting name is not found.
     */
    public function get_setting($name) {
        $result = null;
        if (isset($this->settings[$name])) {
            $result = $this->settings[$name];
        } else {
            throw new base_plan_exception('setting_by_name_not_found', $name);
        }
        return $result;
    }

    /**
     * Wrapper over @get_setting() that returns if the requested setting exists or no
     */
    public function setting_exists($name) {
        try {
            $this->get_setting($name);
            return true;
        } catch (base_plan_exception $e) {
            // Nothing to do
        }
        return false;
    }


    /**
     * Function responsible for building the tasks of any plan
     * with their corresponding settings
     * (must set the $built property to true)
     */
    public abstract function build();

    public function is_checksum_correct($checksum) {
        return $this->calculate_checksum() === $checksum;
    }

    public function calculate_checksum() {
        // Let's do it using name and tasks (settings are part of tasks)
        return md5($this->name . '-' . backup_general_helper::array_checksum_recursive($this->tasks));
    }

    /**
     * Function responsible for executing the tasks of any plan
     */
    public function execute() {
        if (!$this->built) {
            throw new base_plan_exception('base_plan_not_built');
        }

        // Calculate the total weight of all tasks and start progress tracking.
        $progress = $this->get_progress();
        $totalweight = 0;
        foreach ($this->tasks as $task) {
            $totalweight += $task->get_weight();
        }
        $progress->start_progress($this->get_name(), $totalweight);

        // Build and execute all tasks.
        foreach ($this->tasks as $task) {
            $task->build();
            $task->execute();
        }

        // Finish progress tracking.
        $progress->end_progress();
    }

    /**
     * Gets the progress reporter, which can be used to report progress within
     * the backup or restore process.
     *
     * @return \core\progress\base Progress reporting object
     */
    public abstract function get_progress();

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // Before reseting anything, call destroy recursively
        foreach ($this->tasks as $task) {
            $task->destroy();
        }
        foreach ($this->settings as $setting) {
            $setting->destroy();
        }
        // Everything has been destroyed recursively, now we can reset safely
        $this->tasks = array();
        $this->settings = array();
    }
}


/*
 * Exception class used by all the @base_plan stuff
 */
class base_plan_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
