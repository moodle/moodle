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
     * @param stdClass $submission the assign_submission record being submitted.
     * @return bool|string 'true' if OK to proceed with submission, otherwise a
     *                        a message to display to the user
     */
    public function precheck_submission($submission) {
        return true;
    }

    /**
     * Carry out any extra processing required when the work is submitted for grading
     * @param stdClass $submission the assign_submission record being submitted.
     * @return void
     */
    public function submit_for_grading($submission) {
    }

    /**
     * Copy the plugin specific submission data to a new submission record.
     *
     * @param stdClass $oldsubmission - Old submission record
     * @param stdClass $submission - New submission record
     * @return bool
     */
    public function copy_submission( stdClass $oldsubmission, stdClass $submission) {
        return true;
    }

    /**
     * Carry out any extra processing required when the work is locked.
     *
     * @param stdClass|false $submission - assign_submission data if any
     * @param stdClass $flags - User flags record
     * @return void
     */
    public function lock($submission, stdClass $flags) {
    }

    /**
     * Carry out any extra processing required when the work is unlocked.
     *
     * @param stdClass|false $submission - assign_submission data if any
     * @param stdClass $flags - User flags record
     * @return void
     */
    public function unlock($submission, stdClass $flags) {
    }

    /**
     * Carry out any extra processing required when the work reverted to draft.
     *
     * @param stdClass $submission - assign_submission data
     * @return void
     */
    public function revert_to_draft(stdClass $submission) {
    }

    /**
     * Carry out any extra processing required when a student is given a new attempt
     * (i.e. when the submission is "reopened"
     * @param stdClass $oldsubmission The previous attempt
     * @param stdClass $newsubmission The new attempt
     */
    public function add_attempt(stdClass $oldsubmission, stdClass $newsubmission) {
    }

    /**
     * Determine if a submission is empty
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data) {
        return false;
    }
}
