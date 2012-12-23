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
 * Block for displayed logged in user's course completion status
 *
 * @package    block
 * @subpackage completion
 * @copyright  2009-2012 Catalyst IT Ltd
 * @author     Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/completionlib.php");

/**
 * Course completion status
 * Displays overall, and individual criteria status for logged in user
 */
class block_completionstatus extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_completionstatus');
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function get_content() {
        global $USER;

        // If content is cached
        if ($this->content !== NULL) {
            return $this->content;
        }

        $course  = $this->page->course;
        $context = context_course::instance($course->id);

        // Create empty content
        $this->content = new stdClass();

        // Can edit settings?
        $can_edit = has_capability('moodle/course:update', $context);

        // Get course completion data
        $info = new completion_info($course);

        // Don't display if completion isn't enabled!
        if (!completion_info::is_enabled_for_site()) {
            if ($can_edit) {
                $this->content->text = get_string('completionnotenabledforsite', 'completion');
            }
            return $this->content;

        } else if (!$info->is_enabled()) {
            if ($can_edit) {
                $this->content->text = get_string('completionnotenabledforcourse', 'completion');
            }
            return $this->content;
        }

        // Load criteria to display
        $completions = $info->get_completions($USER->id);

        // Check if this course has any criteria
        if (empty($completions)) {
            if ($can_edit) {
                $this->content->text = get_string('nocriteriaset', 'completion');
            }
            return $this->content;
        }

        // Check this user is enroled
        if ($info->is_tracked_user($USER->id)) {

            // Generate markup for criteria statuses
            $shtml = '';

            // For aggregating activity completion
            $activities = array();
            $activities_complete = 0;

            // For aggregating course prerequisites
            $prerequisites = array();
            $prerequisites_complete = 0;

            // Flag to set if current completion data is inconsistent with
            // what is stored in the database
            $pending_update = false;

            // Loop through course criteria
            foreach ($completions as $completion) {

                $criteria = $completion->get_criteria();
                $complete = $completion->is_complete();

                if (!$pending_update && $criteria->is_pending($completion)) {
                    $pending_update = true;
                }

                // Activities are a special case, so cache them and leave them till last
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {
                    $activities[$criteria->moduleinstance] = $complete;

                    if ($complete) {
                        $activities_complete++;
                    }

                    continue;
                }

                // Prerequisites are also a special case, so cache them and leave them till last
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_COURSE) {
                    $prerequisites[$criteria->courseinstance] = $complete;

                    if ($complete) {
                        $prerequisites_complete++;
                    }

                    continue;
                }

                $shtml .= '<tr><td>';
                $shtml .= $criteria->get_title();
                $shtml .= '</td><td style="text-align: right">';
                $shtml .= $completion->get_status();
                $shtml .= '</td></tr>';
            }

            // Aggregate activities
            if (!empty($activities)) {

                $shtml .= '<tr><td>';
                $shtml .= get_string('activitiescompleted', 'completion');
                $shtml .= '</td><td style="text-align: right">';
                $a = new stdClass();
                $a->first = $activities_complete;
                $a->second = count($activities);
                $shtml .= get_string('firstofsecond', 'block_completionstatus', $a);
                $shtml .= '</td></tr>';
            }

            // Aggregate prerequisites
            if (!empty($prerequisites)) {

                $phtml  = '<tr><td>';
                $phtml .= get_string('dependenciescompleted', 'completion');
                $phtml .= '</td><td style="text-align: right">';
                $a = new stdClass();
                $a->first = $prerequisites_complete;
                $a->second = count($prerequisites);
                $phtml .= get_string('firstofsecond', 'block_completionstatus', $a);
                $phtml .= '</td></tr>';

                $shtml = $phtml . $shtml;
            }

            // Display completion status
            $this->content->text  = '<table width="100%" style="font-size: 90%;"><tbody>';
            $this->content->text .= '<tr><td colspan="2"><b>'.get_string('status').':</b> ';

            // Is course complete?
            $coursecomplete = $info->is_course_complete($USER->id);

            // Load course completion
            $params = array(
                'userid' => $USER->id,
                'course' => $course->id
            );
            $ccompletion = new completion_completion($params);

            // Has this user completed any criteria?
            $criteriacomplete = $info->count_course_user_data($USER->id);

            if ($pending_update) {
                $this->content->text .= '<i>'.get_string('pending', 'completion').'</i>';
            } else if ($coursecomplete) {
                $this->content->text .= get_string('complete');
            } else if (!$criteriacomplete && !$ccompletion->timestarted) {
                $this->content->text .= '<i>'.get_string('notyetstarted', 'completion').'</i>';
            } else {
                $this->content->text .= '<i>'.get_string('inprogress','completion').'</i>';
            }

            $this->content->text .= '</td></tr>';
            $this->content->text .= '<tr><td colspan="2">';

            // Get overall aggregation method
            $overall = $info->get_aggregation_method();

            if ($overall == COMPLETION_AGGREGATION_ALL) {
                $this->content->text .= get_string('criteriarequiredall', 'completion');
            } else {
                $this->content->text .= get_string('criteriarequiredany', 'completion');
            }

            $this->content->text .= ':</td></tr>';
            $this->content->text .= '<tr><td><b>'.get_string('requiredcriteria', 'completion').'</b></td><td style="text-align: right"><b>'.get_string('status').'</b></td></tr>';
            $this->content->text .= $shtml.'</tbody></table>';

            // Display link to detailed view
            $details = new moodle_url('/blocks/completionstatus/details.php', array('course' => $course->id));
            $this->content->footer = '<br><a href="'.$details->out().'">'.get_string('moredetails', 'completion').'</a>';
        } else {
            // If user is not enrolled, show error
            $this->content->text = get_string('notenroled', 'completion');
        }

        if (has_capability('report/completion:view', $context)) {
            $report = new moodle_url('/report/completion/index.php', array('course' => $course->id));
            $this->content->footer .= '<br /><a href="'.$report->out().'">'.get_string('viewcoursereport', 'completion').'</a>';
        }


        return $this->content;
    }
}
