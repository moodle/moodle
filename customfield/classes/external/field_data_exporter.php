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

namespace core_customfield\external;

use core_customfield\handler;
use core\external\exporter;
use core\output\renderer_base;
use core_customfield\output\field_data;

/**
 * Custom field data exporter
 *
 * @package    core_customfield
 * @copyright  2025 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_data_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'component' => 'string',
            'area' => 'string',
            'itemid' => 'int?',
            'instanceid' => 'int',
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'data' => [
                'type' => [
                    'value' => ['type' => PARAM_TEXT, 'null' => NULL_ALLOWED],
                    'type' => ['type' => PARAM_TEXT],
                    'shortname' => ['type' => PARAM_TEXT],
                    'name' => ['type' => PARAM_TEXT],
                    'hasvalue' => ['type' => PARAM_BOOL],
                    'instanceid' => ['type' => PARAM_INT],
                ],
                'multiple' => true,
            ],
        ];
    }

    /**
     * Get additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {

        /** @var string $component */
        $component = $this->related['component'];

        /** @var string $area */
        $area = $this->related['area'];

        /** @var int $itemid */
        $itemid = (int) $this->related['itemid'];

        /** @var int $instanceid */
        $instanceid = $this->related['instanceid'];

        $handler = handler::get_handler($component, $area, $itemid);

        $data = array_map(
            fn(field_data $fielddata): array => (array) $fielddata->export_for_template($output),
            $handler->export_instance_data($instanceid),
        );

        return [
            'data' => $data,
        ];
    }
}
