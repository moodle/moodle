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

use core\context\system;
use core\external\exporter;
use core\output\renderer_base;
use core_reportbuilder\output\report_action;

/**
 * Encapsulate a report action
 *
 * @package     core_reportbuilder
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_action_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'reportaction' => report_action::class,
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'tag' => [
                'type' => PARAM_ALPHA,
            ],
            'title' => [
                'type' => PARAM_TEXT,
            ],
            'attributes' => [
                'type' => [
                    'name' => [
                        'type' => PARAM_RAW,
                        'optional' => true,
                    ],
                    'value' => [
                        'type' => PARAM_RAW,
                        'optional' => true,
                    ],
                ],
                'multiple' => true,
            ],
        ];
    }

    /**
     * Return text formatting parameters for title property
     *
     * @return array
     */
    protected function get_format_parameters_for_title(): array {
        return [
            'context' => system::instance(),
        ];
    }

    /**
     * Get the additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {

        /** @var report_action $reportaction */
        $reportaction = $this->related['reportaction'];

        $attributes = array_map(static function($key, $value): array {
            return ['name' => $key, 'value' => $value];
        }, array_keys($reportaction->attributes), $reportaction->attributes);

        return [
            'tag' => $reportaction->tag ?: 'button',
            'title' => $reportaction->title,
            'attributes' => $attributes,
        ];
    }
}
