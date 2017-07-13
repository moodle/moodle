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
 * @subpackage backup-helper
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * This class is one varying singleton that, for all the logs corresponding to
 * one task, is in charge of storing all its {@link restore_log_rule} rules,
 * dispatching to the correct one and insert/log the resulting information.
 *
 * Each time the task getting the instance changes, the rules are completely
 * reloaded with the ones in the new task. And all rules are informed with
 * new fixed values if explicity set.
 *
 * This class adopts the singleton pattern to be able to provide some persistency
 * of rules along the restore of all the logs corresponding to one restore_task
 */
class restore_logs_processor {

    private static $instance; // The current instance of restore_logs_processor
    private static $task;     // The current restore_task instance this processor belongs to
    private $rules;           // Array of restore_log_rule rules (module-action being keys), supports multiple per key

    private function __construct($values) { // Private constructor

        // Constructor has been called, so we need to reload everything
        // Process rules
        $this->rules = array();
        $rules = call_user_func(array(self::$task, 'define_restore_log_rules'));
        foreach ($rules as $rule) {
            // TODO: Check it is one restore_log_rule

            // Set rule restoreid
            $rule->set_restoreid(self::$task->get_restoreid());
            // Set rule fixed values if needed
            if (is_array($values) and !empty($values)) {
                $rule->set_fixed_values($values);
            }
            // Add the rule to the associative array
            if (array_key_exists($rule->get_key_name(), $this->rules)) {
                $this->rules[$rule->get_key_name()][] = $rule;
            } else {
                $this->rules[$rule->get_key_name()] = array($rule);
            }
        }
    }

    public static function get_instance($task, $values) {
        // If the singleton isn't set or if the task is another one, create new instance
        if (!isset(self::$instance) || self::$task !== $task) {
            self::$task = $task;
            self::$instance = new restore_logs_processor($values);
        }
        return self::$instance;
    }

    public function process_log_record($log) {
        // Check we have one restore_log_rule for this log record
        $keyname = $log->module . '-' . $log->action;
        if (array_key_exists($keyname, $this->rules)) {
            // Try it for each rule available
            foreach ($this->rules[$keyname] as $rule) {
                $newlog = $rule->process($log);
                // Some rule has been able to perform the conversion, exit from loop
                if (!empty($newlog)) {
                    break;
                }
            }
            // Arrived here log is empty, no rule was able to perform the conversion, log the problem
            if (empty($newlog)) {
                self::$task->log('Log module-action "' . $keyname . '" process problem. Not restored. ' .
                    json_encode($log), backup::LOG_DEBUG);
            }

        } else { // Action not found log the problem
            self::$task->log('Log module-action "' . $keyname . '" unknown. Not restored. '.json_encode($log), backup::LOG_DEBUG);
            $newlog = false;

        }
        return $newlog;
    }

    /**
     * Adds all the activity {@link restore_log_rule} rules
     * defined in activity task but corresponding to log
     * records at course level (cmid = 0).
     */
    public static function register_log_rules_for_course() {
        $tasks = array(); // To get the list of tasks having log rules for course
        $rules = array(); // To accumulate rules for course

        // Add the module tasks
        $mods = core_component::get_plugin_list('mod');
        foreach ($mods as $mod => $moddir) {
            if (class_exists('restore_' . $mod . '_activity_task')) {
                $tasks[$mod] = 'restore_' . $mod . '_activity_task';
            }
        }

        foreach ($tasks as $mod => $classname) {
            if (!method_exists($classname, 'define_restore_log_rules_for_course')) {
                continue; // This method is optional
            }
            // Get restore_log_rule array and accumulate
            $taskrules = call_user_func(array($classname, 'define_restore_log_rules_for_course'));
            if (!is_array($taskrules)) {
                throw new restore_logs_processor_exception('define_restore_log_rules_for_course_not_array', $classname);
            }
            $rules = array_merge($rules, $taskrules);
        }
        return $rules;
    }
}

/*
 * Exception class used by all the @restore_logs_processor stuff
 */
class restore_logs_processor_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, $a, $debuginfo);
    }
}
