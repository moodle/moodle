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
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\activities;

use core\invalid_persistent_exception;

/**
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'activities';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Course Module ID.',
                'default' => 0,
            ],
            'courseid' => [
                'type' => PARAM_INT,
                'description' => 'Course ID.',
                'default' => 0,
            ],
            'section' => [
                'type' => PARAM_INT,
                'description' => 'Course sections ID.',
                'default' => 0,
            ],
            'module' => [
                'type' => PARAM_TEXT,
                'description' => 'Module type.',
                'default' => '',
            ],
            'instance' => [
                'type' => PARAM_INT,
                'description' => 'Course module instance ID.',
                'default' => 0,
            ],
            'instancename' => [
                'type' => PARAM_RAW,
                'description' => 'Course module instance title.',
                'default' => '',
            ],
            'visible' => [
                'type' => PARAM_INT,
                'description' => 'Course module status',
                'default' => 1,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when course module created.',
                'default' => 0,
            ],
            'completion' => [
                'type' => PARAM_INT,
                'description' => 'Completion tracking.',
                'default' => 0,
            ],
            'completionexpected' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when course module created.',
                'default' => 0,
            ],
            'params' => [
                'type' => PARAM_RAW,
                'description' => 'Additional instance parameters.',
                'default' => '',
            ],
            'availability' => [
                'type' => PARAM_RAW,
                'description' => 'Course module availability.',
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
        global $DB;

        $activitdata = new \stdClass();
        $activitdata->id = $object->id;
        $activitdata->courseid = $object->course;

        if (!count($fields)) {
            if (empty($object->instance)) {
                $object = $DB->get_record('course_modules', ['id' => $object->id]);
            }

            if (!isset($object->modulename) && $object->module) {
                $module = $DB->get_record('modules', ['id' => $object->module]);
                $object->modulename = $module->name;
            }

            if ($instance = $DB->get_record($object->modulename, ['id' => $object->instance])) {
                $activitdata->instance = $instance->id;
                $activitdata->instancename = $instance->name;
                $activitdata->params = observer::set_additional_params($object->modulename, $instance);
            }

            $activitdata->module = $object->modulename;
            $activitdata->section = $object->section;
            $activitdata->visible = isset($object->visible) ? $object->visible : 1;
            $activitdata->timecreated = $object->added;
            $activitdata->availability = isset($object->availability) ? $object->availability : '';
            $activitdata->completionexpected = isset($object->completionexpected) ? $object->completionexpected : 0;
            $activitdata->completion = isset($object->completion) ? $object->completion : 0;
        }

        return $activitdata;
    }
}
