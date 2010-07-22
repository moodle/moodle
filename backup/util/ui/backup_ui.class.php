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
class backup_ui extends base_ui {
    /**
     * The stages of the backup user interface.
     */
    const STAGE_INITIAL = 1;
    const STAGE_SCHEMA = 2;
    const STAGE_CONFIRMATION = 4;
    const STAGE_FINAL = 8;
    const STAGE_COMPLETE = 16;

    /**
     * Intialises what ever stage is requested. If none are requested we check
     * params for 'stage' and default to initial
     *
     * @param int|null $stage The desired stage to intialise or null for the default
     * @return backup_ui_stage_initial|backup_ui_stage_schema|backup_ui_stage_confirmation|backup_ui_stage_final
     */
    protected function initialise_stage($stage = null, array $params=null) {
        if ($stage == null) {
            $stage = optional_param('stage', self::STAGE_INITIAL, PARAM_INT);
        }
        switch ($stage) {
            case backup_ui::STAGE_INITIAL:
                $stage = new backup_ui_stage_initial($this, $params);
                break;
            case backup_ui::STAGE_SCHEMA:
                $stage = new backup_ui_stage_schema($this, $params);
                break;
            case backup_ui::STAGE_CONFIRMATION:
                $stage = new backup_ui_stage_confirmation($this, $params);
                break;
            case backup_ui::STAGE_FINAL:
                $stage = new backup_ui_stage_final($this, $params);
                break;
            default:
                $stage = false;
                break;
        }
        return $stage;
    }
    public function get_uniqueid() {
        return $this->get_backupid();
    }
    /**
     * Gets the backup id from the controller
     * @return string
     */
    public function get_backupid() {
        return $this->controller->get_backupid();
    }
    /**
     * Executes the backup plan
     * @return bool
     */
    public function execute() {
        if ($this->progress >= self::PROGRESS_EXECUTED) {
            throw new backup_ui_exception('backupuialreadyexecuted');
        }
        if ($this->stage->get_stage() < self::STAGE_FINAL) {
            throw new backup_ui_exception('backupuifinalisedbeforeexecute');
        }
        $this->progress = self::PROGRESS_EXECUTED;
        $this->controller->finish_ui();
        $this->controller->execute_plan();
        $this->stage = new backup_ui_stage_complete($this, $this->stage->get_params(), $this->controller->get_results());
        return true;
    }
    /**
     * Loads the backup controller if we are tracking one
     * @return backup_controller|false
     */
    final public static function load_controller($backupid=false) {
        // Get the backup id optional param
        if ($backupid) {
            try {
                // Try to load the controller with it.
                // If it fails at this point it is likely because this is the first load
                $controller = backup_controller::load_controller($backupid);
                return $controller;
            } catch (Exception $e) {
                return false;
            }
        }
        return $backupid;
    }
    /**
     * Cancels the current backup and redirects the user back to the relevant place
     */
    public function cancel_backup() {
        global $PAGE;
        // Determine the approriate URL to redirect the user to
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
            $item = array('text' => strlen(decbin($stage)).'. '.get_string('currentstage'.$stage, 'backup'),'class' => join(' ', $classes));
            if ($stage < $currentstage && $currentstage < self::STAGE_COMPLETE) {
                $item['link'] = new moodle_url($PAGE->url, array('backup'=>$this->get_backupid(), 'stage'=>$stage));
            }
            array_unshift($items, $item);
            $stage = floor($stage/2);
        }
        return $items;
    }

    public function get_name() {
        return 'backup';
    }

    public function get_first_stage_id() {
        return self::STAGE_INITIAL;
    }
}

/**
 * Backup user interface exception. Modelled off the backup_exception class
 */
class backup_ui_exception extends base_ui_exception {}