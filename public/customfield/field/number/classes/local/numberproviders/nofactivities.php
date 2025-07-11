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

declare(strict_types=1);

namespace customfield_number\local\numberproviders;

use context_course;
use core_plugin_manager;
use customfield_number\data_controller;
use customfield_number\provider_base;
use MoodleQuickForm;

/**
 * Class nofactivities to calculate number of activities in the course.
 *
 * @package    customfield_number
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class nofactivities extends provider_base {

    /**
     * Provider name
     */
    public function get_name(): string {
        return get_string('nofactivities', 'customfield_number');
    }

    /**
     * Check if the provider is available for the current field.
     *
     * @return bool
     */
    public function is_available(): bool {
        return $this->field->get_handler()->get_component() === 'core_course' &&
            $this->field->get_handler()->get_area() === 'course';
    }

    /**
     * Add autocomplete field for selecting activity type.
     * Also add checkbox to display the field when the number of activities is zero.
     *
     * @param MoodleQuickForm $mform
     */
    public function config_form_definition(MoodleQuickForm $mform): void {
        $options = [];
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('mod');
        foreach ($plugins as $plugin) {
            $options[$plugin->name] = $plugin->displayname;
        }

        // Define the label for the autocomplete element.
        $valuelabel = get_string('activitytypes', 'customfield_number');
        // Add autocomplete element.
        $mform->addElement('autocomplete', 'configdata[activitytypes]', $valuelabel, $options, ['multiple' => true]);
        $mform->hideIf('configdata[activitytypes]', 'configdata[fieldtype]', 'ne', get_class($this));
        $mform->hideIf('configdata[decimalplaces]', 'configdata[fieldtype]', 'eq', get_class($this));
        $mform->hideIf('configdata[display]', 'configdata[fieldtype]', 'eq', get_class($this));
        $mform->hideIf('configdata[defaultvalue]', 'configdata[fieldtype]', 'eq', get_class($this));
        $mform->hideIf('configdata[minimumvalue]', 'configdata[fieldtype]', 'eq', get_class($this));
        $mform->hideIf('configdata[maximumvalue]', 'configdata[fieldtype]', 'eq', get_class($this));
    }

    /**
     * Recalculate the number of activities in the course.
     *
     * @param int|null $instanceid
     */
    public function recalculate(?int $instanceid = null): void {
        global $DB;
        $types = $this->field->get_configdata_property('activitytypes');
        $displaywhenzero = $this->field->get_configdata_property('displaywhenzero');

        if (empty($types)) {
            return;
        }

        // Subquery to select all modules of selected types.
        [$sqlin, $params] = $DB->get_in_or_equal($types, SQL_PARAMS_NAMED);
        $cmsql = "SELECT m.id
                    FROM {modules} m
                   WHERE m.name $sqlin
                     AND m.visible = 1";

        $where = '';
        if ($instanceid) {
            $where = "AND c.id = :courseid ";
            $params['courseid'] = $instanceid;
        }

        // Number of activities is stored in database. So we count the number and check if it matches the stored value.
        // We update value in database if it doesn't match counted value.
        $sql = "SELECT c.id, COUNT(cm.id) AS cnt, d.id AS dataid, d.decvalue
                  FROM {course} c
             LEFT JOIN {customfield_data} d
                    ON d.fieldid = :fieldid
                   AND d.instanceid = c.id
             LEFT JOIN {course_modules} cm
                    ON cm.course = c.id
                   AND cm.visible = 1
                   AND cm.deletioninprogress = 0
                   AND cm.module IN ($cmsql)
                 WHERE c.id <> :siteid $where
              GROUP BY c.id, d.id, d.decvalue
        ";
        $params['fieldid'] = $fieldid = $this->field->get('id');
        $records = $DB->get_records_sql($sql, $params + ['siteid' => SITEID]);
        foreach ($records as $record) {
            $value = (int)$record->cnt;
            if ((string)$displaywhenzero === '' && !$value) {
                // Do not display the field when the number of activities is zero.
                if ($record->dataid) {
                    (new data_controller(0, (object)['id' => $record->dataid]))->delete();
                }
            } else if (empty($record->dataid) || (int)$record->decvalue != $value) {
                // Stored value is out of date.
                $data = $this->field->get_handler()->get_instance_fields_data(
                    [$fieldid => $this->field], (int)$record->id)[$fieldid];
                $data->set('contextid', context_course::instance($record->id)->id);
                $data->set('decvalue', $value);
                $data->save();
            }
        }
    }

    /**
     * Validate the data on the field configuration form for number of activities provider.
     *
     * @param array $data
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, array $files = []): array {
        $errors = [];
        if (empty($data['configdata']['activitytypes'])) {
            $errors['configdata[activitytypes]'] = get_string('err_required', 'form');
        }
        return $errors;
    }

    /**
     * Preparation for export for number of activities provider.
     *
     * @param mixed $value String or float or null if the value is not present in the database for this instance
     * @param \context|null $context Context
     * @return ?string
     */
    public function prepare_export_value(mixed $value, ?\context $context = null): ?string {
        if ($value === null) {
            return null;
        } else if (round((float)$value) == 0) {
            $whenzero = $this->field->get_configdata_property('displaywhenzero');
            if ((string) $whenzero === '') {
                return null;
            } else {
                return format_string($whenzero, true, ['context' => $context ?? \core\context\system::instance()]);
            }
        } else {
            return format_float((float)$value, 0);
        }
    }
}
