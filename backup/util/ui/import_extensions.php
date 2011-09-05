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
 * This file contains extension of the backup classes that override some methods
 * and functionality in order to customise the backup UI for the purposes of
 * import.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Import UI class
 */
class import_ui extends backup_ui {
    /**
     * Customises the backup progress bar
     *
     * @global moodle_page $PAGE
     * @return array
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
            $item = array('text' => strlen(decbin($stage*2)).'. '.get_string('importcurrentstage'.$stage, 'backup'),'class' => join(' ', $classes));
            if ($stage < $currentstage && $currentstage < self::STAGE_COMPLETE && (!self::$skipcurrentstage || $stage*2 != $currentstage)) {
                $item['link'] = new moodle_url($PAGE->url, $this->stage->get_params() + array('backup'=>$this->get_backupid(), 'stage'=>$stage));
            }
            array_unshift($items, $item);
            $stage = floor($stage/2);
        }
        $selectorlink = new moodle_url($PAGE->url, $this->stage->get_params());
        $selectorlink->remove_params('importid');
        array_unshift($items, array(
                'text' => '1. '.get_string('importcurrentstage0', 'backup'),
                'class' => join(' ', $classes),
                'link' => $selectorlink));
        return $items;
    }

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
        if (self::$skipcurrentstage) {
            $stage *= 2;
        }
        switch ($stage) {
            case backup_ui::STAGE_INITIAL:
                $stage = new import_ui_stage_inital($this, $params);
                break;
            case backup_ui::STAGE_SCHEMA:
                $stage = new import_ui_stage_schema($this, $params);
                break;
            case backup_ui::STAGE_CONFIRMATION:
                $stage = new import_ui_stage_confirmation($this, $params);
                break;
            case backup_ui::STAGE_FINAL:
                $stage = new import_ui_stage_final($this, $params);
                break;
            default:
                $stage = false;
                break;
        }
        return $stage;
    }
}

/**
 * Extends the initial stage
 */
class import_ui_stage_inital extends backup_ui_stage_initial {}

/**
 * Extends the schema stage
 */
class import_ui_stage_schema extends backup_ui_stage_schema {}

/**
 * Extends the confirmation stage.
 *
 * This overides the initialise stage form to remove the filenamesetting heading
 * as it is always hidden.
 */
class import_ui_stage_confirmation extends backup_ui_stage_confirmation {

    /**
     * Initialises the stages moodleform
     * @return moodleform
     */
    protected function initialise_stage_form() {
        $form = parent::initialise_stage_form();
        $form->remove_element('filenamesetting');
        return $form;
    }

    /**
     * Displays the stage
     *
     * This function is overriden so that we can manipulate the strings on the
     * buttons.
     */
    public function display() {
        $form = $this->initialise_stage_form();
        $form->require_definition_after_data();
        if ($e = $form->get_element('submitbutton')) {
            $e->setLabel(get_string('import'.$this->get_ui()->get_name().'stage'.$this->get_stage().'action', 'backup'));
        } else {
            $elements = $form->get_element('buttonar')->getElements();
            foreach ($elements as &$element) {
                if ($element->getName()=='submitbutton') {
                    $element->setValue(get_string('import'.$this->get_ui()->get_name().'stage'.$this->get_stage().'action', 'backup'));
                }
            }
        }
        $form->display();
    }
}
/**
 * Overrides the final stage.
 */
class import_ui_stage_final extends backup_ui_stage_final {}

/**
 * Extends the restore course search to search for import courses.
 */
class import_course_search extends restore_course_search {
    /**
     * Sets up any access restrictions for the courses to be displayed in the search.
     *
     * This will typically call $this->require_capability().
     */
    protected function setup_restrictions() {
        $this->require_capability('moodle/backup:backuptargetimport');
    }
}
