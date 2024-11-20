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

namespace core\hook;

/**
 * Interface for describing of lib.php callbacks that were deprecated by the hook.
 *
 * Please note that, from Moodle 4.4, you can instead use the \core\attribute\hook\replaces_callback attribute.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface deprecated_callback_replacement {
    /**
     * Returns list of lib.php plugin callbacks that were deprecated by the hook.
     *
     * It is used for automatic debugging messages and if present it
     * also skips relevant legacy callbacks in plugins that implemented callbacks
     * for this hook (to allow plugin compatibility with multiple Moodle branches).
     *
     * @return array
     */
    public static function get_deprecated_plugin_callbacks(): array;
}
