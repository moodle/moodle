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
 * lib.php - Contains Plagiarism base class used by plugins.
 *
 * @since Moodle 2.0
 * @package    core_plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}


/**
 * Plagiarism base class used by plugins.
 *
 * @since Moodle 2.0
 * @package    core_plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plagiarism_plugin {

    /**
     * Return the list of form element names.
     * @deprecated Since Moodle 4.0 - this function was a placeholder and not used in core.
     * @todo MDL-71326 Remove this method.
     * @return array contains the form element names.
     */
    public function get_configs() {
        return array();
    }

    /**
     * hook to allow plagiarism specific information to be displayed beside a submission
     * @param array  $linkarraycontains all relevant information for the plugin to generate a link
     * @return string
     */
    public function get_links($linkarray) {
        return '';
    }
    /**
     * hook to allow plagiarism specific information to be returned unformatted
     * @deprecated Since Moodle 4.0 - this function was a placeholder and not used in core Moodle code.
     * @todo MDL-71326 Remove this method.
     * @param int $cmid
     * @param int $userid
     * @param $file file object
     * @return array containing at least:
     *   - 'analyzed' - whether the file has been successfully analyzed
     *   - 'score' - similarity score - ('' if not known)
     *   - 'reporturl' - url of originality report - '' if unavailable
     */
    public function get_file_results($cmid, $userid, $file) {
        return array('analyzed' => '', 'score' => '', 'reporturl' => '');
    }
    /**
     * hook to add plagiarism specific settings to a module settings page
     * @deprecated Since Moodle 3.9. MDL-65835 Please use {plugin name}_coursemodule_edit_post_actions() instead.
     * @todo MDL-67526 Remove this method.
     * @param object $mform  - Moodle form
     * @param object $context - current context
     * @param string $modulename - Name of the module
     */
    public function get_form_elements_module($mform, $context, $modulename = "") {
    }
    /**
     * hook to save plagiarism specific settings on a module settings page
     * @deprecated Since Moodle 3.9. MDL-65835 Please use {plugin name}_coursemodule_standard_elements() instead.
     * @todo MDL-67526 Remove this method.
     * @param object $data - data from an mform submission.
     */
    public function save_form_elements($data) {
    }
    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid) {
    }
    /**
     * hook to allow status of submitted files to be updated - called on grading/report pages.
     * @deprecated Since Moodle 4.0 - Please use {plugin name}_before_standard_top_of_body_html instead.
     * @todo MDL-71326 Remove this method.
     * @param object $course - full Course object
     * @param object $cm - full cm object
     */
    public function update_status($course, $cm) {
    }
}
