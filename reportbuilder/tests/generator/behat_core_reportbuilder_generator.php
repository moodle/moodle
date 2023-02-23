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

use core_reportbuilder\local\models\report;

/**
 * Behat data generator for Report builder
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_reportbuilder_generator extends behat_generator_base {

    /**
     * Get a list of the entities that can be created for this component
     *
     * @return array[]
     */
    protected function get_creatable_entities(): array {
        return [
            'Reports' => [
                'singular' => 'Report',
                'datagenerator' => 'report',
                'required' => [
                    'name',
                    'source',
                ],
            ],
            'Columns' => [
                'singular' => 'Column',
                'datagenerator' => 'column',
                'required' => [
                    'report',
                    'uniqueidentifier',
                ],
                'switchids' => [
                    'report' => 'reportid',
                ],
            ],
            'Conditions' => [
                'singular' => 'Condition',
                'datagenerator' => 'condition',
                'required' => [
                    'report',
                    'uniqueidentifier',
                ],
                'switchids' => [
                    'report' => 'reportid',
                ],
            ],
            'Filters' => [
                'singular' => 'Filter',
                'datagenerator' => 'filter',
                'required' => [
                    'report',
                    'uniqueidentifier',
                ],
                'switchids' => [
                    'report' => 'reportid',
                ],
            ],
            'Audiences' => [
                'singular' => 'Audience',
                'datagenerator' => 'audience',
                'required' => [
                    'report',
                    'configdata',
                ],
                'switchids' => [
                    'report' => 'reportid',
                ],
            ],
            'Schedules' => [
                'singular' => 'Schedule',
                'datagenerator' => 'schedule',
                'required' => [
                    'report',
                    'name',
                ],
                'switchids' => [
                    'report' => 'reportid',
                ],
            ],
        ];
    }

    /**
     * Look up report ID from given name
     *
     * @param string $name
     * @return int
     */
    protected function get_report_id(string $name): int {
        global $DB;

        return (int) $DB->get_field(report::TABLE, 'id', ['name' => $name], MUST_EXIST);
    }

    /**
     * Pre-process audience entity, generate correct config structure
     *
     * @param array $audience
     * @return array
     */
    protected function preprocess_audience(array $audience): array {
        $audience['configdata'] = (array) json_decode($audience['configdata']);

        return $audience;
    }
}
