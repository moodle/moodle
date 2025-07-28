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

namespace core\external;

use core\output\action_link;

/**
 * Class action_link_exporter
 *
 * @package    core
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_link_exporter extends exporter {
    /**
     * Constructor with parameter type hints.
     *
     * @param action_link $data The action link data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        action_link $data,
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
            'content' => [
                'type' => PARAM_RAW,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The rendered content of the action link.',
            ],
            'linkurl' => [
                'type' => PARAM_URL,
                'null' => NULL_ALLOWED,
                'description' => 'The URL of the action link.',

            ],
            'icondata' => [
                'type' => \core\output\pix_icon::read_properties_definition(),
                'null' => NULL_ALLOWED,
                'default' => null,
                'description' => 'The icon data for the action link, if any.',
            ],
            'classes' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'description' => 'A space-separated list of CSS classes to apply to the action link.',
            ],
            'contenttype' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'description' => 'The type of the link content.',
            ],
            'contentjson' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'The data for the link content, if it is an exportable object.',
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output) {
        /** @var action_link $source */
        $source = $this->data;
        $templatedata = $source->export_for_template($output);
        $context = $this->related['context'] ?? \core\context\system::instance();

        // Action links allow both string and renderable text objects.
        // If the text is a renderable, we need to check if it can be exported.
        if ($source->text instanceof \core\output\externable) {
            $textype = $source->text::class;
            $textdata = $source->text->get_exporter($context)->export($output);
        } else {
            $textype = 'string';
            $textdata = null;
        }

        $icondata = null;
        if ($source->icon && $source->icon instanceof \core\output\externable) {
            $icondata = $source->icon->get_exporter($context)->export($output);
        }

        return [
            'content' => $templatedata->text,
            'linkurl' => $source->url->out(false),
            'icondata' => $icondata,
            'classes' => $templatedata->classes,
            'contenttype' => $textype,
            'contentjson' => ($textdata === null) ? null : json_encode($textdata),
        ];
    }
}
