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

namespace core_calendar\external;

use core\external\exporter;
use core_calendar\output\humandate;

/**
 * Class humandate_exporter
 *
 * @package    core_calendar
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class humandate_exporter extends exporter {
    /**
     * Constructor with typed params.
     *
     * @param humandate $data The humandate data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        humandate $data,
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
            'timestamp' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The timestamp of the date.',
            ],
            'userdate' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The user-friendly date string.',
            ],
            'date' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The formatted date string.',
            ],
            'time' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The formatted time string.',
            ],
            'needtitle' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether a title is needed for the date.',
            ],
            'link' => [
                'type' => PARAM_URL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'A link associated with the date.',
            ],
            'ispast' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the date is in the past.',
            ],
            'isnear' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the date is near.',
            ],
            'nearicon' => [
                'type' => \core\output\pix_icon::read_properties_definition(),
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'An icon to indicate that the date is near.',
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output) {
        /** @var humandate $source */
        $source = $this->data;
        $templatedata = $source->export_for_template($output);

        $icondata = null;
        if ($icon = $source->get_near_icon()) {
            $context = $this->related['context'] ?? \core\context\system::instance();
            $icondata = $icon->get_exporter($context)->export($output);
        }

        return [
            'timestamp' => $templatedata['timestamp'],
            'userdate' => $templatedata['userdate'],
            'date' => $templatedata['date'],
            'time' => $templatedata['time'],
            'needtitle' => $templatedata['needtitle'],
            'link' => $templatedata['link'],
            'ispast' => $templatedata['ispast'],
            'isnear' => $templatedata['isnear'] ?? false,
            'nearicon' => $icondata,
        ];
    }
}
