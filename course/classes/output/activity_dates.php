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

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * The activity dates renderable class.
 *
 * @package    core_course
 * @copyright  2023 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_dates implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param array $activitydates The activity dates.
     */
    public function __construct(
        protected array $activitydates
    ) {
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $activitydates = [];
        foreach ($this->activitydates as $date) {
            if (empty($date['relativeto'])) {
                $date['datestring'] = userdate($date['timestamp'], get_string('strftimedaydatetime', 'core_langconfig'));
            } else {
                $diffstr = get_time_interval_string($date['timestamp'], $date['relativeto']);
                if ($date['timestamp'] >= $date['relativeto']) {
                    $date['datestring'] = get_string('relativedatessubmissionduedateafter', 'core_course',
                        ['datediffstr' => $diffstr]);
                } else {
                    $date['datestring'] = get_string('relativedatessubmissionduedatebefore', 'core_course',
                        ['datediffstr' => $diffstr]);
                }
            }
            $activitydates[] = $date;
        }

        return (object) [
            'hasdates' => !empty($this->activitydates),
            'activitydates' => $activitydates,
        ];
    }
}
