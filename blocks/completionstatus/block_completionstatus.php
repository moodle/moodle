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
 * @package    block_completionstatus
 * @copyright  2009-2012 Catalyst IT Ltd
 * @author     Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/completionlib.php");

/**
 * Course completion status.
 * Displays overall, and individual criteria status for logged in user.
 */
class block_completionstatus extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_completionstatus');
    }

    public function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function get_content() {
        global $USER;

        $rows = array();
        $srows = array();
        $prows = array();
        // If content is cached.
        if ($this->content !== null) {
            return $this->content;
        }

        $course = $this->page->course;
        $context = context_course::instance($course->id);

        // Create empty content.
        $this->content = new stdClass();

        // Can edit settings?
        $can_edit = has_capability('moodle/course:update', $context);

        // Get course completion data.
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

        // Load criteria to display.
        $completions = $info->get_completions($USER->id);

        // Check if this course has any criteria.
        if (empty($completions)) {
            if ($can_edit) {
                $this->content->text = get_string('nocriteriaset', 'completion');
            }
            return $this->content;
        }

        // Check this user is enroled.
        if ($info->is_tracked_user($USER->id)) {

            // Generate markup for criteria statuses.
            $data = '';

            // For aggregating activity completion.
            $activities = array();
            $activities_complete = 0;

            // For aggregating course prerequisites.
            $prerequisites = array();
            $prerequisites_complete = 0;

            // Flag to set if current completion data is inconsistent with what is stored in the database.
            $pending_update = false;

            // Loop through course criteria.
            foreach ($completions as $completion) {
                $criteria = $completion->get_criteria();
                $complete = $completion->is_complete();

                if (!$pending_update && $criteria->is_pending($completion)) {
                    $pending_update = true;
                }

                // Activities are a special case, so cache them and leave them till last.
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {
                    $activities[$criteria->moduleinstance] = $complete;

                    if ($complete) {
                        $activities_complete++;
                    }

                    continue;
                }

                // Prerequisites are also a special case, so cache them and leave them till last.
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_COURSE) {
                    $prerequisites[$criteria->courseinstance] = $complete;

                    if ($complete) {
                        $prerequisites_complete++;
                    }

                    continue;
                }
                $row = new html_table_row();
                $row->cells[0] = new html_table_cell($criteria->get_title());
                $row->cells[1] = new html_table_cell($completion->get_status());
                $row->cells[1]->style = 'text-align: right;';
                $srows[] = $row;
            }

            // Aggregate activities.
            if (!empty($activities)) {
                $a = new stdClass();
                $a->first = $activities_complete;
                $a->second = count($activities);

                $row = new html_table_row();
                $row->cells[0] = new html_table_cell(get_string('activitiescompleted', 'completion'));
                $row->cells[1] = new html_table_cell(get_string('firstofsecond', 'block_completionstatus', $a));
                $row->cells[1]->style = 'text-align: right;';
                $srows[] = $row;
            }

            // Aggregate prerequisites.
            if (!empty($prerequisites)) {
                $a = new stdClass();
                $a->first = $prerequisites_complete;
                $a->second = count($prerequisites);

                $row = new html_table_row();
                $row->cells[0] = new html_table_cell(get_string('dependenciescompleted', 'completion'));
                $row->cells[1] = new html_table_cell(get_string('firstofsecond', 'block_completionstatus', $a));
                $row->cells[1]->style = 'text-align: right;';
                $prows[] = $row;

                $srows = array_merge($prows, $srows);
            }

            // Display completion status.
            $table = new html_table();
            $table->width = '100%';
            $table->attributes = array('style'=>'font-size: 90%;', 'class'=>'');

            $row = new html_table_row();
            $content = html_writer::tag('b', get_string('status').': ');

            // Is course complete?
            $coursecomplete = $info->is_course_complete($USER->id);

            // Load course completion.
            $params = array(
                'userid' => $USER->id,
                'course' => $course->id
            );
            $ccompletion = new completion_completion($params);

            // Has this user completed any criteria?
            $criteriacomplete = $info->count_course_user_data($USER->id);

            if ($pending_update) {
                $content .= html_writer::tag('i', get_string('pending', 'completion'));
            } else if ($coursecomplete) {
                $content .= get_string('complete');
            } else if (!$criteriacomplete && !$ccompletion->timestarted) {
                $content .= html_writer::tag('i', get_string('notyetstarted', 'completion'));
            } else {
                $content .= html_writer::tag('i', get_string('inprogress', 'completion'));
            }

            $row->cells[0] = new html_table_cell($content);
            $row->cells[0]->colspan = '2';

            $rows[] = $row;
            $row = new html_table_row();
            $content = "";
            // Get overall aggregation method.
            $overall = $info->get_aggregation_method();
            if ($overall == COMPLETION_AGGREGATION_ALL) {
                $content .= get_string('criteriarequiredall', 'completion');
            } else {
                $content .= get_string('criteriarequiredany', 'completion');
            }
            $content .= ':';
            $row->cells[0] = new html_table_cell($content);
            $row->cells[0]->colspan = '2';
            $rows[] = $row;

            $row = new html_table_row();
            $row->cells[0] = new html_table_cell(html_writer::tag('b', get_string('requiredcriteria', 'completion')));
            $row->cells[1] = new html_table_cell(html_writer::tag('b', get_string('status')));
            $row->cells[1]->style = 'text-align: right;';
            $rows[] = $row;

            // Array merge $rows and $data here.
            $rows = array_merge($rows, $srows);

            $table->data = $rows;
            $this->content->text = html_writer::table($table);

            // Display link to detailed view.
            $details = new moodle_url('/blocks/completionstatus/details.php', array('course' => $course->id));
            $this->content->footer = html_writer::link($details, get_string('moredetails', 'completion'));
        } else {
            // If user is not enrolled, show error.
            $this->content->text = get_string('nottracked', 'completion');
        }

        if (has_capability('report/completion:view', $context)) {
            $report = new moodle_url('/report/completion/index.php', array('course' => $course->id));
            if (empty($this->content->footer)) {
                $this->content->footer = '';
            }
            $this->content->footer .= html_writer::empty_tag('br');
            $this->content->footer .= html_writer::link($report, get_string('viewcoursereport', 'completion'));
        }

        return $this->content;
    }
}
