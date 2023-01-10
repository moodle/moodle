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
 * @package   local_iomad_track
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to import completion information.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/csvlib.class.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$completions = optional_param('completions', 0, PARAM_BOOL);
$checkcourses = optional_param('checkcourses', 0, PARAM_BOOL);
$viewenabled = optional_param('viewenabled', 0, PARAM_BOOL);
$viewcriteria = optional_param('viewcriteria', 0, PARAM_BOOL);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$submit = optional_param('submitbutton', '', PARAM_ALPHANUM);
$fileimport = optional_param('fileimport', 0, PARAM_BOOL);
$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$readcount   = optional_param('readcount', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('local/iomad_track:importfrommoodle', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

$linktext = get_string('importcompletionrecords', 'local_iomad_track');

// Set the url.
$linkurl = new moodle_url('/local/iomad_track/import.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Array of all valid fields for validation.
$stdfields = array('username', 'userid', 'courseid', 'coursename', 'coursecode', 'timeenrolled', 'timestarted', 'timecompleted',
        'finalscore', 'licensename', 'licenseallocated', 'licenseid', 'companyid', 'company', 'departmentid', 'department');

// Check if there are any potential errors.
$courseswithoutcompletionenabled = $DB->get_records_sql("SELECT * FROM {course} WHERE enablecompletion = 0 AND id != :siteid", ['siteid' => SITEID]);
$courseswithoutcompletioncriteria = $DB->get_records_sql("SELECT * FROM {course} WHERE id != :siteid AND id NOT IN (SELECT course FROM {course_completion_criteria})", ['siteid' => SITEID]);
$courseswithoutcompletionenabledcount = count($courseswithoutcompletionenabled);
$courseswithoutcompletioncriteriacount = count($courseswithoutcompletioncriteria);

// Process current completions.
if (!empty($completions)) {
    if (confirm_sesskey() && $confirm == md5($completions)) {
        $task = new local_iomad_track\task\importmoodlecompletioninformation();
        \core\task\manager::queue_adhoc_task($task, true);
        redirect($linkurl);
    } else {
        echo $OUTPUT->header();
        $optionsyes = array('completions' => $completions, 'confirm' => md5($completions), 'sesskey' => sesskey());
        if (empty($courseswithoutcompletionenabledcount) && empty($courseswithoutcompletioncriteriacount)) {
            echo $OUTPUT->confirm(get_string('importcompletionsfrommoodlefull', 'local_iomad_track'),
                                  new moodle_url('/local/iomad_track/import.php', $optionsyes), $linkurl);
        } else {
            echo $OUTPUT->confirm(get_string('importcompletionsfrommoodlefullwitherrors', 'local_iomad_track'),
                                  new moodle_url('/local/iomad_track/import.php', $optionsyes), $linkurl);
        }
        echo $OUTPUT->footer();
        die;
    }
}

if (!empty($fileimport)) {
    if (empty($iid)) {
        $mform = new local_iomad_track\forms\completion_import_form();
        if ($mform->is_cancelled()) {
            redirect($linkurl);
        }
        if ($importdata = $mform->get_data()) {
            // Verification moved to two places: after upload and into form2.
            $userserrors  = 0;
            $erroredusers = array();
            $errorstr = get_string('error');


            $iid = csv_import_reader::get_new_iid('uploadcompletion');
            $cir = new csv_import_reader($iid, 'uploadcompletion');

            $content = $mform->get_file_content('importfile');
            $readcount = $cir->load_csv_content($content,
                                                $importdata->encoding,
                                                $importdata->delimiter_name,
                                                'validate_uploadcompletion_columns');

            if (!$columns = $cir->get_columns()) {
               print_error('cannotreadtmpfile', 'error', $returnurl);
            }

            unset($content);

            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('uploadcompletionresult', 'local_iomad_track'));

            $cir->init();
            $runtime = time();
            $linenum = 1; // Column header is first line.

            // Init upload progress tracker.
            $upt = new upload_progress_tracker();
            $upt->init(); // Start table.
            while ($line = $cir->next()) {
                $upt->flush();
                $linenum++;
                $errornum = 1;
                $completionrec = new stdclass();
                $upt->track('line', $linenum);
                foreach ($line as $key => $value) {
                    if ($value !== '') {
                        $key = $columns[$key];

                        if (strpos($key, 'username') !== false) {
                            if (!$userrec = $DB->get_record('user', array('username' => $value))) {
                                $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                                $upt->track('username', $errorstr, 'error');
                                $line[] = get_string('missingfield', 'error', 'username');
                                $userserrors++;
                                $errornum++;
                                $erroredusers[] = $line;
                                continue 2;
                            }
                            $completionrec->userid = $userrec->id;
                            $upt->track($key, $userrec->username);
                            
                        } else if (strpos($key, 'userid') !== false) {
                            if (!$userrec = $DB->get_record('user', array('id' => $value))) {
                                $upt->track('status', get_string('missingfield', 'error', 'userid'), 'error');
                                $upt->track('username', $errorstr, 'error');
                                $line[] = get_string('missingfield', 'error', 'userid');
                                $userserrors++;
                                $errornum++;
                                $erroredusers[] = $line;
                                continue 2;
                            }
                            $completionrec->userid = $userrec->id;
                            $upt->track('username', $userrec->username);
                        } else if (strpos($key, 'coursename') !== false) {
                            if (!$courserec = $DB->get_record('course', array('shortname' => $value))) {
                                $upt->track('status', get_string('missingfield', 'error', 'coursename'), 'error');
                                $upt->track('course', $errorstr, 'error');
                                $line[] = get_string('missingfield', 'error', 'coursename');
                                $userserrors++;
                                $errornum++;
                                $erroredusers[] = $line;
                                continue 2;
                            }
                            $completionrec->courseid = $courserec->id;
                            $completionrec->coursename = $courserec->fullname;
                            $upt->track('course', $courserec->fullname);
                        } else if (strpos($key, 'coursecode') !== false) {
                            if (!$courserec = $DB->get_record('course', array('idnumber' => $value))) {
                                $upt->track('status', get_string('missingfield', 'error', 'coursename'), 'error');
                                $upt->track('course', $errorstr, 'error');
                                $line[] = get_string('missingfield', 'error', 'coursename');
                                $userserrors++;
                                $errornum++;
                                $erroredusers[] = $line;
                                continue 2;
                            }
                            $completionrec->courseid = $courserec->id;
                            $completionrec->coursename = $courserec->fullname;
                            $upt->track('course', $courserec->fullname);
                        } else if (strpos($key, 'courseid') !== false) {
                            if (!$courserec = $DB->get_record('course', array('id' => $value))) {
                                $upt->track('status', get_string('missingfield', 'error', 'courseid'), 'error');
                                $upt->track('course', $errorstr, 'error');
                                $line[] = get_string('missingfield', 'error', 'courseid');
                                $userserrors++;
                                $errornum++;
                                $erroredusers[] = $line;
                                continue 2;
                            }
                            $completionrec->courseid = $courserec->id;
                            $completionrec->coursename = $courserec->fullname;
                            $upt->track('course', $courserec->fullname);
                        } else if (strpos($key, 'time') !== false) {
                            $completionrec->$key = strtotime($value);
                            $upt->track($key, date($CFG->iomad_date_format, $completionrec->$key));
                        } else if (strpos($key, 'licenseallocated') !== false) {
                            $completionrec->$key = strtotime($value);
                            $upt->track($key, date($CFG->iomad_date_format, $completionrec->$key));
                        } else {
                            $completionrec->$key = $value;
                            if (in_array($key, $upt->columns)) {
                                $upt->track($key, $value);
                            }
                        }
                    }
                }

                // We should by now have a user record and a course record.
                if (empty($userrec) || empty($courserec)) {
                    $userserrors++;
                    $errornum++;
                    $erroredusers[] = $line;
                    continue;
                }

                // Do we have everything?
                if (empty(($completionrec->companyid))) {
                    if (!$company = company::by_userid($completionrec->userid)) {
                        $upt->track('status', get_string('missingfield', 'error', 'companyid'), 'error');
                        $upt->track('company', $errorstr, 'error');
                        $line[] = get_string('missingfield', 'error', 'companyid');
                        $userserrors++;
                        $errornum++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $completionrec->companyid = $company->id;
                        $upt->track('company', $company->get_name());
                    }
                } else {
                    if (!$usercompany = $DB->get_record('company', array('id', $completionrec->companyid))) {
                        $upt->track('status', get_string('missingfield', 'error', 'companyid'), 'error');
                        $upt->track('company', $errorstr, 'error');
                        $line[] = get_string('missingfield', 'error', 'companyid');
                        $userserrors++;
                        $errornum++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $completionrec->companyid = $usercompany->id;
                        $company = new company($usercompany->id);
                        $upt->track('company', $usercompany->name);
                    }
                }
                if (empty($completionrec->departmentid)) {
                    $departments = $company->get_userlevel($userrec);
                    if (empty($departments)) {
                        $upt->track('status', get_string('missingfield', 'error', 'departmentid'), 'error');
                        $upt->track('department', $errorstr, 'error');
                        $line[] = get_string('missingfield', 'error', 'departmentid');
                        $userserrors++;
                        $errornum++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $completionrec->departmentid = key($departments);
                        $upt->track('department', $departments[$completionrec->departmentid]->name);
                    }
                }
                if (empty($completionrec->timeenrolled)) {
                    $completionrec->timeenrolled = $completionrec->timecompleted;
                    $upt->track('timeenrolled', date($CFG->iomad_date_format, $completionrec->timeenrolled));
                }
                if (empty($completionrec->timestarted)) {
                    $completionrec->timestarted = $completionrec->timecompleted;
                    $upt->track('timestarted', date($CFG->iomad_date_format, $completionrec->timestarted));
                }
                if ($DB->get_record('iomad_courses', array('courseid' => $courserec->id, 'licensed' => 1))) {
                    if (empty($completionrec->licensename)) {
                        $upt->track('status', get_string('missingfield', 'error', 'licensename'), 'error');
                        $upt->track('licensename', $errorstr, 'error');
                        $line[] = get_string('missingfield', 'error', 'licensename');
                        $userserrors++;
                        $errornum++;
                        $erroredusers[] = $line;
                        continue;
                    }
                }
                if (empty($completionrec->licenseallocated) && !empty($completionrec->licensename)) {
                    $completionrec->licenseallocated = $completionrec->timecompleted;
                    $upt->track('licenseallocated', date($CFG->iomad_date_format, $completionrec->timeenrolled));
                }
                $completionrec->modifiedtime = $runtime;
                $completionrec->coursecleared = 1;

                // Write the info to the db.
                $trackid = $DB->insert_record('local_iomad_track', $completionrec);
                $upt->track('id', $trackid);
                $upt->track('status', get_string('ok'));

                \local_iomad_track\observer::record_certificates($courserec->id, $userrec->id, $trackid, false);
            }

            $upt->flush();
            $upt->close(); // Close table.

            $cir->close();
            $cir->cleanup(true);

            // Deal with any erroring users.
            if (!empty($erroredusers)) {
                echo get_string('erroredusers', 'block_iomad_company_admin');
                $erroredtable = new html_table();
                foreach ($erroredusers as $erroreduser) {
                    $erroredtable->data[] = $erroreduser;
                }
                echo html_writer::table($erroredtable);

            }
                echo html_writer::tag('a',
                                      get_string('continue'),
                                      array('class' => 'btn-primary',
                                      'href' => $linkurl));

            echo $OUTPUT->footer();
            die;
        } else {
            $mform->set_data(array('fileimport' => $fileimport));
            echo $OUTPUT->header();
            $mform->display();
            echo $OUTPUT->footer();
        }
    }

}

// Display the page.
echo $OUTPUT->header();

echo html_writer::start_tag('p');
echo html_writer::tag('a',
                      get_string('checkcoursestatusmoodle', 'local_iomad_track'),
                      array('class' => 'btn-primary',
                            'href' => new moodle_url('/local/iomad_track/import.php',
                                                     array('checkcourses' => true,
                                                           'sesskey' => sesskey()))));

if ($checkcourses) {

    echo html_writer::start_tag('p');
    echo get_string('courseswithoutcompletionenabledcouunt', 'local_iomad_track', $courseswithoutcompletionenabledcount) . '&nbsp';
    echo html_writer::tag('a', get_string('view'), ['href' => new moodle_url('/local/iomad_track/import.php', ['checkcourses' => 1, 'viewenabled' => 1])]);
    if ($viewenabled) {
        echo html_writer::start_tag('table');
        foreach ($courseswithoutcompletionenabled as $course) {
            echo html_writer::start_tag('tr');
            echo html_writer::start_tag('td');
            echo html_writer::tag('a', format_string($course->fullname), ['href' => new moodle_url('/course/edit.php', ['id' => $course->id]), 'target' => 'new']);
            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }
        echo html_writer::start_tag('table');
    }
    echo html_writer::end_tag('p');
    echo html_writer::start_tag('p');
    echo get_string('courseswithoutcompletioncriteriacouunt', 'local_iomad_track', $courseswithoutcompletioncriteriacount) . '&nbsp';
    echo html_writer::tag('a', get_string('view'), ['href' => new moodle_url('/local/iomad_track/import.php', ['checkcourses' => 1, 'viewcriteria' => 1])]);
    if ($viewcriteria) {
        echo html_writer::start_tag('table');
        foreach ($courseswithoutcompletioncriteria as $course) {
            echo html_writer::start_tag('tr');
            echo html_writer::start_tag('td');
            echo html_writer::tag('a', format_string($course->fullname), ['href' => new moodle_url('/course/completion.php', ['id' => $course->id]), 'target' => 'new']);
            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }
        echo html_writer::start_tag('table');
    }
    echo html_writer::end_tag('p');

}

echo html_writer::end_tag('p');
echo html_writer::start_tag('p');
echo html_writer::tag('a',
                      get_string('importcompletionsfrommoodle', 'local_iomad_track'),
                      array('class' => 'btn-primary',
                            'href' => new moodle_url('/local/iomad_track/import.php',
                                                     array('completions' => true,
                                                           'sesskey' => sesskey()))));

echo html_writer::end_tag('p');
echo html_writer::start_tag('p');
echo html_writer::tag('a',
                      get_string('importcompletionsfromfile', 'local_iomad_track'),
                      array('class' => 'btn-primary',
                            'href' => new moodle_url('/local/iomad_track/import.php',
                                                     array('fileimport' => true,
                                                           'sesskey' => sesskey()))));
echo html_writer::end_tag('p');

echo $OUTPUT->footer();

/*
* Utility functions and classes
*/

class upload_progress_tracker {
    public $_row;
    public $columns = array('status',
                            'line',
                            'id',
                            'username',
                            'company',
                            'department',
                            'course',
                            'timeenrolled',
                            'timestarted',
                            'timecompleted',
                            'finalscore',
                            'licensename',
                            'licenseallocated');

    public function __construct() {
    }

    public function init() {
        $ci = 0;
        echo '<table id="uploadresults" class="generaltable boxaligncenter flexible-wrap" summary="'.
               get_string('uploadcompletionresult', 'local_iomad_track').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'tool_uploaduser').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('username').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('company', 'block_iomad_company_admin').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('department').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('course').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('dateenrolled', 'local_report_completion').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('datestarted', 'local_report_completion').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('datecompleted', 'local_report_completion').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('grade').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('licensename', 'block_iomad_company_admin').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('licensedateallocated', 'block_iomad_company_admin').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($field[$type] !== '') {
                    $field[$type] = '<span class="uu'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
        }
    }

    public function track($col, $msg, $level= 'normal', $merge=true) {
        if (empty($this->_row)) {
            $this->flush(); // Init arrays.
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= '<br />';
            }
            $this->_row[$col][$level] .= s($msg);
        } else {
            $this->_row[$col][$level] = s($msg);
        }
    }

    public function close() {
        echo '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts column names to lowercase too.
 */
function validate_uploadcompletion_columns(&$columns) {
    global $stdfields;

    if (count($columns) < 3) {
        return get_string('csvfewcolumns', 'error');
    }
    // Test columns.
    $processed = array();
    foreach ($columns as $key => $unused) {
        $field = $columns[$key];
        if (!in_array($field, $stdfields)) {
            // If not a standard field and not an enrolment field, then we have an error!
            return get_string('invalidfieldname', 'error', $field);
        }
        if (in_array($field, $processed)) {
            return get_string('csvcolumnduplicates', 'error');
        }
        $processed[] = $field;
    }
    if (!(in_array('username', $processed) || in_array('userid', $processed))) {
        return get_string('missingusername', 'local_iomad_track');
    }
    if (!(in_array('coursename', $processed) || in_array('courseid', $processed) || in_array('coursecode', $processed))) {
        return get_string('missingcoursename', 'local_iomad_track');
    }
    if (!in_array('timecompleted', $processed)) {
        return get_string('missingtimecompleted', 'local_iomad_track');
    }

    return true;
}


