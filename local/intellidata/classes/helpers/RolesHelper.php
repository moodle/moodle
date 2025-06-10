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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class RolesHelper {

    /**
     * Context list.
     */
    const CONTEXTLIST = [
        CONTEXT_COURSE => ParamsHelper::CONTEXT_COURSE,
        CONTEXT_SYSTEM => ParamsHelper::CONTEXT_SYSTEM,
        CONTEXT_USER => ParamsHelper::CONTEXT_USER,
        CONTEXT_COURSECAT => ParamsHelper::CONTEXT_COURSECAT,
    ];

    /**
     * Get context type.
     *
     * @param $contextlevel
     * @return int
     */
    public static function get_contexttype($contextlevel) {

        if (isset(self::CONTEXTLIST[$contextlevel])) {
            return self::CONTEXTLIST[$contextlevel];
        }

        return ParamsHelper::CONTEXT_SYSTEM;
    }
}
