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

/**
 * Provides the {@link tool_iomadpolicy\iomadpolicy_exporter} class.
 *
 * @package   tool_iomadpolicy
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;

/**
 * Exporter of a iomadpolicy document model.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class iomadpolicy_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
            ],
            'sortorder' => [
                'type' => PARAM_INT,
                'default' => 999,
            ],
            'currentversionid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
            ],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'versions' => 'tool_iomadpolicy\iomadpolicy_version_exporter[]',
        ];
    }

    /**
     * Return the list of additional, generated dynamically from the given properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'currentversion' => [
                'type' => iomadpolicy_version_exporter::read_properties_definition(),
                'null' => NULL_ALLOWED,
            ],
            'draftversions' => [
                'type' => iomadpolicy_version_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'archivedversions' => [
                'type' => iomadpolicy_version_exporter::read_properties_definition(),
                'multiple' => true,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $othervalues = [
            'currentversion' => null,
            'draftversions' => [],
            'archivedversions' => [],
        ];

        foreach ($this->related['versions'] as $exporter) {
            $data = $exporter->export($output);

            if ($data->id == $this->data->currentversionid) {
                $othervalues['currentversion'] = $data;

            } else if ($data->archived) {
                $othervalues['archivedversions'][] = $data;

            } else {
                $othervalues['draftversions'][] = $data;
            }
        }

        return $othervalues;
    }
}
