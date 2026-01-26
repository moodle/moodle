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

namespace customfield_number\task;

use core\task\adhoc_task;
use core_customfield\field_controller;
use customfield_number\provider_base;

/**
 * Recalculates data for the given number field with a provider
 *
 * @since      Moodle 4.5.1
 * @package    customfield_number
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recalculate extends adhoc_task {
    #[\Override]
    public function execute() {
        global $DB;
        $customdata = $this->get_custom_data();
        $fieldid = clean_param($customdata->fieldid ?? null, PARAM_INT);
        $fieldtype = $customdata->fieldtype ?? null;

        // Find all fields that we need to recalculate (either by 'fieldid' or 'fieldtype').
        $fields = [];
        if ($fieldid) {
            try {
                $fields[] = field_controller::create($fieldid);
            } catch (\Exception $e) {
                // Could be a race condition when the field was already deleted by the time ad-hoc task runs.
                return;
            }
        } else if ($fieldtype) {
            $records = $DB->get_records('customfield_field', ['type' => 'number']);
            foreach ($records as $record) {
                $configdata = @json_decode($record->configdata, true);
                if (($configdata['fieldtype'] ?? '') === $fieldtype) {
                    $fields[] = field_controller::create(0, $record);
                }
            }
        }

        // Schedule recalculate for each field, checking component, area and the presense of provider.
        $instanceid = clean_param($customdata->instanceid ?? null, PARAM_INT);
        $component = clean_param($customdata->component ?? 'core_course', PARAM_COMPONENT);
        $area = clean_param($customdata->area ?? 'course', PARAM_AREA);
        $itemid = clean_param($customdata->itemid ?? 0, PARAM_INT);
        foreach ($fields as $field) {
            if ($this->field_is_scheduled($field)) {
                $provider = provider_base::instance($field);
                if ($provider?->is_available()) {
                    $provider->recalculate($instanceid ?: null, $component, $area, $itemid);
                }
            }
        }
    }

    /**
     * Helper method validating that the field should be recalculated
     *
     * @param \core_customfield\field_controller $field
     * @return bool
     */
    protected function field_is_scheduled(field_controller $field): bool {
        $customdata = $this->get_custom_data();
        if (!empty($customdata->component) && $field->get_handler()->get_component() !== $customdata->component) {
            return false;
        }
        if (!empty($customdata->area) && $field->get_handler()->get_area() !== $customdata->area) {
            return false;
        }
        return true;
    }

    /**
     * Schedule recalculation for the given number custom field (and optionally, instanceid)
     *
     * @param int $fieldid in of the custom field
     * @param int|null $instanceid if specified, only recalculates for the given instance id
     * @param string|null $component the component related to the instance
     * @param string|null $area the area related to the instance
     * @param int $itemid
     * @return void
     */
    public static function schedule_for_field(
        int $fieldid,
        ?int $instanceid = null,
        ?string $component = null,
        ?string $area = null,
        int $itemid = 0,
    ) {
        $task = new static();
        $task->set_custom_data([
            'fieldid' => $fieldid,
            'instanceid' => $instanceid,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
        ]);
        \core\task\manager::queue_adhoc_task($task, true);
    }

    /**
     * Schedule recalculation for all number custom fields that use the provider (optionally with instanceid)
     *
     * @param string $fieldtype name of the class extending provider_base
     * @param string|null $component
     * @param string|null $area
     * @param int|null $instanceid
     * @param int $itemid
     * @return void
     */
    public static function schedule_for_fieldtype(
        string $fieldtype,
        ?string $component = null,
        ?string $area = null,
        ?int $instanceid = null,
        int $itemid = 0,
    ) {
        $task = new static();
        $task->set_custom_data([
            'fieldtype' => $fieldtype,
            'component' => $component,
            'area' => $area,
            'instanceid' => $instanceid,
            'itemid' => $itemid,
        ]);
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
