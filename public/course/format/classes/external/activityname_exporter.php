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
use core_courseformat\output\local\overview\activityname;

/**
 * Class activityname_exporter
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activityname_exporter extends exporter {
    /**
     * Constructor with typed params.
     *
     * @param activityname $data The activity name data to export.
     * @param array $related Related data for the exporter.
     */
    public function __construct(
        activityname $data,
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
            'activityname' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'The name of the activity.',
            ],
            'activityurl' => [
                'type' => PARAM_URL,
                'null' => NULL_ALLOWED,
                'description' => 'The URL of the activity.',
            ],
            'hidden' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the activity is hidden.',
            ],
            'stealth' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the activity is stealth.',
            ],
            'sectiontitle' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'description' => 'The title of the section containing the activity.',
            ],
            'errormessages' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Contains any possible error message to display.',
                'multiple' => true,
                'default' => [],
            ],
            'available' => [
                'type' => PARAM_BOOL,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Whether the activity is available.',
            ],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output) {
        /** @var activityname $source */
        $source = $this->data;
        $templatedata = $source->export_for_template($output);

        return [
            'activityname' => $templatedata->activityname,
            'activityurl' => $templatedata->activityurl ? $templatedata->activityurl->out(false) : null,
            'hidden' => $templatedata->hidden,
            'stealth' => $templatedata->stealth,
            'sectiontitle' => $templatedata->sectiontitle ?? null,
            'available' => $templatedata->available,
            'errormessages' => $source->get_error_messages(),
        ];
    }
}
