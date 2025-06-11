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
use core_reportbuilder\form\condition;
use core_reportbuilder\local\report\filter;

/**
 * Custom report conditions exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_conditions_exporter extends exporter {

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
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'hasavailableconditions' => [
                'type' => PARAM_BOOL,
            ],
            'availableconditions' => [
                'type' => [
                    'optiongroup' => [
                        'type' => [
                            'text' => ['type' => PARAM_TEXT],
                            'values' => [
                                'type' => [
                                    'value' => ['type' => PARAM_TEXT],
                                    'visiblename' => ['type' => PARAM_TEXT],
                                ],
                                'multiple' => true,
                            ],
                        ],
                    ],
                ],
                'multiple' => true,
            ],
            'hasactiveconditions' => [
                'type' => PARAM_BOOL,
            ],
            'activeconditionsform' => [
                'type' => PARAM_RAW,
            ],
            'helpicon' => [
                'type' => PARAM_RAW,
            ],
            'javascript' => [
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
        /** @var datasource $report */
        $report = $this->related['report'];

        // Current condition instances contained in the report.
        $conditions = $report->get_active_conditions();
        $conditionidentifiers = array_map(static function(filter $condition): string {
            return $condition->get_unique_identifier();
        }, $conditions);

        $availableconditions = [];

        // Populate available conditions.
        foreach ($report->get_conditions() as $condition) {

            // Conditions can only be added once per report, skip if it already exists.
            if (in_array($condition->get_unique_identifier(), $conditionidentifiers) || $condition->get_is_deprecated()) {
                continue;
            }

            $entityname = $condition->get_entity_name();
            if (!array_key_exists($entityname, $availableconditions)) {
                $availableconditions[$entityname] = [
                    'optiongroup' => [
                        'text' => $report->get_entity_title($entityname)->out(),
                        'values' => [],
                    ],
                ];
            }

            $availableconditions[$entityname]['optiongroup']['values'][] = [
                'value' => $condition->get_unique_identifier(),
                'visiblename' => $condition->get_header(),
            ];
        }

        // Generate conditions form if any present.
        $conditionspresent = !empty($conditions);
        if ($conditionspresent) {
            $conditionsform = new condition(null, null, 'post', '', [], true, [
                'reportid' => $report->get_report_persistent()->get('id'),
            ]);
            $conditionsform->set_data_for_dynamic_submission();
        }

        return [
            'hasavailableconditions' => !empty($availableconditions),
            'availableconditions' => array_values($availableconditions),
            'hasactiveconditions' => $conditionspresent,
            'activeconditionsform' => $conditionspresent ? $conditionsform->render() : '',
            'helpicon' => $output->help_icon('conditions', 'core_reportbuilder'),
        ];
    }
}
