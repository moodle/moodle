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

namespace core_courseformat\external;

use cm_info;
use core\external\exporter;
use core_courseformat\output\local\overview\overviewtable;
use core_courseformat\local\overview\overviewitem;
use renderer_base;

/**
 * The overviewtable output data exporter for Webservice.
 *
 * This class is used to define the get_overview_information web service response structure
 * and normalize the data for external use.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewtable_exporter extends exporter {
    /**
     * Constructor with typed params.
     *
     * @param overviewtable $data The overview table data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        overviewtable $data,
        array $related = [],
    ) {
        parent::__construct($data, $related);
    }

    #[\Override]
    protected static function define_properties(): array {
        return [
        ];
    }

    #[\Override]
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    #[\Override]
    protected static function define_other_properties() {
        return [
            'courseid' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The ID of the course this overview table belongs to.',
            ],
            'hasintegration' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Indicates if there is any integration available for this overview table.',
            ],
            'headers' => [
                'type' => [
                    'name' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The name of the header.',
                    ],
                    'key' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The key of the header, used to identify it.',
                    ],
                    'align' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_ALLOWED,
                        'default' => null,
                        'description' => 'The text alignment of the header.',
                    ],
                ],
                'multiple' => true,
            ],
            'activities' => [
                'type' => [
                    'name' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The name of the activity.',
                    ],
                    'modname' => [
                        'type' => PARAM_PLUGIN,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The module name of the activity.',
                    ],
                    'contextid' => [
                        'type' => PARAM_INT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The context ID of the activity.',
                    ],
                    'cmid' => [
                        'type' => PARAM_INT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The course module ID of the activity.',
                    ],
                    'url' => [
                        'type' => PARAM_URL,
                        'null' => NULL_ALLOWED,
                        'default' => null,
                        'description' => 'The URL of the activity, if available.',
                    ],
                    'haserror' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'Indicate if the activity has an error.',
                    ],
                    'items' => [
                        'type' => overviewitem::read_properties_definition(),
                        'multiple' => true,
                        'description' => 'The items associated with the activity, exported using overviewitem_exporter.',
                    ],
                ],
                'multiple' => true,
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(renderer_base $output) {
        /** @var \core_courseformat\output\local\overview\overviewtable $source */
        $source = $this->data;
        $data = $source->export_for_external();
        return [
            'courseid' => $data->course->id,
            'hasintegration' => $data->hasintegration,
            'headers' => $this->export_headers($data->headers),
            'activities' => $this->export_activities($data->activities, $output),
        ];
    }

    /**
     * Export the headers data.
     *
     * @param array $headers The headers to export.
     * @return array The exported headers.
     */
    private function export_headers(array $headers): array {
        $exported = [];
        foreach ($headers as $header) {
            $exported[] = (object)[
                'name' => $header->name,
                'key' => $header->key,
                'align' => $header->align,
            ];
        }
        return $exported;
    }

    /**
     * Export the activities data.
     *
     * @param array $activities The activities to export.
     * @param renderer_base $output The renderer to use for exporting.
     * @return array The exported activities data.
     */
    private function export_activities(array $activities, renderer_base $output): array {
        $exported = [];
        foreach ($activities as $activity) {
            /** @var \cm_info $cm */
            $cm = $activity->cm;
            $activityinfo = (object)[
                'name' => $cm->name,
                'modname' => $cm->modname,
                'contextid' => $cm->context->id,
                'cmid' => $cm->id,
                'url' => $cm->url?->out(false) ?? null,
                'haserror' => $activity->haserror ?? false,
                'items' => $this->export_activity_items($cm, $activity->items, $output),
            ];
            $exported[] = $activityinfo;
        }
        return $exported;
    }

    /**
     * Export the activity items.
     *
     * @param cm_info $cm The course module information.
     * @param overviewitem[] $items The items to export.
     * @param renderer_base $output The renderer to use for exporting.
     * @return array The exported activity items.
     */
    private function export_activity_items(cm_info $cm, array $items, renderer_base $output): array {
        $activities = [];
        foreach ($items as $item) {
            $activities[] = $item->get_exporter($cm->context)->export($output);
        }
        return $activities;
    }
}
