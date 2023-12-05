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
namespace fake_hooktest;

/**
 * Hook discovery for fake plugin.
 *
 * @package   core
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hooks implements \core\hook\discovery_agent {
    public static function discover_hooks(): array {
        return [
            'fake_hooktest\hook\hook_replacing_callback' => [
                'class' => 'fake_hooktest\hook\hook_replacing_callback',
                'description' => 'Hook replacing callback',
                'tags' => ['test'],
            ],
            'fake_hooktest\hook\hook_replacing_class_callback' => [
                'class' => 'fake_hooktest\hook\hook_replacing_class_callback',
                'description' => 'Hook replacing class callback',
                'tags' => ['test'],
            ],
        ];
    }
}
