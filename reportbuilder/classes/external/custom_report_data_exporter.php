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
use core_reportbuilder\datasource;
use core_reportbuilder\table\custom_report_table_view;

/**
 * Custom report data exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_data_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'report' => datasource::class,
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

        /** @var datasource $report */
        $report = $this->related['report'];

        $table = custom_report_table_view::create($report->get_report_persistent()->get('id'));
        $table->setup();

        // Internally the current page is zero-based, but this method expects value plus one.
        $table->set_page_number($this->related['page'] + 1);
        $table->query_db($this->related['perpage'], false);

        $tablerows = [];
        foreach ($table->rawdata as $record) {
            $tablerows[] = [
                'columns' => array_values($table->format_row($record)),
            ];
        }

        $table->close_recordset();

        return [
            'headers' => $table->headers,
            'rows' => $tablerows,
            'totalrowcount' => $table->totalrows,
        ];
    }
}
