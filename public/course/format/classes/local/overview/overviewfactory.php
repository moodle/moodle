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

namespace core_courseformat\local\overview;

use cm_info;
use core_courseformat\activityoverviewbase;

/**
 * Plugin overview instance factory.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewfactory {
    /**
     * Creates an instance of the appropriate overview class for the given course module.
     *
     * @param cm_info $cm The course module information.
     * @return activityoverviewbase An instance of the appropriate overview class.
     */
    public static function create(cm_info $cm): activityoverviewbase {
        $classname = "\\mod_{$cm->modname}\\courseformat\\overview";
        if (!class_exists($classname)) {
            $classname = resourceoverview::class;
        }

        $result = \core\di::get_container()->make($classname, ['cm' => $cm]);
        if (!($result instanceof activityoverviewbase)) {
            throw new \coding_exception("Class $classname must extend " . activityoverviewbase::class);
        }

        return $result;
    }

    /**
     * Checks if a given activity module has an overview integration.
     *
     * The method search for an integration class named `\mod_{modname}\course\overview`.
     *
     * @param string $modname The activity module name.
     * @return bool True if the activity module has an overview integration, false otherwise.
     */
    public static function activity_has_overview_integration(string $modname): bool {
        $classname = 'mod_' . $modname . '\courseformat\overview';
        if ($modname === 'resource') {
            $classname = 'core_courseformat\local\overview\resourceoverview';
        }
        return class_exists($classname);
    }
}
