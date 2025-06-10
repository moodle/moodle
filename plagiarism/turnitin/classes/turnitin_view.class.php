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

use Integrations\PhpSdk\TiiLTI;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');

class turnitin_view {

    /**
     * Abstracted version of print_header() / header()
     *
     * @param string $url The URL of the page
     * @param string $title Appears at the top of the window
     * @param string $heading Appears at the top of the page
     * @param bool $return If true, return the visible elements of the header instead of echoing them.
     * @return mixed If return=true then string else void
     */
    public function output_header($url, $title = '', $heading = '', $return = false) {
        global $PAGE, $OUTPUT;

        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);

        if ($return) {
            return $OUTPUT->header();
        } else {
            echo $OUTPUT->header();
        }
    }

    /**
     * Prints the tab menu for the plugin settings
     *
     * @param string $currenttab The currect tab to be styled as selected
     */
    public function draw_settings_tab_menu($currenttab, $notice = null) {
        global $OUTPUT;

        $tabs = array();
        $tabs[] = new tabobject('turnitinsettings', 'settings.php',
                        get_string('config', 'plagiarism_turnitin'), get_string('config', 'plagiarism_turnitin'), false);
        $tabs[] = new tabobject('turnitindefaults', 'settings.php?do=defaults',
                        get_string('defaults', 'plagiarism_turnitin'), get_string('defaults', 'plagiarism_turnitin'), false);
        $tabs[] = new tabobject('dbexport', new moodle_url('/plagiarism/turnitin/dbexport.php'), get_string('dbexport', 'plagiarism_turnitin'));
        $tabs[] = new tabobject('apilog', 'settings.php?do=apilog',
                        get_string('logs'), get_string('logs'), false);
        $tabs[] = new tabobject('unlinkusers', 'settings.php?do=unlinkusers',
            get_string('unlinkusers', 'plagiarism_turnitin'), get_string('unlinkusers', 'plagiarism_turnitin'), false);
        $tabs[] = new tabobject('turnitinerrors', 'settings.php?do=errors',
                        get_string('errors', 'plagiarism_turnitin'), get_string('errors', 'plagiarism_turnitin'), false);
        print_tabs(array($tabs), $currenttab);

        if (!is_null($notice)) {
            echo $OUTPUT->box($notice["message"], 'generalbox boxaligncenter', $notice["type"]);
        }
    }

    /**
     * Due to moodle's internal plugin hooks we can not use our bespoke form class for Turnitin
     * settings. This form shows in settings > defaults as well as the activity creation screen.
     *
     * @global type $CFG
     * @param type $plugin_defaults
     * @return type
     */
    public function add_elements_to_settings_form($mform, $course, $location = "activity", $modulename = "", $cmid = 0, $currentrubric = 0) {
        global $PAGE, $USER, $DB, $CFG;

        // Include JS strings
        $PAGE->requires->string_for_js('changerubricwarning', 'plagiarism_turnitin');
        $PAGE->requires->string_for_js('closebutton', 'plagiarism_turnitin');

        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $configwarning = '';
        $rubrics = array();

        if ($location == "activity" && $modulename != 'mod_forum') {
            $instructor = new turnitin_user($USER->id, 'Instructor');

            $instructor->join_user_to_class($course->turnitin_cid);

            $rubrics = array(get_string('attachrubric', 'plagiarism_turnitin') => $instructor->get_instructor_rubrics());

            // Get rubrics that are shared on the account.
            $turnitinclass = new turnitin_class($course->id);
            $turnitinclass->sharedrubrics = array();
            $turnitinclass->read_class_from_tii();

            // This will ensure all rubric keys are integers.
            $rubricsnew = array(0 => get_string('norubric', 'plagiarism_turnitin'));
            foreach ($rubrics as $options => $rubriclist) {
                foreach ($rubriclist as $key => $value) {
                    $rubricsnew[$key] = $value;
                }
            }
            $rubricsnew = array($options => $rubricsnew);

            // Merge the arrays, prioritising instructor owned arrays.
            $rubrics = array_merge($rubricsnew, $turnitinclass->sharedrubrics);
        }

        $options = array(0 => get_string('no'), 1 => get_string('yes'));
        $plagiarismturnitin = new plagiarism_plugin_turnitin();
        $genparams = $plagiarismturnitin->plagiarism_get_report_gen_speed_params();
        $genoptions = array(0 => get_string('reportgen_immediate_add_immediate', 'plagiarism_turnitin'),
                            1 => get_string('reportgen_immediate_add_duedate', 'plagiarism_turnitin'),
                            2 => get_string('reportgen_duedate_add_duedate', 'plagiarism_turnitin'));
        $excludetypeoptions = array( 0 => get_string('no'), 1 => get_string('excludewords', 'plagiarism_turnitin'),
                            2 => get_string('excludepercent', 'plagiarism_turnitin'));

        if ($location == "defaults") {
            $mform->addElement('header', 'turnitin_plugin_header', get_string('turnitindefaults', 'plagiarism_turnitin'));
            $mform->addElement('html', get_string("defaultsdesc", "plagiarism_turnitin"));
        }

        if ($location != "defaults") {
            $mform->addElement('header', 'turnitin_plugin_header', get_string('turnitinpluginsettings', 'plagiarism_turnitin'));

            // Add in custom Javascript and CSS.
            $PAGE->requires->jquery_plugin('ui');
            $PAGE->requires->js_call_amd('plagiarism_turnitin/refresh_submissions', 'refreshSubmissions');
            if ($CFG->version >= 2023100900) {
                $PAGE->requires->js_call_amd('plagiarism_turnitin/new_peermark', 'newPeermarkLaunch');
                $PAGE->requires->js_call_amd('plagiarism_turnitin/new_quickmark', 'newQuickmarkLaunch');
                $PAGE->requires->js_call_amd('plagiarism_turnitin/new_rubric', 'newRubric');
            } else {
                // TODO: We can remove these when we no longer have to support Moodle versions 4.3 and below
                $PAGE->requires->js_call_amd('plagiarism_turnitin/peermark', 'peermarkLaunch');
                $PAGE->requires->js_call_amd('plagiarism_turnitin/quickmark', 'quickmarkLaunch');
                $PAGE->requires->js_call_amd('plagiarism_turnitin/rubric', 'rubric');
            }
            // Refresh Grades.
            $refreshgrades = '';
            if ($cmid != 0) {
                // If assignment has submissions then show a refresh grades button.
                $numsubs = $DB->count_records('plagiarism_turnitin_files', array('cm' => $cmid));
                if ($numsubs > 0) {
                    $refreshgrades = html_writer::tag(
                        'div',
                        html_writer::tag(
                            'span',
                            get_string('turnitinrefreshsubmissions', 'plagiarism_turnitin')
                        ),
                        array(
                            'class' => 'plagiarism_turnitin_refresh_grades',
                            'tabindex' => 0,
                            'role' => 'link'
                        )
                    );

                    $refreshgrades .= html_writer::tag('div', html_writer::tag('span', get_string('turnitinrefreshingsubmissions', 'plagiarism_turnitin')),
                                                                    array('class' => 'plagiarism_turnitin_refreshing_grades'));
                }
            }

            // Quickmark Manager.
            $quickmarkmanagerlink = '';
            if ($config->plagiarism_turnitin_usegrademark) {

                $quickmarkmanagerlink .= html_writer::tag(
                    'a',
                    get_string('launchquickmarkmanager', 'plagiarism_turnitin'),
                    array(
                        'href' => '#',
                        'class' => 'plagiarism_turnitin_quickmark_manager_launch',
                        'id' => 'quickmark_manager_form',
                        'tabindex' => 0
                    )
                );

                $quickmarkmanagerlink = html_writer::tag('div', $quickmarkmanagerlink, array('class' => 'row_quickmark_manager'));
            }

            $useturnitin = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cmid, 'name' => 'use_turnitin'));

            // Peermark Manager.
            $peermarkmanagerlink = '';
            if (!empty($config->plagiarism_turnitin_enablepeermark) && !empty($useturnitin->value)) {
                if ($cmid != 0) {
                    $peermarkmanagerlink .= html_writer::tag(
                        'a',
                        get_string('launchpeermarkmanager', 'plagiarism_turnitin'),
                        array(
                            'href' => '#',
                            'class' => 'peermark_manager_launch',
                            'id' => 'peermark_manager_form',
                            'tabindex' => 0
                        )
                    );
                    $peermarkmanagerlink = html_writer::tag('div', $peermarkmanagerlink, array('class' => 'row_peermark_manager'));
                }
            }

            if (!empty($quickmarkmanagerlink) || !empty($peermarkmanagerlink) || !empty($refreshgrades)) {
                $mform->addElement('static', 'static', '', $refreshgrades.$quickmarkmanagerlink.$peermarkmanagerlink);
            }
        }

        $locks = $DB->get_records_sql("SELECT name, value FROM {plagiarism_turnitin_config} WHERE cm IS NULL");

        if (empty($configwarning)) {
            $mform->addElement('select', 'use_turnitin', get_string("useturnitin", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);

            $mform->addElement('select', 'plagiarism_show_student_report', get_string("studentreports", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_show_student_report', 'studentreports', 'plagiarism_turnitin');

            if ($mform->elementExists('submissiondrafts') || $location == 'defaults') {
                $tiidraftoptions = array(0 => get_string("submitondraft", "plagiarism_turnitin"),
                                         1 => get_string("submitonfinal", "plagiarism_turnitin"));

                $mform->addElement('select', 'plagiarism_draft_submit', get_string("draftsubmit", "plagiarism_turnitin"), $tiidraftoptions);
                $this->lock($mform, $location, $locks);
                $mform->disabledIf('plagiarism_draft_submit', 'submissiondrafts', 'eq', 0);
            }

            $mform->addElement('select', 'plagiarism_allow_non_or_submissions', get_string("allownonor", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_allow_non_or_submissions', 'allownonor', 'plagiarism_turnitin');

            $suboptions = array(0 => get_string('norepository', 'plagiarism_turnitin'),
                                1 => get_string('standardrepository', 'plagiarism_turnitin'));
            switch ($config->plagiarism_turnitin_repositoryoption) {
                case 0; // Standard options.
                    $mform->addElement('select', 'plagiarism_submitpapersto', get_string('submitpapersto', 'plagiarism_turnitin'), $suboptions);
                    $mform->addHelpButton('plagiarism_submitpapersto', 'submitpapersto', 'plagiarism_turnitin');
                    $this->lock($mform, $location, $locks);
                    break;
                case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED; // Standard options + Allow Instituional Repository.
                    $suboptions[PLAGIARISM_TURNITIN_SUBMIT_TO_INSTITUTIONAL_REPOSITORY] = get_string('institutionalrepository', 'plagiarism_turnitin');

                    $mform->addElement('select', 'plagiarism_submitpapersto', get_string('submitpapersto', 'plagiarism_turnitin'), $suboptions);
                    $mform->addHelpButton('plagiarism_submitpapersto', 'submitpapersto', 'plagiarism_turnitin');
                    $this->lock($mform, $location, $locks);
                    break;
                case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_STANDARD; // Force Standard Repository.
                    $mform->addElement('hidden', 'plagiarism_submitpapersto', PLAGIARISM_TURNITIN_SUBMIT_TO_STANDARD_REPOSITORY);
                    $mform->setType('plagiarism_submitpapersto', PARAM_RAW);
                    break;
                case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_NO; // Force No Repository.
                    $mform->addElement('hidden', 'plagiarism_submitpapersto', PLAGIARISM_TURNITIN_SUBMIT_TO_NO_REPOSITORY);
                    $mform->setType('plagiarism_submitpapersto', PARAM_RAW);
                    break;
                case PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL; // Force Institutional Repository.
                    $mform->addElement('hidden', 'plagiarism_submitpapersto', PLAGIARISM_TURNITIN_SUBMIT_TO_INSTITUTIONAL_REPOSITORY);
                    $mform->setType('plagiarism_submitpapersto', PARAM_RAW);
                    break;
            }

            $mform->addElement('html', html_writer::tag('div', get_string('checkagainstnote', 'plagiarism_turnitin'),
                                                                                array('class' => 'tii_checkagainstnote')));

            $mform->addElement('select', 'plagiarism_compare_student_papers', get_string("spapercheck", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_compare_student_papers', 'spapercheck', 'plagiarism_turnitin');

            $mform->addElement('select', 'plagiarism_compare_internet', get_string("internetcheck", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_compare_internet', 'internetcheck', 'plagiarism_turnitin');

            $mform->addElement('select', 'plagiarism_compare_journals', get_string("journalcheck", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_compare_journals', 'journalcheck', 'plagiarism_turnitin');

            if ($config->plagiarism_turnitin_repositoryoption == PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED ||
                $config->plagiarism_turnitin_repositoryoption == PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL) {
                $mform->addElement('select', 'plagiarism_compare_institution',
                                                get_string('compareinstitution', 'plagiarism_turnitin'), $options);
                $this->lock($mform, $location, $locks);
            }

            $mform->addElement('select', 'plagiarism_report_gen', get_string("reportgenspeed", "plagiarism_turnitin"), $genoptions);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_report_gen', 'reportgenspeed', 'plagiarism_turnitin');

            $mform->addElement('select', 'plagiarism_exclude_biblio', get_string("excludebiblio", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_exclude_biblio', 'excludebiblio', 'plagiarism_turnitin');

            $mform->addElement('select', 'plagiarism_exclude_quoted', get_string("excludequoted", "plagiarism_turnitin"), $options);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_exclude_quoted', 'excludequoted', 'plagiarism_turnitin');

            $mform->addElement('select', 'plagiarism_exclude_matches', get_string("excludevalue", "plagiarism_turnitin"),
                                                                                $excludetypeoptions);
            $this->lock($mform, $location, $locks);
            $mform->addHelpButton('plagiarism_exclude_matches', 'excludevalue', 'plagiarism_turnitin');

            $mform->addElement('text', 'plagiarism_exclude_matches_value', get_string("excludesmallmatchesvalue", "plagiarism_turnitin"));
            $mform->setType('plagiarism_exclude_matches_value', PARAM_INT);
            $mform->addRule('plagiarism_exclude_matches_value', null, 'numeric', null, 'client');
            $mform->disabledIf('plagiarism_exclude_matches_value', 'plagiarism_exclude_matches', 'eq', 0);

            if ($location == 'defaults') {
                $mform->addElement('text', 'plagiarism_locked_message', get_string("locked_message", "plagiarism_turnitin"), 'maxlength="50" size="50"' );
                $mform->setType('plagiarism_locked_message', PARAM_TEXT);
                $mform->setDefault('plagiarism_locked_message', get_string("locked_message_default", "plagiarism_turnitin") );
                $mform->addHelpButton('plagiarism_locked_message', 'locked_message', 'plagiarism_turnitin');
            }

            if ($location == "activity" && $modulename != "mod_forum" && $config->plagiarism_turnitin_usegrademark) {
                if (!empty($currentrubric)) {
                    $attachrubricstring = get_string('attachrubric', 'plagiarism_turnitin');
                    if (!isset($rubrics[$attachrubricstring][$currentrubric])) {
                        $rubrics[$attachrubricstring][$currentrubric] = get_string('otherrubric', 'plagiarism_turnitin');
                    }
                }

                $rubricmanagerlink = html_writer::tag(
                    'span',
                    get_string('launchrubricmanager', 'plagiarism_turnitin'),
                    array(
                        'class' => 'rubric_manager_launch',
                        'data-courseid' => $course->id,
                        'data-cmid' => $cmid,
                        'title' => get_string('launchrubricmanager', 'plagiarism_turnitin'),
                        'id' => 'rubric_manager_form',
                        'role' => 'link',
                        'tabindex' => '0'
                    )
                );

                $rubricmanagerlink = html_writer::tag('div', $rubricmanagerlink, array('class' => 'row_rubric_manager'));
                $mform->addElement('selectgroups', 'plagiarism_rubric', get_string('attachrubric', 'plagiarism_turnitin'), $rubrics);
                $mform->addElement('static', 'rubric_link', '', $rubricmanagerlink);
                $mform->setDefault('plagiarism_rubric', '');

                $mform->addElement('hidden', 'rubric_warning_seen', '');
                $mform->setType('rubric_warning_seen', PARAM_RAW);

                $mform->addElement('static', 'rubric_note', '', get_string('attachrubricnote', 'plagiarism_turnitin'));
            } else {
                $mform->addElement('hidden', 'plagiarism_rubric', '');
                $mform->setType('plagiarism_rubric', PARAM_RAW);
            }

            $mform->addElement('html', html_writer::tag('div', get_string('anonblindmarkingnote', 'plagiarism_turnitin'),
                                                                                array('class' => 'tii_anonblindmarkingnote')));

            if ($config->plagiarism_turnitin_transmatch) {
                $mform->addElement('select', 'plagiarism_transmatch', get_string("transmatch", "plagiarism_turnitin"), $options);
            } else {
                $mform->addElement('hidden', 'plagiarism_transmatch', 0);
            }
            $mform->setType('plagiarism_transmatch', PARAM_INT);

            $mform->addElement('hidden', 'action', "defaults");
            $mform->setType('action', PARAM_RAW);
        } else {
            $mform->addElement('hidden', 'use_turnitin', 0);
            $mform->setType('use_turnitin', PARAM_INT);
        }

        // Disable the form change checker - added in 2.3.2.
        if (is_callable(array($mform, 'disable_form_change_checker'))) {
            $mform->disable_form_change_checker();
        }
    }

    public function show_file_errors_table($page = 0) {
        global $CFG, $OUTPUT;

        $limit = 100;
        $offset = $page * $limit;

        $plagiarismpluginturnitin = new plagiarism_plugin_turnitin();
        $filescount = $plagiarismpluginturnitin->get_file_upload_errors(0, 0, true);
        $files = $plagiarismpluginturnitin->get_file_upload_errors($offset, $limit);

        $baseurl = new moodle_url('/plagiarism/turnitin/settings.php', array('do' => 'errors'));
        $pagingbar = $OUTPUT->paging_bar($filescount, $page, $limit, $baseurl);

        // Do the table headers.
        $cells = array();
        $selectall = html_writer::checkbox('errors_select_all', false, false, '', array("class" => "select_all_checkbox"));
        $cells["checkbox"] = new html_table_cell($selectall);
        $cells["id"] = new html_table_cell(get_string('id', 'plagiarism_turnitin'));
        $cells["user"] = new html_table_cell(get_string('student', 'plagiarism_turnitin'));
        $cells["user"]->attributes['class'] = 'left';
        $cells["course"] = new html_table_cell(get_string('course', 'plagiarism_turnitin'));
        $cells["module"] = new html_table_cell(get_string('module', 'plagiarism_turnitin'));
        $cells["file"] = new html_table_cell(get_string('file'));
        $cells["error"] = new html_table_cell(get_string('error'));
        $cells["delete"] = new html_table_cell('&nbsp;');
        $cells["delete"]->attributes['class'] = 'centered_cell';

        $table = new html_table();
        $table->head = $cells;

        $i = 0;
        $rows = array();

        if (count($files) == 0) {
            $cells = array();
            $cells["checkbox"] = new html_table_cell(get_string('semptytable', 'plagiarism_turnitin'));
            $cells["checkbox"]->colspan = 8;
            $cells["checkbox"]->attributes['class'] = 'centered_cell';
            $rows[0] = new html_table_row($cells);
        } else {
            foreach ($files as $k => $v) {
                $cells = array();
                if (!empty($v->moduletype) && $v->moduletype != "forum") {

                    $cm = get_coursemodule_from_id($v->moduletype, $v->cm);

                    $checkbox = html_writer::checkbox('check_'.$k, $k, false, '', array("class" => "errors_checkbox"));
                    $cells["checkbox"] = new html_table_cell($checkbox);

                    $cells["id"] = new html_table_cell($k);
                    $cells["user"] = new html_table_cell($v->firstname." ".$v->lastname." (".$v->email.")");

                    $courselink = new moodle_url($CFG->wwwroot.'/course/view.php', array('id' => $v->courseid));
                    $cells["course"] = new html_table_cell(html_writer::link($courselink,
                                                                $v->coursename, array('title' => $v->coursename)));

                    $modulelink = new moodle_url($CFG->wwwroot.'/mod/'.$v->moduletype.'/view.php', array('id' => $v->cm));
                    $cells["module"] = new html_table_cell(html_writer::link($modulelink, $cm->name, array('title' => $cm->name)));

                    if ($v->submissiontype == "file") {
                        $fs = get_file_storage();
                        if ($file = $fs->get_file_by_hash($v->identifier)) {
                            $cells["file"] = new html_table_cell(html_writer::link($CFG->wwwroot.'/pluginfile.php/'.
                                                    $file->get_contextid().'/'.$file->get_component().'/'.$file->get_filearea().'/'.
                                                    $file->get_itemid().'/'.$file->get_filename(),
                                                    $OUTPUT->pix_icon('fileicon', 'open '.$file->get_filename(), 'plagiarism_turnitin').
                                                        " ".$file->get_filename()));
                        } else {
                            $cells["file"] = get_string('filedoesnotexist', 'plagiarism_turnitin');
                        }
                    } else {
                        $cells["file"] = str_replace('_', ' ', ucfirst($v->submissiontype));
                    }

                    $errorcode = $v->errorcode;
                    // Deal with legacy error issues.
                    if (is_null($errorcode)) {
                        $errorcode = 0;
                        if ($v->submissiontype == 'file') {
                            if (is_object($file) && $file->get_filesize() > PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE) {
                                $errorcode = 2;
                            }
                        }
                    }

                    // Show error message if there is one.
                    $errormsg = $v->errormsg;
                    if ($errorcode == 0) {
                        $errorstring = (is_null($errormsg)) ? get_string('ppsubmissionerrorseelogs', 'plagiarism_turnitin') : $errormsg;
                    } else {
                        $errorstring = get_string('errorcode'.$v->errorcode,
                                            'plagiarism_turnitin', display_size(PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE));
                    }
                    $cells["error"] = $errorstring;

                    $fnd = array("\n", "\r");
                    $rep = array('\n', '\r');
                    $string = str_replace($fnd, $rep, get_string('deleteconfirm', 'plagiarism_turnitin'));

                    $attributes["onclick"] = "return confirm('".$string."');";
                    $cells["delete"] = new html_table_cell(html_writer::link($CFG->wwwroot.
                                            '/plagiarism/turnitin/settings.php?do=errors&action=deletefile&id='.$k,
                                            $OUTPUT->pix_icon('delete', get_string('deletesubmission', 'plagiarism_turnitin'),
                                                'plagiarism_turnitin'), $attributes));
                    $cells["delete"]->attributes['class'] = 'centered_cell';

                    $rows[$i] = new html_table_row($cells);
                    $i++;
                }
            }

            if ($i == 0) {
                $cells = array();
                $cells["checkbox"] = new html_table_cell(get_string('semptytable', 'plagiarism_turnitin'));
                $cells["checkbox"]->colspan = 8;
                $cells["checkbox"]->attributes['class'] = 'centered_cell';
                $rows[0] = new html_table_row($cells);
            } else {
                $table->id = "ppErrors";
            }
        }
        $table->data = $rows;
        $output = html_writer::table($table);

        return $pagingbar.$output.$pagingbar;
    }

    /**
     * This adds a site lock check to the most recently added field
     */
    public function lock($mform, $location, $locks) {

        $field = end($mform->_elements)->_attributes['name'];
        if ($location == 'defaults') {
            // If we are on the site config level, show the lock UI.
            $mform->addElement('advcheckbox', $field . '_lock', '', get_string('locked', 'admin'), array('group' => 1) );

        } else {

            // If we are at the plugin level, and we are locked then freeze.
            $locked = (isset($locks[$field.'_lock']->value)) ? $locks[$field.'_lock']->value : 0;
            if ($locked) {
                $mform->freeze($field);
                // Show custom message why.
                $msg = $locks['plagiarism_locked_message']->value;
                if ($msg) {
                    $mform->addElement('static', $field . '_why', '', $msg );
                }
            }
        }
    }

    /**
     * Return the output for a form to launch LTI views, it is then submitted
     * on load via Javascript
     *
     * @param string $type the type of document viewer that needs to be opened
     * @param int $submissionid the Turnitin submission id
     * @param int $userid the Turnitin user id
     * @param string $userrole the role the user has on Turnitin in the course/class
     * @param string $buttonstring string for the submit button
     * @return string form
     */
    public static function output_launch_form($type, $submissionid, $userid, $userrole,
                                              $buttonstring = "Submit", $ltireturn = false) {
        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Construct LTI Form Launcher.
        $lti = new TiiLTI();
        if ($type != "useragreement") {
            $lti->setSubmissionId($submissionid);
        }
        $lti->setUserId($userid);
        $lti->setRole($userrole);
        $lti->setButtonText($buttonstring);
        $lti->setFormTarget('');

        switch ($type) {
            case "useragreement":
                $ltifunction = "outputUserAgreementForm";
                break;

            case "downloadoriginal":
                $ltifunction = "outputDownloadOriginalFileForm";
                break;

            case "default":
                $ltifunction = "outputDVDefaultForm";
                break;

            case "origreport":
                $ltifunction = "outputDVReportForm";
                break;

            case "grademark":
                $ltifunction = "outputDVGradeMarkForm";
                break;
        }

        if ($ltireturn == false) {
            // Read the LTI launch form in the output buffer.
            ob_start();
            $turnitincall->$ltifunction($lti, $ltireturn);
            $launchdv = ob_get_contents();
            ob_end_clean();

            return $launchdv;
        } else {
            $lti->setAsJson(true);
            return $turnitincall->$ltifunction($lti, $ltireturn);
        }
    }

    /**
     * Return the output for a form to launch the relevant LTi function
     * It is then submitted on load via Javascript
     *
     * @param string $userrole either Instructor or Learner
     * @param int $userid
     * @return string form
     */
    public static function output_lti_form_launch($type, $userrole, $partid = 0, $classid = 0) {
        global $USER;
        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $user = new turnitin_user($USER->id, $userrole);

        $lti = new TiiLTI();
        $lti->setUserId($user->tiiuserid);
        $lti->setRole($userrole);
        $lti->setFormTarget('');
        $lti->setWideMode(true);

        switch ($type) {
            case "messages_inbox":
                $ltifunction = "outputMessagesForm";
                break;

            case "rubric_manager":
                if ($classid != 0) {
                    $lti->setClassId($classid);
                }
                $ltifunction = "outputRubricManagerForm";
                break;

            case "rubric_view":
                $lti->setAssignmentId($partid);
                $ltifunction = "outputRubricViewForm";
                break;

            case "quickmark_manager":
                $ltifunction = "outputQuickmarkManagerForm";
                break;

            case "peermark_manager":
                $lti->setAssignmentId($partid);
                $ltifunction = "outputPeerMarkSetupForm";
                break;

            case "peermark_reviews":
                $lti->setAssignmentId($partid);
                $ltifunction = "outputPeerMarkReviewForm";
                break;
        }

        ob_start();
        $turnitincall->$ltifunction($lti);
        $rubricform = ob_get_contents();
        ob_end_clean();

        return $rubricform;
    }
}
