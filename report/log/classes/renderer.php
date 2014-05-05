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
 * Log report renderer.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Report log renderer's for printing reports.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_log_renderer extends plugin_renderer_base {

    /**
     * Render log report page.
     *
     * @param report_log_renderable $reportlog object of report_log.
     */
    public function render_report_log_renderable(report_log_renderable $reportlog) {
        if (empty($reportlog->selectedlogreader)) {
            echo $this->output->notification(get_string('nologreaderenabled', 'report_log'), 'notifyproblem');
            return;
        }
        if ($reportlog->showselectorform) {
            $this->report_selector_form($reportlog);
        }

        if ($reportlog->showreport) {
            $reportlog->tablelog->out($reportlog->perpage, true);
        }
    }

    /**
     * Prints/return reader selector
     *
     * @param report_log_renderable $reportlog log report.
     */
    public function reader_selector(report_log_renderable $reportlog) {
        $readers = $reportlog->get_readers(true);
        if (empty($readers)) {
            $readers = array(get_string('nologreaderenabled', 'report_log'));
        }
        $select = new single_select($reportlog->url, 'logreader', $readers, $reportlog->selectedlogreader, null);
        $select->set_label(get_string('selectlogreader', 'report_log'));
        echo $this->output->render($select);
    }

    /**
     * This function is used to generate and display selector form
     *
     * @param report_log_renderable $reportlog log report.
     */
    public function report_selector_form(report_log_renderable $reportlog) {
        echo html_writer::start_tag('form', array('class' => 'logselecform', 'action' => $reportlog->url, 'method' => 'get'));
        echo html_writer::start_div();
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'chooselog', 'value' => '1'));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'showusers', 'value' => $reportlog->showusers));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'showcourses',
            'value' => $reportlog->showcourses));

        $selectedcourseid = empty($reportlog->course) ? 0 : $reportlog->course->id;

        // Add course selector.
        $courses = $reportlog->get_course_list();
        if (!empty($courses)) {
            if ($reportlog->showcourses) {
                echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
                echo html_writer::select($courses, "id", $selectedcourseid, null);
            } else {
                $courseoption[$selectedcourseid] = $courses[$selectedcourseid];
                unset($courses);
                echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
                echo html_writer::select($courseoption, "id", $selectedcourseid, null);

                // Check if user is admin and this came because of limitation on number of courses to show in dropdown.
                $sitecontext = context_system::instance();
                if (has_capability('report/log:view', $sitecontext)) {
                    $a = new stdClass();
                    $a->url = new moodle_url('/report/log/index.php', array('chooselog' => 0,
                        'group' => $reportlog->get_selected_group(), 'user' => $reportlog->userid,
                        'id' => $selectedcourseid, 'date' => $reportlog->date, 'modid' => $reportlog->modid,
                        'showcourses' => 1, 'showusers' => $reportlog->showusers));
                    print_string('logtoomanycourses', 'moodle', $a);
                }
            }
        }

        // Add group selector.
        $groups = $reportlog->get_group_list();
        if (!empty($groups)) {
            echo html_writer::label(get_string('selectagroup'), 'menugroup', false, array('class' => 'accesshide'));
            echo html_writer::select($groups, "group", $reportlog->groupid, get_string("allgroups"));
        }

        // Add user selector.
        $users = $reportlog->get_user_list();
        if (!empty($users)) {
            if ($reportlog->showusers) {
                echo html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
                echo html_writer::select($users, "user", $reportlog->userid, get_string("allparticipants"));
            } else {
                $users = array();
                if (!empty($reportlog->userid)) {
                    $users[$reportlog->userid] = $reportlog->get_selected_user_fullname();
                } else {
                    $users[0] = get_string('allparticipants');
                }
                echo html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
                echo html_writer::select($users, "user", $reportlog->userid, false);
                $a = new stdClass();
                $a->url = new moodle_url('/report/log/index.php', array('chooselog' => 0,
                    'group' => $reportlog->get_selected_group(), 'user' => $reportlog->userid,
                    'id' => $selectedcourseid, 'date' => $reportlog->date, 'modid' => $reportlog->modid,
                    'showcourses' => 1, 'showusers' => $reportlog->showusers, 'showcourses' => $reportlog->showcourses));
                print_string('logtoomanyusers', 'moodle', $a);
            }
        }

        // Add date selector.
        $dates = $reportlog->get_date_options();
        echo html_writer::label(get_string('date'), 'menudate', false, array('class' => 'accesshide'));
        echo html_writer::select($dates, "date", $reportlog->date, get_string("alldays"));

        // Add activity selector.
        $activities = $reportlog->get_activities_list();
        echo html_writer::label(get_string('activities'), 'menumodid', false, array('class' => 'accesshide'));
        echo html_writer::select($activities, "modid", $reportlog->modid, get_string("allactivities"));

        // Add actions selector.
        echo html_writer::label(get_string('actions'), 'menumodaction', false, array('class' => 'accesshide'));
        echo html_writer::select($reportlog->get_actions(), 'modaction', $reportlog->action, get_string("allactions"));

        // Add edulevel.
        $edulevel = $reportlog->get_edulevel_options();
        echo html_writer::label(get_string('edulevel'), 'menuedulevel', false, array('class' => 'accesshide'));
        echo html_writer::select($edulevel, 'edulevel', $reportlog->edulevel, false);

        // Add reader option.
        // If there is some reader available then only show submit button.
        $readers = $reportlog->get_readers(true);
        if (!empty($readers)) {
            if (count($readers) == 1) {
                $attributes = array('type' => 'hidden', 'name' => 'logreader', 'value' => key($readers));
                echo html_writer::empty_tag('input', $attributes);
            } else {
                echo html_writer::label(get_string('selectlogreader', 'report_log'), 'menureader', false,
                        array('class' => 'accesshide'));
                echo html_writer::select($readers, 'logreader', $reportlog->selectedlogreader, false);
            }
            echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('gettheselogs')));
        }
        echo html_writer::end_div();
        echo html_writer::end_tag('form');
    }
}

