<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace test_plugin\hook;

/**
 * Fixture for testing of hooks.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hook implements
    \core\hook\described_hook,
    \core\hook\deprecated_callback_replacement {

    /**
     * Hook description.
     */
    public static function get_hook_description(): string {
        return 'Test hook 1.';
    }

    /**
     * Deprecation info.
     */
    public static function get_deprecated_plugin_callbacks(): array {
        return ['oldcallback'];
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['test'];
    }
}
