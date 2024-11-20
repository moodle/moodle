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

namespace core_customfield;

use core\persistent;

/**
 * Customfield category persistent class
 *
 * @package   core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends persistent {
    /**
     * Database table.
     */
    const TABLE = 'customfield_category';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return array(
                'name' => [
                        'type' => PARAM_TEXT,
                ],
                'description' => [
                        'type' => PARAM_RAW,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                ],
                'descriptionformat' => [
                        'type' => PARAM_INT,
                        'default' => FORMAT_MOODLE,
                        'optional' => true,
                        'null' => NULL_ALLOWED,
                ],
                'component' => [
                        'type' => PARAM_COMPONENT
                ],
                'area' => [
                        'type' => PARAM_COMPONENT
                ],
                'itemid' => [
                        'type' => PARAM_INT,
                        'optional' => true,
                        'default' => 0
                ],
                'contextid' => [
                        'type' => PARAM_INT,
                        'optional' => false
                ],
                'sortorder' => [
                        'type' => PARAM_INT,
                        'optional' => true,
                        'default' => -1,
                        'null' => NULL_ALLOWED,
                ],
        );
    }
}
