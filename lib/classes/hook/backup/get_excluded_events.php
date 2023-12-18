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

namespace core\hook\backup;

use core\hook\described_hook;

/**
 * Get a list of event names which are excluded to trigger from course changes in automated backup.
 *
 * @package    core
 * @copyright  2023 Tomo Tsuyuki <tomotsuyuki@catalyst-au.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_excluded_events implements described_hook {

    /**
     * @var string[] Array of event names.
     */
    private $events = [];

    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Get a list of event names which are excluded to trigger from course changes in automated backup.';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['backup'];
    }

    /**
     * Add an array of event names which are excluded to trigger from course changes in automated backup.
     * This is set from plugin hook.
     * e.g. ['\local_course\event\update', '\local_course\event\sync']
     *
     * @param string[] $events Array of event name strings
     * @return void
     */
    public function add_events(array $events): void {
        $this->events = array_merge($this->events, $events);
    }

    /**
     * Get an array of event names which are excluded to trigger from course changes in automated backup.
     * This is called after dispatch for the hook and use values to exclude events for backup.
     *
     * @return array
     */
    public function get_events(): array {
        return $this->events;
    }
}
