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
 * Customfield shared persistent class
 *
 * @package   core_customfield
 * @copyright 2025 David Carrillo <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shared extends persistent {
    /**
     * Database table.
     */
    const TABLE = 'customfield_shared';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
                'categoryid' => [
                        'type' => PARAM_INT,
                ],
                'component' => [
                        'type' => PARAM_COMPONENT,
                ],
                'area' => [
                        'type' => PARAM_COMPONENT,
                ],
                'itemid' => [
                        'type' => PARAM_INT,
                        'optional' => true,
                        'default' => 0,
                ],
        ];
    }
}
