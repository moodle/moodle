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
 * Overrides myoverview block renderer.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

use block_myoverview\output\main;
use html_writer;
use stdClass;

// BEGIN LSU - completion filter
require_once($CFG->libdir . '/completionlib.php');
// END LSU - completion filter

defined('MOODLE_INTERNAL') || die();

class block_myoverview_renderer extends \block_myoverview\output\renderer {

    // BEGIN LSU - completion filter
    public function get_completion($course) {
        global $USER;
        $cinfo = new \completion_info($course);
        return $cinfo->is_course_complete($USER->id);
    }
    // END LSU - completion filter
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main(main $main) {
        global $USER;

        if (!count(enrol_get_all_users_courses($USER->id, true))) {
            return $this->render_from_template(
                'block_myoverview/zero-state',
                $main->export_for_zero_state_template($this)
            );
        }
        $data = $main->export_for_template($this);

        // BEGIN LSU - get user pref for year and completion.
        if (!$prefyear = get_user_preferences('snap_user_grouping_year_preference')) {
            $prefyear = 'all';
        }
        if ($prefyear == 'all') {
            $data['prefyear'] = 'All years';
        } else if ($prefyear != '') {
            $data['prefyear'] = $prefyear;
        }

        if (!$prefprog = get_user_preferences('snap_user_grouping_progress_preference')) {
            $prefprog = 'all';
        }
        $progstatus = 0;
        if ($prefprog == 'all') {
            $progstatus = 2;
            $data['prefprog'] = 'Progress';
        } else if ($prefprog == 'completed') {
            $progstatus = 1;
            $data['prefprog'] = 'Completed';
        } else if ($prefprog == 'notcompleted') {
            $progstatus = 0;
            $data['prefprog'] = 'Not completed';
        }

        $courses = enrol_get_my_courses('enddate', 'fullname ASC, id DESC');
        $coursesyears = [];
        foreach ($courses as $course) {
            
            $endyear = userdate($course->enddate, '%Y');
            if (empty($course->enddate)) {
                $endyear = userdate($course->startdate, '%Y');
            }
            
            $yearlink = html_writer::tag('a', $endyear,[
                'class' => 'dropdown-item',
                'href' => '#',
                'data-filter' => 'year',
                'data-value' => $endyear,
                'data-pref' => $endyear,
            ]);
            $yearitem = new stdClass();
            $yearitem->$endyear = html_writer::tag('li', $yearlink);
            $coursesyears[$endyear] = $yearitem;
            ksort($coursesyears);
        }
        if (!empty($coursesyears)) {
            $allyearslink = html_writer::tag('a', get_string('allyears', 'theme_snap'),[
                'class' => 'dropdown-item',
                'href' => '#',
                'data-filter' => 'year',
                'data-value' => 'all',
                'data-pref' => 'all',
            ]);
            $yearslist = $allyearslink;
            foreach ($coursesyears as $year => $yearlistitem) {
                $yearslist .= $yearlistitem->$year;
            }
            $data['years'] = $yearslist;
        }
        // END LSU - get user pref for year and completion.

        return $this->render_from_template('block_myoverview/main', $data);
    }
}
