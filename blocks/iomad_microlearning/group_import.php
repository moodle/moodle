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
 * @package   block_iomad_microlearning
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
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$submit = optional_param('submitbutton', '', PARAM_ALPHANUM);
$fileimport = optional_param('fileimport', 0, PARAM_BOOL);
$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$readcount   = optional_param('readcount', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:importgroupfromcsv', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

$linktext = get_string('importusergroups', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/group_import.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
// Set the page heading.
$url = new moodle_url('/blocks/iomad_microlearning/index.php');
$title = get_string('pluginname', 'block_iomad_microlearning');

$PAGE->set_heading($linktext);

// Deal with the link back to the main microlearning page.
$buttoncaption = get_string('threads', 'block_iomad_microlearning');
$buttonlink = new moodle_url('/blocks/iomad_microlearning/threads.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

$companyid = iomad::get_my_companyid($context);

// Array of all valid fields for validation.
$stdfields = array('username', 'email', 'thread', 'group');

if (!empty($fileimport)) {
    if (empty($iid)) {
        $mform = new block_iomad_microlearning\forms\user_group_import_form();
        if ($mform->is_cancelled()) {
            redirect($linkurl);
        }
        if ($importdata = $mform->get_data()) {
            // Verification moved to two places: after upload and into form2.
            $grouperrors  = 0;
            $erroredgroups = array();
            $errorstr = get_string('error');


            $iid = csv_import_reader::get_new_iid('uploadgroup');
            $cir = new csv_import_reader($iid, 'uploadgroup');

            $content = $mform->get_file_content('importfile');
            $readcount = $cir->load_csv_content($content,
                                                $importdata->encoding,
                                                $importdata->delimiter_name,
                                                'validate_uploadgroup_columns');

            if (!$columns = $cir->get_columns()) {
               print_error('cannotreadtmpfile', 'error', $returnurl);
            }

            unset($content);

            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('uploadgroupresult', 'block_iomad_microlearning'));

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
                $grouprec = (object) [];
                $upt->track('line', $linenum);
                foreach ($line as $key => $value) {
                    if ($columns[$key] == 'thread') {
                        if (!$threadrec = $DB->get_record('microlearning_thread', ['name' => $value, 'companyid' => $companyid])) {
                            $grouperrors++;
                            $errornum++;
                            $erroredgroups[] = $line;
                            continue;
                        } else {
                            $grouprec->threadid = $threadrec->id;
                            $grouprec->thread = $threadrec->name;
                        }
                    } else if ($columns[$key] == 'username') {
                        if (!$userrec = $DB->get_record('user', ['username' => $value])) {
                            $grouperrors++;
                            $errornum++;
                            $erroredgroups[] = $line;
                            continue;
                        } else {
                            $grouprec->userid = $userrec->id;
                            $grouprec->username = $userrec->username;
                            $grouprec->email = $userrec->email;
                        }
                    } else if ($columns[$key] == 'email') {
                        if (!$userrec = $DB->get_record('user', ['email' => $value])) {
                            $grouperrors++;
                            $errornum++;
                            $erroredgroups[] = $line;
                            continue;
                        } else {
                            $grouprec->userid = $userrec->id;
                            $grouprec->username = $userrec->username;
                            $grouprec->email = $userrec->email;
                        }
                    } else if ($columns[$key] == 'group') {
                        $threadkey = array_search('thread', $columns);
                        if ($threadrec = $DB->get_record('microlearning_thread', ['name' => $line[$threadkey], 'companyid' => $companyid])) {
                            if (!$groupinfo = $DB->get_record('microlearning_thread_group', ['name' => $value, 'threadid' => $threadrec->id, 'companyid' => $companyid])) {
                                $grouperrors++;
                                $errornum++;
                                $erroredgroups[] = $line;
                                continue;
                            } else {
                                $grouprec->groupid = $groupinfo->id;
                                $grouprec->groupname = $groupinfo->name;
                            }
                        } else {
                            $grouperrors++;
                            $errornum++;
                            $erroredgroups[] = $line;
                            continue;
                        }
                    } 
                }

                // Write the info to the db.
                $trackid = $linenum;
                $upt->track('id', $trackid);
                $upt->track('username', $grouprec->username);
                $upt->track('email', $grouprec->email);
                $upt->track('thread', $grouprec->thread);
                $upt->track('group', $grouprec->groupname);
                if ($DB->get_records('microlearning_thread_user', ['threadid' => $grouprec->threadid, 'userid' => $grouprec->userid])) {
                    $DB->set_field('microlearning_thread_user', 'groupid', $grouprec->groupid, ['threadid' => $grouprec->threadid, 'userid' => $grouprec->userid]);
                    $upt->track('status', get_string('ok'));
                } else {
                    $upt->track('status', get_string('failed'));
                }
            }

            $upt->flush();
            $upt->close(); // Close table.

            $cir->close();
            $cir->cleanup(true);

            // Deal with any erroring groups.
            if (!empty($erroredgroups)) {
                echo get_string('erroredgroups', 'block_iomad_microlearning');
                $erroredtable = new html_table();
                foreach ($erroredgroups as $erroredgroupr) {
                    $erroredtable->data[] = $erroredgroup;
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

echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/blocks/iomad_microlearning/group_import.php',
                                           array('fileimport' => true,
                                                 'sesskey' => sesskey())),
                                           get_string('importgroupsfromfile', 'block_iomad_microlearning'));
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
                            'email',
                            'thread',
                            'group');

    public function __construct() {
    }

    public function init() {
        $ci = 0;
        echo '<table id="uploadresults" class="generaltable boxaligncenter flexible-wrap" summary="'.
               get_string('uploadgroupresult', 'block_iomad_microlearning').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'tool_uploaduser').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('username').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('email').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('threadname', 'block_iomad_microlearning').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('group', 'block_iomad_microlearning').'</th>';
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
function validate_uploadgroup_columns(&$columns) {
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

    return true;
}
