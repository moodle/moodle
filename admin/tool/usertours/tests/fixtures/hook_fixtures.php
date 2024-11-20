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

namespace tool_usertours\test\hook;

defined('MOODLE_INTERNAL') || die();

final class serverside_filter_fixture extends \tool_usertours\local\filter\base {
}

final class clientside_filter_fixture extends \tool_usertours\local\clientside_filter\clientside_filter {
}

final class hook_fixtures {
    public static function example_serverside_hook(
        \tool_usertours\hook\before_serverside_filter_fetch $hook
    ): void {
        // Add a valid serverside and an invalid clientside filter.
        $hook->add_filter_by_classname(\tool_usertours\test\hook\serverside_filter_fixture::class);
        $hook->remove_filter_by_classname(\tool_usertours\local\filter\accessdate::class);
    }

    public static function example_clientside_hook(
        \tool_usertours\hook\before_clientside_filter_fetch $hook
    ): void {
        $hook->add_filter_by_classname(\tool_usertours\test\hook\clientside_filter_fixture::class);
        $hook->remove_filter_by_classname(\tool_usertours\local\clientside_filter\cssselector::class);
    }
}

$callbacks = [
    [
        'hook' => \tool_usertours\hook\before_serverside_filter_fetch::class,
        'callback' => \tool_usertours\test\hook\hook_fixtures::class . '::example_serverside_hook',
    ],
    [
        'hook' => \tool_usertours\hook\before_clientside_filter_fetch::class,
        'callback' => \tool_usertours\test\hook\hook_fixtures::class . '::example_clientside_hook',
    ],
];
