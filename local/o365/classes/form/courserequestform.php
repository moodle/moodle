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
 * Course request from Microsoft Teams feature.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/request_form.php');

use core_course_category;
use course_request;
use course_request_form;
use local_o365\feature\courserequest\main;
use local_o365\utils;

/**
 * A form for a user to request a course.
 */
class courserequestform extends course_request_form {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        global $CFG, $DB, $USER;

        $mform =& $this->_form;

        if ($pending = $DB->get_records('course_request', ['requester' => $USER->id])) {
            $mform->addElement('header', 'pendinglist', get_string('coursespending'));
            $list = [];
            foreach ($pending as $cp) {
                $list[] = format_string($cp->fullname);
            }
            $list = implode(', ', $list);
            $mform->addElement('static', 'pendingcourses', get_string('courses'), $list);
        }

        $mform->addElement('header', 'coursedetails', get_string('courserequestdetails'));

        // Course full name.
        $mform->addElement('text', 'fullname', get_string('fullnamecourse'), 'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        // Course short name.
        $mform->addElement('text', 'shortname', get_string('shortnamecourse'), 'maxlength="100" size="20"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        // Course category.
        if (empty($CFG->lockrequestcategory)) {
            $displaylist = core_course_category::make_categories_list('moodle/course:request');
            $mform->addElement('autocomplete', 'category', get_string('coursecategory'), $displaylist);
            $mform->addRule('category', null, 'required', null, 'client');
            $mform->setDefault('category', $CFG->defaultrequestcategory);
            $mform->addHelpButton('category', 'coursecategory');
        }

        // Course summary.
        $mform->addElement('editor', 'summary_editor', get_string('summary'), null, course_request::summary_editor_options());
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);

        // Add Microsoft Teams select element.
        // Initialize the course request main class.
        $userobjectid = utils::get_microsoft_account_oid_by_user_id($USER->id);
        $graphapiunavailable = false;
        if (!$userobjectid) {
            $graphapiunavailable = true;
        } else {
            $apiclient = main::get_unified_api();
            if (empty($apiclient)) {
                $graphapiunavailable = true;
            } else {
                $courserequestmain = new main($apiclient);
                $unmatchedteams = $courserequestmain->get_unmatched_teams_by_user_oid($userobjectid);

                if ($unmatchedteams === false) {
                    $graphapiunavailable = true;
                } else {
                    // Initialize the select element.
                    $select = $mform->addElement('select', 'team', get_string('courserequest_teams', 'local_o365'), []);
                    $mform->addHelpButton('team', 'courserequest_teams', 'local_o365');

                    // Populate the select element based on the availability of unmatched teams.
                    if (!empty($unmatchedteams)) {
                        foreach ($unmatchedteams as $unmatchedteam) {
                            $select->addOption($unmatchedteam['displayName'], $unmatchedteam['id']);
                        }
                    } else {
                        $select->addOption(get_string('courserequest_emptyteams', 'local_o365'), '');
                        $mform->disabledIf('team', '', 'eq', '');
                    }

                    // Add client-side validation.
                    $mform->addRule('team', get_string('courserequest_emptyteams', 'local_o365'), 'required', null, 'client');
                }
            }
        }

        // If no team options are available, display a static element with a message.
        if ($graphapiunavailable) {
            $mform->addElement('static', 'team', get_string('courserequest_teams', 'local_o365'),
                get_string('courserequest_graphapi_disabled', 'local_o365'));
        }

        $mform->addRule('team', get_string('courserequest_emptyteams', 'local_o365'), 'required', null, 'client');

        // Request reason.
        $mform->addElement('header', 'requestreason', get_string('courserequestreason'));
        $mform->addElement('textarea', 'reason', get_string('courserequestsupport'), ['rows' => '15', 'cols' => '50']);
        $mform->addRule('reason', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('reason', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('requestcourse'));
    }

    /**
     * Custom validation function.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);

        // Prevent duplicate course requests from the same Team to be created.
        [$statussql, $statusparams] = $DB->get_in_or_equal([main::COURSE_REQUEST_STATUS_PENDING,
            main::COURSE_REQUEST_STATUS_APPROVED], SQL_PARAMS_NAMED, 'status');
        $sql = 'SELECT *
                  FROM {local_o365_course_request}
                 WHERE teamoid = :teamoid
                   AND requeststatus ' . $statussql;
        if ($DB->record_exists_sql($sql, array_merge(['teamoid' => $data['team']], $statusparams))) {
            $errors['team'] = get_string('courserequest_duplicate', 'local_o365');
        }

        return $errors;
    }
}
