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

namespace core\task;

/**
 * Simple task to automatically set the course visibility to hidden when the course end date matches the current day.
 *
 * @package   core
 * @copyright 2023 Sara Arjona <sara@moodle.com> based on code from 2016 Tim Gagen and Amanda Doughty
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hide_ended_courses_task extends show_started_courses_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('hideendedcoursestask', 'course');
    }

    protected function get_field_date(): string {
        return 'enddate';
    }

    protected function get_visibility(): int {
        return 0;
    }

    protected function get_trace_message(): string {
        return 'Hide';
    }

    protected function get_event_classname(): string {
        return '\core\event\course_ended';
    }
}
