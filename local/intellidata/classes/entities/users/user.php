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
 * Class for preparing data for Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\users;


use core\invalid_persistent_exception;
use local_intellidata\helpers\ParamsHelper;

/**
 * Class for preparing data for Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'users';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'username' => [
                'type' => PARAM_RAW,
                'description' => 'User username.',
                'default' => '',
            ],
            'fullname' => [
                'type' => PARAM_RAW,
                'description' => 'User fullname.',
                'default' => '',
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when user was created.',
                'default' => 0,
            ],
            'email' => [
                'type' => PARAM_RAW_TRIMMED,
                'description' => 'User Email.',
                'default' => '',
            ],
            'lang' => [
                'type' => PARAM_TEXT,
                'description' => 'User locale.',
                'default' => '',
            ],
            'country' => [
                'type' => PARAM_TEXT,
                'description' => 'User country.',
                'default' => '',
            ],
            'firstaccess' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp users first access.',
                'default' => 0,
            ],
            'lastaccess' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp users last access.',
                'default' => 0,
            ],
            'lastlogin' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp users last login.',
                'default' => 0,
            ],
            'state' => [
                'type' => PARAM_INT,
                'description' => 'User status.',
                'default' => 1,
            ],
            'idnumber' => [
                'type' => PARAM_TEXT,
                'description' => 'User ID number.',
                'default' => '',
            ],
            'firstname' => [
                'type' => PARAM_TEXT,
                'description' => 'User first name.',
                'default' => '',
            ],
            'lastname' => [
                'type' => PARAM_TEXT,
                'description' => 'User last name.',
                'default' => '',
            ],
            'institution' => [
                'type' => PARAM_TEXT,
                'description' => 'User institution.',
                'default' => '',
            ],
            'department' => [
                'type' => PARAM_TEXT,
                'description' => 'User department.',
                'default' => '',
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
        $object->fullname = fullname($object);
        $object->state = ($object->confirmed && (isset($object->suspended) && !$object->suspended)) ?
            ParamsHelper::STATE_ACTIVE : ParamsHelper::STATE_INACTIVE;
        if (!empty($object->lastlogin) || !empty($object->currentlogin)) {
            $object->lastlogin = max($object->lastlogin, $object->currentlogin);
        }

        return $object;
    }
}
