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

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_view.class.php');

$turnitinview = new turnitin_view();

$cssurl = new moodle_url('/plagiarism/turnitin/amd/src/datatables.css');
$PAGE->requires->css($cssurl);

require_login();
admin_externalpage_setup('plagiarismturnitin');
$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

$do = optional_param('do', "config", PARAM_ALPHA);
$filedate = optional_param('filedate', null, PARAM_ALPHANUMEXT);
$action = optional_param('action', "", PARAM_ALPHA);
$unlink = optional_param('unlink', null, PARAM_ALPHA);
$relink = optional_param('relink', null, PARAM_ALPHA);

if (isset($_SESSION["notice"])) {
    $notice = $_SESSION["notice"];
    $notice["type"] = (empty($_SESSION["notice"]["type"])) ? "general" : $_SESSION["notice"]["type"];
    unset($_SESSION["notice"]);
} else {
    $notice = null;
}

$plagiarismpluginturnitin = new plagiarism_plugin_turnitin();
$plugindefaults = $plagiarismpluginturnitin->get_settings();

// Save Settings.
if (!empty($action)) {
    switch ($action) {
        case "defaults":
            $fields = $plagiarismpluginturnitin->get_settings_fields();

            $settingsfields = array();
            foreach ($fields as $field) {
                array_push($settingsfields, $field);
                array_push($settingsfields, $field . '_lock');
            }
            array_push($settingsfields, 'plagiarism_locked_message');

            foreach ($settingsfields as $field) {
                $defaultfield = new stdClass();
                $defaultfield->cm = null;
                $defaultfield->name = $field;
                if ($field == 'plagiarism_locked_message') {
                    $defaultfield->value = optional_param($field, '', PARAM_TEXT);
                } else {
                    $defaultfield->value = optional_param($field, '', PARAM_ALPHANUMEXT);
                }

                if (isset($plugindefaults[$field])) {
                    $defaultfield->id = $DB->get_field('plagiarism_turnitin_config', 'id',
                                                (array('cm' => null, 'name' => $field)));
                    if (!$DB->update_record('plagiarism_turnitin_config', $defaultfield)) {
                        plagiarism_turnitin_print_error('defaultupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                    }
                } else {
                    $defaultfield->config_hash = $defaultfield->cm."_".$defaultfield->name;
                    if (!$DB->insert_record('plagiarism_turnitin_config', $defaultfield)) {
                        plagiarism_turnitin_print_error('defaultinserterror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                    }
                }
            }

            $_SESSION['notice']['message'] = get_string('defaultupdated', 'plagiarism_turnitin');
            redirect(new moodle_url('/plagiarism/turnitin/settings.php', array('do' => 'defaults')));
            exit;
            break;

        case "deletefile":
            $id = optional_param('id', 0, PARAM_INT);
            $DB->update_record('plagiarism_turnitin_files', array('id' => $id, 'statuscode' => "deleted"));
            redirect(new moodle_url('/plagiarism/turnitin/settings.php', array('do' => 'errors')));
            exit;
            break;
    }
}

// Include Javascript & CSS.
if ($do == "errors" || $do == "config" || $do == "unlinkusers") {
    $PAGE->requires->js_call_amd('plagiarism_turnitin/plugin_settings', 'pluginSettings');
    
    // Strings for JS.
    $PAGE->requires->string_for_js('connecttest', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('connecttestsuccess', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('connecttestfailed', 'plagiarism_turnitin');

    // Strings for js specifically for For data tables.
    $PAGE->requires->string_for_js('nointegration', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('sprevious', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('snext', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('sprocessing', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('szerorecords', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('sinfo', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('ssearch', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('slengthmenu', 'plagiarism_turnitin');
    $PAGE->requires->string_for_js('semptytable', 'plagiarism_turnitin');
}

if ($do != "savereport" && $do != "unlinkusers" && $do != "apilog" && $do != "activitylog") {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'plagiarism_turnitin'), '2', 'main');
}

switch ($do) {
    case "config":
        $turnitinview->draw_settings_tab_menu('turnitinsettings', $notice);

        require_once($CFG->dirroot.'/plagiarism/turnitin/classes/forms/turnitin_setupform.class.php');

        $tiisetupform = new turnitin_setupform();

        // Save posted form data.
        if (($data = $tiisetupform->get_data()) && confirm_sesskey()) {
            $tiisetupform->save($data);
            $output = $OUTPUT->notification(get_string('savesuccess', 'plagiarism_turnitin'), 'notifysuccess');
        }

        $pluginconfig = get_config('plagiarism_turnitin');
        $tiisetupform->set_data($pluginconfig);

        echo $tiisetupform->display();

        break;

    case "defaults":
        $turnitinview->draw_settings_tab_menu('turnitindefaults', $notice);

        require_once($CFG->dirroot.'/plagiarism/turnitin/classes/forms/turnitin_defaultsettingsform.class.php');

        $mform = new turnitin_defaultsettingsform($CFG->wwwroot.'/plagiarism/turnitin/settings.php?do=defaults');
        $mform->set_data($plugindefaults);
        $mform->display();
        break;

    case "apilog":
    case "activitylog":
        $logsdir = $CFG->tempdir . "/plagiarism_turnitin/logs/";
        $savefile = $do.'_'.$filedate.'.txt';
        $output = "";

        if (!is_null($filedate)) {
            header("Content-type: plain/text; charset=UTF-8");
            send_file( $logsdir.$savefile, $savefile, false );
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('pluginname', 'plagiarism_turnitin'), '2', 'main');
            $turnitinview->draw_settings_tab_menu('apilog', $notice);

            $label = 'apilog';
            $tabs[] = new tabobject( $label, $CFG->wwwroot.'/plagiarism/turnitin/settings.php?do='.$label,
                ucfirst( $label ), ucfirst( $label ), false );
            $label = 'activitylog';
            $tabs[] = new tabobject( $label, $CFG->wwwroot.'/plagiarism/turnitin/settings.php?do='.$label,
                ucfirst( $label ), ucfirst( $label ), false );
            $inactive = array($do);
            $selected = $do;
            $output .= "";
            // Get tabs output.
            ob_start();
            print_tabs(array($tabs), $selected, $inactive);
            $output .= ob_get_contents();
            ob_end_clean();

            if (file_exists($logsdir) && $readdir = opendir($logsdir)) {
                $i = 0;
                while ( false !== ($entry = readdir($readdir))) {
                    if (substr_count($entry, $do) > 0) {
                        $i++;
                        $split = preg_split("/_/", $entry);
                        $date = array_pop($split);
                        $date = str_replace('.txt', '', $date);
                        $output .= $OUTPUT->box(html_writer::link($CFG->wwwroot.'/plagiarism/turnitin/settings.php?'.
                            'do='.$do.'&filedate='.$date,
                            ucfirst($do).' ('.
                            userdate(strtotime($date), '%d/%m/%Y').')'), '');
                    }
                }
                if ($i == 0) {
                    $output .= get_string("nologsfound");
                }
            } else {
                $output .= get_string("nologsfound");
            }

            echo $output;
        }
        break;

    case "unlinkusers":
        $jsrequired = true;

        $userids = (isset($_REQUEST['userids'])) ? $_REQUEST["userids"] : array();
        $userids = clean_param_array($userids, PARAM_INT);

        // Relink users if form has been submitted.
        if ((!is_null($relink) || !is_null($unlink)) && isset($userids) && count($userids) > 0) {
            foreach ($userids as $tiiid) {
                $tuser = $DB->get_record('plagiarism_turnitin_users', array('id' => $tiiid));

                if ($muser = $DB->get_record('user', array('id' => $tuser->userid))) {
                    // Get the email address if the user has been deleted.
                    if (empty($muser->email) || strpos($muser->email, '@') === false) {
                        $split = explode('.', $muser->username);
                        array_pop($split);
                        $muser->email = join('.', $split);
                    }

                    // Unlink user from Turnitin.
                    $user = new turnitin_user(
                        $muser->id,
                        $role = null,
                        $enrol = null,
                        $workflowcontext = null,
                        $finduser = false
                    );
                    $user->unlink_user($tiiid);

                    // Relink user.
                    if (!is_null($relink)) {
                        // The user object will create user in Turnitin.
                        $user = new turnitin_user($muser->id);
                    }

                } else {
                    $DB->delete_records('plagiarism_turnitin_users', array('id' => $tiiid));
                }
            }
            redirect(new moodle_url('/plagiarism/turnitin/settings.php', array('do' => 'unlinkusers')));
            exit;
        }

        $output = html_writer::tag('h2', get_string('unlinkrelinkusers', 'plagiarism_turnitin'));

        $table = new html_table();
        $table->id = "unlinkUserTable";
        $rows = array();

        // Do the table headers.
        $cells = array();
        $cells[0] = new html_table_cell(html_writer::checkbox('selectallcb', 1, false));
        $cells[0]->attributes['class'] = 'centered_cell centered_cb_cell';
        $cells[0]->attributes['width'] = "100px";
        $cells['turnitinid'] = new html_table_cell(get_string('turnitinid', 'plagiarism_turnitin'));
        $cells['lastname'] = new html_table_cell(get_string('lastname'));
        $cells['firstname'] = new html_table_cell(get_string('firstname'));
        $string = "&nbsp;";
        if (!empty($config->plagiarism_turnitin_enablepseudo)) {
            $string = get_string('pseudoemailaddress', 'plagiarism_turnitin');
        }
        $cells['pseudoemail'] = new html_table_cell($string);

        $table->head = $cells;

        // Include table within form.
        $elements[] = array('html', html_writer::table($table));
        $customdata["elements"] = $elements;
        $customdata["hide_submit"] = true;

        $multisubmitbuttons = array(
            array('unlink', get_string('unlinkusers', 'plagiarism_turnitin')),
            array('relink', get_string('relinkusers', 'plagiarism_turnitin')));
        $customdata["multi_submit_buttons"] = $multisubmitbuttons;

        require_once($CFG->dirroot.'/plagiarism/turnitin/classes/forms/turnitin_form.class.php');
        $optionsform = new turnitin_form($CFG->wwwroot.'/plagiarism/turnitin/settings.php?do=unlinkusers',
            $customdata);

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('pluginname', 'plagiarism_turnitin'), 2, 'main');
        $turnitinview->draw_settings_tab_menu('unlinkusers', $notice);
        $output .= $optionsform->display();
        echo $output;
        break;

    case "errors":
        $page = optional_param('page', 0, PARAM_INT);
        $resubmitted = optional_param('resubmitted', '', PARAM_ALPHA);
        $turnitinview->draw_settings_tab_menu('turnitinerrors', $notice);
        echo html_writer::tag("p", get_string('pperrorsdesc', 'plagiarism_turnitin'));

        if ($resubmitted == "success") {
            echo html_writer::tag("div", get_string('pperrorssuccess', 'plagiarism_turnitin'),
                                            array('class' => 'pp_errors_success'));
        } else if ($resubmitted == "errors") {
            echo html_writer::tag("div", get_string('pperrorsfail', 'plagiarism_turnitin'),
                                            array('class' => 'pp_errors_warning'));
        }

        echo html_writer::tag("button", get_string('resubmitselected', 'plagiarism_turnitin'),
                                array("class" => "btn btn-primary pp-resubmit-files", "disabled" => "disabled"));

        echo $turnitinview->show_file_errors_table($page);

        echo html_writer::tag("button", get_string('resubmitselected', 'plagiarism_turnitin'),
                                array("class" => "btn btn-primary pp-resubmit-files", "disabled" => "disabled"));
        break;
}

echo $OUTPUT->footer();