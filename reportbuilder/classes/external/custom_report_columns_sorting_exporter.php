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

use pix_icon;
use renderer_base;
use core\external\exporter;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\report\base;

/**
 * Custom report columns sorting exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_columns_sorting_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'report' => base::class,
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'hassortablecolumns' => [
                'type' => PARAM_BOOL,
                'optional' => true,
            ],
            'sortablecolumns' => [
                'type' => [
                    'id' => ['type' => PARAM_INT],
                    'title' => ['type' => PARAM_TEXT],
                    'heading' => ['type' => PARAM_TEXT],
                    'sortdirection' => ['type' => PARAM_INT],
                    'sortenabled' => ['type' => PARAM_INT],
                    'sortorder' => ['type' => PARAM_INT],
                    'sorticon' => [
                        'type' => [
                            'key' => ['type' => PARAM_RAW],
                            'component' => ['type' => PARAM_COMPONENT],
                            'title' => ['type' => PARAM_NOTAGS],
                        ],
                    ],
                    'movetitle' => ['type' => PARAM_TEXT],
                    'sortenabledtitle' => ['type' => PARAM_TEXT],
                ],
                'optional' => true,
                'multiple' => true,
            ],
            'helpicon' => [
                'type' => PARAM_RAW,
                'optional' => true,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        /** @var base $report */
        $report = $this->related['report'];

        $reportid = $report->get_report_persistent()->get('id');
        $activecolumns = column::get_records(['reportid' => $reportid], 'sortorder');
        $sortablecolumns = array_filter($activecolumns, function(column $persistent) use($report): bool {
            $column = $report->get_column($persistent->get('uniqueidentifier'));
            if ($column === null) {
                return false;
            }

            return $column->set_aggregation($persistent->get('aggregation'))->get_is_sortable();
        });

        $sortablecolumns = array_map(function(column $persistent) use ($report) {
            $columntitle = $report->get_column($persistent->get('uniqueidentifier'))->get_title();
            $columnheading = $persistent->get_formatted_heading($report->get_context());

            $columnsortascending = ($persistent->get('sortdirection') == SORT_ASC);
            $sortenabledtitle = $persistent->get('sortenabled') ? 'columnsortdisable' : 'columnsortenable';
            $sortdirectiontitle = $columnsortascending ? 'columnsortdirectiondesc' : 'columnsortdirectionasc';

            $icon = $columnsortascending ? 't/uplong' : 't/downlong';
            $sorticon = new pix_icon($icon, get_string($sortdirectiontitle, 'core_reportbuilder', $columntitle));

            return [
                'id' => $persistent->get('id'),
                'title' => $columntitle,
                'heading' => $columnheading !== '' ? $columnheading : $columntitle,
                'sortdirection' => $persistent->get('sortdirection'),
                'sortenabled' => (int)$persistent->get('sortenabled'),
                'sortorder' => $persistent->get('sortorder'),
                'sorticon' => $sorticon->export_for_pix(),
                'movetitle' => get_string('movesorting', 'core_reportbuilder', $columntitle),
                'sortenabledtitle' => get_string($sortenabledtitle, 'core_reportbuilder', $columntitle),
            ];
        }, $sortablecolumns);

        return [
            'hassortablecolumns' => !empty($sortablecolumns),
            'sortablecolumns' => array_values($sortablecolumns),
            'helpicon' => $output->help_icon('sorting', 'core_reportbuilder'),
        ];
    }
}
