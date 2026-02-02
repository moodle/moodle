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

namespace customfield_number;

use core_customfield\data_controller;

/**
 * Test provider that sets the field data to the current second of the hour.
 *
 * @package    customfield_number
 * @copyright  2026 Sebastian Gundersen <sebastian.gundersen@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class test_provider extends provider_base {
    /**
     * Get provider name.
     *
     * @return string
     */
    public function get_name(): string {
        return 'Test provider';
    }

    /**
     * Check if the provider is available for the field.
     *
     * @return bool
     */
    public function is_available(): bool {
        return true;
    }

    /**
     * Recalculate field value.
     *
     * @param int|null $instanceid
     * @param string $component
     * @param string $area
     * @param int $itemid
     */
    public function recalculate(
        ?int $instanceid = null,
        string $component = 'core_course',
        string $area = 'course',
        int $itemid = 0,
    ): void {
        global $DB;

        $params = [
            'fieldid' => $this->field->get('id'),
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
        ];

        $sql = "SELECT *
                 FROM {customfield_data}
                WHERE fieldid = :fieldid
                  AND component = :component
                  AND area = :area
                  AND itemid = :itemid";

        if ($instanceid !== null) {
            $sql .= " AND instanceid <> :instanceid";
            $params['instanceid'] = $instanceid;
        }

        $records = $DB->get_recordset_sql($sql, $params);
        if (!$records->valid() && $component === 'core_course' && $area === 'course') {
            foreach ($DB->get_records('course') as $course) {
                $data = data_controller::create(0, (object)['instanceid' => (int)$course->id], $this->field);
                $data->set('contextid', \core\context\system::instance()->id);
                $data->set('component', 'core_course');
                $data->set('area', 'course');
                $data->set('decvalue', \core\di::get(\core\clock::class)->time() % 3600);
                $data->save();
            }
        }
        foreach ($records as $record) {
            $data = data_controller::create(0, $record, $this->field);
            $data->set('contextid', \core\context\system::instance()->id);
            $data->set('component', $component);
            $data->set('area', $area);
            $data->set('decvalue', \core\di::get(\core\clock::class)->time() % 3600);
            $data->save();
        }
        $records->close();
    }
}
