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
 * Hook callbacks for local_coursematrix.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\hook_callbacks;

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks class for output hooks.
 */
class output_callbacks {

    /**
     * Callback for the before_standard_top_of_body_html_generation hook.
     * Displays due date banner on course pages for users in learning plans.
     *
     * @param \core\hook\output\before_standard_top_of_body_html_generation $hook
     */
    public static function before_top_of_body(\core\hook\output\before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $USER, $COURSE, $CFG, $DB;

        require_once($CFG->dirroot . '/local/coursematrix/lib.php');

        // Only on course pages.
        if (!isset($PAGE->context) || $PAGE->context->contextlevel != CONTEXT_COURSE || $COURSE->id == 1) {
            return;
        }

        // DEBUG: Check if user has a plan assignment for this course.
        $userplan = $DB->get_record('local_coursematrix_user_plans', [
            'userid' => $USER->id,
            'currentcourseid' => $COURSE->id,
        ]);
        
        // Add debug banner to show what's happening.
        $debugoutput = '<div class="alert alert-secondary" style="margin:0;border-radius:0;font-size:0.8em;">';
        $debugoutput .= '<strong>DEBUG:</strong> Course ID: ' . $COURSE->id . ', User ID: ' . $USER->id;
        if ($userplan) {
            $debugoutput .= ', Plan ID: ' . $userplan->planid . ', Status: ' . $userplan->status;
        } else {
            $debugoutput .= ', <span style="color:red;">No user plan found for this course as currentcourseid</span>';
        }
        $debugoutput .= '</div>';
        $hook->add_html($debugoutput);

        // Get due info for this user/course.
        $dueinfo = local_coursematrix_get_user_course_dueinfo($USER->id, $COURSE->id);
        if (!$dueinfo) {
            return;
        }

        // Build the banner.
        $output = '';

        if ($dueinfo->urgency == 'overdue') {
            $days = abs($dueinfo->daysremaining);
            $text = get_string('overduedays', 'local_coursematrix', $days);
            $output = '<div class="alert alert-danger text-center" style="margin: 0; border-radius: 0; font-weight: bold; font-size: 1.1em;">';
            $output .= '<i class="fa fa-exclamation-triangle mr-2"></i> ' . $text;
            $output .= '</div>';
        } else if ($dueinfo->urgency == 'critical') {
            $text = $dueinfo->daysremaining == 1
                ? get_string('dayremaining', 'local_coursematrix')
                : get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $output = '<div class="alert alert-danger text-center" style="margin: 0; border-radius: 0; font-weight: bold;">';
            $output .= '<i class="fa fa-clock-o mr-2"></i> ' . $text;
            $output .= '</div>';
        } else if ($dueinfo->urgency == 'warning') {
            $text = get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $output = '<div class="alert alert-warning text-center" style="margin: 0; border-radius: 0;">';
            $output .= '<i class="fa fa-clock-o mr-2"></i> ' . $text;
            $output .= '</div>';
        } else {
            $text = get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $output = '<div class="alert alert-info text-center" style="margin: 0; border-radius: 0;">';
            $output .= '<i class="fa fa-info-circle mr-2"></i> ' . $text;
            $output .= '</div>';
        }

        $hook->add_html($output);
    }
}

