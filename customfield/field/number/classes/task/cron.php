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

use core\task\scheduled_task;
use core_customfield\category_controller;
use core_customfield\field_controller;
use customfield_number\provider_base;

/**
 * Scheduled task for customfield_number to recalculate automatically populated fields.
 *
 * @package    customfield_number
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron extends scheduled_task {

    /**
     * Get a descriptive name for the task (shown to admins)
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('crontaskname', 'customfield_number');
    }

    /**
     * Recalculate automatically populated number fields.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute(): void {
        global $DB;
        // Get all number custom fields.
        $sql = "SELECT f.*, c.component, c.area, c.itemid, c.contextid
                  FROM {customfield_field} f
                  JOIN {customfield_category} c ON f.categoryid = c.id
                 WHERE f.type = ?";
        $res = $DB->get_records_sql($sql, ['number']);
        foreach ($res as $row) {
            $cat = (object)[
                'id' => $row->categoryid,
                'component' => $row->component,
                'area' => $row->area,
                'itemid' => $row->itemid,
                'contextid' => $row->contextid,
            ];
            unset($row->component, $row->area, $row->itemid, $row->contextid);
            $category = category_controller::create(0, $cat);
            // Create an instance of field controller for each field and recalculate the value if field provider is available.
            $field = field_controller::create(0, $row, $category);
            if ($provider = provider_base::instance($field)) {
                if ($provider->is_available()) {
                    $provider->recalculate();
                }
            }
        }
    }
}
