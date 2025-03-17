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

namespace editor_tiny;

use context;

/**
 * An interface representing a plugin with configuration for external functions.
 *
 * @package    editor_tiny
 * @copyright  2025 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface plugin_with_configuration_for_external {
    /**
     * Get the configuration for external functions provided by this plugin.
     *
     * @param context $context The context that the editor is used within.
     * @return array
     */
    public static function get_plugin_configuration_for_external(context $context): array;
}
