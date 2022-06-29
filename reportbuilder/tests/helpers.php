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

use core_reportbuilder\table\custom_report_table_view;

/**
 * Helper base class for reportbuilder unit tests
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_reportbuilder_testcase extends advanced_testcase {

    /**
     * Retrieve content for given report as array of report data
     *
     * @param int $reportid
     * @param int $pagesize
     * @return array[]
     */
    protected function get_custom_report_content(int $reportid, int $pagesize = 30): array {
        $records = [];

        // Create table instance.
        $table = custom_report_table_view::create($reportid);
        $table->setup();
        $table->query_db($pagesize, false);

        // Extract raw data.
        foreach ($table->rawdata as $record) {
            $records[] = $table->format_row($record);
        }

        $table->close_recordset();

        return $records;
    }
}
