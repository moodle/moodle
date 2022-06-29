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

/**
 * Custom report menu cards exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_menu_cards_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'menucards' => 'array[]',
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'menucards' => [
                'type' => [
                    'name' => [
                        'type' => PARAM_TEXT,
                        'optional' => true,
                    ],
                    'key' => [
                        'type' => PARAM_TEXT,
                        'optional' => true,
                    ],
                    'items' => [
                        'type' => [
                            'name' => [
                                'type' => PARAM_TEXT,
                            ],
                            'identifier' => [
                                'type' => PARAM_TEXT,
                            ],
                            'title' => [
                                'type' => PARAM_TEXT,
                            ],
                            'action' => [
                                'type' => PARAM_TEXT,
                            ],
                            'disabled' => [
                                'type' => PARAM_BOOL,
                                'optional' => true,
                                'default' => false,
                            ],
                        ],
                        'optional' => true,
                        'multiple' => true,
                    ],
                ],
                'optional' => true,
                'multiple' => true,
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
        return [
            'menucards' => $this->related['menucards']
        ];
    }
}
