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
 * Trait for course completion creation in unit tests
 *
 * @package     core_completion
 * @category    test
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/completion/criteria/completion_criteria.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_role.php');

/**
 * Trait for unit tests and completion.
 *
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait completion_creation {

    /** @var stdClass The course object. */
    public $course;

    /** @var context The course context object. */
    public $coursecontext;

    /** @var stdClass The course module object */
    public $cm;

    /**
     * Create completion information.
     */
    public function create_course_completion() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $coursecontext = context_course::instance($course->id);

        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'completion' => 1]);
        $modulecontext = context_module::instance($assign->cmid);
        $cm = get_coursemodule_from_id('assign', $assign->cmid);

        // Set completion rules.
        $completion = new \completion_info($course);

        $criteriadata = (object) [
            'id' => $course->id,
            'criteria_activity' => [
                $cm->id => 1
            ]
        ];
        $criterion = new \completion_criteria_activity();
        $criterion->update_config($criteriadata);

        $criteriadata = (object) [
            'id' => $course->id,
            'criteria_role' => [3 => 3]
        ];
        $criterion = new \completion_criteria_role();
        $criterion->update_config($criteriadata);

        // Handle overall aggregation.
        $aggdata = array(
            'course'        => $course->id,
            'criteriatype'  => COMPLETION_CRITERIA_TYPE_ACTIVITY
        );
        $aggregation = new \completion_aggregation($aggdata);
        $aggregation->setMethod(COMPLETION_AGGREGATION_ALL);
        $aggregation->save();
        $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ROLE;
        $aggregation = new \completion_aggregation($aggdata);
        $aggregation->setMethod(COMPLETION_AGGREGATION_ANY);
        $aggregation->save();

        // Set variables for access in tests.
        $this->course = $course;
        $this->coursecontext = $coursecontext;
        $this->cm = $cm;
    }

    /**
     * Complete some of the course completion criteria.
     *
     * @param  stdClass $user The user object
     */
    public function complete_course($user) {
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id, 'student');
        $completion = new \completion_info($this->course);
        $criteriacompletions = $completion->get_completions($user->id, COMPLETION_CRITERIA_TYPE_ROLE);
        $criteria = completion_criteria::factory(['id' => 3, 'criteriatype' => COMPLETION_CRITERIA_TYPE_ROLE]);
        foreach ($criteriacompletions as $ccompletion) {
            $criteria->complete($ccompletion);
        }
        // Set activity as complete.
        $completion->update_state($this->cm, COMPLETION_COMPLETE, $user->id);
    }
}
