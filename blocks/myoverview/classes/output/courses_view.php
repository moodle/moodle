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

/**
 * Class containing data for courses view in the myoverview block.
 *
 * @package    block_myoverview
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_myoverview\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
/**
 * Class containing data for courses view in the myoverview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_view implements renderable, templatable {
    /** Quantity of courses per page. */
    const COURSES_PER_PAGE = 6;

    /** @var array $courses List of courses the user is enrolled in. */
    protected $courses = [];

    /**
     * The courses_view constructor.
     *
     * @param array $courses list of courses.
     */
    public function __construct($courses) {
        $this->courses = $courses;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $today = time();

        // Build courses view data structure.
        $coursesview = [];

        // How many courses we have per status?
        $coursesbystatus = ['past' => 0, 'inprogress' => 0, 'future' => 0];
        foreach ($this->courses as $course) {
            $startdate = $course->startdate;
            $enddate = $course->enddate;

            if ($startdate < $today && $enddate < $today) {

                $pastpages = floor($coursesbystatus['past'] / $this::COURSES_PER_PAGE);

                $coursesview['past']['pages'][$pastpages]['courses'][] = $course;
                $coursesview['past']['pages'][$pastpages]['active'] = ($pastpages == 0 ? true : false);
                $coursesview['past']['pages'][$pastpages]['page'] = $pastpages + 1;
                $coursesbystatus['past']++;

            } elseif ($startdate <= $today && $enddate >= $today) {
                $inprogresspages = floor($coursesbystatus['inprogress'] / $this::COURSES_PER_PAGE);

                $coursesview['inprogress']['pages'][$inprogresspages]['courses'][] = $course;
                $coursesview['inprogress']['pages'][$inprogresspages]['active'] = ($inprogresspages == 0 ? true : false);
                $coursesview['inprogress']['pages'][$inprogresspages]['page'] = $inprogresspages + 1;
                $coursesbystatus['inprogress']++;
            } else {
                $futurepages = floor($coursesbystatus['future'] / $this::COURSES_PER_PAGE);

                $coursesview['future']['pages'][$futurepages]['courses'][] = $course;
                $coursesview['future']['pages'][$futurepages]['active'] = ($futurepages == 0 ? true : false);
                $coursesview['future']['pages'][$futurepages]['page'] = $futurepages + 1;
                $coursesbystatus['future']++;
            }
        }

        // Build courses view paging bar structure.
        foreach ($coursesbystatus as $status => $total) {
            $quantpages = ceil($total / $this::COURSES_PER_PAGE);
            $coursesview[$status]['pagingbar']['pagecount'] = $quantpages;
            $coursesview[$status]['pagingbar']['first'] = ['page' => '&laquo;', 'url' => '#'];
            $coursesview[$status]['pagingbar']['last'] = ['page' => '&raquo;', 'url' => '#'];
            for ($page = 0; $page < $quantpages; $page++) {
                $coursesview[$status]['pagingbar']['pages'][$page] = [
                    'number' => $page+1,
                    'page' => $page+1,
                    'url' => '#',
                    'active' => ($page == 0 ? true : false)
                ];
            }
        }

        // Check if we have any page to be rendered.
        $coursesview['haspages'] = ($coursesbystatus['past'] > 0 || $coursesbystatus['inprogress'] > 0 ||
                $coursesbystatus['future'] > 0);

        return $coursesview;
    }
}
