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

namespace block_timeline\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

require_once($CFG->dirroot . '/blocks/timeline/lib.php');

/**
 * Class containing data for the timeline block.
 *
 * Produces the minimal props needed to bootstrap the React frontend.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /** @var string The current sort/order preference. */
    public string $order;

    /** @var string The current day-filter preference. */
    public string $filter;

    /** @var int The current activity-limit preference. */
    public int $limit;

    /**
     * Constructor.
     *
     * @param string|null $order Sort preference from user preferences.
     * @param string|null $filter Filter preference from user preferences.
     * @param string|null $limit Activity limit preference from user preferences.
     */
    public function __construct(?string $order, ?string $filter, ?string $limit) {
        $this->order  = $order ?: BLOCK_TIMELINE_SORT_BY_DATES;
        $this->filter = $filter ?: BLOCK_TIMELINE_FILTER_BY_30_DAYS;
        $this->limit  = (int) ($limit ?: BLOCK_TIMELINE_ACTIVITIES_LIMIT_DEFAULT);
    }

    /**
     * Export the props required to mount the React Timeline component.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $courses = enrol_get_my_courses(['id'], null, 1);
        return [
            'midnight'            => usergetmidnight(time()),
            'filter'              => $this->filter,
            'order'               => $this->order,
            'limit'               => $this->limit,
            'nocoursesurl'        => $output->image_url('courses', 'block_timeline')->out(),
            'noeventsurl'         => $output->image_url('activities', 'block_timeline')->out(),
            'hasenrolledcourses'  => !empty($courses),
        ];
    }
}
