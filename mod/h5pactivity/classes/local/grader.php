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
 * H5P activity grader class.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;

use context_module;
use cm_info;
use moodle_recordset;
use stdClass;

/**
 * Class for handling H5P activity grading.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grader {

    /** @var stdClass course_module record. */
    private $instance;

    /** @var string idnumber course_modules idnumber. */
    private $idnumber;

    /**
     * Class contructor.
     *
     * @param stdClass $instance H5Pactivity instance object
     * @param string $idnumber course_modules idnumber
     */
    public function __construct(stdClass $instance, string $idnumber = '') {
        $this->instance = $instance;
        $this->idnumber = $idnumber;
    }

    /**
     * Delete grade item for given mod_h5pactivity instance.
     *
     * @return int Returns GRADE_UPDATE_OK, GRADE_UPDATE_FAILED, GRADE_UPDATE_MULTIPLE or GRADE_UPDATE_ITEM_LOCKED
     */
    public function grade_item_delete(): ?int {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        return grade_update('mod/h5pactivity', $this->instance->course, 'mod', 'h5pactivity',
                $this->instance->id, 0, null, ['deleted' => 1]);
    }

    /**
     * Creates or updates grade item for the given mod_h5pactivity instance.
     *
     * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
     * @return int 0 if ok, error code otherwise
     */
    public function grade_item_update($grades = null): int {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        $item = [];
        $item['itemname'] = clean_param($this->instance->name, PARAM_NOTAGS);
        $item['gradetype'] = GRADE_TYPE_VALUE;
        if (!empty($this->idnumber)) {
            $item['idnumber'] = $this->idnumber;
        }

        if ($this->instance->grade > 0) {
            $item['gradetype'] = GRADE_TYPE_VALUE;
            $item['grademax']  = $this->instance->grade;
            $item['grademin']  = 0;
        } else if ($this->instance->grade < 0) {
            $item['gradetype'] = GRADE_TYPE_SCALE;
            $item['scaleid']   = -$this->instance->grade;
        } else {
            $item['gradetype'] = GRADE_TYPE_NONE;
        }

        if ($grades === 'reset') {
            $item['reset'] = true;
            $grades = null;
        }

        return grade_update('mod/h5pactivity', $this->instance->course, 'mod',
                'h5pactivity', $this->instance->id, 0, $grades, $item);
    }

    /**
     * Update grades in the gradebook.
     *
     * @param int $userid Update grade of specific user only, 0 means all participants.
     */
    public function update_grades(int $userid = 0): void {
        // Scaled and none grading doesn't have grade calculation.
        if ($this->instance->grade <= 0) {
            $this->grade_item_update();
            return;
        }
        // Populate array of grade objects indexed by userid.
        $grades = $this->get_user_grades_for_gradebook($userid);

        if (!empty($grades)) {
            $this->grade_item_update($grades);
        } else {
            $this->grade_item_update();
        }
    }

    /**
     * Get an updated list of user grades and feedback for the gradebook.
     *
     * @param int $userid int or 0 for all users
     * @return array of grade data formated for the gradebook api
     *         The data required by the gradebook api is userid,
     *                                                   rawgrade,
     *                                                   feedback,
     *                                                   feedbackformat,
     *                                                   usermodified,
     *                                                   dategraded,
     *                                                   datesubmitted
     */
    private function get_user_grades_for_gradebook(int $userid = 0): array {
        $grades = [];

        // In case of using manual grading this update must delete previous automatic gradings.
        if ($this->instance->grademethod == manager::GRADEMANUAL || !$this->instance->enabletracking) {
            return $this->get_user_grades_for_deletion($userid);
        }

        $manager = manager::create_from_instance($this->instance);

        $scores = $manager->get_users_scaled_score($userid);
        if (!$scores) {
            return $grades;
        }

        // Maxgrade depends on the type of grade used:
        // - grade > 0: regular quantitative grading.
        // - grade = 0: no grading.
        // - grade < 0: scale used.
        $maxgrade = floatval($this->instance->grade);

        // Convert scaled scores into gradebok compatible objects.
        foreach ($scores as $userid => $score) {
            $grades[$userid] = [
                'userid' => $userid,
                'rawgrade' => $maxgrade * $score->scaled,
                'dategraded' => $score->timemodified,
                'datesubmitted' => $score->timemodified,
            ];
        }

        return $grades;
    }

    /**
     * Get an deletion list of user grades and feedback for the gradebook.
     *
     * This method is used to delete all autmatic gradings when grading method is set to manual.
     *
     * @param int $userid int or 0 for all users
     * @return array of grade data formated for the gradebook api
     *         The data required by the gradebook api is userid,
     *                                                   rawgrade (null to delete),
     *                                                   dategraded,
     *                                                   datesubmitted
     */
    private function get_user_grades_for_deletion(int $userid = 0): array {
        $grades = [];

        if ($userid) {
            $grades[$userid] = [
                'userid' => $userid,
                'rawgrade' => null,
                'dategraded' => time(),
                'datesubmitted' => time(),
            ];
        } else {
            $manager = manager::create_from_instance($this->instance);
            $users = get_enrolled_users($manager->get_context(), 'mod/h5pactivity:submit');
            foreach ($users as $user) {
                $grades[$user->id] = [
                    'userid' => $user->id,
                    'rawgrade' => null,
                    'dategraded' => time(),
                    'datesubmitted' => time(),
                ];
            }
        }
        return $grades;
    }
}
