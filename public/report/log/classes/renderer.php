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
     * This method should never be manually called, it should only be called by process.
     *
     * @deprecated since 2.8, to be removed in 2.9
     * @param report_log_renderable $reportlog
     * @return string
     */
    public function render_report_log_renderable(report_log_renderable $reportlog) {
        debugging('Do not call this method. Please call $renderer->render($reportlog) instead.', DEBUG_DEVELOPER);
        return $this->render($reportlog);
    }

    /**
     * Render log report page.
     *
     * @param report_log_renderable $reportlog object of report_log.
     */
    protected function render_report_log(report_log_renderable $reportlog) {
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
        $url = fullclone ($reportlog->url);
        $url->remove_params(array('logreader'));
        $select = new single_select($url, 'logreader', $readers, $reportlog->selectedlogreader, null);
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
        echo html_writer::start_div('d-flex flex-wrap align-items-center gap-2');
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'chooselog', 'value' => '1'));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'showusers', 'value' => $reportlog->showusers));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'showcourses',
            'value' => $reportlog->showcourses));

        $selectedcourseid = empty($reportlog->sitecoursefilter)
            ? (empty($reportlog->course) ? 0 : $reportlog->course->id)
            : $reportlog->sitecoursefilter;

        echo $this->get_course_selector_field($reportlog, $selectedcourseid);

        // Add group selector.
        $groups = $reportlog->get_group_list();
        if (!empty($groups)) {
            echo html_writer::label(get_string('selectagroup'), 'menugroup', false, array('class' => 'accesshide'));
            echo html_writer::select($groups, "group", $reportlog->groupid, get_string("allgroups"));
        }

        // Add user selector.
        $users = $reportlog->get_user_list();

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
                'showusers' => 1, 'showcourses' => $reportlog->showcourses));
            $a->url = $a->url->out(false);
            echo html_writer::start_span('mx-1');
            print_string('logtoomanyusers', 'moodle', $a);
            echo html_writer::end_span();
        }

        // Add date selector.
        $dates = $reportlog->get_date_options();
        echo html_writer::label(get_string('date'), 'menudate', false, array('class' => 'accesshide'));
        echo html_writer::select($dates, "date", $reportlog->date, get_string("alldays"));

        // Add activity selector.
        echo $this->get_activity_selector_field($reportlog);

        // Add actions selector.
        echo html_writer::label(get_string('actions'), 'menumodaction', false, array('class' => 'accesshide'));
        echo html_writer::select($reportlog->get_actions(), 'modaction', $reportlog->action,
            get_string("allactions"));

        // Add origin.
        $origin = $reportlog->get_origin_options();
        echo html_writer::label(get_string('origin', 'report_log'), 'menuorigin', false, array('class' => 'accesshide'));
        echo html_writer::select($origin, 'origin', $reportlog->origin, false);

        // Add edulevel.
        $edulevel = $reportlog->get_edulevel_options();
        echo html_writer::label(get_string('edulevel'), 'menuedulevel', false, array('class' => 'accesshide'));
        echo html_writer::select($edulevel, 'edulevel', $reportlog->edulevel, false) .$this->help_icon('edulevel');

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
            echo html_writer::end_div();
            echo html_writer::start_div('mt-2');
            echo html_writer::empty_tag('input', array('type' => 'submit',
                'value' => get_string('gettheselogs'), 'class' => 'btn btn-primary'));
            echo html_writer::end_div();
        }
        echo html_writer::end_tag('form');
    }

    /**
     * Generates the course selector field for the log report.
     *
     * @param report_log_renderable $reportlog The output instance.
     * @param int $selectedcourseid
     * @return string
     */
    private function get_course_selector_field(report_log_renderable $reportlog, int $selectedcourseid): string {
        // When the log report is accessed vie an activity, we do not need a course selector.
        if ($reportlog->isactivitypage || $reportlog->iscoursepage) {
            return html_writer::empty_tag(
                'input',
                ['type' => 'hidden', 'name' => 'id', 'value' => $selectedcourseid]
            );
        }

        $result = '';
        $sitecontext = context_system::instance();
        $courses = $reportlog->get_course_list();

        if (!empty($courses) && $reportlog->showcourses) {
            $result .= html_writer::label(get_string('selectacourse'), 'menuid', false, ['class' => 'accesshide']);
            $result .= html_writer::select($courses, "id", $selectedcourseid, null);
            return $result;
        }

        $courses = [];
        $courseinfo = ($selectedcourseid == SITEID) ? ' (' . get_string('site') . ') ' : '';
        $courses[$selectedcourseid] = get_course_display_name_for_list($reportlog->course) . $courseinfo;

        $result .= html_writer::label(get_string('selectacourse'), 'menuid', false, ['class' => 'accesshide']);
        $result .= html_writer::select($courses, "id", $selectedcourseid, false);

        // Check if user is admin and this came because of limitation on number of courses to show in dropdown.
        if (has_capability('report/log:view', $sitecontext)) {
            $a = new stdClass();
            $a->url = new moodle_url(
                '/report/log/index.php',
                [
                    'chooselog' => 0,
                    'group' => $reportlog->get_selected_group(),
                    'user' => $reportlog->userid,
                    'id' => $selectedcourseid,
                    'date' => $reportlog->date,
                    'modid' => $reportlog->modid,
                    'showcourses' => 1, 'showusers' => $reportlog->showusers,
                ]
            );
            $a->url = $a->url->out(false);
            $result .= get_string('logtoomanycourses', 'moodle', $a);
        }

        return $result;
    }

    /**
     * Generates the activity selector field for the log report.
     *
     * @param report_log_renderable $reportlog The output instance.
     * @return string
     */
    private function get_activity_selector_field(report_log_renderable $reportlog): string {
        $result = '';
        // When the log report is accessed vie an activity, we do not need an activity selector.
        if ($reportlog->isactivitypage) {
            $result .= html_writer::empty_tag(
                'input',
                ['type' => 'hidden', 'name' => 'isactivitypage', 'value' => $reportlog->isactivitypage]
            );
            $result .= html_writer::empty_tag(
                'input',
                ['type' => 'hidden', 'name' => 'modid', 'value' => $reportlog->modid]
            );
            return $result;
        }

        [$activities, $disabled] = $reportlog->get_activities_list();

        $result .= html_writer::label(
            text: get_string('activities'),
            for: 'menumodid',
            colonize: false,
            attributes: ['class' => 'accesshide'],
        );
        $result .= html_writer::select(
            options: $activities,
            name: "modid",
            selected: $reportlog->modid,
            nothing: get_string("allactivities"),
            attributes: [],
            disabled: $disabled,
        );
        return $result;
    }
}
