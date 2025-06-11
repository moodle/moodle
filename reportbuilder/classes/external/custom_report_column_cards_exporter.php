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
use core_reportbuilder\datasource;

/**
 * Custom report column cards exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_column_cards_exporter extends custom_report_menu_cards_exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'report' => datasource::class,
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

        $menucards = [];

        foreach ($report->get_columns() as $column) {
            if ($column->get_is_deprecated()) {
                continue;
            }

            // New menu card per entity.
            $entityname = $column->get_entity_name();
            if (!array_key_exists($entityname, $menucards)) {
                $menucards[$entityname] = [
                    'name' => (string) $report->get_entity_title($entityname),
                    'key' => $entityname,
                    'items' => [],
                ];
            }

            // Append menu card item per column.
            $menucards[$entityname]['items'][] = [
                'name' => $column->get_title(),
                'identifier' => $column->get_unique_identifier(),
                'title' => get_string('addcolumn', 'core_reportbuilder', $column->get_title()),
                'action' => 'report-add-column',
            ];
        }

        return [
            'menucards' => array_values($menucards),
        ];
    }
}
