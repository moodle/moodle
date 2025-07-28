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
use core_courseformat\output\local\overview\overviewdialog;

/**
 * Class to export overview dialog data for external use.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewdialog_exporter extends exporter {
    /**
     * Constructor with typed params.
     *
     * @param overviewdialog $data The overview dialog data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        overviewdialog $data,
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
            'buttoncontent' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The content of the button.',
            ],
            'title' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The title of the overview dialog content.',
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The description of the overview dialog content.',
            ],
            'disabled' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the dialog is disabled or not.',
                'default' => false,
            ],
            'items' => [
                'type' => [
                    'label' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The label of the item.',
                    ],
                    'value' => [
                        'type' => PARAM_TEXT,
                        'null' => NULL_NOT_ALLOWED,
                        'description' => 'The value of the item.',
                    ],

                ],
                'description' => 'The list of items in the overview dialog.',
                'multiple' => true,
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output) {
        /** @var overviewdialog $source */
        $source = $this->data;
        return [
            'buttoncontent' => $source->get_button_content(),
            'title' => $source->get_title(),
            'description' => $source->get_description(),
            'disabled' => $source->get_disabled(),
            'items' => $source->get_items(),
        ];
    }
}
