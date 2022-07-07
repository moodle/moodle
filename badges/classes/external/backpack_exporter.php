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
 * Contains competency class for displaying a badge backpack.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for displaying a badge competency.
 *
 * @package   core_badges
 * @copyright 2018 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backpack_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Backpack id',
            ],
            'backpackapiurl' => [
                'type' => PARAM_URL,
                'description' => 'Backpack API URL',
            ],
            'backpackweburl' => [
                'type' => PARAM_URL,
                'description' => 'Backpack Website URL',
            ],
            'sitebackpack' => [
                'type' => PARAM_BOOL,
                'description' => 'Is this the current site backpack',
            ],
            'apiversion' => [
                'type' => PARAM_FLOAT,
                'description' => 'API version supported',
            ],
            'sortorder' => [
                'type' => PARAM_INT,
                'description' => 'Sort order'
            ]
        ];
    }
}
