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
 * This file contains the restore user interface class
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is the restore user interface class
 *
 * The restore user interface class manages the user interface and restore for
 * Moodle.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_ui extends base_ui {
    /**
     * The stages of the restore user interface.
     */
    const STAGE_CONFIRM = 1;
    const STAGE_DESTINATION = 2;
    const STAGE_SETTINGS = 4;
    const STAGE_SCHEMA = 8;
    const STAGE_REVIEW = 16;
    const STAGE_PROCESS = 32;
    const STAGE_COMPLETE = 64;

    /**
     *
     * @var restore_ui_stage
     */
    protected $stage = null;

    /**
     * String mappings to the above stages
     * @var array
     */
    public static $stages = array(
        restore_ui::STAGE_CONFIRM       => 'confirm',
        restore_ui::STAGE_DESTINATION   => 'destination',
        restore_ui::STAGE_SETTINGS      => 'settings',
        restore_ui::STAGE_SCHEMA        => 'schema',
        restore_ui::STAGE_REVIEW        => 'review',
        restore_ui::STAGE_PROCESS       => 'process',
        restore_ui::STAGE_COMPLETE      => 'complete'
    );
    /**
     * Intialises what ever stage is requested. If none are requested we check
     * params for 'stage' and default to initial
     *
     * @param int|null $stage The desired stage to intialise or null for the default
     * @return restore_ui_stage_initial|restore_ui_stage_schema|restore_ui_stage_confirmation|restore_ui_stage_final
     */
    protected function initialise_stage($stage = null, array $params=null) {
        if ($stage == null) {
            $stage = optional_param('stage', self::STAGE_CONFIRM, PARAM_INT);
        }
        $class = 'restore_ui_stage_'.self::$stages[$stage];
        if (!class_exists($class)) {
            throw new restore_ui_exception('unknownuistage');
        }
        $stage = new $class($this, $params);
        return $stage;
    }
    /**
     * This processes the current stage of the restore
     * @return bool
     */
    public function process() {
        if ($this->progress >= self::PROGRESS_PROCESSED) {
            throw new restore_ui_exception('restoreuialreadyprocessed');
        }
        $this->progress = self::PROGRESS_PROCESSED;

        if (optional_param('previous', false, PARAM_BOOL) && $this->stage->get_stage() > self::STAGE_CONFIRM) {
            $this->stage = $this->initialise_stage($this->stage->get_prev_stage(), $this->stage->get_params());
            return false;
        }

        // Process the stage
        $processoutcome = $this->stage->process();
        if ($processoutcome !== false && !($this->get_stage()==self::STAGE_PROCESS && optional_param('substage', false, PARAM_BOOL))) {
            $this->stage = $this->initialise_stage($this->stage->get_next_stage(), $this->stage->get_params());
        }

        // Process UI event after to check changes are valid
        $this->controller->process_ui_event();
        return $processoutcome;
    }
    /**
     * Returns true if the stage is independent (not requiring a restore controller)
     * @return bool
     */
    public function is_independent() {
        return false;
    }
    /**
     * Gets the unique ID associated with this UI
     * @return string
     */
    public function get_uniqueid() {
        return $this->get_restoreid();
    }
    /**
     * Gets the restore id from the controller
     * @return string
     */
    public function get_restoreid() {
        return $this->controller->get_restoreid();
    }
    /**
     * Executes the restore plan
     * @return bool
     */
    public function execute() {
        if ($this->progress >= self::PROGRESS_EXECUTED) {
            throw new restore_ui_exception('restoreuialreadyexecuted');
        }
        if ($this->stage->get_stage() < self::STAGE_PROCESS) {
            throw new restore_ui_exception('restoreuifinalisedbeforeexecute');
        }
        if ($this->controller->get_target() == backup::TARGET_CURRENT_DELETING || $this->controller->get_target() == backup::TARGET_EXISTING_DELETING) {
            restore_dbops::delete_course_content($this->controller->get_courseid());
        }
        $this->controller->execute_plan();
        $this->progress = self::PROGRESS_EXECUTED;
        $this->stage = new restore_ui_stage_complete($this, $this->stage->get_params(), $this->controller->get_results());
        return true;
    }

    /**
     * Delete course which is created by restore process
     */
    public function cleanup() {
        $courseid = $this->controller->get_courseid();
        if ($this->is_temporary_course_created($courseid)) {
            delete_course($courseid, false);
        }
    }

    /**
     * Checks if the course is not restored fully and current controller has created it.
     * @param int $courseid id of the course which needs to be checked
     * @return bool
     */
    protected function is_temporary_course_created($courseid) {
        global $DB;
        //Check if current controller instance has created new course.
        if ($this->controller->get_target() == backup::TARGET_NEW_COURSE) {
            $results = $DB->record_exists_sql("SELECT bc.itemid
                                               FROM {backup_controllers} bc, {course} c
                                               WHERE bc.operation = 'restore'
                                                 AND bc.type = 'course'
                                                 AND bc.itemid = c.id
                                                 AND bc.itemid = ?",
                                               array($courseid)
                                             );
            return $results;
        }
        return false;
    }

    /**
     * Returns true if enforce_dependencies changed any settings
     * @return bool
     */
    public function enforce_changed_dependencies() {
        return ($this->dependencychanges > 0);
    }
    /**
     * Loads the restore controller if we are tracking one
     * @return restore_controller|false
     */
    final public static function load_controller($restoreid=false) {
        // Get the restore id optional param
        if ($restoreid) {
            try {
                // Try to load the controller with it.
                // If it fails at this point it is likely because this is the first load
                $controller = restore_controller::load_controller($restoreid);
                return $controller;
            } catch (Exception $e) {
                return false;
            }
        }
        return $restoreid;
    }
    /**
     * Initialised the requested independent stage
     *
     * @param int $stage One of self::STAGE_*
     * @param int $contextid
     * @return restore_ui_stage_confirm|restore_ui_stage_destination
     */
    final public static function engage_independent_stage($stage, $contextid) {
        if (!($stage & self::STAGE_CONFIRM + self::STAGE_DESTINATION)) {
            throw new restore_ui_exception('dependentstagerequested');
        }
        $class = 'restore_ui_stage_'.self::$stages[$stage];
        if (!class_exists($class)) {
            throw new restore_ui_exception('unknownuistage');
        }
        return new $class($contextid);
    }
    /**
     * Cancels the current restore and redirects the user back to the relevant place
     */
    public function cancel_process() {
        //Delete temporary restore course if exists.
        if ($this->controller->get_target() == backup::TARGET_NEW_COURSE) {
            $this->cleanup();
        }
        parent::cancel_process();
    }
    /**
     * wrapper of cancel_process, kept for avoiding regression.
     */
    public function cancel_restore() {
        $this->cancel_process();
    }
    /**
     * Gets an array of progress bar items that can be displayed through the restore renderer.
     * @return array Array of items for the progress bar
     */
    public function get_progress_bar() {
        global $PAGE;

        $stage = self::STAGE_COMPLETE;
        $currentstage = $this->stage->get_stage();
        $items = array();
        while ($stage > 0) {
            $classes = array('backup_stage');
            if (floor($stage/2) == $currentstage) {
                $classes[] = 'backup_stage_next';
            } else if ($stage == $currentstage) {
                $classes[] = 'backup_stage_current';
            } else if ($stage < $currentstage) {
                $classes[] = 'backup_stage_complete';
            }
            $item = array('text' => strlen(decbin($stage)).'. '.get_string('restorestage'.$stage, 'backup'),'class' => join(' ', $classes));
            if ($stage < $currentstage && $currentstage < self::STAGE_COMPLETE && $stage > self::STAGE_DESTINATION) {
                $item['link'] = new moodle_url($PAGE->url, array('restore'=>$this->get_restoreid(), 'stage'=>$stage));
            }
            array_unshift($items, $item);
            $stage = floor($stage/2);
        }
        return $items;
    }
    /**
     * Gets the name of this UI
     * @return string
     */
    public function get_name() {
        return 'restore';
    }
    /**
     * Gets the first stage for this UI
     * @return int STAGE_CONFIRM
     */
    public function get_first_stage_id() {
        return self::STAGE_CONFIRM;
    }
    /**
     * Returns true if this stage has substages of which at least one needs to be displayed
     * @return bool
     */
    public function requires_substage() {
        return ($this->stage->has_sub_stages() && !$this->stage->process());
    }
    /**
     * Displays this stage
     *
     * @param core_backup_renderer $renderer
     * @return string HTML code to echo
     */
    public function display($renderer) {
        if ($this->progress < self::PROGRESS_SAVED) {
            throw new base_ui_exception('backupsavebeforedisplay');
        }
        return $this->stage->display($renderer);
    }
}

/**
 * restore user interface exception. Modelled off the restore_exception class
 */
class restore_ui_exception extends base_ui_exception {}
