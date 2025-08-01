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

use core\external\action_link_exporter;
use core_courseformat\output\local\overview\overviewaction;

/**
 * Class to export overview action data for external use.
 *
 * @package    core_courseformat
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewaction_exporter extends action_link_exporter {
    #[\Override]
    protected static function define_other_properties() {
        $values = parent::define_other_properties();
        $values['badge'] = [
            'type' => [
                'value' => [
                    'type' => PARAM_TEXT,
                    'null' => NULL_NOT_ALLOWED,
                    'description' => 'The value of the badge.',
                ],
                'title' => [
                    'type' => PARAM_TEXT,
                    'null' => NULL_NOT_ALLOWED,
                    'description' => 'The title of the item.',
                ],
                'style' => [
                    'type' => PARAM_TEXT,
                    'null' => NULL_NOT_ALLOWED,
                    'description' => 'The badge style to apply.',
                ],
            ],
            'description' => 'The badge information near the action link.',
            'null' => NULL_ALLOWED,
        ];
        $values['onlytext'] = [
            'type' => PARAM_TEXT,
            'null' => NULL_NOT_ALLOWED,
            'description' => 'The text of the action link without the badge.',
        ];

        return $values;
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output) {
        $values = parent::get_other_values($output);

        /** @var overviewaction $source */
        $source = $this->data;
        $values['onlytext'] = $source->text;

        if ($source->get_badgevalue() === null) {
            // If there is no badge value, the badge information is not included.
            $values['badge'] = null;
            return $values;
        }

        $values['badge'] = [
            'value' => $source->get_badgevalue(),
            'title' => $source->get_badgetitle(),
            'style' => $source->get_badgestyle(),
        ];

        return $values;
    }
}
