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

namespace report_log;

/**
 * Helper class for displaying logs.
 *
 * @package    report_log
 * @copyright  2024 Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Returns a string that attempts to infer event context display when context no longer exists.
     * This can be used to show a minimum amount of info in log tables, but ideally this would
     * be a last resort as log display should not change.
     *
     * @param \core\event\base|\stdClass $event
     * @return string inferred context display, or an empty string.
     */
    public static function get_context_fallback(\core\event\base|\stdClass $event): string {
        if (empty($event) || empty($event->contextlevel) || empty($event->component)) {
            return '';
        }

        $name = \context_helper::get_level_name($event->contextlevel);
        $instanceid = $event->contextinstanceid ?? '';

        // Use the plugin name for modules and blocks.
        if ($event->contextlevel == CONTEXT_MODULE || $event->contextlevel == CONTEXT_BLOCK) {
            // Some events list the component as core and store the modulename in other.
            $component = !empty($event->other['modulename']) ? 'mod_' . $event->other['modulename'] : $event->component;

            // Use module name to keep names consistent. Making component human readable is a close approximation.
            $modulename = preg_replace('/^(mod_|block_)/', '', $component);
            $name = str_replace('_', ' ', $modulename);
        }

        // Can't access context get_url methods, so don't bother showing a url.
        return get_string('missingcontext', 'report_log', [
            'name' => strtolower($name),
            'instanceid' => $instanceid,
        ]);
    }
}
