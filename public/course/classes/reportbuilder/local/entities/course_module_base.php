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

namespace core_course\reportbuilder\local\entities;

use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\database;

/**
 * Abstract base course module entity, to be extended by all course module specific entities
 *
 * @package     core_course
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class course_module_base extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'course_modules',
        ];
    }

    /**
     * Return course modules joins
     *
     * @param string $modulename Name of the module, e.g. "forum"
     * @param string $modulefieldid Field used to select the module ID, e.g. "f.id"
     * @return string[]
     */
    final public function get_course_modules_joins(string $modulename, string $modulefieldid): array {
        [
            'context' => $contextalias,
            'course_modules' => $coursemodulesalias,
        ] = $this->get_table_aliases();

        $modulesalias = database::generate_alias();

        return [
            "JOIN {course_modules} {$coursemodulesalias}
               ON {$coursemodulesalias}.instance = {$modulefieldid}",
            "JOIN {modules} {$modulesalias}
               ON {$modulesalias}.id = {$coursemodulesalias}.module AND {$modulesalias}.name = '{$modulename}'",
            "JOIN {context} {$contextalias}
               ON {$contextalias}.contextlevel = " . CONTEXT_MODULE . " AND {$contextalias}.instanceid = {$coursemodulesalias}.id",
        ];
    }
}
