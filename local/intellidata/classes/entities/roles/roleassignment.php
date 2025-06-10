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
 * Class for preparing data for Roles Assignment.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\roles;

use local_intellidata\helpers\RolesHelper;

/**
 * Class for preparing data for Role Assignment.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class roleassignment extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'roleassignments';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Role assignment ID.',
                'default' => 0,
            ],
            'roleid' => [
                'type' => PARAM_INT,
                'description' => 'Role ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'courseid' => [
                'type' => PARAM_INT,
                'description' => 'Course ID.',
                'default' => 0,
            ],
            'contexttype' => [
                'type' => PARAM_INT,
                'description' => 'Assignment context type.',
                'default' => 0,
            ],
            'component' => [
                'type' => PARAM_TEXT,
                'description' => 'Assignment component.',
                'default' => '',
            ],
            'itemid' => [
                'type' => PARAM_INT,
                'description' => 'Assignment item ID.',
                'default' => 0,
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        global $DB;

        $context = $DB->get_record('context', ['id' => $object->contextid]);
        if ($context) {
            $object->courseid = $context->instanceid;
            $object->contexttype = RolesHelper::get_contexttype($context->contextlevel);;
        }

        return $object;
    }
}
