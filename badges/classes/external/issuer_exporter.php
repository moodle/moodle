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

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for displaying a badge competency.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issuer_exporter extends exporter {

    /**
     * Map data depending on the version.
     *
     * @param \stdClass $data The remote data.
     * @param string $apiversion The backpack version used to communicate remotely.
     * @return \stdClass
     */
    public static function map_external_data($data, $apiversion) {
        $mapped = new \stdClass();
        if (isset($data->entityType)) {
            $mapped->type = $data->entityType;
        } else {
            $mapped->type = $data->type;
        }
        if (isset($data->entityId)) {
            $mapped->id = $data->entityId;
        } else {
            $mapped->id = $data->id;
        }
        $mapped->name = $data->name;
        $mapped->email = $data->email;
        $mapped->url = $data->url;
        return $mapped;
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'type' => [
                'type' => PARAM_ALPHA,
                'description' => 'Issuer',
            ],
            'id' => [
                'type' => PARAM_RAW,
                'description' => 'Unique identifier for this issuer',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Name of the issuer',
            ],
            'email' => [
                'type' => PARAM_EMAIL,
                'description' => 'Email of the issuer',
            ],
            'url' => [
                'type' => PARAM_URL,
                'description' => 'URL for this issuer',
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
