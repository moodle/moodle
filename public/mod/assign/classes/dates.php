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
 * Contains the class for fetching the important dates in mod_assign for a given module instance and a user.
 *
 * @package   mod_assign
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_assign;

use core\activity_dates;

/**
 * Class for fetching the important dates in mod_assign for a given module instance and a user.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {

    /** @var int|null $timedue the activity due date */
    private ?int $timedue;

    /**
     * Returns a list of important dates in mod_assign
     *
     * @return array
     */
    protected function get_dates(): array {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $this->timedue = null;

        $course = get_course($this->cm->course);
        $context = \context_module::instance($this->cm->id);
        $assign = new \assign($context, $this->cm, $course);

        $instance = $assign->get_instance($this->userid);
        $timeopen = $instance->allowsubmissionsfromdate ?? null;
        $timedue = $instance->duedate ?? null;

        $useroverride = $DB->get_record('assign_overrides', [
            'assignid' => $this->cm->instance,
            'userid' => $this->userid,
        ]);
        $overrides = $useroverride ? [$useroverride] : [];

        $groups = groups_get_user_groups($this->cm->course, $this->userid);
        if (!empty($groups[0])) {
            [$groupidsql, $params] = $DB->get_in_or_equal(array_values($groups[0]), SQL_PARAMS_NAMED);
            $params['assignid'] = $this->cm->instance;
            $overrides = array_merge($overrides, $DB->get_records_select(
                'assign_overrides',
                "assignid = :assignid AND groupid {$groupidsql}",
                $params,
            ));
        }

        foreach ($overrides as $override) {
            if (isset($override->allowsubmissionsfromdate)) {
                $timeopen = empty($timeopen) ? $override->allowsubmissionsfromdate :
                    min($timeopen, $override->allowsubmissionsfromdate);
            }
            if (isset($override->duedate)) {
                $timedue = empty($timedue) || empty($override->duedate) ? $override->duedate : max($timedue, $override->duedate);
            }
        }

        $userflags = $assign->get_user_flags($this->userid, false);
        if (!empty($userflags->extensionduedate)) {
            $timedue = empty($timedue) ? $userflags->extensionduedate : max($timedue, $userflags->extensionduedate);
        }

        $now = \core\di::get(\core\clock::class)->time();
        $dates = [];

        if ($timeopen) {
            $openlabelid = $timeopen > $now ? 'activitydate:submissionsopen' : 'activitydate:submissionsopened';
            $date = [
                'dataid' => 'allowsubmissionsfromdate',
                'label' => get_string($openlabelid, 'mod_assign'),
                'timestamp' => (int) $timeopen,
            ];
            if ($course->relativedatesmode && $assign->can_view_grades()) {
                $date['relativeto'] = $course->startdate;
            }
            $dates[] = $date;
        }

        if ($timedue) {
            $this->timedue = (int) $timedue;
            $date = [
                'dataid' => 'duedate',
                'label' => get_string('activitydate:submissionsdue', 'mod_assign'),
                'timestamp' => $this->timedue,
            ];
            if ($course->relativedatesmode && $assign->can_view_grades()) {
                $date['relativeto'] = $course->startdate;
            }
            $dates[] = $date;
        }

        return $dates;
    }

    /**
     * Returns the dues date data, if any.
     * @return int|null the due date timestamp or null if not set.
     */
    public function get_due_date(): ?int {
        if (!isset($this->timedue)) {
            $this->get_dates();
        }
        return $this->timedue;
    }
}
