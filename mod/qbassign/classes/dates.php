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
 * Contains the class for fetching the important dates in mod_qbassign for a given module instance and a user.
 *
 * @package   mod_qbassign
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_qbassign;

use core\activity_dates;

/**
 * Class for fetching the important dates in mod_qbassign for a given module instance and a user.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {

    /**
     * Returns a list of important dates in mod_qbassign
     *
     * @return array
     */
    protected function get_dates(): array {
        global $CFG;

        require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

        $course = get_course($this->cm->course);
        $context = \context_module::instance($this->cm->id);
        $qbassign = new \qbassign($context, $this->cm, $course);

        $timeopen = $this->cm->customdata['allowsubmissionsfromdate'] ?? null;
        $timedue = $this->cm->customdata['duedate'] ?? null;

        $activitygroup = groups_get_activity_group($this->cm, true);
        if ($activitygroup) {
            if ($qbassign->can_view_grades()) {
                $groupoverride = \cache::make('mod_qbassign', 'overrides')->get("{$this->cm->instance}_g_{$activitygroup}");
                if (!empty($groupoverride->allowsubmissionsfromdate)) {
                    $timeopen = $groupoverride->allowsubmissionsfromdate;
                }
                if (!empty($groupoverride->duedate)) {
                    $timedue = $groupoverride->duedate;
                }
            }
        }

        $now = time();
        $dates = [];

        if ($timeopen) {
            $openlabelid = $timeopen > $now ? 'activitydate:submissionsopen' : 'activitydate:submissionsopened';
            $date = [
                'dataid' => 'allowsubmissionsfromdate',
                'label' => get_string($openlabelid, 'mod_qbassign'),
                'timestamp' => (int) $timeopen,
            ];
            if ($course->relativedatesmode && $qbassign->can_view_grades()) {
                $date['relativeto'] = $course->startdate;
            }
            $dates[] = $date;
        }

        if ($timedue) {
            $date = [
                'dataid' => 'duedate',
                'label' => get_string('activitydate:submissionsdue', 'mod_qbassign'),
                'timestamp' => (int) $timedue,
            ];
            if ($course->relativedatesmode && $qbassign->can_view_grades()) {
                $date['relativeto'] = $course->startdate;
            }
            $dates[] = $date;
        }

        return $dates;
    }
}
