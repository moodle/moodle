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

declare(strict_types=1);

namespace tool_cohortroles;

use core\event\user_deleted;

/**
 * Plugin event observer callbacks
 *
 * @package    tool_cohortroles
 * @copyright  2023 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observers {

    /**
     * User deleted event, remove cohort role assignments specific to them
     *
     * @param user_deleted $event
     */
    public static function user_deleted(user_deleted $event): void {
        $cohortroleassignments = cohort_role_assignment::get_records(['userid' => $event->objectid]);
        foreach ($cohortroleassignments as $cohortroleassignment) {
            $cohortroleassignment->delete();
        }
    }
}
