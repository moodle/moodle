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
 * This file defines interface of all grading evaluation classes
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Base class for all grading evaluation subplugins.
 */
abstract class workshop_evaluation {

    /**
     * Calculates grades for assessment and updates 'gradinggrade' fields in 'workshop_assessments' table
     *
     * @param stdClass $settings settings for this round of evaluation
     * @param null|int|array $restrict if null, update all reviewers, otherwise update just grades for the given reviewers(s)
     */
    abstract public function update_grading_grades(stdClass $settings, $restrict=null);

    /**
     * Returns an instance of the form to provide evaluation settings.
      *
     * This is called by view.php (to display) and aggregate.php (to process and dispatch).
     * It returns the basic form with just the submit button by default. Evaluators may
     * extend or overwrite the default form to include some custom settings.
     *
     * @return workshop_evaluation_settings_form
     */
    public function get_settings_form(moodle_url $actionurl=null) {

        $customdata = array('workshop' => $this->workshop);
        $attributes = array('class' => 'evalsettingsform');

        return new workshop_evaluation_settings_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * Delete all data related to a given workshop module instance
     *
     * This is called from {@link workshop_delete_instance()}.
     *
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid) {

    }
}


/**
 * Base form to hold eventual evaluation settings.
 */
class workshop_evaluation_settings_form extends moodleform {

    /**
     * Defines the common form fields.
     */
    public function definition() {
        $mform = $this->_form;

        $workshop = $this->_customdata['workshop'];

        $mform->addElement('header', 'general', get_string('evaluationsettings', 'mod_workshop'));

        $this->definition_sub();

        $mform->addElement('submit', 'submit', get_string('aggregategrades', 'workshop'));
    }

    /**
     * Defines the subplugin specific fields.
     */
    protected function definition_sub() {
    }
}
