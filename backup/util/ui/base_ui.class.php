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
 * This file contains the backup user interface class
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is the backup user interface class
 *
 * The backup user interface class manages the user interface and backup for
 * Moodle.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_ui {
    /**
     * The progress of this instance of the backup ui class
     */
    const PROGRESS_INTIAL = 0;
    const PROGRESS_PROCESSED = 1;
    const PROGRESS_SAVED = 2;
    const PROGRESS_EXECUTED = 3;
    /**
     * The controller
     * @var backup_controller|restore_controller
     */
    protected $controller;
    /**
     * The current stage
     * @var base_ui_stage
     */
    protected $stage;
    /**
     * The current progress of the UI
     * @var int One of self::PROGRESS_*
     */
    protected $progress;
    /**
     * The number of changes made by dependency enforcement
     * @var int
     */
    protected $dependencychanges = 0;

    /**
     * Yay for constructors
     * @param backup_controller $controller
     */
    public function __construct($controller, array $params=null) {
        $this->controller = $controller;
        $this->progress = self::PROGRESS_INTIAL;
        $this->stage = $this->initialise_stage(null, $params);
        // Process UI event before to be safe
        $this->controller->process_ui_event();
    }
    /**
     * Destorys the backup controller and the loaded stage.
     */
    public function destroy() {

        if ($this->controller) {
            $this->controller->destroy();
        }
        unset($this->stage);

    }
    /**
     * Intialises what ever stage is requested. If none are requested we check
     * params for 'stage' and default to initial
     *
     * @param int|null $stage The desired stage to intialise or null for the default
     * @return base_ui_stage
     */
    abstract protected function initialise_stage($stage = null, array $params=null);
    /**
     * This processes the current stage of the backup
     * @return bool
     */
    public function process() {
        if ($this->progress >= self::PROGRESS_PROCESSED) {
            throw new backup_ui_exception('backupuialreadyprocessed');
        }
        $this->progress = self::PROGRESS_PROCESSED;

        if (optional_param('previous', false, PARAM_BOOL) && $this->stage->get_stage() > $this->get_first_stage_id()) {
            $this->stage = $this->initialise_stage($this->stage->get_prev_stage(), $this->stage->get_params());
            return false;
        }

        // Process the stage
        $processoutcome = $this->stage->process();

        if ($processoutcome !== false) {
            $this->stage = $this->initialise_stage($this->stage->get_next_stage(), $this->stage->get_params());
        }

        // Process UI event after to check changes are valid
        $this->controller->process_ui_event();
        return $processoutcome;
    }
    /**
     * Saves the backup controller.
     *
     * Once this has been called nothing else can be changed in the controller.
     *
     * @return bool
     */
    public function save_controller() {
        if ($this->progress >= self::PROGRESS_SAVED) {
            throw new base_ui_exception('backupuialreadysaved');
        }
        $this->progress = self::PROGRESS_SAVED;
        // First enforce dependencies
        $this->enforce_dependencies();
        // Process UI event after to check any changes are valid
        $this->controller->process_ui_event();
        // Save the controller
        $this->controller->save_controller();
        return true;
    }
    /**
     * Displays the UI for the backup!
     *
     * Note: The UI makes use of mforms (ewww!) thus it will automatically print
     * out the result rather than returning a string of HTML like other parts of Moodle
     *
     * @return bool
     */
    public function display() {
        if ($this->progress < self::PROGRESS_SAVED) {
            throw new base_ui_exception('backupsavebeforedisplay');
        }
        $this->stage->display();
    }
    /**
     * Gets all backup tasks from the controller
     * @return array Array of backup_task
     */
    public function get_tasks() {
        $plan = $this->controller->get_plan();
        $tasks = $plan->get_tasks();
        return $tasks;
    }
    /**
     * Gets the stage we are on
     * @return int
     */
    public function get_stage() {
        return $this->stage->get_stage();
    }
    /**
     * Gets the name of the stage we are on
     * @return string
     */
    public function get_stage_name() {
        return $this->stage->get_name();
    }
    /**
     * Gets the backup id from the controller
     * @return string
     */
    abstract public function get_uniqueid();
    /**
     * Executes the backup plan
     * @return bool
     */
    abstract public function execute();
    /**
     * Enforces dependencies on all settings. Call before save
     * @return bool True if dependencies were enforced and changes were made
     */
    protected function enforce_dependencies() {
        // Get the plan
        $plan = $this->controller->get_plan();
        // Get the tasks as a var so we can iterate by reference
        $tasks = $plan->get_tasks();
        $changes = 0;
        foreach ($tasks as &$task) {
            // Store as a var so we can iterate by reference
            $settings = $task->get_settings();
            foreach ($settings as &$setting) {
                // Get all dependencies for iteration by reference
                $dependencies = $setting->get_dependencies();
                foreach ($dependencies as &$dependency) {
                    // Enforce each dependency
                    if ($dependency->enforce()) {
                        $changes++;
                    }
                }
            }
        }
        // Store the number of settings that changed through enforcement
        $this->dependencychanges = $changes;
        return ($changes>0);
    }
    /**
     * Returns true if enforce_dependencies changed any settings
     * @return bool
     */
    public function enforce_changed_dependencies() {
        return ($this->dependencychanges > 0);
    }
    /**
     * Loads the backup controller if we are tracking one
     * @return backup_controller|false
     */
    public static function load_controller($uniqueid=false) {
        throw new coding_exception('load_controller() method needs to be overridden in each subclass of base_ui');
    }

    /**
     * Cancels the current backup/restore and redirects the user back to the relevant place
     */
    public function cancel_process() {
        global $PAGE;
        // Determine the appropriate URL to redirect the user to
        if ($PAGE->context->contextlevel == CONTEXT_MODULE && $PAGE->cm !== null) {
            $relevanturl = new moodle_url('/mod/'.$PAGE->cm->modname.'/view.php', array('id'=>$PAGE->cm->id));
        } else {
            $relevanturl = new moodle_url('/course/view.php', array('id'=>$PAGE->course->id));
        }
        redirect($relevanturl);
    }

    /**
     * Gets an array of progress bar items that can be displayed through the backup renderer.
     * @return array Array of items for the progress bar
     */
    abstract public function get_progress_bar();
    /**
     * Gets the format for the backup
     * @return int
     */
    public function get_format() {
        return $this->controller->get_format();
    }
    /**
     * Gets the type of the backup
     * @return int
     */
    public function get_type() {
        return $this->controller->get_type();
    }
    public function get_controller() {
        return $this->controller;
    }
    /**
     * Gets the ID used in creating the controller. Relates to course/section/cm
     * @return int
     */
    public function get_controller_id() {
        return $this->controller->get_id();
    }
    /**
     * Gets the requested setting
     * @param string $name
     * @return mixed
     */
    public function get_setting($name, $default = false) {
        try {
            return $this->controller->get_plan()->get_setting($name);
        } catch (Exception $e) {
            debugging('Failed to find the setting: '.$name, DEBUG_DEVELOPER);
            return $default;
        }
    }
    /**
     * Gets the value for the requested setting
     *
     * @param string $name
     * @return mixed
     */
    public function get_setting_value($name, $default = false) {
        try {
            return $this->controller->get_plan()->get_setting($name)->get_value();
        } catch (Exception $e) {
            debugging('Failed to find the setting: '.$name, DEBUG_DEVELOPER);
            return $default;
        }
    }

    abstract public function get_name();

    abstract public function get_first_stage_id();
}

/**
 * Backup user interface exception. Modelled off the backup_exception class
 */
class base_ui_exception extends backup_exception {}