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

namespace core_reportbuilder\external;

use renderer_base;
use core\external\exporter;
use core_reportbuilder\system_report;
use core_reportbuilder\table\system_report_table;

/**
 * System report data exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_data_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'report' => system_report::class,
            'page' => 'int',
            'perpage' => 'int',
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'headers' => [
                'type' => PARAM_RAW,
                'multiple' => true,
            ],
            'rows' => [
                'type' => [
                    'columns' => [
                        'type' => PARAM_RAW,
                        'null' => NULL_ALLOWED,
                        'multiple' => true,
                    ],
                ],
                'multiple' => true,
            ],
            'totalrowcount' => ['type' => PARAM_INT],
        ];
    }

    /**
     * Get the additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        global $DB;

        /** @var system_report $report */
        $report = $this->related['report'];

        $table = system_report_table::create($report->get_report_persistent()->get('id'), $report->get_parameters());
        $table->guess_base_url();
        $table->setup();

        // Internally the current page is zero-based, but this method expects value plus one.
        $table->set_page_number($this->related['page'] + 1);
        $table->query_db($this->related['perpage'], false);

        // Ensure we only return defined columns, excluding those such as "select all" and "actions".
        $columnsbyalias = $report->get_active_columns_by_alias();

        $tableheaders = array_combine(array_flip($table->columns), $table->headers);
        $tableheaders = array_intersect_key($tableheaders, $columnsbyalias);

        $tablerows = [];
        foreach ($table->rawdata as $record) {
            $columns = array_intersect_key($table->format_row($record), $columnsbyalias);
            $tablerows[] = [
                'columns' => array_values($columns),
            ];
        }

        $table->close_recordset();

        return [
            'headers' => array_values($tableheaders),
            'rows' => $tablerows,
            'totalrowcount' => $DB->count_records_sql($table->countsql, $table->countparams),
        ];
    }
}
