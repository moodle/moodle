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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../duplicateddata/assignsubmissionduplicateddatamerger.php');
require_once(__DIR__ . '/../db/dbassignsubmission.php');

class AssignSubmissionTableMerger extends GenericTableMerger {

    private $findassignsubmissions;

    public function __construct() {
        parent::__construct(new AssignSubmissionDuplicatedDataMerger());
        $this->findassignsubmissions = new db_assign_submission();
        $this->duplicateddatamerger = new AssignSubmissionDuplicatedDataMerger();
    }

    public function mergeCompoundIndex($data, $userfield, $otherfields, &$recordsToModify, &$actionLog,
            &$errorMessages) {

        $fromuserid = $data['fromid'];
        $touserid = $data['toid'];
        $assignstocheck = $recordsToModify;
        $recordsToModify = [];
        $assignsubmissionstoremove = [];

        foreach ($assignstocheck as $assignid) {
            $olduserlatestsubmission = $this->findassignsubmissions->latest_from_assign_and_user($assignid, $fromuserid);
            $newuserlatestsubmission = $this->findassignsubmissions->latest_from_assign_and_user($assignid, $touserid);

            if (!empty($newuserlatestsubmission)) {
                $duplicateddata = $this->duplicateddatamerger->merge($olduserlatestsubmission, $newuserlatestsubmission);
                $recordsToModify += $duplicateddata->to_modify();
                $assignsubmissionstoremove += $duplicateddata->to_remove();
                continue;
            }

            if ($oldusersubmissions = $this->findassignsubmissions->all_from_assign_and_user($assignid, $fromuserid)) {
                $assignsubmissionstomodify = array_keys($oldusersubmissions);
                $recordsToModify += array_combine($assignsubmissionstomodify, $assignsubmissionstomodify);
            }
        }

        foreach ($assignsubmissionstoremove as $assignsubmissionid) {
            if (isset($recordsToModify[$assignsubmissionid])) {
                unset($recordsToModify[$assignsubmissionid]);
            }
        }

        $this->cleanRecordsOnCompoundIndex($data, $assignsubmissionstoremove, $actionLog, $errorMessages);
    }

    protected function get_records_to_be_updated($data, $fieldName) {
        global $DB;
        // Assign submissions may have attempts. We need a unique list of assignment ids.
        return $DB->get_records($data['tableName'], [$fieldName => $data['fromid']], '', 'DISTINCT assignment');
    }
}
