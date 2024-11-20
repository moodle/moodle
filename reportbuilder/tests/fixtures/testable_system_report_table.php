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

namespace core_reportbuilder;

use stdClass;
use core_reportbuilder\table\system_report_table;

/**
 * Testable system report table for getting report data
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_system_report_table extends system_report_table {

    /**
     * Format each row of returned data, replacing aliased column names with the original
     *
     * @param array|stdClass $row
     * @return array
     */
    public function format_row($row): array {
        $record = parent::format_row($row);
        $result = [];

        $columns = $this->report->get_columns();
        foreach ($columns as $column) {
            $result[$column->get_name()] = $record[$column->get_column_alias()];
        }

        return $result;
    }

    /**
     * Return all table rows
     *
     * @return array
     */
    public function get_table_rows(): array {
        global $PAGE;

        $PAGE->set_url('/');

        $result = [];

        $this->guess_base_url();
        $this->setup();

        $this->query_db(0, false);
        foreach ($this->rawdata as $record) {
            $result[] = $this->format_row($record);
        }

        $this->close_recordset();

        return $result;
    }
}
