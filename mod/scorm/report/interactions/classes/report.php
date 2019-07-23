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
 * Core Report class of basic reporting plugin
 * @package   scormreport
 * @subpackage interactions
 * @author    Dan Marsden and Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace scormreport_interactions;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/scorm/report/interactions/responsessettings_form.php');

class report extends \mod_scorm\report {
    /**
     * displays the full report
     * @param \stdClass $scorm full SCORM object
     * @param \stdClass $cm - full course_module object
     * @param \stdClass $course - full course object
     * @param string $download - type of download being requested
     */
    public function display($scorm, $cm, $course, $download) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $contextmodule = \context_module::instance($cm->id);
        $action = optional_param('action', '', PARAM_ALPHA);
        $attemptids = optional_param_array('attemptid', array(), PARAM_RAW);
        $attemptsmode = optional_param('attemptsmode', SCORM_REPORT_ATTEMPTS_ALL_STUDENTS, PARAM_INT);
        $PAGE->set_url(new \moodle_url($PAGE->url, array('attemptsmode' => $attemptsmode)));

        if ($action == 'delete' && has_capability('mod/scorm:deleteresponses', $contextmodule) && confirm_sesskey()) {
            if (scorm_delete_responses($attemptids, $scorm)) { // Delete responses.
                echo $OUTPUT->notification(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
            }
        }
        // Find out current groups mode.
        $currentgroup = groups_get_activity_group($cm, true);

        // Detailed report.
        $mform = new \mod_scorm_report_interactions_settings($PAGE->url, compact('currentgroup'));
        if ($fromform = $mform->get_data()) {
            $pagesize = $fromform->pagesize;
            $includeqtext = $fromform->qtext;
            $includeresp = $fromform->resp;
            $includeright = $fromform->right;
            $includeresult = $fromform->result;
            set_user_preference('scorm_report_pagesize', $pagesize);
            set_user_preference('scorm_report_interactions_qtext', $includeqtext);
            set_user_preference('scorm_report_interactions_resp', $includeresp);
            set_user_preference('scorm_report_interactions_right', $includeright);
            set_user_preference('scorm_report_interactions_result', $includeresult);
        } else {
            $pagesize = get_user_preferences('scorm_report_pagesize', 0);
            $includeqtext = get_user_preferences('scorm_report_interactions_qtext', 0);
            $includeresp = get_user_preferences('scorm_report_interactions_resp', 1);
            $includeright = get_user_preferences('scorm_report_interactions_right', 0);
            $includeresult = get_user_preferences('scorm_report_interactions_result', 0);
        }
        if ($pagesize < 1) {
            $pagesize = SCORM_REPORT_DEFAULT_PAGE_SIZE;
        }

        // Select group menu.
        $displayoptions = array();
        $displayoptions['attemptsmode'] = $attemptsmode;
        $displayoptions['qtext'] = $includeqtext;
        $displayoptions['resp'] = $includeresp;
        $displayoptions['right'] = $includeright;
        $displayoptions['result'] = $includeresult;

        $mform->set_data($displayoptions + array('pagesize' => $pagesize));
        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
            if (!$download) {
                groups_print_activity_menu($cm, new \moodle_url($PAGE->url, $displayoptions));
            }
        }
        $formattextoptions = array('context' => \context_course::instance($course->id));

        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $candelete = has_capability('mod/scorm:deleteresponses', $contextmodule)
                && ($attemptsmode != SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO);
        // Select the students.
        $nostudents = false;

        if (empty($currentgroup)) {
            // All users who can attempt scoes.
            if (!$students = get_users_by_capability($contextmodule, 'mod/scorm:savetrack', 'u.id', '', '', '', '', '', false)) {
                echo $OUTPUT->notification(get_string('nostudentsyet'));
                $nostudents = true;
                $allowedlist = '';
            } else {
                $allowedlist = array_keys($students);
            }
            unset($students);
        } else {
            // All users who can attempt scoes and who are in the currently selected group.
            if (!$groupstudents = get_users_by_capability($contextmodule,
                                                            'mod/scorm:savetrack', 'u.id', '', '', '',
                                                            $currentgroup, '', false)) {
                echo $OUTPUT->notification(get_string('nostudentsingroup'));
                $nostudents = true;
                $groupstudents = array();
            }
            $allowedlist = array_keys($groupstudents);
            unset($groupstudents);
        }
        if ( !$nostudents ) {
            // Now check if asked download of data.
            $coursecontext = \context_course::instance($course->id);
            if ($download) {
                $filename = clean_filename("$course->shortname ".format_string($scorm->name, true, $formattextoptions));
            }

            // Define table columns.
            $columns = array();
            $headers = array();
            if (!$download && $candelete) {
                $columns[] = 'checkbox';
                $headers[] = $this->generate_master_checkbox();
            }
            if (!$download && $CFG->grade_report_showuserimage) {
                $columns[] = 'picture';
                $headers[] = '';
            }
            $columns[] = 'fullname';
            $headers[] = get_string('name');

            $extrafields = get_extra_user_fields($coursecontext);
            foreach ($extrafields as $field) {
                $columns[] = $field;
                $headers[] = get_user_field_name($field);
            }
            $columns[] = 'attempt';
            $headers[] = get_string('attempt', 'scorm');
            $columns[] = 'start';
            $headers[] = get_string('started', 'scorm');
            $columns[] = 'finish';
            $headers[] = get_string('last', 'scorm');
            $columns[] = 'score';
            $headers[] = get_string('score', 'scorm');
            $scoes = $DB->get_records('scorm_scoes', array("scorm" => $scorm->id), 'sortorder, id');
            foreach ($scoes as $sco) {
                if ($sco->launch != '') {
                    $columns[] = 'scograde'.$sco->id;
                    $headers[] = format_string($sco->title, '', $formattextoptions);
                }
            }

            $params = array();
            list($usql, $params) = $DB->get_in_or_equal($allowedlist, SQL_PARAMS_NAMED);
            // Construct the SQL.
            $select = 'SELECT DISTINCT '.$DB->sql_concat('u.id', '\'#\'', 'COALESCE(st.attempt, 0)').' AS uniqueid, ';
            $select .= 'st.scormid AS scormid, st.attempt AS attempt, ' .
                    \user_picture::fields('u', array('idnumber'), 'userid') .
                    get_extra_user_fields_sql($coursecontext, 'u', '', array('email', 'idnumber')) . ' ';

            // This part is the same for all cases - join users and scorm_scoes_track tables.
            $from = 'FROM {user} u ';
            $from .= 'LEFT JOIN {scorm_scoes_track} st ON st.userid = u.id AND st.scormid = '.$scorm->id;
            switch ($attemptsmode) {
                case SCORM_REPORT_ATTEMPTS_STUDENTS_WITH:
                    // Show only students with attempts.
                    $where = ' WHERE u.id ' .$usql. ' AND st.userid IS NOT NULL';
                    break;
                case SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO:
                    // Show only students without attempts.
                    $where = ' WHERE u.id ' .$usql. ' AND st.userid IS NULL';
                    break;
                case SCORM_REPORT_ATTEMPTS_ALL_STUDENTS:
                    // Show all students with or without attempts.
                    $where = ' WHERE u.id ' .$usql. ' AND (st.userid IS NOT NULL OR st.userid IS NULL)';
                    break;
            }

            $countsql = 'SELECT COUNT(DISTINCT('.$DB->sql_concat('u.id', '\'#\'', 'COALESCE(st.attempt, 0)').')) AS nbresults, ';
            $countsql .= 'COUNT(DISTINCT('.$DB->sql_concat('u.id', '\'#\'', 'st.attempt').')) AS nbattempts, ';
            $countsql .= 'COUNT(DISTINCT(u.id)) AS nbusers ';
            $countsql .= $from.$where;
            $questioncount = get_scorm_question_count($scorm->id);
            $nbmaincolumns = count($columns);
            for ($id = 0; $id < $questioncount; $id++) {
                if ($displayoptions['qtext']) {
                    $columns[] = 'question' . $id;
                    $headers[] = get_string('questionx', 'scormreport_interactions', $id);
                }
                if ($displayoptions['resp']) {
                    $columns[] = 'response' . $id;
                    $headers[] = get_string('responsex', 'scormreport_interactions', $id);
                }
                if ($displayoptions['right']) {
                    $columns[] = 'right' . $id;
                    $headers[] = get_string('rightanswerx', 'scormreport_interactions', $id);
                }
                if ($displayoptions['result']) {
                    $columns[] = 'result' . $id;
                    $headers[] = get_string('resultx', 'scormreport_interactions', $id);
                }
            }

            if (!$download) {
                $table = new \flexible_table('mod-scorm-report');

                $table->define_columns($columns);
                $table->define_headers($headers);
                $table->define_baseurl($PAGE->url);

                $table->sortable(true);
                $table->collapsible(true);

                // This is done to prevent redundant data, when a user has multiple attempts.
                $table->column_suppress('picture');
                $table->column_suppress('fullname');
                foreach ($extrafields as $field) {
                    $table->column_suppress($field);
                }

                $table->no_sorting('start');
                $table->no_sorting('finish');
                $table->no_sorting('score');
                $table->no_sorting('checkbox');
                $table->no_sorting('picture');

                for ($id = 0; $id < $questioncount; $id++) {
                    if ($displayoptions['qtext']) {
                        $table->no_sorting('question'.$id);
                    }
                    if ($displayoptions['resp']) {
                        $table->no_sorting('response'.$id);
                    }
                    if ($displayoptions['right']) {
                        $table->no_sorting('right'.$id);
                    }
                    if ($displayoptions['result']) {
                        $table->no_sorting('result'.$id);
                    }
                }

                foreach ($scoes as $sco) {
                    if ($sco->launch != '') {
                        $table->no_sorting('scograde'.$sco->id);
                    }
                }

                $table->column_class('picture', 'picture');
                $table->column_class('fullname', 'bold');
                $table->column_class('score', 'bold');

                $table->set_attribute('cellspacing', '0');
                $table->set_attribute('id', 'attempts');
                $table->set_attribute('class', 'generaltable generalbox');

                // Start working -- this is necessary as soon as the niceties are over.
                $table->setup();
            } else if ($download == 'ODS') {
                require_once("$CFG->libdir/odslib.class.php");

                $filename .= ".ods";
                // Creating a workbook.
                $workbook = new \MoodleODSWorkbook("-");
                // Sending HTTP headers.
                $workbook->send($filename);
                // Creating the first worksheet.
                $sheettitle = get_string('report', 'scorm');
                $myxls = $workbook->add_worksheet($sheettitle);
                // Format types.
                $format = $workbook->add_format();
                $format->set_bold(0);
                $formatbc = $workbook->add_format();
                $formatbc->set_bold(1);
                $formatbc->set_align('center');
                $formatb = $workbook->add_format();
                $formatb->set_bold(1);
                $formaty = $workbook->add_format();
                $formaty->set_bg_color('yellow');
                $formatc = $workbook->add_format();
                $formatc->set_align('center');
                $formatr = $workbook->add_format();
                $formatr->set_bold(1);
                $formatr->set_color('red');
                $formatr->set_align('center');
                $formatg = $workbook->add_format();
                $formatg->set_bold(1);
                $formatg->set_color('green');
                $formatg->set_align('center');
                // Here starts workshhet headers.

                $colnum = 0;
                foreach ($headers as $item) {
                    $myxls->write(0, $colnum, $item, $formatbc);
                    $colnum++;
                }
                $rownum = 1;
            } else if ($download == 'Excel') {
                require_once("$CFG->libdir/excellib.class.php");

                $filename .= ".xls";
                // Creating a workbook.
                $workbook = new \MoodleExcelWorkbook("-");
                // Sending HTTP headers.
                $workbook->send($filename);
                // Creating the first worksheet.
                $sheettitle = get_string('report', 'scorm');
                $myxls = $workbook->add_worksheet($sheettitle);
                // Format types.
                $format = $workbook->add_format();
                $format->set_bold(0);
                $formatbc = $workbook->add_format();
                $formatbc->set_bold(1);
                $formatbc->set_align('center');
                $formatb = $workbook->add_format();
                $formatb->set_bold(1);
                $formaty = $workbook->add_format();
                $formaty->set_bg_color('yellow');
                $formatc = $workbook->add_format();
                $formatc->set_align('center');
                $formatr = $workbook->add_format();
                $formatr->set_bold(1);
                $formatr->set_color('red');
                $formatr->set_align('center');
                $formatg = $workbook->add_format();
                $formatg->set_bold(1);
                $formatg->set_color('green');
                $formatg->set_align('center');

                $colnum = 0;
                foreach ($headers as $item) {
                    $myxls->write(0, $colnum, $item, $formatbc);
                    $colnum++;
                }
                $rownum = 1;
            } else if ($download == 'CSV') {
                $csvexport = new \csv_export_writer("tab");
                $csvexport->set_filename($filename, ".txt");
                $csvexport->add_data($headers);
            }

            if (!$download) {
                $sort = $table->get_sql_sort();
            } else {
                $sort = '';
            }
            // Fix some wired sorting.
            if (empty($sort)) {
                $sort = ' ORDER BY uniqueid';
            } else {
                $sort = ' ORDER BY '.$sort;
            }

            if (!$download) {
                // Add extra limits due to initials bar.
                list($twhere, $tparams) = $table->get_sql_where();
                if ($twhere) {
                    $where .= ' AND '.$twhere; // Initial bar.
                    $params = array_merge($params, $tparams);
                }

                if (!empty($countsql)) {
                    $count = $DB->get_record_sql($countsql, $params);
                    $totalinitials = $count->nbresults;
                    if ($twhere) {
                        $countsql .= ' AND '.$twhere;
                    }
                    $count = $DB->get_record_sql($countsql, $params);
                    $total  = $count->nbresults;
                }

                $table->pagesize($pagesize, $total);

                echo \html_writer::start_div('scormattemptcounts');
                if ( $count->nbresults == $count->nbattempts ) {
                    echo get_string('reportcountattempts', 'scorm', $count);
                } else if ( $count->nbattempts > 0 ) {
                    echo get_string('reportcountallattempts', 'scorm', $count);
                } else {
                    echo $count->nbusers.' '.get_string('users');
                }
                echo \html_writer::end_div();
            }

            // Fetch the attempts.
            if (!$download) {
                $attempts = $DB->get_records_sql($select.$from.$where.$sort, $params,
                $table->get_page_start(), $table->get_page_size());
                echo \html_writer::start_div('', array('id' => 'scormtablecontainer'));
                if ($candelete) {
                    // Start form.
                    $strreallydel  = addslashes_js(get_string('deleteattemptcheck', 'scorm'));
                    echo \html_writer::start_tag('form', array('id' => 'attemptsform', 'method' => 'post',
                                                                'action' => $PAGE->url->out(false),
                                                                'onsubmit' => 'return confirm("'.$strreallydel.'");'));
                    echo \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'delete'));
                    echo \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
                    echo \html_writer::start_div('', array('style' => 'display: none;'));
                    echo \html_writer::input_hidden_params($PAGE->url);
                    echo \html_writer::end_div();
                    echo \html_writer::start_div();
                }
                $table->initialbars($totalinitials > 20); // Build table rows.
            } else {
                $attempts = $DB->get_records_sql($select.$from.$where.$sort, $params);
            }
            if ($attempts) {
                foreach ($attempts as $scouser) {
                    $row = array();
                    if (!empty($scouser->attempt)) {
                        $timetracks = scorm_get_sco_runtime($scorm->id, false, $scouser->userid, $scouser->attempt);
                    } else {
                        $timetracks = '';
                    }
                    if (in_array('checkbox', $columns)) {
                        if ($candelete && !empty($timetracks->start)) {
                            $row[] = $this->generate_row_checkbox('attemptid[]', "{$scouser->userid}:{$scouser->attempt}");
                        } else if ($candelete) {
                            $row[] = '';
                        }
                    }
                    if (in_array('picture', $columns)) {
                        $user = new \stdClass();
                        $additionalfields = explode(',', \user_picture::fields());
                        $user = username_load_fields_from_object($user, $scouser, null, $additionalfields);
                        $user->id = $scouser->userid;
                        $row[] = $OUTPUT->user_picture($user, array('courseid' => $course->id));
                    }
                    if (!$download) {
                        $url = new \moodle_url('/user/view.php', array('id' => $scouser->userid, 'course' => $course->id));
                        $row[] = \html_writer::link($url, fullname($scouser));
                    } else {
                        $row[] = fullname($scouser);
                    }
                    foreach ($extrafields as $field) {
                        $row[] = s($scouser->{$field});
                    }
                    if (empty($timetracks->start)) {
                        $row[] = '-';
                        $row[] = '-';
                        $row[] = '-';
                        $row[] = '-';
                    } else {
                        if (!$download) {
                            $url = new \moodle_url('/mod/scorm/report/userreport.php',
                                                    array('id' => $cm->id,
                                                        'user' => $scouser->userid,
                                                        'attempt' => $scouser->attempt));
                            $row[] = \html_writer::link($url, $scouser->attempt);
                        } else {
                            $row[] = $scouser->attempt;
                        }
                        if ($download == 'ODS' || $download == 'Excel' ) {
                            $row[] = userdate($timetracks->start, get_string("strftimedatetime", "langconfig"));
                        } else {
                            $row[] = userdate($timetracks->start);
                        }
                        if ($download == 'ODS' || $download == 'Excel' ) {
                            $row[] = userdate($timetracks->finish, get_string('strftimedatetime', 'langconfig'));
                        } else {
                            $row[] = userdate($timetracks->finish);
                        }
                        $row[] = scorm_grade_user_attempt($scorm, $scouser->userid, $scouser->attempt);
                    }
                    // Print out all scores of attempt.
                    $emptyrow = $download ? '' : '&nbsp;';
                    foreach ($scoes as $sco) {
                        if ($sco->launch != '') {
                            if ($trackdata = scorm_get_tracks($sco->id, $scouser->userid, $scouser->attempt)) {
                                if ($trackdata->status == '') {
                                    $trackdata->status = 'notattempted';
                                }
                                $strstatus = get_string($trackdata->status, 'scorm');
                                // If raw score exists, print it.
                                if ($trackdata->score_raw != '') {
                                    $score = $trackdata->score_raw;
                                    // Add max score if it exists.
                                    if (isset($trackdata->score_max)) {
                                        $score .= '/'.$trackdata->score_max;
                                    }
                                } else { // Else print out status.
                                    $score = $strstatus;
                                }
                                if (!$download) {
                                    $url = new \moodle_url('/mod/scorm/report/userreporttracks.php', array('id' => $cm->id,
                                        'scoid' => $sco->id, 'user' => $scouser->userid, 'attempt' => $scouser->attempt));
                                    $row[] = $OUTPUT->pix_icon($trackdata->status, $strstatus, 'scorm') . '<br>' .
                                        \html_writer::link($url, $score, array('title' => get_string('details', 'scorm')));
                                } else {
                                    $row[] = $score;
                                }
                                // Interaction data.
                                for ($i = 0; $i < $questioncount; $i++) {
                                    if ($displayoptions['qtext']) {
                                        $element = 'cmi.interactions_'.$i.'.id';
                                        if (isset($trackdata->$element)) {
                                            $row[] = s($trackdata->$element);
                                        } else {
                                            $row[] = $emptyrow;
                                        }
                                    }
                                    if ($displayoptions['resp']) {
                                        $element = 'cmi.interactions_'.$i.'.student_response';
                                        if (isset($trackdata->$element)) {
                                            $row[] = s($trackdata->$element);
                                        } else {
                                            $row[] = $emptyrow;
                                        }
                                    }
                                    if ($displayoptions['right']) {
                                        $j = 0;
                                        $element = 'cmi.interactions_'.$i.'.correct_responses_'.$j.'.pattern';
                                        $rightans = '';
                                        if (isset($trackdata->$element)) {
                                            while (isset($trackdata->$element)) {
                                                if ($j > 0) {
                                                    $rightans .= ',';
                                                }
                                                $rightans .= s($trackdata->$element);
                                                $j++;
                                                $element = 'cmi.interactions_'.$i.'.correct_responses_'.$j.'.pattern';
                                            }
                                            $row[] = $rightans;
                                        } else {
                                            $row[] = $emptyrow;
                                        }
                                    }
                                    if ($displayoptions['result']) {
                                        $element = 'cmi.interactions_'.$i.'.result';
                                        if (isset($trackdata->$element)) {
                                            $row[] = s($trackdata->$element);
                                        } else {
                                            $row[] = $emptyrow;
                                        }
                                    }
                                }
                                // End of interaction data.
                            } else {
                                // If we don't have track data, we haven't attempted yet.
                                $strstatus = get_string('notattempted', 'scorm');
                                if (!$download) {
                                    $row[] = $OUTPUT->pix_icon('notattempted', $strstatus, 'scorm') . '<br>' . $strstatus;
                                } else {
                                    $row[] = $strstatus;
                                }
                                // Complete the empty cells.
                                for ($i = 0; $i < count($columns) - $nbmaincolumns; $i++) {
                                    $row[] = $emptyrow;
                                }
                            }
                        }
                    }

                    if (!$download) {
                        $table->add_data($row);
                    } else if ($download == 'Excel' or $download == 'ODS') {
                        $colnum = 0;
                        foreach ($row as $item) {
                            $myxls->write($rownum, $colnum, $item, $format);
                            $colnum++;
                        }
                        $rownum++;
                    } else if ($download == 'CSV') {
                        $csvexport->add_data($row);
                    }
                }
                if (!$download) {
                    $table->finish_output();
                    if ($candelete) {
                        echo \html_writer::start_tag('table', array('id' => 'commands'));
                        echo \html_writer::start_tag('tr').\html_writer::start_tag('td');
                        echo $this->generate_delete_selected_button();
                        echo \html_writer::end_tag('td').\html_writer::end_tag('tr').\html_writer::end_tag('table');
                        // Close form.
                        echo \html_writer::end_tag('div');
                        echo \html_writer::end_tag('form');
                    }
                    echo \html_writer::end_div();
                    if (!empty($attempts)) {
                        echo \html_writer::start_tag('table', array('class' => 'boxaligncenter')).\html_writer::start_tag('tr');
                        echo \html_writer::start_tag('td');
                        echo $OUTPUT->single_button(new \moodle_url($PAGE->url,
                                                                   array('download' => 'ODS') + $displayoptions),
                                                                   get_string('downloadods'),
                                                                   'post',
                                                                   ['class' => 'mt-1']);
                        echo \html_writer::end_tag('td');
                        echo \html_writer::start_tag('td');
                        echo $OUTPUT->single_button(new \moodle_url($PAGE->url,
                                                                   array('download' => 'Excel') + $displayoptions),
                                                                   get_string('downloadexcel'),
                                                                   'post',
                                                                   ['class' => 'mt-1']);
                        echo \html_writer::end_tag('td');
                        echo \html_writer::start_tag('td');
                        echo $OUTPUT->single_button(new \moodle_url($PAGE->url,
                                                                   array('download' => 'CSV') + $displayoptions),
                                                                   get_string('downloadtext'),
                                                                   'post',
                                                                   ['class' => 'mt-1']);
                        echo \html_writer::end_tag('td');
                        echo \html_writer::start_tag('td');
                        echo \html_writer::end_tag('td');
                        echo \html_writer::end_tag('tr').\html_writer::end_tag('table');
                    }
                }
            } else {
                if ($candelete && !$download) {
                    echo \html_writer::end_div();
                    echo \html_writer::end_tag('form');
                    $table->finish_output();
                }
                echo \html_writer::end_div();
            }
            // Show preferences form irrespective of attempts are there to report or not.
            if (!$download) {
                $mform->set_data(compact('pagesize', 'attemptsmode'));
                $mform->display();
            }
            if ($download == 'Excel' or $download == 'ODS') {
                $workbook->close();
                exit;
            } else if ($download == 'CSV') {
                $csvexport->download_file();
                exit;
            }
        } else {
            echo $OUTPUT->notification(get_string('noactivity', 'scorm'));
        }
    }// Function ends.
}
