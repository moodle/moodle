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
     * Displays due date banner on course pages and injects course card badges.
     *
     * @param \core\hook\output\before_standard_top_of_body_html_generation $hook
     */
    public static function before_top_of_body(\core\hook\output\before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $USER, $COURSE, $CFG, $DB;

        require_once($CFG->dirroot . '/local/coursematrix/lib.php');

        // Get all user's active plan courses for course card badges.
        self::inject_course_card_badges($hook, $USER->id);

        // Course page banner - only on course pages.
        if (isset($PAGE->context) && $PAGE->context->contextlevel == CONTEXT_COURSE && $COURSE->id > 1) {
            self::inject_course_page_banner($hook, $USER->id, $COURSE->id);
        }
    }

    /**
     * Inject JavaScript and CSS to show badges on course cards in "My courses" page.
     */
    private static function inject_course_card_badges($hook, $userid): void {
        global $DB, $CFG;
        
        require_once($CFG->libdir . '/completionlib.php');

        // Get all user's plan assignments.
        $sql = "SELECT up.*, pc.courseid, pc.duedays, p.name as planname, c.fullname as coursename
                FROM {local_coursematrix_user_plans} up
                JOIN {local_coursematrix_plan_courses} pc ON pc.planid = up.planid
                JOIN {local_coursematrix_plans} p ON p.id = up.planid
                JOIN {course} c ON c.id = pc.courseid
                WHERE up.userid = ?";
        $enrollments = $DB->get_records_sql($sql, [$userid]);

        if (empty($enrollments)) {
            return;
        }

        // Build course status data.
        $coursestatuses = [];
        foreach ($enrollments as $e) {
            // Check if this is the current course in the plan.
            $iscurrent = ($e->currentcourseid == $e->courseid);
            
            // Check completion status.
            $course = $DB->get_record('course', ['id' => $e->courseid]);
            $completion = new \completion_info($course);
            $iscomplete = $completion->is_enabled() && $completion->is_course_complete($userid);
            
            // Calculate due date (only for current course).
            $duedate = null;
            $daysremaining = null;
            $status = 'not_started';
            
            if ($iscomplete) {
                $status = 'completed';
            } else if ($iscurrent) {
                $duedate = $e->startdate + ($e->duedays * 86400);
                $daysremaining = ceil(($duedate - time()) / 86400);
                
                if ($daysremaining < 0) {
                    $status = 'overdue';
                } else if ($daysremaining <= 2) {
                    $status = 'critical';
                } else if ($daysremaining <= 7) {
                    $status = 'warning';
                } else {
                    $status = 'normal';
                }
            }
            
            $coursestatuses[$e->courseid] = [
                'courseid' => $e->courseid,
                'status' => $status,
                'daysremaining' => $daysremaining,
                'planname' => $e->planname,
            ];
        }

        if (empty($coursestatuses)) {
            return;
        }

        // Inject CSS and JS.
        $json = json_encode($coursestatuses);
        
        $css = '
<style>
.coursematrix-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.coursematrix-badge.completed {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}
.coursematrix-badge.overdue {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    animation: pulse 1.5s infinite;
}
.coursematrix-badge.critical {
    background: linear-gradient(135deg, #fd7e14, #dc3545);
    color: white;
}
.coursematrix-badge.warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: #212529;
}
.coursematrix-badge.normal {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.course-card .card-img-top {
    position: relative;
}
</style>';

        $js = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    var courseStatuses = ' . $json . ';
    
    // Find all course cards.
    var courseCards = document.querySelectorAll(".coursebox, .course-card, [data-courseid]");
    
    courseCards.forEach(function(card) {
        var courseId = card.getAttribute("data-courseid");
        if (!courseId) {
            // Try to find course ID from link.
            var link = card.querySelector("a[href*=\'/course/view.php?id=\']");
            if (link) {
                var match = link.href.match(/id=(\d+)/);
                if (match) courseId = match[1];
            }
        }
        
        if (courseId && courseStatuses[courseId]) {
            var info = courseStatuses[courseId];
            var badge = document.createElement("div");
            badge.className = "coursematrix-badge " + info.status;
            
            if (info.status === "completed") {
                badge.innerHTML = "<i class=\"fa fa-check-circle\"></i> Completed";
            } else if (info.status === "overdue") {
                badge.innerHTML = "<i class=\"fa fa-exclamation-triangle\"></i> Overdue " + Math.abs(info.daysremaining) + "d";
            } else if (info.status === "critical" || info.status === "warning" || info.status === "normal") {
                badge.innerHTML = "<i class=\"fa fa-clock-o\"></i> " + info.daysremaining + " days left";
            }
            
            // Insert badge.
            var imgContainer = card.querySelector(".card-img-top, .courseimage, .course-image");
            if (imgContainer) {
                imgContainer.style.position = "relative";
                imgContainer.appendChild(badge);
            } else {
                card.style.position = "relative";
                card.insertBefore(badge, card.firstChild);
            }
        }
    });
});
</script>';

        $hook->add_html($css . $js);
    }

    /**
     * Inject banner at top of course page.
     */
    private static function inject_course_page_banner($hook, $userid, $courseid): void {
        global $CFG;
        
        require_once($CFG->dirroot . '/local/coursematrix/lib.php');

        // Get due info for this user/course.
        $dueinfo = local_coursematrix_get_user_course_dueinfo($userid, $courseid);
        if (!$dueinfo) {
            return;
        }

        // Build the banner.
        $output = '';
        $iconclass = '';
        $alertclass = '';
        $text = '';

        if ($dueinfo->urgency == 'overdue') {
            $days = abs($dueinfo->daysremaining);
            $text = $days == 1 ? '1 day overdue' : $days . ' days overdue';
            $text = '<strong>' . get_string('overdue', 'local_coursematrix') . ':</strong> ' . $text . '!';
            $alertclass = 'alert-danger';
            $iconclass = 'fa-exclamation-triangle';
        } else if ($dueinfo->urgency == 'critical') {
            $text = $dueinfo->daysremaining == 1
                ? get_string('dayremaining', 'local_coursematrix')
                : get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $text = '<strong>' . get_string('urgent', 'local_coursematrix') . ':</strong> ' . $text;
            $alertclass = 'alert-danger';
            $iconclass = 'fa-clock-o';
        } else if ($dueinfo->urgency == 'warning') {
            $text = get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $alertclass = 'alert-warning';
            $iconclass = 'fa-clock-o';
        } else {
            $text = get_string('daysremaining', 'local_coursematrix', $dueinfo->daysremaining);
            $alertclass = 'alert-info';
            $iconclass = 'fa-info-circle';
        }

        $output = '<div class="alert ' . $alertclass . ' text-center" style="margin: 0; border-radius: 0; font-size: 1.1em;">';
        $output .= '<i class="fa ' . $iconclass . ' mr-2"></i> ' . $text;
        $output .= '</div>';

        $hook->add_html($output);
    }
}
