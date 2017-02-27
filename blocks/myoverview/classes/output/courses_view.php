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

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $courses = enrol_get_my_courses('startdate, enddate');
        $today = time();

        // How many courses we have per status?
        $coursesbystatus = ['past' => 0, 'inprogress' => 0, 'future' => 0];
        foreach ($courses as $course) {
            $startdate = $course->startdate;
            $enddate = $course->enddate;

            if ($startdate < $today && $enddate < $today) {
                $coursesbystatus['past']++;
            } elseif ($startdate <= $today && $enddate >= $today) {
                $coursesbystatus['inprogress']++;
            } else {
                $coursesbystatus['future']++;
            }
        }

        // Build paging bar structure.
        $pagingbar = [];
        foreach ($coursesbystatus as $status => $total) {
            $quantpages = ceil($total / $this::COURSES_PER_PAGE);
            $pagingbar[$status]['pagingbar']['pagecount'] = $quantpages;
            $pagingbar[$status]['pagingbar']['first'] = ['page' => '&laquo;', 'url' => '#'];
            $pagingbar[$status]['pagingbar']['last'] = ['page' => '&raquo;', 'url' => '#'];
            for ($page = 0; $page < $quantpages; $page++) {
                $pagingbar[$status]['pagingbar']['pages'][$page] = [
                    'number' => $page+1,
                    'page' => $page+1,
                    'url' => '#',
                    'active' => ($page == 0 ? true : false)
                ];
            }
        }

        return $pagingbar;
    }
}
