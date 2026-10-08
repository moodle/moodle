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

namespace core_course\output;

use core\output\react_component_renderable;
use core\output\renderer_base;
use renderable;
use stdClass;

/**
 * The activity dates, rendered by the core_course/ActivityDates React component.
 *
 * @package    core_course
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_dates_component implements react_component_renderable, renderable {
    /**
     * Constructor.
     *
     * @param array $activitydates The activity dates, as returned by {@see \core\activity_dates::get_dates_for_module()}.
     */
    public function __construct(
        /** @var array $activitydates the activity dates information. */
        protected array $activitydates,
    ) {
    }

    /**
     * Whether there are any dates to display.
     *
     * @return bool
     */
    public function has_dates(): bool {
        return !empty($this->activitydates);
    }

    /**
     * Get the activity dates, with their date formatted.
     *
     * @return array[] Each element is an array with the "label" and the formatted date as "value".
     */
    public function get_formatted_dates(): array {
        $dates = [];
        foreach ($this->activitydates as $date) {
            if (empty($date['relativeto'])) {
                $value = userdate($date['timestamp'], get_string('strftimedaydatetime', 'core_langconfig'));
            } else {
                $diffstr = get_time_interval_string($date['timestamp'], $date['relativeto']);
                $identifier = $date['timestamp'] >= $date['relativeto']
                    ? 'relativedatessubmissionduedateafter'
                    : 'relativedatessubmissionduedatebefore';
                $value = get_string($identifier, 'core_course', ['datediffstr' => $diffstr]);
            }
            $dates[] = ['label' => $date['label'], 'value' => $value];
        }
        return $dates;
    }

    #[\Override]
    public function get_react_component_name(): string {
        return 'core_course/ActivityDates';
    }

    #[\Override]
    public function get_react_component_props(renderer_base $renderer): stdClass {
        return (object) ['dates' => $this->get_formatted_dates()];
    }
}
