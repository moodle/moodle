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
 * Contains class for displaying a collection.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use stdClass;

/**
 * Class for displaying a badge competency.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection_exporter extends exporter {

    /**
     * Either map version 1 data to version 2 or return it untouched.
     *
     * @param stdClass $data The remote data.
     * @param string $apiversion The backpack version used to communicate remotely.
     * @return stdClass
     */
    public static function map_external_data($data, $apiversion) {
        if ($apiversion == OPEN_BADGES_V1) {
            $result = new stdClass();
            $result->entityType = 'BackpackCollection';
            $result->entityId = $data->groupId;
            $result->name = $data->name;
            $result->description = $data->description;
            $result->assertions = [];
            return $result;
        }
        return $data;
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'entityType' => [
                'type' => PARAM_ALPHA,
                'description' => 'BackpackCollection',
            ],
            'entityId' => [
                'type' => PARAM_RAW,
                'description' => 'Unique identifier for this collection',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Collection name',
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'description' => 'Collection description',
            ],
            'share_url' => [
                'type' => PARAM_URL,
                'description' => 'Url to view this collection',
            ],
            'published' => [
                'type' => PARAM_BOOL,
                'description' => 'True means this collection is public.',
            ],
            'assertions' => [
                'type' => PARAM_RAW,
                'description' => 'List of assertion ids in this collection',
                'multiple' => true,
            ],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return array(
            'context' => 'context',
        );
    }
}
