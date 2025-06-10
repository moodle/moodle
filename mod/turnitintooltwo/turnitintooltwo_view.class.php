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

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__."/lib.php");
require_once(__DIR__.'/turnitintooltwo_form.class.php');
require_once(__DIR__.'/turnitintooltwo_submission.class.php');

class turnitintooltwo_view {

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
     * Load the Javascript and CSS components for page.
     *
     * @global type $PAGE
     */
    public function load_page_components($hidebg = false) {
        global $PAGE;

        // Include CSS.
        if ($hidebg) {
            $cssurl = new moodle_url('/mod/turnitintooltwo/css/hide_bg.css');
            $PAGE->requires->css($cssurl);
        }
        $cssurl = new moodle_url('/mod/turnitintooltwo/styles.css');
        $PAGE->requires->css($cssurl);
        $cssurl = new moodle_url('/mod/turnitintooltwo/css/jquery-ui-1.8.4.custom.css');
        $PAGE->requires->css($cssurl);
        $cssurl = new moodle_url('/mod/turnitintooltwo/css/font-awesome.min.css');
        $PAGE->requires->css($cssurl);
        $cssurl = new moodle_url('/mod/turnitintooltwo/css/tii-icon-webfont.css');
        $PAGE->requires->css($cssurl);

        // Include JS.
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('turnitintooltwo-dataTables', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-dataTables_plugins', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-turnitintooltwo', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-turnitintooltwo_extra', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-turnitintooltwo_settings', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-datatables_columnfilter', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-colorbox', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-cookie', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-uieditable', 'mod_turnitintooltwo');
        $PAGE->requires->jquery_plugin('turnitintooltwo-moment', 'mod_turnitintooltwo');

        // Javascript i18n strings.
        $PAGE->requires->string_for_js('close', 'turnitintooltwo');
        $PAGE->requires->string_for_js('nointegration', 'turnitintooltwo');
        $PAGE->requires->string_for_js('sprevious', 'turnitintooltwo');
        $PAGE->requires->string_for_js('snext', 'turnitintooltwo');
        $PAGE->requires->string_for_js('sprocessing', 'turnitintooltwo');
        $PAGE->requires->string_for_js('szerorecords', 'turnitintooltwo');
        $PAGE->requires->string_for_js('sinfo', 'turnitintooltwo');
        $PAGE->requires->string_for_js('ssearch', 'turnitintooltwo');
        $PAGE->requires->string_for_js('slengthmenu', 'turnitintooltwo');
        $PAGE->requires->string_for_js('semptytable', 'turnitintooltwo');
        $PAGE->requires->string_for_js('tiisubmissionsgeterror', 'turnitintooltwo');
        $PAGE->requires->string_for_js('membercheckerror', 'turnitintooltwo');
        $PAGE->requires->string_for_js('resubmissiongradewarn', 'turnitintooltwo');
        $PAGE->requires->string_for_js('submitnothingwarning', 'turnitintooltwo');
        $PAGE->requires->string_for_js('maxmarkserror', 'turnitintooltwo');
        $PAGE->requires->string_for_js('disableanonconfirm', 'turnitintooltwo');
        $PAGE->requires->string_for_js('closebutton', 'turnitintooltwo');
        $PAGE->requires->string_for_js('loadingdv', 'turnitintooltwo');
        $PAGE->requires->string_for_js('postdate_warning', 'turnitintooltwo');
        $PAGE->requires->string_for_js('deleteconfirm', 'turnitintooltwo');
        $PAGE->requires->string_for_js('turnitindeleteconfirm', 'turnitintooltwo');
        $PAGE->requires->string_for_js('max_marks_warning', 'turnitintooltwo');
        $PAGE->requires->string_for_js('download_button_warning', 'turnitintooltwo');
    }

    /**
     * Output the Menu in the settings area as an HTML list
     *
     * @global type $CFG
     * @global type $DB
     * @return output the menu as an HTML list
     */
    public function draw_settings_menu($cmd) {
        global $CFG, $DB;

        $tabs = array();

        $tabs[] = new tabobject('settings', $CFG->wwwroot.'/admin/settings.php?section=modsettingturnitintooltwo',
                        get_string('settings', 'turnitintooltwo'), get_string('settings', 'turnitintooltwo'), false);

        $tabs[] = new tabobject('viewreport', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=viewreport',
                        get_string('showusage', 'turnitintooltwo'), get_string('showusage', 'turnitintooltwo'), false);

        $tabs[] = new tabobject('savereport', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=savereport',
                        get_string('saveusage', 'turnitintooltwo'), get_string('saveusage', 'turnitintooltwo'), false);

        $tabs[] = new tabobject('apilog', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=apilog',
                        get_string('logs'), get_string('logs'), false);

        $tabs[] = new tabobject('unlinkusers', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=unlinkusers',
                        get_string('unlinkusers', 'turnitintooltwo'), get_string('unlinkusers', 'turnitintooltwo'), false);

        $tabs[] = new tabobject('files', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=files',
                        get_string('files', 'turnitintooltwo'), get_string('files', 'turnitintooltwo'), false);

        $tabs[] = new tabobject('courses', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=courses',
                        get_string('restorationheader', 'turnitintooltwo'), get_string('restorationheader', 'turnitintooltwo'), false);

        // Include Moodle v1 migration tab if v1 is installed.
        $module = $DB->get_record('config_plugins', array('plugin' => 'mod_turnitintool'));
        if ( $module ) {
            $tabs[] = new tabobject('v1migration', $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=v1migration',
                        get_string('v1migrationtitle', 'turnitintooltwo'), get_string('v1migrationtitle', 'turnitintooltwo'), false);
        }

        $selected = ($cmd == 'activitylog') ? 'apilog' : $cmd;

        // Read the LTI launch form in the output buffer and put in link to test Turnitin connection.
        ob_start();
        print_tabs(array($tabs), $selected);
        $settingstabs = ob_get_contents();
        ob_end_clean();

        return $settingstabs;
    }

    /**
     * Prints the tab link menu across the top of the activity module
     *
     * @param object $cm The moodle course module object for this instance
     * @param object $selected The query string parameter to determine the page we are on
     * @param array $notice
     */
    public function draw_tool_tab_menu($cm, $selected) {
        global $CFG;

        $tabs = array();
        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $tabs[] = new tabobject('submissions', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.'&do=submissions',
                    get_string('allsubmissions', 'turnitintooltwo'), get_string('allsubmissions', 'turnitintooltwo'), false);

            $tabs[] = new tabobject('tutors', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.'&do=tutors',
                    get_string('turnitintutors', 'turnitintooltwo'), get_string('turnitintutors', 'turnitintooltwo'), false);

            $tabs[] = new tabobject('students', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.'&do=students',
                    get_string('turnitinstudents', 'turnitintooltwo'), get_string('turnitinstudents', 'turnitintooltwo'), false);
        } else {
            $tabs[] = new tabobject('submissions', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.'&do=submissions',
                    get_string('mysubmissions', 'turnitintooltwo'), get_string('mysubmissions', 'turnitintooltwo'), false);
        }

        print_tabs(array($tabs), $selected);
    }

    /**
     * Configure html for a notice to be shown at the top of the screen if required
     *
     * @param type $notice
     * @return mixed html containing notice
     */
    public function show_notice($notice) {
        global $OUTPUT;

        return $OUTPUT->box($notice["message"], 'alert alert-'.$notice["type"], "alert");
    }

    public function show_digital_receipt($digitalreceipt) {
        global $OUTPUT;

        $receipt = html_writer::tag('p', get_string('submissionuploadsuccess', 'turnitintooltwo'),
                                        array('class' => 'bold', 'id' => 'mod_turnitintooltwo_upload_success'));

        $receipt .= html_writer::tag('h2', get_string('digitalreceipt', 'turnitintooltwo'),
                                        array("id" => "digital_receipt"));
        $receipt .= html_writer::tag('p', html_writer::tag('span',
                                            get_string('turnitinsubmissionid', 'turnitintooltwo').":",
                                                            array('class' => 'bold'))." ".
                                            html_writer::tag('span', $digitalreceipt["tii_submission_id"],
                                                            array('class' => 'tii_submission_id')));
        $receipt .= html_writer::tag('p', get_string('submissionextract', 'turnitintooltwo').":", array('class' => 'bold'));
        $receipt .= html_writer::tag('span', html_writer::tag('p', $digitalreceipt["extract"]), array('class' => 'extract_text'));

        $icon = $OUTPUT->box($OUTPUT->pix_icon('icon', get_string('turnitin', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo'), 'centered_div');
        $output = $OUTPUT->box($icon.$receipt, 'generalbox', 'digital_receipt');

        return $output;
    }

    /**
     * Warning display to indicate duplicated assignments, normally as a result of a backup and restore
     *
     * @param object $cm The moodle course module object for this instance
     * @param object $turnitintooltwo The turnitin assignment data object
     * @return mixed Returns HTML duplication warning if the logged in users has grade rights otherwise null
     */
    public function show_duplicate_assignment_warning($turnitintooltwoassignment, $parts) {
        global $CFG, $OUTPUT;

        $dups = array();
        $output = '';
        foreach ($parts as $part) {
            $dupparts = $turnitintooltwoassignment->get_duplicate_parts($part->tiiassignid, $part->turnitintooltwoid);
            $dups = array_merge($dups, $dupparts);
        }
        if (count($dups) > 0) {
            $output .= $OUTPUT->box_start('generalbox boxaligncenter notepost', 'warning');
            $output .= html_writer::tag("h3", get_string('notice'), array("class" => "error"));
            $output .= html_writer::tag("p", get_string('duplicatesfound', 'turnitintooltwo'));

            $listcourses = array();
            foreach ($dups as $duppart) {
                $listcourses[] = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$duppart->cm_id,
                                    $duppart->course_name.' (' . $duppart->course_shortname . ') - '.
                                        $duppart->tool_name.' - ' . $duppart->partname);
            }
            $output .= html_writer::alist($listcourses);
            $output .= $OUTPUT->box_end();
        }
        return $output;
    }

    /**
     * Outputs the HTML for the submission form
     *
     * @global object $CFG
     * @global object $OUTPUT
     * @param object $cm The moodle course module object for this instance
     * @param object $turnitintooltwoassignment The turnitintooltwo object for this activity
     * @param int $partid The part id being submitted to
     * @param int $userid The user id who the submission is for
     * @param array $turnitintooltwofileuploadoptions upload options for the file manager
     * @return string returns the HTML of the form
     */
    public function show_submission_form($cm, $turnitintooltwoassignment, $partid, $turnitintooltwofileuploadoptions,
                                $viewcontext = "box", $userid = 0) {
        global $CFG, $OUTPUT, $USER;

        $output = "";
        $config = turnitintooltwo_admin_config();
        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        // Check if the submitting user has accepted the EULA.
        $eulaaccepted = false;
        if ($userid == $USER->id) {
            $user = new turnitintooltwo_user($userid, "Learner");
            $coursetype = turnitintooltwo_get_course_type($turnitintooltwoassignment->turnitintooltwo->legacy);
            $coursedata = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
            $user->join_user_to_class($coursedata->turnitin_cid);
            $eulaaccepted = ($user->useragreementaccepted != 1) ? $user->get_accepted_user_agreement() : $user->useragreementaccepted;
        }

        $parts = $turnitintooltwoassignment->get_parts_available_to_submit(0, $istutor);
        if (!empty($parts)) {

            $elements = array();
            $elements[] = array('header', 'submitpaper', get_string('submitpaper', 'turnitintooltwo'));

            $elements[] = array('hidden', 'submissionassignment', $turnitintooltwoassignment->turnitintooltwo->id);
            $elements[] = array('hidden', 'action', 'submission');

            // Get any previous submission to determine if this is a resubmission.
            $prevsubmission = $turnitintooltwoassignment->get_user_submissions($userid, $turnitintooltwoassignment->turnitintooltwo->id, $partid);

            if ($istutor || $eulaaccepted == 1) {

                if ($prevsubmission && ($istutor || $turnitintooltwoassignment->turnitintooltwo->studentreports)) {
                    $genparams = turnitintooltwo_get_report_gen_speed_params();
                    $elements[] = array('html', '<div class="tii_checkagainstnote">' . get_string('reportgenspeed_resubmission', 'turnitintooltwo', $genparams) . '</div>');
                }

                // Upload type.
                switch ($turnitintooltwoassignment->turnitintooltwo->type) {
                    case 0:
                        $options = $this->get_filetypes(false);
                        $elements[] = array('select', 'submissiontype', get_string('submissiontype', 'turnitintooltwo'),
                                                                                                'submissiontype', $options);
                        break;
                    case 1:
                    case 2:
                        $elements[] = array('hidden', 'submissiontype', $turnitintooltwoassignment->turnitintooltwo->type);
                        break;
                }

                // User id if applicable.
                if ($istutor) {
                    $elements[] = array('hidden', 'studentsname', $userid);
                }

                // Submission Title.
                $elements[] = array('text', 'submissiontitle', get_string('submissiontitle', 'turnitintooltwo'), 'submissiontitle', '',
                                    'required', get_string('submissiontitleerror', 'turnitintooltwo'), PARAM_TEXT);

                // Submission Part(s).
                if ($partid == 0) {
                    $options = array();
                    foreach ($parts as $part) {
                        $options[$part->id] = $part->partname;
                    }
                    $elements[] = array('select', 'submissionpart', get_string('submissionpart', 'turnitintooltwo'),
                                                                                            'submissionpart', $options);
                } else {
                    $elements[] = array('hidden', 'submissionpart', $partid);
                }

                // File input for uploads.
                if ($turnitintooltwoassignment->turnitintooltwo->type == 0 || $turnitintooltwoassignment->turnitintooltwo->type == 1) {
                    $elements[] = array('filemanager', 'submissionfile', get_string('filetosubmit', 'turnitintooltwo'),
                                                                        'filetosubmit', $turnitintooltwofileuploadoptions);
                }

                // Textarea.
                if ($turnitintooltwoassignment->turnitintooltwo->type == 0) {
                    $elements[] = array('textarea', 'submissiontext', get_string('texttosubmit', 'turnitintooltwo'), 'texttosubmit');
                } else if ($turnitintooltwoassignment->turnitintooltwo->type == 2) {
                    $elements[] = array('textarea', 'submissiontext', get_string('texttosubmit', 'turnitintooltwo'),
                                    'texttosubmit', '', 'required', get_string('submissiontexterror', 'turnitintooltwo'),
                                        PARAM_TEXT);
                }

                // Show agreement if applicable.
                if ($istutor || empty($config->agreement)) {
                    $elements[] = array('hidden', 'submissionagreement', 1);
                    $customdata["checkbox_label_after"] = false;
                } else {
                    $elements[] = array('advcheckbox', 'submissionagreement', $config->agreement, '', array(0, 1),
                                    'required', get_string('copyrightagreementerror', 'turnitintooltwo'), PARAM_INT);
                    $customdata["checkbox_label_after"] = true;
                }
            }

            // Output a link for the student to accept the turnitin licence agreement.
            $noscripteula = "";
            $eula = "";
            if ($userid == $USER->id) {
                if ($eulaaccepted != 1) {
                    $eula = html_writer::tag('p', get_string('turnitinula', 'turnitintooltwo'), array('class' => 'mod_turnitintooltwo_eula_text'));
                    $eula .= html_writer::tag('div', self::output_dv_launch_form("useragreement", 0, $user->tiiuserid,
                                "Learner", get_string('turnitinula_btn', 'turnitintooltwo'), false),
                                    array('class' => 'mod_turnitintooltwo_eula', 'data-userid' => $userid));

                    $noscripteula = html_writer::tag('noscript',
                                            $this->output_dv_launch_form("useragreement", 0, $user->tiiuserid, "Learner",
                                            get_string('turnitinula', 'turnitintooltwo'), false)." ".
                                                get_string('noscriptula', 'turnitintooltwo'),
                                            array('class' => 'warning mod_turnitintooltwo_eula_noscript'));
                }
            }

            $customdata["elements"] = $elements;
            $customdata["show_cancel"] = false;

            // Determine which label to show based on whether this is a resubmission.
            $submitstr = (count($prevsubmission) == 0) ? 'addsubmission' : 'resubmission';
            $customdata["submit_label"] = get_string($submitstr, 'turnitintooltwo');
            $customdata["disable_form_change_checker"] = true;

            $optionsform = new turnitintooltwo_form($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                    '&do=submitpaper&view_context='.$viewcontext, $customdata);
            $output .= $eula.$noscripteula;
            $output .= $OUTPUT->box($optionsform->display(), "submission_form_container");

            $turnitincomms = new turnitintooltwo_comms();
            $turnitincall = $turnitincomms->initialise_api();

            $customdata = array("disable_form_change_checker" => true,
                                "elements" => array(array('html', $OUTPUT->box('', '', 'useragreement_inputs'))));
            $eulaform = new turnitintooltwo_form($turnitincall->getApiBaseUrl().TiiLTI::EULAENDPOINT, $customdata,
                                                    'POST', 'eulaWindow', array('id' => 'eula_launch'));
            $output .= $OUTPUT->box($eulaform->display(), '', 'useragreement_form');
        }

        return $output;
    }

    /**
     * Outputs the file type array for acceptable file type uploads
     *
     * @param boolean $setup True if the call is from the assignment activity setup screen
     * @param array The array of filetypes ready for the modform parameter
     */
    public function get_filetypes($setup = true) {
        $output = array(
            1 => get_string('fileupload', 'turnitintooltwo'),
            2 => get_string('textsubmission', 'turnitintooltwo')
        );
        if ($setup) {
            $output[0] = get_string('anytype', 'turnitintooltwo');
        }
        ksort($output);
        return $output;
    }

    /**
     * Output the table structures with headings for the Submission inbox, they will be populated via Ajax
     *
     * @global type $CFG
     * @global type $OUTPUT
     * @param type $cm
     * @param type $turnitintooltwoassignment
     * @return type
     */
    public function init_submission_inbox($cm, $turnitintooltwoassignment, $partdetails, $turnitintooltwouser) {
        global $CFG, $OUTPUT;
        $config = turnitintooltwo_admin_config();

        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        // Output user role to hidden var for use in jQuery calls.
        $output = $OUTPUT->box($turnitintooltwouser->get_user_role(), '', 'user_role');
        $output .= $OUTPUT->box($turnitintooltwoassignment->turnitintooltwo->id, '', 'assignment_id');

        if ($turnitintooltwouser->get_user_role() == 'Learner') {
            $output .= html_writer::tag('noscript', get_string('noscriptsummary', 'turnitintooltwo'), array("class" => "warning"));
        }

        $origreportenabled = ($turnitintooltwoassignment->turnitintooltwo->studentreports) ? 1 : 0;
        $grademarkenabled = ($config->usegrademark) ? 1 : 0;

        // Do the table headers.
        $cells = array();
        $cells["part"] = new html_table_cell('part');
        $selectallcb = html_writer::checkbox(false, false, false, '', array("class" => "select_all_checkbox"));
        $cells["checkbox"] = new html_table_cell( ($istutor) ? $selectallcb : '&nbsp;' );
        if ($turnitintooltwouser->get_user_role() != 'Learner') {
            // These columns are used for sorting, and should retain their hidden_class class.
            $cells["studentlastname"] = new html_table_cell( get_string('studentlastname', 'turnitintooltwo'));
            $cells["studentlastname"]->attributes["class"] = 'sorting_name sorting_name_last';
        }
        if ($istutor) {
            $cells["student"] = new html_table_cell(
                html_writer::tag('div', get_string('studentfirstname', 'turnitintooltwo'), array('class' => 'data-table-splitter splitter-firstname sorting', 'data-col'=> 18 )).
                html_writer::tag('div', ' / '.get_string('studentlastname', 'turnitintooltwo'), array('class' => 'data-table-splitter splitter-lastname sorting', 'data-col' => 2))
            );
        } else {
            $cells["student"] = new html_table_cell('&nbsp;');
        }
        $cells["student"]->attributes['class'] = 'left';
        $cells["title_raw"] = new html_table_cell('&nbsp;');
        $cells["title_raw"]->attributes['class'] = 'raw_data';
        $cells["title"] = new html_table_cell(get_string('submissiontitle', 'turnitintooltwo'));
        $cells["title"]->attributes['class'] = 'left';
        $cells["paper_id"] = new html_table_cell(get_string('objectid', 'turnitintooltwo'));
        $cells["paper_id"]->attributes['class'] = 'right';
        $cells["submitted_date_raw"] = new html_table_cell('&nbsp;');
        $cells["submitted_date_raw"]->attributes['class'] = 'raw_data';
        $cells["submitted_date"] = new html_table_cell(get_string('submitted', 'turnitintooltwo'));
        $cells["submitted_date"]->attributes['class'] = 'right';
        if (($turnitintooltwouser->get_user_role() == 'Instructor') ||
                ($turnitintooltwouser->get_user_role() == 'Learner' && $origreportenabled)) {
            $cells["report_raw"] = new html_table_cell('&nbsp;');
            $cells["report_raw"]->attributes['class'] = 'raw_data';
            $cells["report"] = new html_table_cell(get_string('submissionorig', 'turnitintooltwo'));
            $cells["report"]->attributes['class'] = 'right';
        }
        if ($grademarkenabled) {
            $cells["grade_raw"] = new html_table_cell('&nbsp;');
            $cells["grade_raw"]->attributes['class'] = 'raw_data';
            $cells["grade"] = new html_table_cell(get_string('submissiongrade', 'turnitintooltwo'));
            $cells["grade"]->id = "grademark";
            $cells["grade"]->attributes['class'] = 'right';
            if (count($partdetails) > 1 || $turnitintooltwoassignment->turnitintooltwo->grade < 0) {
                $cells["overallgrade"] = new html_table_cell(get_string('overallgrade', 'turnitintooltwo'));
                $cells["overallgrade"]->attributes['class'] = 'right';
            }
        }
        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $cells["student_read"] = new html_table_cell('&nbsp;');
        }
        $cells["upload"] = new html_table_cell('&nbsp;');
        $cells["upload"]->attributes['class'] = "noscript_hide";

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $cells["refresh"] = new html_table_cell('&nbsp;');
        }

        $cells["download"] = new html_table_cell('&nbsp;');
        $cells["delete"] = new html_table_cell('&nbsp;');

        if ($turnitintooltwouser->get_user_role() != 'Learner') {
            // These columns are used for sorting, and should retain their hidden_class class.
            // Put the user firstame in the latest hidden cell.
            $cells["studentfirstname"] = new html_table_cell( get_string('studentfirstname', 'turnitintooltwo'));
            $cells["studentfirstname"]->attributes["class"] = 'sorting_name sorting_first_last';
        }

        $tableheaders = $cells;

        $tables = "";
        $output .= $OUTPUT->box_start('', 'tabs');
        $tabitems = array();
        $i = 0;

        // Determine which tab position to enable after a submission deletion.
        $tabposition = array_search(optional_param('partid', 0, PARAM_INT), array_keys($partdetails));
        if ($tabposition) {
            $output .= html_writer::tag('div', $tabposition, array('id' => 'tab_position', 'class' => 'hidden_class'));
        }

        foreach ($partdetails as $partid => $partobject) {
            if (!empty($partid)) {
                $i++;

                $tabitems[$i] = html_writer::link("#tabs-".$partid, $partobject->partname);
                $tables .= html_writer::tag('h2', $partobject->partname,
                                array('class' => 'js_hide', 'data-submitted' => $partobject->submitted));
                $tables .= $OUTPUT->box_start('part_table', 'tabs-'.$partid, array('data-submitted' => $partobject->submitted));

                $downloadlinks = "";
                if ($turnitintooltwouser->get_user_role() == 'Instructor') {
                    $origfilesziplang = "origfileszip";
                    $grademarkziplang = "grademarkzip";

                    // Output icon to download zip file of selected submissions in original format.
                    $exportorigfileszip = html_writer::tag('div',
                                                            html_writer::tag('i', '', array('class' => 'fa fa-file-o',
                                                    'title' => get_string($origfilesziplang, 'turnitintooltwo'))).' '.
                                                    get_string($origfilesziplang, 'turnitintooltwo'),
                                                            array('class' => 'mod_turnitintooltwo_zip_open mod_turnitintooltwo_origchecked_zip_open',
                                                                    'id' => 'origchecked_zip_'.$partobject->id));
                    // Put in div placeholder for launch form.
                    $exportorigfileszip .= $OUTPUT->box('', 'launch_form', 'origchecked_zip_form_'.$partobject->id);

                    // Output icon to download zip file of submissions in pdf format.
                    $exportgrademarkzip = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.
                                                    $cm->id.'&part='.$partid.'&do=export_pdfs&view_context=box_solid',
                                                    html_writer::tag('i', '', array('class' => 'fa fa-file-pdf-o',
                                                    'title' => get_string($grademarkziplang, 'turnitintooltwo'))).' '.
                                                    get_string($grademarkziplang, 'turnitintooltwo'),
                                            array("class" => "mod_turnitintooltwo_gmpdfzip_box", "id" => "gmpdf_zip_".$partobject->id));

                    $linkstyles = array('class' => 'btn dropdown-toggle', 'data-toggle' => 'dropdown', 'disabled' => 'disabled', 'title' => get_string("download_button_warning", 'turnitintooltwo'));
                    $linkdropdown = html_writer::tag('ul',
                                                html_writer::tag('li', $exportorigfileszip).
                                                    html_writer::tag('li', $exportgrademarkzip),
                                                array('class' => 'dropdown-menu mod_turnitintooltwo_dropdown-menu'));
                    $downloadlinks = html_writer::tag('div',
                                        html_writer::tag('button', get_string('download', 'turnitintooltwo'),
                                            $linkstyles).$linkdropdown,
                                            array('id' => 'mod_turnitintooltwo_download_links', 'class' => 'btn-group'));
                }

                // Include download links and info table.
                $tables .= html_writer::tag('div', $downloadlinks, array('id' => 'part_' . $partobject->id, 'class' => 'mod_turnitintooltwo_zip_downloads'));
                $tables .= $this->get_submission_inbox_part_details($cm, $turnitintooltwoassignment, $partdetails, $partid);

                // Construct submissions table.
                $table = new html_table();
                $table->id = $partid;
                $table->attributes['class'] = 'mod_turnitintooltwo_submissions_data_table';
                $table->head = $tableheaders;

                // Populate inbox if user is a student incase they do not have javascript enabled.
                if ($turnitintooltwouser->get_user_role() == 'Learner') {
                    $submission = current($this->get_submission_inbox($cm, $turnitintooltwoassignment, $partdetails, $partid));

                    // If not logged in as a tutor then refresh submissions.
                    $turnitintooltwoassignment->refresh_submissions($cm, $partobject);

                    $j = 0;
                    $cells = array();
                    foreach ($submission as $cell) {
                        $cells[$j] = new html_table_cell($cell);
                        if ($j == 2 || $j == 3 || $j == 4) {
                            $cells[$j]->attributes['class'] = "left";
                        } else if ($j == 5 || $j == 7) {
                            $cells[$j]->attributes['class'] = "right";
                        } else if (($j == 8 && $origreportenabled) || ($j == 8 && !$origreportenabled && $grademarkenabled) ||
                                    ($j == 10 && $origreportenabled && $grademarkenabled)) {
                            $cells[$j]->attributes['class'] = "raw_data";
                        } else if (($j == 12 && $origreportenabled) || ($j == 11 && !$origreportenabled)) {
                            $cells[$j]->attributes['class'] = "right";
                        } else {
                            $cells[$j]->attributes['class'] = "centered_cell";
                        }

                        if ((count($submission) == 16 && $j == 11) || (count($submission) == 15 && $j == 10)) {
                            $cells[$j]->attributes['class'] = "noscript_hide";
                        }

                        $j++;
                    }
                    $rows[0] = new html_table_row($cells);
                    $table->data = $rows;
                }

                $tables .= html_writer::table($table);
                $tables .= $OUTPUT->box_end(true);

                // Link to open Turnitin Messages inbox.
                $messagesinbox = '';
                if ($turnitintooltwouser->get_user_role() == 'Instructor') {
                    $icon = html_writer::tag('i', '', array('class' => 'fa fa-envelope-o fa-lg'));
                    $loading_icon = $OUTPUT->pix_icon('loading',
                        get_string('turnitinloading', 'turnitintooltwo'), 'mod_turnitintooltwo');
                    $messagesinbox = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                    '&user='.$turnitintooltwouser->id.'&do=loadmessages&view_context=box',
                                                        $icon.' '.get_string('messagesinbox', 'turnitintooltwo').
                                                        ' ('.html_writer::tag('span', '', array('class' => 'messages_amount')).
                                                            html_writer::tag('span', $loading_icon,
                                                            array('class' => 'mod_turnitintooltwo_messages_loading')).')',
                                                array("class" => "mod_turnitintooltwo_messages_inbox"));
                }

                // Check that nonsubmitter messages have been configured to be sent.
                $nonsubsemailpermitted = $this->is_nonsubmitter_emails_enabled();

                // Link to email nonsubmitters.
                $emailnonsubmitters = '';
                if ($turnitintooltwouser->get_user_role() == 'Instructor' && $nonsubsemailpermitted) {
                    $icon = html_writer::tag('i', '', array('class' => 'fa fa-reply-all fa-lg'));
                    $emailnonsubmitters = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                            '&part='.$partid.'&do=emailnonsubmittersform&view_context=box_solid',
                                                            $icon.' '.get_string('messagenonsubmitters', 'turnitintooltwo'),
                                                            array("class" => "mod_turnitintooltwo_nonsubmitters_link", "id" => "nonsubmitters_".$partid));
                }

                // Link to refresh submissions with latest data from Turnitin.
                $refreshlink = html_writer::tag('div', html_writer::tag('i', '', array('class' => 'fa fa-refresh fa-lg',
                                                    'title' => get_string('turnitinrefreshingsubmissions', 'turnitintooltwo')))." ".
                                                    get_string('turnitinrefreshsubmissions', 'turnitintooltwo'),
                                                        array('class' => 'mod_turnitintooltwo_refresh_link', 'id' => 'refresh_'.$partid));

                // Link which appears during the refresh of submissions.
                $refreshinglink = html_writer::tag('div', html_writer::tag('i', '', array('class' => 'fa fa-spinner fa-spin fa-lg',
                                                    'title' => get_string('turnitinrefreshingsubmissions', 'turnitintooltwo')))." ".
                                                    get_string('turnitinrefreshingsubmissions', 'turnitintooltwo'),
                                                        array('class' => 'mod_turnitintooltwo_refreshing_link', 'id' => 'refreshing_'.$partid));

                // Output the links.
                $output .= $OUTPUT->box($messagesinbox.$emailnonsubmitters.$refreshlink.$refreshinglink,
                                'tii_table_functions', 'tii_table_functions_'.$partid);
            }
        }

        $output .= html_writer::alist($tabitems, array("id" => "part_tabs_menu"));

        $output .= $tables;
        $output .= $OUTPUT->box_end(true);

        return $output;
    }

    /**
     * Construct table with part details
     *
     * @global type $OUTPUT
     * @global type $CFG
     * @param type $cm
     * @param type $turnitintooltwoassignment
     * @param type $partdetails
     * @param type $partid
     * @return type
     */
    private function get_submission_inbox_part_details($cm, $turnitintooltwoassignment, $partdetails, $partid) {
        global $OUTPUT, $CFG;

        $config = turnitintooltwo_admin_config();
        $table = new html_table();
        $rows = array();

        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        $cells = array();
        $cells[0] = new html_table_cell(get_string('title', 'turnitintooltwo'));
        $cells[0]->attributes['class'] = 'left';
        $cells[1] = new html_table_cell(get_string('dtstart', 'turnitintooltwo'));
        $cells[2] = new html_table_cell(get_string('dtdue', 'turnitintooltwo'));
        $cells[3] = new html_table_cell(get_string('dtpost', 'turnitintooltwo'));
        if (!empty($config->usegrademark)) {
            $cells[4] = new html_table_cell(get_string('marksavailable', 'turnitintooltwo'));
        }
        if ($istutor) {
            $cells[5] = new html_table_cell(get_string('downloadexport', 'turnitintooltwo'));
            $cells[6] = new html_table_cell('');
        }
        $partsheaders = $cells;

        $cells = array();

        // Allow part name to be editable if a tutor is logged in.
        $textfield = $partdetails[$partid]->partname;
        if ($istutor) {
            $textfield = html_writer::link('#', $partdetails[$partid]->partname,
                                            array('title' => get_string('edit', 'turnitintooltwo'),
                                                'class' => 'editable_text editable_text_'.$partid,
                                                'data-type' => 'text', 'data-pk' => $partid, 'data-name' => 'partname',
                                                'id' => 'part_name_'.$partid,
                                                'data-params' => "{ 'assignment': ".
                                                                    $turnitintooltwoassignment->turnitintooltwo->id.", ".
                                                                    "'action': 'edit_field', 'sesskey': '".sesskey()."' }"));
        }
        $cells[0] = new html_table_cell($turnitintooltwoassignment->turnitintooltwo->name." - ".$textfield." ");

        // Allow start date field to be editable if a tutor is logged in.
        $dateformat = ($CFG->ostype == 'WINDOWS') ? '%d %b %Y - %H:%M' : '%d %h %Y - %H:%M';
        $datefield = userdate($partdetails[$partid]->dtstart, $dateformat);
        if ($istutor) {
            $datefield = html_writer::link('#', $datefield,
                                            array('title' => get_string('edit', 'turnitintooltwo'),
                                                'class' => 'editable_date editable_date_'.$partid,
                                                'data-pk' => $partid, 'data-name' => 'dtstart', 'id' => 'date_start_'.$partid,
                                                'data-params' => "{ 'assignment': ".
                                                                    $turnitintooltwoassignment->turnitintooltwo->id.", ".
                                                                    "'action': 'edit_field', 'sesskey': '".sesskey()."' }"));
        }
        $cells[1] = new html_table_cell($datefield);
        $cells[1]->attributes['class'] = 'data';

        // Allow due date field to be editable if a tutor is logged in.
        $dateformat = ($CFG->ostype == 'WINDOWS') ? '%d %b %Y - %H:%M' : '%d %h %Y - %H:%M';
        $datefield = userdate($partdetails[$partid]->dtdue, $dateformat);
        if ($istutor) {
            $datefield = html_writer::link('#', $datefield,
                                            array('data-anon' => $turnitintooltwoassignment->turnitintooltwo->anon,
                                                'title' => get_string('edit', 'turnitintooltwo'),
                                                'class' => 'editable_postdue editable_date editable_date_'.$partid,
                                                'data-pk' => $partid, 'data-name' => 'dtdue', 'id' => 'date_due_'.$partid,
                                                'data-params' => "{ 'assignment': ".
                                                                    $turnitintooltwoassignment->turnitintooltwo->id.", ".
                                                                    "'action': 'edit_field', 'sesskey': '".sesskey()."' }"));
        }
        $cells[2] = new html_table_cell($datefield);
        $cells[2]->attributes['class'] = 'data';

        // Allow post date field to be editable if a tutor is logged in.
        $dateformat = ($CFG->ostype == 'WINDOWS') ? '%d %b %Y - %H:%M' : '%d %h %Y - %H:%M';
        $datefield = userdate($partdetails[$partid]->dtpost, $dateformat);
        if ($istutor) {
            $datefield = html_writer::link('#', $datefield,
                                            array('data-anon' => $turnitintooltwoassignment->turnitintooltwo->anon,
                                                'data-unanon' => $partdetails[$partid]->unanon,
                                                'data-submitted' => $partdetails[$partid]->submitted,
                                                'title' => get_string('edit', 'turnitintooltwo'),
                                                'class' => 'editable_postdue editable_date editable_date_'.$partid,
                                                'data-pk' => $partid, 'data-name' => 'dtpost', 'id' => 'date_post_'.$partid,
                                                'data-params' => "{ 'assignment': ".
                                                                    $turnitintooltwoassignment->turnitintooltwo->id.", ".
                                                                    "'action': 'edit_field', 'sesskey': '".sesskey()."' }"));
        }
        $cells[3] = new html_table_cell($datefield);
        $cells[3]->attributes['class'] = 'data';

        // Don't show Grade column if not using GradeMark.
        if (!empty($config->usegrademark)) {
            // Show Rubric view if applicable to students.
            $rubricviewlink = '';
            if (!$istutor && !empty($turnitintooltwoassignment->turnitintooltwo->rubric)) {
                $rubricviewlink .= $OUTPUT->box_start('row_rubric_manager', '');
                $rubricviewlink .= html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                        '&part='.$partid.'&do=rubricview&view_context=box',
                                                    html_writer::tag('span', '',
                                                        array('class' => 'tiiicon icon-rubric icon-lg', 'id' => 'mod_turnitintooltwo_rubric_view_form')),
                                                    array('class' => 'mod_turnitintooltwo_rubric_view_launch', 'id' => 'rubric_view_launch',
                                                        'title' => get_string('launchrubricview', 'turnitintooltwo')));
                $rubricviewlink .= $OUTPUT->box_end(true);
            }

            // Show warning to instructor when changing maxmarks if grades exist
            $turnitintooltwosubmission = new turnitintooltwo_submission();
            $getgrades = $turnitintooltwosubmission->count_graded_submissions($turnitintooltwoassignment->turnitintooltwo->id);

            $class = $getgrades > 0 ? 'max_marks_warning' : '';

            // Allow marks to be editable if a tutor is logged in.
            $textfield = $partdetails[$partid]->maxmarks.$rubricviewlink;
            if ($istutor) {
                $textfield = html_writer::link('#', $partdetails[$partid]->maxmarks,
                                                array('title' => get_string('edit', 'turnitintooltwo'),
                                                    'class' => 'editable_text editable_text_'.$partid . ' ' . $class, 'id' => 'marks_'.$partid,
                                                    'data-type' => 'text', 'data-pk' => $partid, 'data-name' => 'maxmarks',
                                                    'data-params' => "{ 'assignment': ".
                                                                        $turnitintooltwoassignment->turnitintooltwo->id.", ".
                                                                        "'action': 'edit_field', 'sesskey': '".sesskey()."' }"));
            }
            $cells[4] = new html_table_cell($textfield);
            $cells[4]->attributes['class'] = 'data';
        }

        if ($istutor) {
            // Output icon to download zip file of submissions in original format.
            $exportoriginalzip = $OUTPUT->box_start('row_export_orig', '');
            $exportoriginalzip .= $OUTPUT->box(
                html_writer::tag('i', '', array('title' => get_string('exportoriginal', 'turnitintooltwo'),
                                                'class' => 'fa fa-file-o fa-lg')),
                'mod_turnitintooltwo_zip_open orig_zip_open', 'orig_zip_'.$partdetails[$partid]->tiiassignid
            );
            // Put in div placeholder for launch form.
            $exportoriginalzip .= $OUTPUT->box('', 'launch_form', 'orig_zip_form_'.$partdetails[$partid]->tiiassignid);
            $exportoriginalzip .= $OUTPUT->box_end(true);

            // Output icon to download zip file of submissions in pdf format.
            $exportpdfzip = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.
                                    $cm->id.'&part='.$partid.'&do=export_pdfs&view_context=box_solid',
                                    html_writer::tag('i', '', array('title' => get_string('exportpdf', 'turnitintooltwo'),
                                        'class' => 'fa fa-file-pdf-o fa-lg middle-padding')),
                                    array("class" => "downloadpdf_box",
                                            "id" => "download_".$partdetails[$partid]->tiiassignid));

            // Output icon to download excel spreadsheet of grades.
            $exportxlszip = $OUTPUT->box_start('row_export_xls', '');
            $exportxlszip .= $OUTPUT->box(
                    html_writer::tag('i', '', array('title' => get_string('exportexcel', 'turnitintooltwo'),
                        'class' => 'fa fa-file-excel-o fa-lg')),
                    'mod_turnitintooltwo_zip_open xls_inbox_open', 'xls_inbox_'.$partdetails[$partid]->tiiassignid
                );

            // Put in div placeholder for launch form.
            $exportxlszip .= $OUTPUT->box('', 'launch_form', 'xls_inbox_form_'.$partdetails[$partid]->tiiassignid);
            $exportxlszip .= $OUTPUT->box_end(true);
            $showexport = ($turnitintooltwoassignment->turnitintooltwo->anon == 0 || time() > $partdetails[$partid]->dtpost);
            $exportoptions = $showexport ? 'tii_export_options_show' : 'tii_export_options_hide';

            $links = $OUTPUT->box_start($exportoptions, 'export_options');

            // Show the export links if they should be available.
            if ($turnitintooltwoassignment->turnitintooltwo->anon == 0 || time() > $partdetails[$partid]->dtpost) {
                $links .= $exportxlszip.$exportpdfzip.$exportoriginalzip;
            }

            $links .= $OUTPUT->box_end(true);

            if ($turnitintooltwoassignment->count_submissions($cm, $partid) == 0) {
                $links = html_writer::tag('div', $links, array('id' => 'export_links', 'class' => 'hidden_class'));
            }

            $cells[5] = new html_table_cell($links);
            $cells[5]->attributes['class'] = 'export_data';

            // Show feature links (rubric and quickmark).
            if ($config->usegrademark) {
                // Rubric Manager.
                $coursetype = turnitintooltwo_get_course_type($turnitintooltwoassignment->turnitintooltwo->legacy);
                $coursedata = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
                $rubricmanagerlink = $OUTPUT->box_start('row_rubric_manager', '');
                $rubricmanagerlink .= html_writer::link($CFG->wwwroot.
                                        '/mod/turnitintooltwo/extras.php?cmd=rubricmanager&tiicourseid='.
                                            $coursedata->turnitin_cid.'&view_context=box',
                                                html_writer::tag('i', '', array('class' => 'tiiicon icon-rubric icon-lg')),
                                                array('class' => 'mod_turnitintooltwo_rubric_manager_launch', 'id' => 'rubric_manager_inbox_launch',
                                                    'title' => get_string('launchrubricmanager', 'turnitintooltwo')));
                $rubricmanagerlink .= html_writer::tag('span', '', array('class' => 'launch_form', 'id' => 'rubric_manager_form'));
                $rubricmanagerlink .= $OUTPUT->box_end(true);

                // Quickmark Manager.
                $quickmarkmanagerlink = $OUTPUT->box_start('row_quickmark_manager', '');
                $quickmarkmanagerlink .= html_writer::link($CFG->wwwroot.
                                            '/mod/turnitintooltwo/extras.php?cmd=quickmarkmanager&view_context=box',
                                                html_writer::tag('i', '', array('class' => 'tiiicon icon-quickmarks icon-lg')),
                                                array('class' => 'mod_turnitintooltwo_quickmark_manager_launch',
                                                        'title' => get_string('launchquickmarkmanager', 'turnitintooltwo')));
                $quickmarkmanagerlink .= html_writer::tag('span', '', array('class' => 'launch_form',
                                                                            'id' => 'quickmark_manager_form'));
                $quickmarkmanagerlink .= $OUTPUT->box_end(true);

                $cells[6] = new html_table_cell($rubricmanagerlink.$quickmarkmanagerlink);
                $cells[6]->attributes['class'] = 'rubric_qm';
            }
        }
        $rows['part_details'] = new html_table_row($cells);

        // Show summary box.
        if (!empty($turnitintooltwoassignment->turnitintooltwo->intro)) {
            $cells = array();

            $introtext = format_module_intro('turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo, $cm->id);
            $intro = html_writer::tag('div', get_string("turnitintooltwointro", "turnitintooltwo").": ".
                        $introtext, array("class" => "introduction"));

            $cells[0] = new html_table_cell($intro);
            $cells[0]->attributes['class'] = 'introduction_cell';
            $cells[0]->colspan = ($config->usegrademark) ? '7' : '6';

            $rows['intro'] = new html_table_row($cells);
        }

        // Show Peermark row if enabled.
        if ($config->enablepeermark) {

            $cells = array();
            // Show Peermark hide/show links.
            $hideclass = 'hide_peermarks_'.$turnitintooltwoassignment->turnitintooltwo->id;
            $hidetext = html_writer::tag('i', '', array('class' => 'fa fa-minus-circle red fa-lg '.$hideclass));
            $showclass = 'show_peermarks_'.$turnitintooltwoassignment->turnitintooltwo->id;
            $showtext = html_writer::tag('i', '', array('class' => 'fa fa-plus-circle green fa-lg '.$showclass));

            $links = html_writer::link('javascript:void(0)', $showtext.$hidetext , array('class' => 'toggle_peermarks js_hide'));

            // Peermark Settings Link.
            $peermarkmanagerlink = "";
            if ($istutor) {
                $peermarkmanagerlink .= $OUTPUT->box_start('row_peermark_manager', '');
                $peermarkmanagerlink .= html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                        '&part='.$partid.'&do=peermarkmanager&view_context=box',
                                                    html_writer::tag('i', '', array('class' => 'tiiicon icon-settings icon-lg')),
                                                    array('class' => 'tii_peermark_manager_launch',
                                                        'id' => 'peermark_manager_'.$partid,
                                                        'title' => get_string('launchpeermarkmanager', 'turnitintooltwo')));
                $peermarkmanagerlink .= html_writer::tag('span', '', array('class' => 'launch_form',
                                                                    'id' => 'peermark_manager_form'));
                $peermarkmanagerlink .= $OUTPUT->box_end(true);
            }

            // Peermark Reviews Link.
            $peermarkreviewslink = $OUTPUT->box_start('row_peermark_reviews', '');
            $peermarkreviewslink .= html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.
                                                        '&part='.$partid.'&do=peermarkreviews&view_context=box',
                                                    html_writer::tag('i', '', array('class' => 'tiiicon icon-peermark icon-lg')),
                                                    array('class' => 'tii_peermark_reviews_launch',
                                                        'title' => get_string('launchpeermarkreviews', 'turnitintooltwo')));
            $peermarkreviewslink .= html_writer::tag('span', '', array('class' => 'launch_form', 'id' => 'peermark_reviews_form'));
            $peermarkreviewslink .= $OUTPUT->box_end(true);

            // If logged in as a student then show peermark data straightaway as they may have javascript disabled.
            $count = ($istutor) ? 0 : count($partdetails[$partid]->peermark_assignments);

            // Build peermark header row.
            if ($istutor || $count > 0) {
                $cells[0] = new html_table_cell($links.html_writer::tag('div',
                                                get_string('peermarkassignments', 'turnitintooltwo').' ('.
                                                    html_writer::tag('span', $count, array('class' => 'peermark_count')).
                                                    html_writer::tag('span', $OUTPUT->pix_icon('loading',
                                                        get_string('turnitinloading', 'turnitintooltwo'), 'mod_turnitintooltwo'),
                                                    array('class' => 'peermark-loading mod_turnitintooltwo_peermark-loading-span')).')',
                                                    array('class' => 'peermark_header')).$peermarkreviewslink.$peermarkmanagerlink);
                $cells[0]->attributes['class'] = 'peermarks';
                $cells[0]->colspan = ($config->usegrademark) ? '7' : '6';
                $rows['peermark'] = new html_table_row($cells);

                $peermarktable = ($istutor) ? '' : $this->show_peermark_assignment($partdetails[$partid]->peermark_assignments);
                $cells[0] = new html_table_cell(html_writer::tag('div', $peermarktable,
                                                    array("class" => "peermark_assignments_container")).
                                                html_writer::tag('div', $OUTPUT->pix_icon('loading',
                                                    get_string('turnitinloading', 'turnitintooltwo'), 'mod_turnitintooltwo'),
                                                        array('class' => 'peermark_loading peermark_loading_row')));
                $cells[0]->attributes['class'] = 'peermark_assignments_cell';
                $cells[0]->colspan = ($config->usegrademark) ? '7' : '6';
                $rows['peermark_assignments'] = new html_table_row($cells);
            }
        }

        $table->attributes['class'] = 'mod_turnitintooltwo_part_details';
        $table->head = $partsheaders;

        $table->data = $rows;

        $output = html_writer::table($table);

        return $output;
    }

    public function show_peermark_assignment($peermarkassignments) {
        $table = new html_table();
        $rows = array();

        // Headers.
        $cells = array();
        $cells[0] = new html_table_cell(get_string('title', 'turnitintooltwo'));
        $cells[0]->attributes['class'] = 'left';
        $cells[1] = new html_table_cell(get_string('dtstart', 'turnitintooltwo'));
        $cells[2] = new html_table_cell(get_string('dtdue', 'turnitintooltwo'));
        $cells[3] = new html_table_cell(get_string('dtpost', 'turnitintooltwo'));
        $cells[4] = new html_table_cell(get_string('marksavailable', 'turnitintooltwo'));
        $cells[5] = new html_table_cell(get_string('noofreviewsrequired', 'turnitintooltwo'));
        $table->head = $cells;

        foreach ($peermarkassignments as $peermarkassignment) {
            $cells = array();

            // Show Peermark Instructions hide/show links.
            if (!empty($peermarkassignment->instructions)) {
                $hidetext = html_writer::tag('i', '',
                                array('class' => 'fa  fa-minus-circle red fa-lg hide_peermark_instructions',
                                        'id' => 'hide_peermark_instructions_'.$peermarkassignment->tiiassignid));
                $showtext = html_writer::tag('i', '',
                                array('class' => 'fa  fa-plus-circle green fa-lg show_peermark_instructions',
                                        'id' => 'show_peermark_instructions_'.$peermarkassignment->tiiassignid));

                $links = html_writer::link('javascript:void(0)', $showtext.$hidetext ,
                                    array('class' => 'toggle_peermark_instructions js_hide'));

            } else {
                $links = html_writer::tag('div', '', array('class' => 'peermark_instructions_spacer'));
            }

            $cells[0] = new html_table_cell($links.$peermarkassignment->title);
            $cells[1] = new html_table_cell(userdate($peermarkassignment->dtstart,
                                                    get_string('strftimedatetimeshort', 'langconfig')));
            $cells[1]->attributes['class'] = 'data';
            $cells[2] = new html_table_cell(userdate($peermarkassignment->dtdue,
                                                    get_string('strftimedatetimeshort', 'langconfig')));
            $cells[2]->attributes['class'] = 'data';
            $cells[3] = new html_table_cell(userdate($peermarkassignment->dtpost,
                                                    get_string('strftimedatetimeshort', 'langconfig')));
            $cells[3]->attributes['class'] = 'data';
            $cells[4] = new html_table_cell($peermarkassignment->maxmarks);
            $cells[4]->attributes['class'] = 'data';

            $totalreviews = $peermarkassignment->distributed_reviews + $peermarkassignment->selected_reviews + $peermarkassignment->self_review;
            $cells[5] = new html_table_cell($totalreviews);
            $cells[5]->attributes['class'] = 'data';
            $rows[] = $cells;

            // Put instructions row in if applicable.
            if (!empty($peermarkassignment->instructions)) {
                $cells = array();
                $peermarkinstructions = html_writer::tag('div', get_string("instructions", "auth").": ".
                                                            $peermarkassignment->instructions,
                                                    array("class" => "peermark_instructions",
                                                        "id" => "peermark_instructions_".$peermarkassignment->tiiassignid));

                $cells[0] = new html_table_cell($peermarkinstructions);
                $cells[0]->colspan = '6';
                $cells[0]->attributes['class'] = 'peermark_instructions_cell';
                $rows[] = $cells;
            }
        }

        $table->data = $rows;
        $table->attributes['class'] = 'mod_turnitintooltwo_peermark_details';
        $output = html_writer::table($table);

        return $output;
    }

    /**
     * Get the row for this submission in the inbox table
     *
     * @global object $CFG
     * @global type $OUTPUT
     * @param type $cm
     * @param type $turnitintooltwoassignment
     * @param type $parts
     * @param type $partid
     * @param type $submission
     * @param type $useroverallgrades
     * @param type $istutor
     * @return type
     */
    public function get_submission_inbox_row($cm, $turnitintooltwoassignment, $parts, $partid, $submission,
                                            &$useroverallgrades, $istutor, $context = 'all') {
        global $CFG, $OUTPUT, $USER, $DB;
        $config = turnitintooltwo_admin_config();
        $moodleuserid = (substr($submission->userid, 0, 3) != 'nm-' && $submission->userid != 0) ? $submission->userid : 0;

        // Determine whether we display the overall grade based on the post date of all parts to students.
        // Also, determine whether all parts have been unanoymised for displaying overall grade to instructors.
        $displayoverallgrade = 1;
        $allpartsunanonymised = 1;
        foreach (array_keys($parts) as $k => $v) {
            if ($parts[$v]->dtpost > time()) {
                $displayoverallgrade = 0;
            }
            if ($turnitintooltwoassignment->turnitintooltwo->anon && $parts[$v]->unanon != 1 && $allpartsunanonymised) {
                $allpartsunanonymised = 0;
            }
        }

        if (!$istutor) {
            $user = new turnitintooltwo_user($USER->id, "Learner");
        }

        $checkbox = "&nbsp;";
        if (!empty($submission->submission_objectid) && $istutor) {
            $checkbox = html_writer::checkbox('check_'.$submission->submission_objectid, $submission->submission_objectid,
                                        false, '', array("class" => "inbox_checkbox"));
        }

        if ( !$istutor ) {
            // If students viewing it will show 'digital receipt' link.
            if ( !empty($submission->submission_objectid) ) {
                $linkurl = $CFG->wwwroot.'/mod/turnitintooltwo/view.php';
                $querystr = 'id='.$cm->id.'&do=digital_receipt&submissionid='.$submission->submission_objectid.'&view_context=box';
                $studentname = html_writer::link($linkurl.'?'.$querystr,
                    $OUTPUT->pix_icon('receipt', get_string('digitalreceipt', 'turnitintooltwo'), 'mod_turnitintooltwo',
                        array('id' => 'tii_digital_receipt_icon')) . get_string('viewdigitalreceipt', 'turnitintooltwo'),
                            array('class' => 'mod_turnitintooltwo_digital_receipt')
                );
            } else {
                $studentname = "--";
            }
        } else {
            if ($turnitintooltwoassignment->turnitintooltwo->anon && $parts[$partid]->unanon != 1) {
                if (empty($submission->submission_unanon) AND $parts[$partid]->dtpost > time() AND
                                                        !empty($submission->submission_objectid)) {
                    // Anonymous marking is on, postdate has not passed and a submission has been made.
                    $studentname = html_writer::link('.mod_turnitintooltwo_unanonymise_form',
                                        get_string('anonenabled', 'turnitintooltwo'),
                                        array("class" => "unanonymise", "id" => "submission_".$submission->submission_objectid));
                    $studentlastname = get_string('anonenabled', 'turnitintooltwo');

                } else if (($parts[$partid]->dtpost <= time() OR !empty($submission->submission_unanon)) AND
                        empty($submission->nmoodle)) {
                    // Post date has passed or anonymous marking disabled for user and user is a moodle user.
                    $studentname = html_writer::link(
                                    $CFG->wwwroot."/user/view.php?id=".$submission->userid."&course="
                                        .$turnitintooltwoassignment->turnitintooltwo->course, $submission->fullname);
                    $studentlastname = $submission->lastname;
                } else if (($parts[$partid]->dtpost <= time() OR
                                !empty($submission->submission_unanon)) AND !empty($submission->nmoodle)) {
                    // Post date has passed or anonymous marking disabled for user and user is a NON moodle user.
                    $studentname = html_writer::tag("span",
                                        $submission->fullname." (".get_string('nonmoodleuser', 'turnitintooltwo').")",
                                        array("class" => "italic"));
                    $studentlastname = $submission->lastname;
                } else {
                    // User has not made a submission.
                    $studentname = html_writer::tag("span", get_string('anonenabled', 'turnitintooltwo'),
                                        array("class" => "italic"));
                    $studentlastname = $studentname;
                }
            } else {
                if (empty($submission->nmoodle)) {
                    // Link to user profile.
                    $studentname = html_writer::link($CFG->wwwroot."/user/view.php?id=".$submission->userid."&course=".
                                                $turnitintooltwoassignment->turnitintooltwo->course,
                                                $submission->fullname);
                    $studentlastname = $submission->lastname;
                } else if (!empty($submission->nmoodle) && substr($submission->userid, 0, 3) != 'nm-') {
                    // Moodle User not enrolled on this course as a student.
                    $studentname = html_writer::link($CFG->wwwroot."/user/view.php?id=".$submission->userid."&course=".
                                            $turnitintooltwoassignment->turnitintooltwo->course,
                                            $submission->fullname." (".get_string('nonenrolledstudent', 'turnitintooltwo').")",
                                                array("class" => "italic"));
                    $studentlastname = $submission->lastname;
                } else {
                    // Non Moodle user.
                    $studentname = html_writer::tag("span",
                                                $submission->fullname." (".get_string('nonmoodleuser', 'turnitintooltwo').")",
                                                array("class" => "italic"));
                    $studentlastname = $submission->lastname;
                }
            }
        }

        // Submission title with link to open DV.
        if ( !empty($submission->submission_objectid) AND !empty($submission->submission_objectid) ) {
            $titleinner = html_writer::tag('div', format_string($submission->submission_title),
                 	        array('class' => 'submission_title underline'));

            $titleinner .= html_writer::tag('div', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id,
                 	        array('id' => 'default_url_'.$submission->submission_objectid,
                                    'class' => 'dv_url'));

            $title = html_writer::tag('div', $titleinner,
                	        array('id' => 'default_'.$submission->submission_objectid.'_'.$partid.'_'.$moodleuserid,
                                    'class' => 'default_open'));
            $rawtitle = $submission->submission_title;
        } else {
            $title = "--";
            $rawtitle = "--";
        }

        $objectid = (!empty($submission->submission_objectid)) ? $submission->submission_objectid : "--";

        // Show date of submission or link to submit if it didn't work.
        if (empty($submission->submission_objectid) AND !empty($submission->id)) {
            $rawmodified = 1;
            $modified = html_writer::link($CFG->wwwroot."/mod/turnitintooltwo/view.php?id=".$cm->id."&action=manualsubmission".
                                            "&sub=".$submission->id.'&sesskey='.sesskey(),
                                                $OUTPUT->pix_icon('tii-icon', get_string('submittoturnitin', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo')." ".get_string('submittoturnitin', 'turnitintooltwo'));

        } else if (empty($submission->submission_objectid)) {
            $rawmodified = 0;
            $modified = "--";
        } else {
            $rawmodified = (int)$submission->submission_modified;
            $modified = userdate($submission->submission_modified, get_string('strftimedatetimeshort', 'langconfig'));
            if ($submission->submission_modified > $parts[$partid]->dtdue) {
                $modified = html_writer::tag('span', $modified, array("class" => "late_submission"));
            }
        }

        // Show Originality score with link to open document viewer.
        $rawscore = null;
        $score = '--';
        if (!empty($submission->id) && !empty($submission->submission_objectid) &&
                ($istutor || $turnitintooltwoassignment->turnitintooltwo->studentreports)) {

            //Show score.
            if (is_null($submission->submission_score)) {
                $scoreinner = html_writer::tag('div', '&nbsp;',
                                array('class' => 'score_colour score_colour_'));
                $scoreinner .= html_writer::tag('div', get_string('pending', 'turnitintooltwo'),
                                array('class' => 'origreport_score'));

            } else {
                // Put EN flag if translated matching is on and that is the score used.
                $transmatch = ($submission->submission_transmatch == 1) ? 'EN' : '&nbsp;';

                $scoreinner = html_writer::tag('div', $transmatch,
                                array('class' => 'score_colour score_colour_'.round($submission->submission_score, -1) ));
                $scoreinner .= html_writer::tag('div', $submission->submission_score.'%',
                                array('class' => 'origreport_score'));
                $rawscore = $submission->submission_score;
            }

            // Put in div placeholder for DV launch form.
            $scoreinner .= html_writer::tag('div', '',
                            array('id' => 'origreport_form_'.$submission->submission_objectid,
                                'class' => 'launch_form'));
            // URL for DV launcher.
            $scoreinner .= html_writer::tag('div', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id,
                            array('id' => 'origreport_url_'.$submission->submission_objectid,
                                'class' => 'dv_url'));

            if (is_null($submission->submission_score)) {
                $score = html_writer::tag('div', $scoreinner,
                                array('id' => 'origreport_'.$submission->submission_objectid.'_'.$partid.'_'.$moodleuserid,
                                    'class' => 'row_score'));
            } else {
                $score = html_writer::tag('div', $scoreinner,
                                array('id' => 'origreport_'.$submission->submission_objectid.'_'.$partid.'_'.$moodleuserid,
                                    'class' => 'row_score origreport_open'));
            }
        }

        // Show grade and link to DV.
        if ($config->usegrademark) {
            if ($turnitintooltwoassignment->turnitintooltwo->grade == 0) {
                // We set the grade column to N/A if there is no grade type set.
                $rawgrade = null;
                $grade = html_writer::tag('div', 'N/A', array());
            } else if (isset($submission->submission_objectid) && ($istutor || (!$istutor && $parts[$partid]->dtpost < time()))) {
                $submissiongrade = (!is_null($submission->submission_grade)) ? $submission->submission_grade : '';

                if (is_null($submission->submission_grade) || ($submission->submission_gmimaged == 0 && !$istutor)) {
                    $submissiongrade = "--";
                }

                // Show warning to instructor if student can still resubmit.
                $canresubmit = $turnitintooltwoassignment->turnitintooltwo->reportgenspeed > 0;
                $tutorbeforeduedate = $istutor && time() < $parts[$partid]->dtdue;
                $allowedlate = $turnitintooltwoassignment->turnitintooltwo->allowlate == 1 && empty($submission->nmoodle);
                $class = $canresubmit && ($tutorbeforeduedate || $allowedlate) ? 'graded_warning' : '';

                // Output grademark icon.
                $grade = '';
                if (!is_null($submission->submission_grade) || $submission->submission_gmimaged != 0 || $istutor) {

                    $submissiongradeicon = html_writer::tag('i', '',
                                                array('title' => get_string('submissiongrade', 'turnitintooltwo'),
                                                    'class' => 'fa fa-pencil fa-lg gm-blue'));

                    $grade = html_writer::tag('div', $submissiongradeicon,
                                                array('id' => 'grademark_' . $submission->submission_objectid . '_' . $partid . '_' . $moodleuserid,
                                                    'class' => 'grademark_open ' . $class,
                                                    'title' => $CFG->wwwroot . '/mod/turnitintooltwo/view.php?id=' . $cm->id));
                }

                // Show grade.
                if ($turnitintooltwoassignment->turnitintooltwo->gradedisplay == 2) { // 2 is fraction.
                    $grade .= html_writer::tag('span', $submissiongrade, array("class" => "grade"))
                            .html_writer::tag('span', "/".$parts[$partid]->maxmarks,
                                    array("class" => "grademark_grade"));
                } else if ($turnitintooltwoassignment->turnitintooltwo->gradedisplay == 1) { // 1 is percentage.
                    $submissiongrade = (is_numeric($submissiongrade) && $parts[$partid]->maxmarks > 0) ? round($submissiongrade / $parts[$partid]->maxmarks * 100, 1).'%' : $submissiongrade;
                    $grade .= html_writer::tag('span', $submissiongrade,
                                    array('class' => 'grade grademark_grade'));
                }

                // Put in div placeholder for DV launch form.
                $grade .= html_writer::tag('div', '',
                    	array('id' => 'grademark_form_'.$submission->submission_objectid,
                        	    'class' => 'launch_form'));
                // URL for DV launcher.
                $grade .= html_writer::tag('div', $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id,
                    	array('id' => 'grademark_url_'.$submission->submission_objectid,
                                'class' => 'dv_url'));

                $rawgrade = ($submissiongrade == "--") ? null : $submissiongrade;

            } else if (!isset($submission->submission_objectid) && empty($submission->id) && $istutor ) {
                // Allow nothing submission if no submission has been made and this is a tutor.
                $greysubmissiongradeicon = html_writer::tag('i', '', array('class' => 'fa fa-pencil fa-lg grey'));

                $grade = html_writer::tag('div', $greysubmissiongradeicon,
                        	array('id' => 'submitnothing_0_'.$partid . '_' . $submission->userid,
                                    'class' =>'submit_nothing'));
                $rawgrade = null;
            } else {
                $rawgrade = null;
                $grade = html_writer::tag('div', '--', array());
            }

            // Show average grade if more than 1 part or using a scale.
            if (count($parts) > 1 || $turnitintooltwoassignment->turnitintooltwo->grade < 0) {
                $overallgrade = '--';

                if ($submission->nmoodle != 1 && $allpartsunanonymised &&
                        ($istutor || (!$istutor && $parts[$partid]->dtpost < time()))) {
                    if (!isset($useroverallgrades[$submission->userid])) {
                        $usersubmissions = $turnitintooltwoassignment->get_user_submissions($submission->userid,
                                                                                $turnitintooltwoassignment->turnitintooltwo->id);
                        $useroverallgrades[$submission->userid] = $turnitintooltwoassignment->get_overall_grade($usersubmissions);
                    }

                    if ($turnitintooltwoassignment->turnitintooltwo->grade == 0 ||
                                                    $useroverallgrades[$submission->userid] === '--' ||
                                                    (!$istutor && $displayoverallgrade == 0)) {
                        $overallgrade = '--';
                    } else if ($turnitintooltwoassignment->turnitintooltwo->grade < 0) { // Scale.
                        $scale = $DB->get_record('scale', array('id' => $turnitintooltwoassignment->turnitintooltwo->grade * -1));
                        $scalearray = explode(",", $scale->scale);
                        $overallgrade = $scalearray[$useroverallgrades[$submission->userid] - 1];
                    } else {
                        $useroverallgrade = $useroverallgrades[$submission->userid];
                        $overallgrade = round($useroverallgrade / $turnitintooltwoassignment->turnitintooltwo->grade * 100, 1);
                        $overallgrade = $overallgrade.'%';
                    }

                    if ($overallgrade != '--') {
                        $overallgrade = html_writer::tag("span", $overallgrade,
                                                            array("class" => "overallgrade_".$submission->userid));
                    }
                }
            }
        }

        // Indicate whether student has seen grademark image.
        if ($istutor) {
            if (isset($submission->submission_objectid)) {
                $submissionattempts = (!empty($submission->submission_attempts)) ? $submission->submission_attempts : 0;
                if ($submissionattempts > 0) {
                    $studentread = $OUTPUT->pix_icon('icon-student-read',
                                        get_string('student_read', 'turnitintooltwo').' '.userdate($submissionattempts),
                                        'mod_turnitintooltwo', array("class" => "student_read_icon"));
                } else {
                    $studentread = $OUTPUT->pix_icon('icon-dot', get_string('student_notread', 'turnitintooltwo'),
                                        'mod_turnitintooltwo', array("class" => "student_dot_icon"));
                }
            } else {
                $studentread = "--";
            }
        }

        // Upload Submission.
        if ((!isset($submission->submission_objectid) || $turnitintooltwoassignment->turnitintooltwo->reportgenspeed != 0) &&
            empty($submission->nmoodle) && time() > $parts[$partid]->dtstart) {

            if (empty($submission->submission_objectid)) {
                $submission->submission_objectid = 0;
            }

            $uploadtext = (!$istutor) ? html_writer::tag('span', get_string('submitpaper', 'turnitintooltwo')) : '';

            $eulaaccepted = 0;
            if ($submission->userid == $USER->id) {
                $submissionuser = new turnitintooltwo_user($submission->userid, "Learner");

                $legacyassignment = (empty($turnitintooltwoassignment->turnitintooltwo->legacy)) ? 0 : 1;
                $coursetype = turnitintooltwo_get_course_type($legacyassignment);
                $coursedata = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);

                if (empty($_SESSION["unit_test"])) {
                    $submissionuser->join_user_to_class($coursedata->turnitin_cid);
                }

                // Has the student accepted the EULA?
                $eulaaccepted = $submissionuser->useragreementaccepted;
                if ($submissionuser->useragreementaccepted == 0 && !empty($_SESSION["unit_test"])) {
                    $eulaaccepted = $submissionuser->get_accepted_user_agreement();
                }
            }

            $upload = html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.$cm->id.'&part='.$partid.'&user='.
                                    $submission->userid.'&do=submitpaper&view_context=box_solid', $uploadtext.' '.
                                    html_writer::tag('i', '', array('title' => get_string('submitpaper', 'turnitintooltwo'),
                                        'class' => 'fa fa-cloud-upload fa-lg')),
                                    array("class" => "upload_box nowrap",
                                            "id" => "upload_".$submission->submission_objectid."_".$partid."_".$submission->userid,
                                            'data-eula' => $eulaaccepted, 'data-user-type' => $istutor));

            $duedatepassed = time() > $parts[$partid]->dtdue;
            $latesubmissionsallowed = $turnitintooltwoassignment->turnitintooltwo->allowlate;
            $submissionexists = empty($submission->submission_objectid);

            // Show option to submit only when due date has passed, late submissions are allowed and student has not submitted.
            // An instructor will always have the ability to make a late submission - to account for student exemptions.
            if (!$istutor && ($duedatepassed && ($latesubmissionsallowed == 0 || ($latesubmissionsallowed == 1 && !$submissionexists)))) {
                $upload = "&nbsp";
            }

        } else {
            $upload = "&nbsp;";
        }

        // Download submission in original format.
        if (!empty($submission->submission_objectid) && !empty($submission->id) && !$submission->submission_acceptnothing) {

            $downloadicon = html_writer::tag('i', '',
                                array('title' => get_string('downloadsubmission', 'turnitintooltwo'),
                                    'class' => 'fa fa-download fa-lg'));

            $download = html_writer::tag('div', $downloadicon,
                                array('id' => 'downloadoriginal_' . $submission->submission_objectid . "_" . $partid . "_" . $moodleuserid,
                                    'class'=> 'download_original_open'));

            $download .= html_writer::tag('div', '',
                                array('id' => 'downloadoriginal_form_'.$submission->submission_objectid,
                                    'class' => 'launch_form'));

            // Add in LTI launch form incase Javascript is disabled.
            if (!$istutor) {
                $download .= html_writer::tag('noscript', $this->output_dv_launch_form("downloadoriginal",
                                                $submission->submission_objectid, $user->tiiuserid, "Learner",
                                                get_string('downloadsubmission', 'turnitintooltwo')));
            }
        } else {
            $download = "--";
        }

        $refresh = '--';
        if (!empty($submission->id) && $istutor) {
            $refresh = html_writer::tag('div', html_writer::tag('i', '', array('class' => 'fa fa-refresh fa-lg',
                                                    'title' => get_string('turnitinrefreshsubmissions', 'turnitintooltwo'))).
                                                html_writer::tag('i', '', array('class' => 'fa fa-spinner fa-spin')),
                                                        array('class' => 'refresh_row',
                                                                'id' => 'refreshrow_'.$submission->submission_objectid.
                                                                    '_'.$partid.'_'.$moodleuserid));
        }

        // Delete Link.
        $delete = "--";
        if ($this->show_delete_link($istutor, $submission, $parts[$partid]->dtdue, $turnitintooltwoassignment->turnitintooltwo->allowlate)) {

            $confirmstring = (!empty($submission->id) && empty($submission->submission_objectid)) ? 'deleteconfirm' : 'turnitindeleteconfirm';
            $delete = html_writer::tag('div', html_writer::tag('i', '', array('title' => get_string('deletesubmission', 'turnitintooltwo'),
                                                'class' => 'fa fa-trash-o fa-lg')),
                                                array('class' => 'delete_paper',
                                                    'id' => 'delete_paperrow',
                                                    "data-confirm" => $confirmstring,
                                                    "data-paper" => $submission->id,
                                                    "data-part" => $partid,
                                                    "data-assignment" => $cm->instance
                                                ));
        }

        // The studentfirstname and studentlastname fields are for sorting only, and thus should not be present if the user is a student.
        if (!$istutor) {
            $data = array($partid, $checkbox, $studentname, $rawtitle, $title, $objectid, $rawmodified, $modified);
        } else {
            $data = array($partid, $checkbox, $studentlastname, $studentname, $rawtitle, $title, $objectid, $rawmodified, $modified);
        }

        if (($istutor) || (!$istutor && $turnitintooltwoassignment->turnitintooltwo->studentreports)) {
            $data[] = $rawscore;
            $data[] = $score;
        }
        if ($config->usegrademark) {
            $data[] = $rawgrade;
            $data[] = $grade;
            if (count($parts) > 1 || $turnitintooltwoassignment->turnitintooltwo->grade < 0) {
                $data[] = $overallgrade;
            }
        }
        if ($istutor) {
            $data[] = $studentread;
        }
        $data[] = $upload;
        $data[] = $download;
        if ($istutor) {
            $data[] = $refresh;
        }
        $data[] = $delete;

        if ($istutor) {
            $data[] = $submission->firstname;
        }

        return $data;
    }

    /**
     * Return whether the delete link should be shown.
     * @param boolean $istutor
     * @param object $submission
     * @param int $dtdue
     * @param boolean $allowlatesubmissions
     *
     * @return boolean
     */
    public function show_delete_link($istutor, $submission, $dtdue, $allowlatesubmissions) {

        if ($istutor && !empty($submission->id)) {
            return true;
        } else {
            if ((empty($submission->submission_objectid) && !empty($submission->id)
                && ((time() < $dtdue) || (time() >= $dtdue && $allowlatesubmissions == 1)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return submission inbox in a JSON array
     *
     * @param object $cm
     * @param object $turnitintooltwoassignment
     * @param int $partid
     * @return array inbox data
     */
    public function get_submission_inbox($cm, $turnitintooltwoassignment, $parts, $partid, $start = 0) {
        $useroverallgrades = array();

        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        if ($start == 0) {
            $submissions = $turnitintooltwoassignment->get_submissions($cm, $partid);
            $_SESSION["submissions"][$partid] = $submissions[$partid];
            $_SESSION["num_submissions"][$partid] = count($submissions[$partid]);
        }

        $submissiondata = array();

        $j = 0;

        // Unanonymise parts if necessary.
        foreach (array_keys($parts) as $part) {
            if ($parts[$part]->dtpost < time()) {
                $parts[$part]->unanon = 1;
            }
        }
        foreach ($_SESSION["submissions"][$partid] as $submission) {
            $data = $this->get_submission_inbox_row($cm, $turnitintooltwoassignment, $parts, $partid, $submission,
                                                        $useroverallgrades, $istutor);
            $submissiondata[] = $data;
            // Remove submission from session.
            unset($_SESSION["submissions"][$partid][$submission->userid]);
            $j++;

            if ($j == TURNITINTOOLTWO_SUBMISSION_GET_LIMIT) {
                break;
            }
        }

        return $submissiondata;
    }

    /**
     * Show the form to allow tutor to reveal the name of a student who has submitted
     * to an assignment that has anonymous marking enabled
     *
     * @return output
     */
    public function show_unanonymise_form() {
        $output = html_writer::tag("span", get_string('revealdesc', 'turnitintooltwo'), array("id" => "mod_turnitintooltwo_unanonymise_desc"));

        $elements = array();
        $elements[] = array('textarea', 'anonymous_reveal_reason', get_string('revealreason', 'turnitintooltwo'),
                                '', array(), 'required', get_string('revealerror', 'turnitintooltwo'), PARAM_TEXT);
        $elements[] = array('hidden', 'assignment_id', '');
        $elements[] = array('hidden', 'submission_id', '');
        $elements[] = array('button', 'reveal', get_string('reveal', 'turnitintooltwo'));

        $customdata["elements"] = $elements;
        $customdata["hide_submit"] = true;
        $customdata["disable_form_change_checker"] = true;
        $optionsform = new turnitintooltwo_form('', $customdata);

        return html_writer::tag('div', $output.$optionsform->display(), array('class' => 'mod_turnitintooltwo_unanonymise_form'));
    }

    /**
     * Return the output for a form to launch the document viewer, it is then submitted
     * on load via Javascript
     *
     * @param str $type the type of document viewer that needs to be opened
     * @param int $submissionid the Turnitin submission id
     * @param int $userid the Turnitin user id
     * @param str $userrole the role the user has on Turnitin in the course/class
     * @param str $buttonstring string for the submit button
     * @return output form
     */
    public static function output_dv_launch_form($type, $submissionid, $userid, $userrole,
                                                $buttonstring = "Submit", $ltireturn = false) {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
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
     * Return the output for a form to launch a zip or xls download, it is then submitted
     * on load via Javascript
     *
     * @param str $type the type of download that needs to be launched
     * @param int $partid the Turnitin id of the assignment part
     * @param int $userid the Turnitin user id
     * @param str $buttonstring string for the submit button
     * @return output form
     */
    public static function output_download_launch_form($type, $userid, $partid, $submissionids = array(),
                                                        $buttonstring = "Submit") {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Construct LTI Form Launcher.
        $lti = new TiiLTI();
        $lti->setAssignmentId($partid);
        $lti->setUserId($userid);
        $lti->setRole("Instructor");
        $lti->setButtonText($buttonstring);

        if (!empty($submissionids)) {
            $lti->setSubmissionIds($submissionids);
        }

        switch ($type) {
            case "orig_zip":
                $ltifunction = "outputDownloadZipForm";
                break;

            case "pdf_zip":
                $ltifunction = "outputDownloadPDFZipForm";
                $lti->setFormTarget('');
                break;

            case "xls_inbox":
                $ltifunction = "outputDownloadXLSForm";
                break;

            case "origchecked_zip":
                $ltifunction = "outputDownloadZipForm";
                break;

            case "gmpdf_zip":
                $ltifunction = "outputDownloadGradeMarkZipForm";
                $lti->setFormTarget('');
                break;

        }

        // Read the LTI launch form in the output buffer.
        ob_start();
        $turnitincall->$ltifunction($lti);
        $launchdownload = ob_get_contents();
        ob_end_clean();

        return $launchdownload;
    }

    /**
     * Return the output for a form to launch the relevant LTi function
     * It is then submitted on load via Javascript
     *
     * @param string $userrole either Instructor or Learner
     * @param int $userid
     * @return output form
     */
    public static function output_lti_form_launch($type, $userrole, $partid = 0, $classid = 0) {
        global $USER;
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $user = new turnitintooltwo_user($USER->id, $userrole);

        $lti = new TiiLTI();
        $lti->setUserId($user->tiiuserid);
        $lti->setRole($userrole);
        $lti->setFormTarget('');

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

    /**
     * Return a table containing all the assignments in the relevant course
     *
     * @global type $CFG
     * @global type $OUTPUT
     * @param obj $course the moodle course data
     * @return output
     */
    public function show_assignments($course) {
        global $CFG, $OUTPUT, $USER;

        $turnitintooltwos = turnitintooltwo_assignment::get_all_assignments_in_course($course);

        $table = new html_table();
        $table->id = "dataTable";
        $rows = array();

        // Do the table headers.
        $cells = array();
        if ($course->format == "weeks") {
            $cells["weeks"] = new html_table_cell(get_string("week"));
        } else if ($course->format == "topics") {
            $cells["topics"] = new html_table_cell(get_string("topic"));
        }
        $cells["name"] = new html_table_cell(get_string("name"));
        $cells["start_date"] = new html_table_cell(get_string("dtstart", "turnitintooltwo"));
        $cells["number_of_parts"] = new html_table_cell(get_string("numberofparts", "turnitintooltwo"));
        $cells["submissions"] = new html_table_cell(get_string("submissions", "turnitintooltwo"));
        $table->head = $cells;

        $i = 1;
        foreach ($turnitintooltwos as $turnitintooltwo) {

            $cm = get_coursemodule_from_id('turnitintooltwo', $turnitintooltwo->coursemodule, $course->id);
            $turnitintooltwoassignment = new turnitintooltwo_assignment($turnitintooltwo->id, $turnitintooltwo);

            if ($course->format == "weeks" || $course->format == "topics") {
                $cells[$course->format] = new html_table_cell($turnitintooltwoassignment->turnitintooltwo->section);
                $cells[$course->format]->attributes["class"] = "centered_cell";
            }

            // Show links dimmed if the mod is hidden.
            $attributes["class"] = (!$turnitintooltwo->visible) ? 'dimmed' : '';
            $linkurl = $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.
                            $turnitintooltwoassignment->turnitintooltwo->coursemodule.'&do=submissions';

            $cells["name"] = new html_table_cell(html_writer::link($linkurl, $turnitintooltwo->name, $attributes));
            $cells["start_date"] = new html_table_cell(userdate($turnitintooltwoassignment->get_start_date(),
                                                            get_string('strftimedatetimeshort', 'langconfig')));
            $cells["start_date"]->attributes["class"] = "centered_cell";

            $cells["number_of_parts"] = new html_table_cell(count($turnitintooltwoassignment->get_parts()));
            $cells["number_of_parts"]->attributes["class"] = "centered_cell";

            if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
                $noofsubmissions = $turnitintooltwoassignment->count_submissions($cm, 0);
            } else {
                $noofsubmissions = count($turnitintooltwoassignment->get_user_submissions($USER->id,
                                                        $turnitintooltwoassignment->turnitintooltwo->id));
            }
            $cells["submissions"] = new html_table_cell(html_writer::link($linkurl, $noofsubmissions, $attributes));
            $cells["submissions"]->attributes["class"] = "centered_cell";

            $rows[$i] = new html_table_row($cells);
            $i++;
        }

        $table->data = $rows;
        return $OUTPUT->box(html_writer::table($table), 'generalbox boxaligncenter');
    }

    /**
     * Initialise the table that will show a list of tutors or students that are enrolled on
     * a particular course. Accessible from the assignment summary screen.
     *
     * @global type $OUTPUT
     * @param type $role the user role to view a list of
     * @return output
     */
    public function init_tii_member_by_role_table($cm, $turnitintooltwoassignment, $role = "Learner") {
        global $OUTPUT;

        $rolestring = ($role == "Instructor") ? 'turnitintutors' : 'turnitinstudents';
        $cellheader = get_string($rolestring, 'turnitintooltwo');
        $output = "";

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {

            // Link to enrol all students on course.
            if ($role == "Learner") {
                $output .= $OUTPUT->box(get_string('errorenrollingall', 'turnitintooltwo'),
                                            'mod_turnitintooltwo_general_warning', 'enrolling_error');

                $enrollink = $OUTPUT->box($OUTPUT->pix_icon('enrolicon',
                                                    get_string('turnitinenrolstudents', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo')." ".
                                                        get_string('turnitinenrolstudents', 'turnitintooltwo'), 'enrol_link');

                $enrollingcontainer = $OUTPUT->box($OUTPUT->pix_icon('loader',
                                                    get_string('enrolling', 'turnitintooltwo'),
                                                    'mod_turnitintooltwo')." ".
                                                        get_string('enrolling', 'turnitintooltwo'), 'enrolling_container');

                $output .= $OUTPUT->box($enrollingcontainer.$enrollink, '');
            }

            // Output user role to hidden var for use in jQuery calls.
            $output .= $OUTPUT->box($role, '', 'user_role');
            $output .= $OUTPUT->box($turnitintooltwoassignment->turnitintooltwo->id, '', 'assignment_id');

            $table = new html_table();
            $table->attributes["class"] = "enrolledMembers";

            // Do the table headers.
            $cells = array();
            $cells[0] = new html_table_cell("&nbsp;");
            $cells[0]->attributes["class"] = "narrow";
            $cells[1] = new html_table_cell($cellheader);
            $table->head = $cells;

            $output = $OUTPUT->box($output.html_writer::table($table), 'generalbox boxaligncenter', 'members');
        }

        return $output;
    }

    /**
     * Show Tutors/Students enrolled on a particular course with Turnitin
     *
     * @global type $CFG
     * @global type $DB
     * @param type $cm course module data
     * @param type $turnitintooltwoassignment the assignment object
     * @param array $members of the course in Turnitin
     * @return array $memberdata in a format to be shown as rows in a datatable
     */
    public function get_tii_members_by_role($cm, $turnitintooltwoassignment, $members, $role = "Learner") {
        global $CFG, $DB;

        switch ($role) {
            case "Learner":
                $removestr = get_string('turnitinstudentsremove', 'turnitintooltwo');
                $removeaction = "removestudent";
                $do = "students";
                break;
            case "Instructor":
                $removestr = get_string('turnitintutorsremove', 'turnitintooltwo');
                $removeaction = "removetutor";
                $do = "tutors";
                break;
        }

        $memberdata = array();
        foreach ($members as $k => $v) {
            $membermoodleid = turnitintooltwo_user::get_moodle_user_id($k);
            if ($membermoodleid > 0) {
                $user = $DB->get_record('user', array('id' => $membermoodleid));

                $deleteurl = new moodle_url($CFG->wwwroot."/mod/turnitintooltwo/view.php",
                                            array('id' => $cm->id, 'do' => $do, 'sesskey' => sesskey(),
                                                'action' => $removeaction, 'membership_id' => $v['membership_id']));

                $attributes["onclick"] = 'return confirm(\''.$removestr.'\');';
                $link = html_writer::link($deleteurl, html_writer::tag('i', '', array('title' => get_string('deletesubmission', 'turnitintooltwo'),
                                                                                    'class' => 'fa fa-trash-o fa-lg')),
                                                                                                        $attributes);
                $userdetails = html_writer::link($CFG->wwwroot.'/user/view.php?id='.$membermoodleid.
                                                    '&course='.$turnitintooltwoassignment->turnitintooltwo->course,
                                                    fullname($user)).' ('.$user->email.')';
                $memberdata[] = array($link, $userdetails);
            }
        }

        return $memberdata;
    }

    /**
     * Show a form with a dropdown box to allow tutors who are enrolled in Moodle
     * on this course to be added to this course in Turnitin
     *
     * @global type $CFG
     * @global type $OUTPUT
     * @param obj $cm course module data
     * @param array $tutors tutors who are currently enrolled with Turnitin
     * @return output
     */
    public function show_add_tii_tutors_form($cm, $tutors) {
        global $CFG, $OUTPUT;

        $moodletutors = get_enrolled_users(context_module::instance($cm->id), 'mod/turnitintooltwo:grade', 0, 'u.id');

        // Populate elements array which will generate the form elements
        // Each element is in following format: (type, name, label, helptext (minus _help), options (if select).
        $elements = array();
        $elements[] = array('header', 'add_tii_tutors', get_string('turnitintutorsadd', 'turnitintooltwo'));

        $options = array();
        foreach ($moodletutors as $k => $v) {
            $availabletutor = new turnitintooltwo_user($v->id, "Instructor", false, "site", false);

            if (array_key_exists($availabletutor->id, $tutors)) {
                unset($moodletutors[$k]);
            } else {
                $options[$availabletutor->id] = $availabletutor->fullname.' ('.$availabletutor->email.')';
            }
        }

        if (count($options) == 0) {
            $elements[] = array('static', 'turnitintutors', get_string('turnitintutors', 'turnitintooltwo').": ",
                                    '', get_string('turnitintutorsallenrolled', 'turnitintooltwo'));
            $customdata["hide_submit"] = true;
        } else {
            $elements[] = array('select', 'turnitintutors', get_string('turnitintutors', 'turnitintooltwo'), '', $options);
            $elements[] = array('hidden', 'action', 'addtutor');
            $customdata["show_cancel"] = false;
            $customdata["submit_label"] = get_string('turnitintutorsadd', 'turnitintooltwo');

        }
        $customdata["elements"] = $elements;
        $form = new turnitintooltwo_form($CFG->wwwroot.'/mod/turnitintooltwo/view.php'.'?id='.$cm->id.'&do=tutors', $customdata);

        $output = $OUTPUT->box($form->display(), 'generalbox boxaligncenter', 'general');
        return $output;
    }

    /**
     * Check whether email nosubmitters is enabled, and return true if so.
     */
    private function is_nonsubmitter_emails_enabled() {
        global $CFG;

        $messageoutputs = get_config('message');

        if ($CFG->branch >= 400) {
            if (isset($messageoutputs->mod_turnitintooltwo_nonsubmitters_disable) && $messageoutputs->mod_turnitintooltwo_nonsubmitters_disable == "0") {
                return true;
            }
        } else {
            // Support for older versions.
            foreach ($messageoutputs as $k => $v) {
                if (strpos($k, '_mod_turnitintooltwo_nonsubmitters_loggedin') !== false) {
                    return true;
                }
            }
            return false;
        }
    }
}
