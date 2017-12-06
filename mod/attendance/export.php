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
 * Export attendance sessions
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/export_form.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

$id             = required_param('id', PARAM_INT);

$cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/attendance:export', $context);

$att = new mod_attendance_structure($att, $cm, $course, $context);

$PAGE->set_url($att->url_export());
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('export', 'attendance'));

$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $context);
$mform = new mod_attendance_export_form($att->url_export(), $formparams);

if ($formdata = $mform->get_data()) {

    $pageparams = new mod_attendance_page_with_filter_controls();
    $pageparams->init($cm);
    $pageparams->page = 0;
    $pageparams->group = $formdata->group;
    $pageparams->set_current_sesstype($formdata->group ? $formdata->group : mod_attendance_page_with_filter_controls::SESSTYPE_ALL);
    if (isset($formdata->includeallsessions)) {
        if (isset($formdata->includenottaken)) {
            $pageparams->view = ATT_VIEW_ALL;
        } else {
            $pageparams->view = ATT_VIEW_ALLPAST;
            $pageparams->curdate = time();
        }
        $pageparams->init_start_end_date();
    } else {
        $pageparams->startdate = $formdata->sessionstartdate;
        $pageparams->enddate = $formdata->sessionenddate;
    }
    if ($formdata->selectedusers) {
        $pageparams->userids = $formdata->users;
    }
    $att->pageparams = $pageparams;

    $reportdata = new attendance_report_data($att);
    if ($reportdata->users) {
        $filename = clean_filename($course->shortname.'_Attendances_'.userdate(time(), '%Y%m%d-%H%M'));

        $group = $formdata->group ? $reportdata->groups[$formdata->group] : 0;
        $data = new stdClass;
        $data->tabhead = array();
        $data->course = $att->course->fullname;
        $data->group = $group ? $group->name : get_string('allparticipants');

        if (isset($formdata->ident['id'])) {
            $data->tabhead[] = get_string('studentid', 'attendance');
        }
        if (isset($formdata->ident['uname'])) {
            $data->tabhead[] = get_string('username');
        }

        $optional = array('idnumber', 'institution', 'department');
        foreach ($optional as $opt) {
            if (isset($formdata->ident[$opt])) {
                $data->tabhead[] = get_string($opt);
            }
        }

        $data->tabhead[] = get_string('lastname');
        $data->tabhead[] = get_string('firstname');
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if (!empty($groupmode)) {
            $data->tabhead[] = get_string('groups');
        }

        if (count($reportdata->sessions) > 0) {
            foreach ($reportdata->sessions as $sess) {
                $text = userdate($sess->sessdate, get_string('strftimedmyhm', 'attendance'));
                $text .= ' ';
                if (!empty($sess->groupid) && empty($reportdata->groups[$sess->groupid])) {
                    $text .= get_string('deletedgroup', 'attendance');
                } else {
                    $text .= $sess->groupid ? $reportdata->groups[$sess->groupid]->name : get_string('commonsession', 'attendance');
                }
                $data->tabhead[] = $text;
                if (isset($formdata->includeremarks)) {
                    $data->tabhead[] = ''; // Space for the remarks.
                }
            }
        } else {
            print_error('sessionsnotfound', 'attendance', $att->url_manage());
        }

        $setnumber = -1;
        foreach ($reportdata->statuses as $sts) {
            if ($sts->setnumber != $setnumber) {
                $setnumber = $sts->setnumber;
            }

            $data->tabhead[] = $sts->acronym;
        }

        $data->tabhead[] = get_string('takensessions', 'attendance');
        $data->tabhead[] = get_string('points', 'attendance');
        $data->tabhead[] = get_string('percentage', 'attendance');

        $i = 0;
        $data->table = array();
        foreach ($reportdata->users as $user) {
            if (isset($formdata->ident['id'])) {
                $data->table[$i][] = $user->id;
            }
            if (isset($formdata->ident['uname'])) {
                $data->table[$i][] = $user->username;
            }

            $optionalrow = array('idnumber', 'institution', 'department');
            foreach ($optionalrow as $opt) {
                if (isset($formdata->ident[$opt])) {
                    $data->table[$i][] = $user->$opt;
                }
            }

            $data->table[$i][] = $user->lastname;
            $data->table[$i][] = $user->firstname;
            if (!empty($groupmode)) {
                $grouptext = '';
                $groupsraw = groups_get_all_groups($course->id, $user->id, 0, 'g.name');
                $groups = array();
                foreach ($groupsraw as $group) {
                    $groups[] = $group->name;;
                }
                $data->table[$i][] = implode(', ', $groups);
            }
            $cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
            $data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells(isset($formdata->includeremarks)));

            $usersummary = $reportdata->summary->get_taken_sessions_summary_for($user->id);

            foreach ($reportdata->statuses as $sts) {
                if (isset($usersummary->userstakensessionsbyacronym[$sts->setnumber][$sts->acronym])) {
                    $data->table[$i][] = $usersummary->userstakensessionsbyacronym[$sts->setnumber][$sts->acronym];
                } else {
                    $data->table[$i][] = 0;
                }
            }

            $data->table[$i][] = $usersummary->numtakensessions;
            $data->table[$i][] = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                                    format_float($usersummary->takensessionsmaxpoints, 1, true, true);
            $data->table[$i][] = format_float($usersummary->takensessionspercentage * 100);

            $i++;
        }

        if ($formdata->format === 'text') {
            attendance_exporttocsv($data, $filename);
        } else {
            attendance_exporttotableed($data, $filename, $formdata->format);
        }
        exit;
    } else {
        print_error('studentsnotfound', 'attendance', $att->url_manage());
    }
}

$output = $PAGE->get_renderer('mod_attendance');
$tabs = new attendance_tabs($att, attendance_tabs::TAB_EXPORT);
echo $output->header();
echo $output->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
echo $output->render($tabs);

$mform->display();

echo $OUTPUT->footer();



