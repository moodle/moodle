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
 * Contains class for displaying a badgeclass.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;

/**
 * Class for displaying a badge competency.
 *
 * @package   core_badges
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badgeclass_exporter extends exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param mixed $data - Either an stdClass or an array of values.
     * @param array $related - An optional list of pre-loaded objects related to this object.
     */
    public function __construct($data, $related = array()) {
        // Having mixed $data is causing some issues. As this class is treating $data as an object everywhere, it can be converted
        // to object at this point, to avoid errors and get the expected behaviour always.
        // $data is an array when this class is a request exporter in backpack_api_mapping, but it is an object when this is
        // used as a response exporter.
        $data = (object) $data;

        $pick = $this->pick_related();
        foreach ($pick as $one) {
            $isarray = false;
            // Allow [] to mean an array of values.
            if (substr($one, -2) === '[]') {
                $one = substr($one, 0, -2);
                $isarray = true;
            }
            $prefixed = 'related_' . $one;
            if (property_exists($data, $one) && !array_key_exists($one, $related)) {
                if ($isarray) {
                    $newrelated = [];
                    foreach ($data->$one as $item) {
                        $newrelated[] = (object) $item;
                    }
                    $related[$one] = $newrelated;
                } else {
                    $related[$one] = (object) $data->$one;
                }
                unset($data->$one);
            } else if (property_exists($data, $prefixed) && !array_key_exists($one, $related)) {
                if ($isarray) {
                    $newrelated = [];
                    foreach ($data->$prefixed as $item) {
                        $newrelated[] = (object) $item;
                    }
                    $related[$one] = $newrelated;
                } else {
                    $related[$one] = (object) $data->$prefixed;
                }
                unset($data->$prefixed);
            } else if (!array_key_exists($one, $related)) {
                $related[$one] = null;
            }
        }
        parent::__construct($data, $related);
    }

    /**
     * List properties passed in $data that should be moved to $related in the constructor.
     *
     * @return array A list of properties to move from $data to $related.
     */
    public static function pick_related() {
        return ['alignment[]', 'criteria'];
    }

    /**
     * Map data from a request response to the internal structure.
     *
     * @param stdClass $data The remote data.
     * @param string $apiversion The backpack version used to communicate remotely.
     * @return stdClass
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
        $mapped->image = $data->image;
        $mapped->issuer = $data->issuer;
        $mapped->description = $data->description;
        if (isset($data->openBadgeId)) {
            $mapped->hostedUrl = $data->openBadgeId;
        } else {
            $mapped->hostedUrl = $data->id;
        }

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
                'description' => 'BadgeClass',
            ],
            'id' => [
                'type' => PARAM_RAW,
                'description' => 'Unique identifier for this badgeclass',
            ],
            'issuer' => [
                'type' => PARAM_RAW,
                'description' => 'Unique identifier for this badgeclass',
                'optional' => true,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Name of the badgeclass',
            ],
            'image' => [
                'type' => PARAM_URL,
                'description' => 'URL to the image.',
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'description' => 'Description of the badge class.',
            ],
            'hostedUrl' => [
                'type' => PARAM_RAW,
                'description' => 'Identifier of the open badge for this assertion',
                'optional' => true,
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
            'alignment' => 'stdClass[]?',
            'criteria' => 'stdClass?',
        );
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return array(
            'alignment' => array(
                'type' => alignment_exporter::read_properties_definition(),
                'optional' => true,
                'multiple' => true
            ),
            'criteriaUrl' => array(
                'type' => PARAM_URL,
                'optional' => true
            ),
            'criteriaNarrative' => array(
                'type' => PARAM_TEXT,
                'optional' => true
            )
        );
    }

    /**
     * We map from related data passed as data to this exporter to clean exportable values.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output) {
        global $DB;
        $result = [];

        if (array_key_exists('alignment', $this->related) && $this->related['alignment'] !== null) {
            $alignment = [];
            foreach ($this->related['alignment'] as $alignment) {
                $exporter = new alignment_exporter($alignment, $this->related);
                $alignments[] = $exporter->export($output);
            }
            $result['alignment'] = $alignments;
        }
        if (array_key_exists('criteria', $this->related) && $this->related['criteria'] !== null) {
            if (property_exists($this->related['criteria'], 'id') && $this->related['criteria']->id !== null) {
                $result['criteriaUrl'] = $this->related['criteria']->id;
            }
            if (property_exists($this->related['criteria'], 'narrative') && $this->related['criteria']->narrative !== null) {
                $result['criteriaNarrative'] = $this->related['criteria']->narrative;
            }
        }

        return $result;
    }
}
