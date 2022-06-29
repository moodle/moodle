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

require_once(__DIR__ . '/duplicateddatamerger.php');
require_once(__DIR__ . '/duplicateddata.php');
require_once(__DIR__ . '/../db/dbassignsubmission.php');
require_once(__DIR__ . '/../db/assignsubmissionquery.php');

class AssignSubmissionDuplicatedDataMerger implements DuplicatedDataMerger {
    // This constants are located at mod/assign/locallib.php. We copy here to avoid loading full locallib.php file.
    const ASSIGN_SUBMISSION_WITH_CONTENT = [
            'submitted',
            'draft',
            'reopened',
    ];
    const ASSIGN_SUBMISSION_NEW = 'new';

    private $findassignsubmissionbyid;

    public function __construct(assign_submission_query $findbyquery = null) {
        $this->findassignsubmissionbyid = $findbyquery ?? new db_assign_submission();
    }

    public function merge($oldsubmission, $newsubmission): DuplicatedData {
        if ($this->old_submission_has_content_and_new_has_no_content($oldsubmission, $newsubmission)) {
            return DuplicatedData::from_remove_and_modify(
                    array_keys($this->findassignsubmissionbyid->all_from_assign_and_user($newsubmission->assignment,
                            $newsubmission->userid)),
                    array_keys($this->findassignsubmissionbyid->all_from_assign_and_user($oldsubmission->assignment,
                            $oldsubmission->userid))
            );
        }

        if ($this->both_submissions_have_content($oldsubmission, $newsubmission)) {
            $submissiontomodify = $newsubmission;
            $submissiontoremove = $oldsubmission;
            if ($this->old_user_submission_is_older_than_new_user_submission($oldsubmission, $newsubmission)) {
                $submissiontomodify = $oldsubmission;
                $submissiontoremove = $newsubmission;
            }
            $modifyid = $this->findassignsubmissionbyid->all_from_assign_and_user($submissiontomodify->assignment,
                    $submissiontomodify->userid);
            $removeid = $this->findassignsubmissionbyid->all_from_assign_and_user($submissiontoremove->assignment,
                    $submissiontoremove->userid);

            return DuplicatedData::from_remove_and_modify(array_keys($removeid), array_keys($modifyid));
        }

        return DuplicatedData::from_remove(
                array_keys($this->findassignsubmissionbyid->all_from_assign_and_user($oldsubmission->assignment,
                        $oldsubmission->userid))
        );
    }

    private function old_submission_has_content_and_new_has_no_content($oldsubmission, $newsubmission) {
        return in_array($oldsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT, true) &&
                $newsubmission->status == self::ASSIGN_SUBMISSION_NEW;
    }

    private function both_submissions_have_content($oldsubmission, $newsubmission) {
        return in_array($oldsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT) &&
                in_array($newsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT);
    }

    private function old_user_submission_is_older_than_new_user_submission($oldsubmission, $newsubmission) {
        return $oldsubmission->timemodified <= $newsubmission->timemodified;
    }
}
