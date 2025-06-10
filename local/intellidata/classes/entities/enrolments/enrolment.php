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
namespace local_intellidata\entities\enrolments;


/**
 * Class for preparing data for Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolment extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'enrolments';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Enrolment ID.',
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
            'enroltype' => [
                'type' => PARAM_TEXT,
                'description' => 'Enrolment type.',
                'default' => '',
            ],
            'status' => [
                'type' => PARAM_INT,
                'description' => 'Enrolment status.',
                'default' => 1,
            ],
            'timestart' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when enrollment should start.',
                'default' => 0,
            ],
            'timeend' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when enrollment should end.',
                'default' => 0,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when enrollment record created.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when enrollment record modified.',
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

        if ($enrol = $DB->get_record('enrol', ['id' => $object->enrolid])) {
            $object->courseid = $enrol->courseid;
            $object->enroltype = $enrol->enrol;
        }

        return $object;
    }
}
