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
 * This file contains the function for feedback_plugin abstract class
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/assign/assignmentplugin.php');

/**
 * Abstract class for feedback_plugin inherited from assign_plugin abstract class.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class assign_feedback_plugin extends assign_plugin {

    /**
     * Return subtype name of the plugin.
     *
     * @return string
     */
    public function get_subtype() {
        return 'assignfeedback';
    }

    /**
     * If this plugin adds to the gradebook comments field, it must specify the format
     * of the comment.
     *
     * (From weblib.php)
     * define('FORMAT_MOODLE',   '0');   // Does all sorts of transformations and filtering
     * define('FORMAT_HTML',     '1');   // Plain HTML (with some tags stripped)
     * define('FORMAT_PLAIN',    '2');   // Plain text (even tags are printed in full)
     * define('FORMAT_WIKI',     '3');   // Wiki-formatted text
     * define('FORMAT_MARKDOWN', '4');   // Markdown-formatted
     *
     * Only one feedback plugin can push comments to the gradebook and that is chosen by the assignment
     * settings page.
     *
     * @param stdClass $grade The grade
     * @return int
     */
    public function format_for_gradebook(stdClass $grade) {
        return FORMAT_MOODLE;
    }

    /**
     * If this plugin adds to the gradebook comments field, it must format the text
     * of the comment.
     *
     * Only one feedback plugin can push comments to the gradebook and that is chosen by the assignment
     * settings page.
     *
     * @param stdClass $grade The grade
     * @return string
     */
    public function text_for_gradebook(stdClass $grade) {
        return '';
    }

    /**
     * Return any files this plugin wishes to save to the gradebook.
     *
     * The array being returned should contain the necessary information to
     * identify and copy the files.
     *
     * eg.
     *
     * [
     *      'contextid' => $modulecontext->id,
     *      'component' => ASSIGNFEEDBACK_XYZ_COMPONENT,
     *      'filearea' => ASSIGNFEEDBACK_XYZ_FILEAREA,
     *      'itemid' => $grade->id
     * ]
     *
     * @param stdClass $grade The assign_grades object from the db
     * @return array
     */
    public function files_for_gradebook(stdClass $grade) : array {
        return [];
    }

    /**
     * Override to indicate a plugin supports quickgrading.
     *
     * @return boolean - True if the plugin supports quickgrading
     */
    public function supports_quickgrading() {
        return false;
    }

    /**
     * Get quickgrading form elements as html.
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param mixed $grade grade or null - The grade data.
     *                     May be null if there are no grades for this user (yet)
     * @return mixed - A html string containing the html form elements required for
     *                 quickgrading or false to indicate this plugin does not support quickgrading
     */
    public function get_quickgrading_html($userid, $grade) {
        return false;
    }

    /**
     * Has the plugin quickgrading form element been modified in the current form submission?
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the quickgrading form element has been modified
     */
    public function is_quickgrading_modified($userid, $grade) {
        return false;
    }

    /**
     * Has the plugin form element been modified in the current submission?
     *
     * @param stdClass $grade The grade.
     * @param stdClass $data Form data from the feedback form.
     * @return boolean - True if the form element has been modified.
     */
    public function is_feedback_modified(stdClass $grade, stdClass $data) {
        debugging('This plugin (' . $this->get_name() . ') has not overwritten the is_feedback_modified() method.
                Please add this method to your plugin', DEBUG_DEVELOPER);
        return true;
    }

    /**
     * Save quickgrading changes.
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the grade changes were saved correctly
     */
    public function save_quickgrading_changes($userid, $grade) {
        return false;
    }

    /**
     * Return a list of the batch grading operations supported by this plugin.
     *
     * @return array - An array of action and description strings.
     *                 The action will be passed to grading_batch_operation.
     */
    public function get_grading_batch_operations() {
        return array();
    }

    /**
     * Return a list of the grading actions supported by this plugin.
     *
     * A grading action is a page that is not specific to a user but to the whole assignment.
     * @return array - An array of action and description strings.
     *                 The action will be passed to grading_action.
     */
    public function get_grading_actions() {
        return array();
    }

    /**
     * Show a grading action form
     *
     * @param string $gradingaction The action chosen from the grading actions menu
     * @return string The page containing the form
     */
    public function grading_action($gradingaction) {
        return '';
    }

    /**
     * Supports injecting content into the review panel of the grading app.
     *
     * @return bool True if this plugin will add content to the review panel of the grading app.
     */
    public function supports_review_panel() {
        return false;
    }

    /**
     * Show a batch operations form
     *
     * @param string $action The action chosen from the batch operations menu
     * @param array $users The list of selected userids
     * @return string The page containing the form
     */
    public function grading_batch_operation($action, $users) {
        return '';
    }
}
