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
 * Class for preparing data for UserInfoDatas.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\userinfodatas;

use core\invalid_persistent_exception;

/**
 * Class for preparing data for UserInfoDatas.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userinfodata extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'userinfodatas';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Info Data ID.',
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'fieldid' => [
                'type' => PARAM_INT,
                'description' => 'Field ID.',
                'default' => 0,
            ],
            'data' => [
                'type' => PARAM_RAW,
                'description' => 'Data Body.',
                'default' => '',
            ],
            'dataformat' => [
                'type' => PARAM_INT,
                'description' => 'Data Format.',
                'default' => 0,
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     *
     * @return \stdClass
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {

        foreach (self::define_properties() as $property => $propertydata) {
            if (empty($object->{$property})) {
                $object->{$property} = isset($propertydata['default']) ? $propertydata['default'] : null;
            }
        }

        return $object;
    }

    /**
     * Hook to execute after an export.
     *
     * @param $record
     * @return mixed
     */
    public function after_export($record) {
        $record->event = '\core\event\user_created';
        return $record;
    }
}
