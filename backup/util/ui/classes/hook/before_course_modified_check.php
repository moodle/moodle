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

namespace core_backup\hook;

/**
 * Get a list of event names which are excluded to trigger from course changes in automated backup.
 *
 * @package    core_backup
 * @copyright  2023 Tomo Tsuyuki <tomotsuyuki@catalyst-au.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Get a list of event names which are excluded to trigger from course changes in automated backup.')]
#[\core\attribute\tags('backup')]
final class before_course_modified_check {
    /**
     * @var string[] Array of event names.
     */
    private $events = [];

    /**
     * Add an array of event names which are excluded to trigger from course changes in automated backup.
     *
     * @param string $events,... Array of event name strings
     */
    public function exclude_events(string ...$events): void {
        $this->events = array_merge($this->events, $events);
    }

    /**
     * Get an array of event names which are excluded to trigger from course changes in automated backup.
     * This is called after dispatch for the hook and use values to exclude events for backup.
     *
     * @return array
     */
    public function get_excluded_events(): array {
        return $this->events;
    }
}
