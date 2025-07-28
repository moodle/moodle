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

use core\external\exporter;
use renderer_base;

/**
 * The overviewitem data exporter for Webservice.
 *
 * This class is used to define the get_overview_information web service response structure
 * and normalize the data for external use.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewitem_exporter extends exporter {
    /**
     * Constructor with typed params.
     *
     * @param \core_courseformat\local\overview\overviewitem $data The overview item data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        \core_courseformat\local\overview\overviewitem $data,
        array $related = [],
    ) {
        parent::__construct($data, $related);
    }

    #[\Override]
    protected static function define_properties(): array {
        return [];
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
            'key' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The key of the overview item, used to identify it.',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The name of the overview item.',
            ],
            'contenttype' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The type of content this overview item has.',
            ],
            'exportertype' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'The class name of the exporter used to export the content of this overview item.',
            ],
            'alertlabel' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'description' => 'The label for the alert associated with this overview item.',
            ],
            'alertcount' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'The count of alerts associated with this overview item.',
            ],
            'contentjson' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'The JSON encoded content data for the overview item.',
            ],
            'extrajson' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'The JSON encoded extra data for the overview item.',
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(renderer_base $output) {
        /** @var \core_courseformat\local\overview\overviewitem $source */
        $source = $this->data;

        $content = $source->get_content();
        $value = $source->get_value();
        $exportertype = null;
        if ($content instanceof \core\output\externable) {
            $context = $this->related['context'] ?? \core\context\system::instance();
            $exporter = $content->get_exporter($context);
            $exportertype = $exporter::class;
            $itemdata = $exporter->export($output);
        } else {
            $itemdata = (object) [
                'value' => $value,
                'datatype' => gettype($value),
                'content' => $source->get_rendered_content($output),
            ];
        }

        $extradata = $source->get_extra_data();

        return [
            'name' => $source->get_name(),
            'key' => $source->get_key() ?? '',
            'contenttype' => $source->get_content_type(),
            'exportertype' => $exportertype,
            'alertlabel' => $source->get_alert_label(),
            'alertcount' => $source->get_alert_count(),
            'contentjson' => json_encode($itemdata),
            'extrajson' => ($extradata !== null) ? json_encode($extradata) : null,
        ];
    }
}
