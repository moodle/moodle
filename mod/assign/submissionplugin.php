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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the definition for the abstract class for submission_plugin
 *
 * This class provides all the functionality for submission plugins.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/assignmentplugin.php');

/**
 * Abstract base class for submission plugin types.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class assign_submission_plugin extends assign_plugin {

    /**
     * return subtype name of the plugin
     *
     * @return string
     */
    public final function get_subtype() {
        return 'assignsubmission';
    }

    /**
     * This plugin accepts submissions from a student
     * The comments plugin has no submission component so should not be counted
     * when determining whether to show the edit submission link.
     * @return boolean
     */
    public function allow_submissions() {
        return true;
    }


    /**
     * Check if the submission plugin has all the required data to allow the work
     * to be submitted for grading
     * @return bool|string 'true' if OK to proceed with submission, otherwise a
     *                        a message to display to the user
     */
    public function precheck_submission() {
        return true;
    }

    /**
     * Carry out any extra processing required when the work is submitted for grading
     *
     * @param stdClass $submission - assign_submission data
     * @return void
     */
    public function submit_for_grading(stdClass $submission) {
    }

    /**
     * Carry out any extra processing required when the work is locked.
     *
     * @param stdClass $submission - assign_submission data
     * @return void
     */
    public function lock(stdClass $submission) {
    }

    /**
     * Carry out any extra processing required when the work is unlocked.
     *
     * @param stdClass $submission - assign_submission data
     * @return void
     */
    public function unlock(stdClass $submission) {
    }

    /**
     * Carry out any extra processing required when the work reverted to draft.
     *
     * @param stdClass $submission - assign_submission data
     * @return void
     */
    public function revert_to_draft(stdClass $submission) {
    }

}
