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
 * Hook fixtures for testing of hooks.
 *
 * @package    tool_usertours
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

final class nocomponent_serverside_filter_fixture {
}

final class nocomponent_serverside_hook_fixtures {
    public static function example_serverside_hook(
        \tool_usertours\hook\before_serverside_filter_fetch $hook
    ): void {
        $hook->add_filter_by_classname(\nocomponent_serverside_filter_fixture::class);
    }
}

$callbacks = [
    [
        'hook' => \tool_usertours\hook\before_serverside_filter_fetch::class,
        'callback' => \nocomponent_serverside_hook_fixtures::class . '::example_serverside_hook',
    ],
];
