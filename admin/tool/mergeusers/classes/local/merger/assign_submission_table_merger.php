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
 * Merges assign submissions.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger;

use db_assign_submission;
use dml_exception;
use tool_mergeusers\local\merger\finder\assign_submission_db_finder;
use tool_mergeusers\local\merger\finder\assign_submission_finder;
use tool_mergeusers\local\merger\duplicateddata\assign_submission_duplicated_data_merger;

/**
 * Merges assign submissions.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_table_merger extends generic_table_merger {
    /** @var assign_submission_finder submission finder. */
    private assign_submission_finder $assignsubmissionfinder;
    /** @var assign_submission_duplicated_data_merger merger when duplicated records arise. */
    private assign_submission_duplicated_data_merger $duplicateddatamerger;

    /**
     * Initializes a merger for assign submissions.
     *
     * @throws dml_exception
     */
    public function __construct() {
        parent::__construct();
        $this->assignsubmissionfinder = new assign_submission_db_finder();
        $this->duplicateddatamerger = new assign_submission_duplicated_data_merger();
    }

    /**
     * Merges records under the presence of a compound index.
     *
     * @param array $data array with the details of merging
     * @param string $userfield table's field name that refers to the user id.
     * @param array $otherfields table's field names that refers to the other members of the coumpund index.
     * @param array $recordstomodify array with current $table's id to update.
     * @param array $logs Array where to append the list of actions done.
     * @param array $errors Array where to append any error occurred.
     * @return void
     * @throws dml_exception
     */
    public function merge_compound_index(
        array $data,
        string $userfield,
        array $otherfields,
        array &$recordstomodify,
        array &$logs,
        array &$errors
    ): void {
        $fromuserid = $data['fromid'];
        $touserid = $data['toid'];
        $assignstocheck = $recordstomodify;
        $recordstomodify = [];
        $assignsubmissionstoremove = [];

        foreach ($assignstocheck as $assignid) {
            $olduserlatestsubmission = $this->assignsubmissionfinder->latest_from_assign_and_user($assignid, $fromuserid);
            $newuserlatestsubmission = $this->assignsubmissionfinder->latest_from_assign_and_user($assignid, $touserid);

            if (!empty($newuserlatestsubmission)) {
                $duplicateddata = $this->duplicateddatamerger->merge($olduserlatestsubmission, $newuserlatestsubmission);
                $recordstomodify += $duplicateddata->to_update();
                $assignsubmissionstoremove += $duplicateddata->to_remove();
                continue;
            }

            if ($oldusersubmissions = $this->assignsubmissionfinder->all_from_assign_and_user($assignid, $fromuserid)) {
                $assignsubmissionstomodify = array_keys($oldusersubmissions);
                $recordstomodify += array_combine($assignsubmissionstomodify, $assignsubmissionstomodify);
            }
        }

        foreach ($assignsubmissionstoremove as $assignsubmissionid) {
            if (isset($recordstomodify[$assignsubmissionid])) {
                unset($recordstomodify[$assignsubmissionid]);
            }
        }

        $this->clean_records_on_compound_index($data, $assignsubmissionstoremove, $logs, $errors);
    }

    /**
     * Get the list of records from the user.id from the to remove on the field name.
     *
     * @param array $data merge details.
     * @param string $fieldname field name from the table.
     * @return array list of records with just unique values for assignment field.
     * @throws dml_exception
     */
    protected function get_records_to_be_updated($data, $fieldname): array {
        global $DB;

        // Assign submissions may have attempts. We need a unique list of assignment ids.
        return $DB->get_records($data['tableName'], [$fieldname => $data['fromid']], '', 'DISTINCT assignment');
    }
}
