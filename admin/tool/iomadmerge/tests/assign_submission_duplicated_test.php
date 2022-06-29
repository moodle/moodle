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

require_once(__DIR__ . '/../lib/db/inmemoryfindbyquery.php');
require_once(__DIR__ . '/../lib/duplicateddata/assignsubmissionduplicateddatamerger.php');

class tool_iomadmerge_assign_submission_duplicated_testcase extends advanced_testcase {

    /**
     * Should do nothing with new submission and remove old submission when old user has no content submission
     * and new user has content submission
     *
     * @group tool_iomadmerge
     * @group tool_iomadmerge_assign_submission
     * @dataProvider remove_old_ignore_new_data_provider
     */
    public function test_remove_old_ignore_new($expectedtomodify, $expectedtoremove, $oldusersubmission, $newusersubmission) {
        $data = [
                1111 => [
                        1 => $oldusersubmission
                ],
                2222 => [
                        2 => $newusersubmission
                ]
        ];

        $inmemoryfindbyquery = new in_memory_assign_submission_query($data);
        $assignsubmissionduplicateddatamerger = new AssignSubmissionDuplicatedDataMerger($inmemoryfindbyquery);

        $duplicateddata = $assignsubmissionduplicateddatamerger->merge($oldusersubmission, $newusersubmission);

        $this->assertEquals($duplicateddata->to_modify(), $expectedtomodify);
        $this->assertEquals($duplicateddata->to_remove(), $expectedtoremove);
    }

    public function remove_old_ignore_new_data_provider() {
        return [
                [
                        [],
                        [1 => 1],
                        $this->get_assign_submission_new(1, 1111),
                        $this->get_assign_submission_submitted(2, 2222)
                ],
                [
                        [],
                        [1 => 1],
                        $this->get_assign_submission_new(1, 1111),
                        $this->get_assign_submission_draft(2, 2222)
                ],
                [
                        [],
                        [1 => 1],
                        $this->get_assign_submission_new(1, 1111),
                        $this->get_assign_submission_reopened(2, 2222)
                ],
                [
                        [],
                        [1 => 1],
                        $this->get_assign_submission_new(1, 1111),
                        $this->get_assign_submission_new(2, 2222)
                ]
        ];
    }

    /**
     * Should update old submission and remove new submission when old user has submitted
     * submission and new user has new submission
     *
     * @group tool_iomadmerge
     * @group tool_iomadmerge_assign_submission
     * @dataProvider update_old_and_remove_new_data_provider
     */
    public function test_update_old_and_remove_new($expectedtomodify, $expectedtoremove, $oldusersubmission, $newusersubmission) {
        $data = [
                1111 => [
                        1 => $oldusersubmission
                ],
                2222 => [
                        2 => $newusersubmission
                ]
        ];
        $inmemoryfindbyquery = new in_memory_assign_submission_query($data);
        $assignsubmissionduplicateddatamerger = new AssignSubmissionDuplicatedDataMerger($inmemoryfindbyquery);

        $duplicateddata = $assignsubmissionduplicateddatamerger->merge($oldusersubmission, $newusersubmission);

        $this->assertEquals($duplicateddata->to_modify(), $expectedtomodify);
        $this->assertEquals($duplicateddata->to_remove(), $expectedtoremove);
    }

    public function update_old_and_remove_new_data_provider() {
        return [
                [
                        [1 => 1],
                        [2 => 2],
                        $this->get_assign_submission_submitted(1, 1111),
                        $this->get_assign_submission_new(2, 2222)
                ],
                [
                        [1 => 1],
                        [2 => 2],
                        $this->get_assign_submission_draft(1, 1111),
                        $this->get_assign_submission_new(2, 2222)
                ],
                [
                        [1 => 1],
                        [2 => 2],
                        $this->get_assign_submission_reopened(1, 1111),
                        $this->get_assign_submission_new(2, 2222)
                ],
        ];
    }

    /**
     * Should update first submission submitted and remove last when user has duplicated submission submitted
     *
     * @group tool_iomadmerge
     * @group tool_iomadmerge_assign_submission
     * @dataProvider update_first_and_remove_last_data_provider
     */
    public function test_update_first_and_remove_last($expectedtomodify, $expectedtoremove, $oldusersubmission,
            $newusersubmission) {
        $data = [
                1111 => [
                        1 => $oldusersubmission
                ],
                2222 => [
                        2 => $newusersubmission
                ]
        ];
        $inmemoryfindbyquery = new in_memory_assign_submission_query($data);
        $assignsubmissionduplicateddatamerger = new AssignSubmissionDuplicatedDataMerger($inmemoryfindbyquery);

        $duplicateddata = $assignsubmissionduplicateddatamerger->merge($oldusersubmission, $newusersubmission);

        $this->assertEquals($duplicateddata->to_modify(), $expectedtomodify);
        $this->assertEquals($duplicateddata->to_remove(), $expectedtoremove);
    }

    public function update_first_and_remove_last_data_provider() {

        return [
                [
                        [1 => 1],
                        [2 => 2],
                        $this->get_assign_submission_submitted_by_date(1, 1111, 123456),
                        $this->get_assign_submission_submitted_by_date(2, 2222, 987654)
                ],
                [
                        [1 => 1],
                        [2 => 2],
                        $this->get_assign_submission_draft_by_date(1, 1111, 123456),
                        $this->get_assign_submission_submitted_by_date(2, 2222, 987654)
                ],
                [
                        [2 => 2],
                        [1 => 1],
                        $this->get_assign_submission_submitted_by_date(1, 1111, 987654),
                        $this->get_assign_submission_submitted_by_date(2, 2222, 123456)
                ],
        ];
    }

    private function get_assign_submission_submitted($id, $assignid) {
        $anoldsubmittedassignsubmision = $this->get_assign_submission($id);
        $anoldsubmittedassignsubmision->status = 'submitted';
        $anoldsubmittedassignsubmision->assignment = $assignid;

        return $anoldsubmittedassignsubmision;
    }

    private function get_assign_submission_submitted_by_date($id, $assignid, $date) {
        $anewsubmittedassignsubmission = $this->get_assign_submission($id);
        $anewsubmittedassignsubmission->status = 'submitted';
        $anewsubmittedassignsubmission->assignment = $assignid;
        $anewsubmittedassignsubmission->timemodified = $date;

        return $anewsubmittedassignsubmission;
    }

    private function get_assign_submission_new($id, $assignid) {
        $anoldsubmittedassignsubmision = $this->get_assign_submission($id);
        $anoldsubmittedassignsubmision->status = 'new';
        $anoldsubmittedassignsubmision->assignment = $assignid;

        return $anoldsubmittedassignsubmision;
    }

    private function get_assign_submission_draft_by_date($id, $assignid, $date) {
        $draft = $this->get_assign_submission_draft($id, $assignid);
        $draft->timemodified = $date;

        return $draft;
    }

    private function get_assign_submission_draft($id, $assignid) {
        $anassignsubmissiondraft = $this->get_assign_submission($id);
        $anassignsubmissiondraft->status = 'draft';
        $anassignsubmissiondraft->assignment = $assignid;

        return $anassignsubmissiondraft;
    }

    private function get_assign_submission_reopened($id, $assignid) {
        $anassignsubmissionreopened = $this->get_assign_submission($id);
        $anassignsubmissionreopened->status = 'reopened';
        $anassignsubmissionreopened->assignment = $assignid;

        return $anassignsubmissionreopened;
    }

    private function get_assign_submission($id) {
        $anewassignsubmision = new stdClass();
        $anewassignsubmision->id = $id;
        $anewassignsubmision->assignment = 123456;
        $anewassignsubmision->userid = 1234;
        $anewassignsubmision->timecreated = 1189615462;
        $anewassignsubmision->timemodified = 1189615462;
        $anewassignsubmision->groupid = 1;
        $anewassignsubmision->attemptnumber = 0;
        $anewassignsubmision->latest = 1;

        return $anewassignsubmision;
    }
}
