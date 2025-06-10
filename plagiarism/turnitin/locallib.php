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
 * Extra helper methods for plagiarism_turnitin component
 *
 * @package   plagiarism_turnitin
 * @copyright 2018 Turnitin
 * @authior   John McGettrick <jmcgettrick@turnitin.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Override the repository option if necessary depending on the configuration setting.
 * @param $submitpapersto int - The repository to submit to.
 * @return $submitpapersto int - The repository to submit to.
 */
function plagiarism_turnitin_override_repository($submitpapersto) {
    $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();

    switch ($config->plagiarism_turnitin_repositoryoption) {
        case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_STANDARD; // Force Standard Repository.
            $submitpapersto = PLAGIARISM_TURNITIN_SUBMIT_TO_STANDARD_REPOSITORY;
            break;
        case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_NO; // Force No Repository.
            $submitpapersto = PLAGIARISM_TURNITIN_SUBMIT_TO_NO_REPOSITORY;
            break;
        case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL; // Force Individual Repository.
            $submitpapersto = PLAGIARISM_TURNITIN_SUBMIT_TO_INSTITUTIONAL_REPOSITORY;
            break;
    }

    return $submitpapersto;
}

/**
 * Retrieve previously made successful submissions that match passed in parameters. This
 * avoids resubmitting them to Turnitin.
 *
 * @param $author
 * @param $cmid
 * @param $identifier
 * @return $plagiarismfiles - an array of succesfully submitted submissions
 */
function plagiarism_turnitin_retrieve_successful_submissions($author, $cmid, $identifier) {
    global $CFG, $DB;

    // Check if the same answer has been submitted previously. Remove if so.
    list($insql, $inparams) = $DB->get_in_or_equal(array('success', 'queued'), SQL_PARAMS_QM, 'param', false);
    $typefield = ($CFG->dbtype == "oci") ? " to_char(statuscode) " : " statuscode ";

    $plagiarismfiles = $DB->get_records_select(
        "plagiarism_turnitin_files",
        " userid = ? AND cm = ? AND identifier = ? AND ".$typefield. " " .$insql,
        array_merge(array($author, $cmid, $identifier), $inparams)
    );

    return $plagiarismfiles;
}

/**
 * Add a config field to show submissions have been made which we use to lock the anonymous marking setting.
 * @param $cmid
 */
function plagiarism_turnitin_lock_anonymous_marking($cmid) {
    global $DB;

    $configfield = new stdClass();
    $configfield->cm = $cmid;
    $configfield->name = 'submitted';
    $configfield->value = 1;
    $configfield->config_hash = $configfield->cm . "_" . $configfield->name;

    if (!$DB->get_field('plagiarism_turnitin_config', 'id',
        (array('cm' => $cmid, 'name' => 'submitted')))) {
        if (!$DB->insert_record('plagiarism_turnitin_config', $configfield)) {
            plagiarism_turnitin_print_error(
                'defaultupdateerror',
                'plagiarism_turnitin', null, null, __FILE__, __LINE__);
        }
    }
}