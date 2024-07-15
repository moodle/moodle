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

declare(strict_types=1);

namespace core_course\reportbuilder\local\formatters;

use core_completion\progress;
use core_reportbuilder\local\helpers\format;
use stdClass;

/**
 * Formatters for the course completion entity
 *
 * @package     core_course
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion {

    /**
     * Return completion progress as a percentage
     *
     * @param string|null $value
     * @param stdClass $row
     * @return string
     */
    public static function completion_progress(?string $value, stdClass $row): string {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        // Do not show progress if there is no userid.
        if (!$row->userid) {
            return '';
        }

        // Make sure courseid and userid have a value, specially userid because get_course_progress_percentage() defaults
        // to the current user if this is null and the result would be wrong.
        $courseid = (int) $row->courseid;
        $userid = (int) $row->userid;
        if ($courseid === 0 || $userid === 0) {
            return format::percent(0);
        }

        $course = get_course($courseid);
        $progress = (float) progress::get_course_progress_percentage($course, $userid);
        return format::percent($progress);
    }

    /**
     * Return number of days for methods daystakingcourse and daysuntilcompletion
     *
     * @param int|null $value
     * @param stdClass $row
     * @return int|null
     *
     * @deprecated since Moodle 4.5 - please do not use this function any more
     */
    #[\core\attribute\deprecated(null, mdl: 'MDL-82467', since: '4.5')]
    public static function get_days(?int $value, stdClass $row): ?int {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        // Do not show anything if there is no userid.
        if (!$row->userid) {
            return null;
        }
        return $value;
    }
}
