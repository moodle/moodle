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
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once(__DIR__."/lib.php");
require_once(__DIR__."/turnitintooltwo_view.class.php");

admin_externalpage_setup('managemodules');

$turnitintooltwoview = new turnitintooltwo_view();
$turnitintooltwoview->load_page_components();

// Get/Set variables and work out which function to perform.
$cmd = optional_param('cmd', "", PARAM_ALPHANUMEXT);
$filedate = optional_param('filedate', null, PARAM_ALPHANUMEXT);
$unlink = optional_param('unlink', null, PARAM_ALPHA);
$relink = optional_param('relink', null, PARAM_ALPHA);
$module = $DB->get_record('modules', array('name' => "turnitintooltwo"));
$viewcontext = optional_param('view_context', "window", PARAM_ALPHAEXT);
$filetodelete = optional_param('file', '', PARAM_INT);
$filehashtodelete = optional_param('filehash', '', PARAM_ALPHANUM);

// Initialise variables.
$output = "";
$jsrequired = false;
$config = turnitintooltwo_admin_config();

switch ($cmd) {
    case "viewreport":
    case "savereport":
        raise_memory_limit(MEMORY_EXTRA);

        if ($cmd == 'viewreport') {

            $output .= "<pre>";
            $output .= "====== Turnitintool Data Dump Output ======\r\n\r\n";

        } else if ($cmd == 'savereport') {

            $filename = 'tii_datadump_'.$config->accountid.'_'.gmdate('dmYhm', time()).'.txt';
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="'.$filename.'"');

            $output .= "====== Turnitintool Data Dump File ======\r\n\r\n";
        }

        $tables = array('turnitintooltwo_users', 'turnitintooltwo_courses', 'turnitintooltwo',
                        'turnitintooltwo_parts', 'turnitintooltwo_submissions');

        // Get Moodle users.
        $moodleusers = $DB->get_records_sql('SELECT id, firstname, lastname
                                                   FROM {user}
                                                   WHERE id IN
                                                         (SELECT userid FROM {turnitintooltwo_users})');

        foreach ($tables as $table) {

            $output .= "== ".$table." ==\r\n\r\n";

            if ($data = $DB->get_records($table)) {

                $headers = array_keys($DB->get_columns($table));
                $columnwidth = 25;
                if ($table == 'turnitintooltwo') {
                    $columnwidth = 20;
                }

                $output .= str_pad('', (($columnwidth + 2) * count($headers)) + 1, "=");
                if ($table == 'turnitintooltwo_users') {
                    $output .= str_pad('', $columnwidth + 2, "=");
                }
                $output .= "\r\n";

                $output .= "|";
                foreach ($headers as $header) {
                    $output .= ' '.str_pad($header, $columnwidth, " ", 1).'|';
                }
                if ($table == 'turnitintooltwo_users') {
                    $output .= ' '.str_pad('Name', $columnwidth, " ", 1).'|';
                }
                $output .= "\r\n";

                $output .= str_pad('', (($columnwidth + 2) * count($headers)) + 1, "=");
                if ($table == 'turnitintooltwo_users') {
                    $output .= str_pad('', $columnwidth + 2, "=");
                }
                $output .= "\r\n";

                foreach ($data as $datarow) {
                    $datarow = get_object_vars($datarow);
                    $output .= "|";
                    foreach ($datarow as $datacell) {
                        $output .= ' '.htmlspecialchars(str_pad(substr($datacell ?? '', 0, $columnwidth), $columnwidth, " ", 1)).'|';
                    }
                    if ($table == 'turnitintooltwo_users' && $moodleusers[$datarow['userid']]) {
                        $firstname = format_string($moodleusers[$datarow['userid']]->firstname);
                        $lastname = format_string($moodleusers[$datarow['userid']]->lastname);
                        $output .= ' '.str_pad(substr($firstname.' '.$lastname, 0, $columnwidth),
                                                $columnwidth, " ", 1).'|';
                    }
                    $output .= "\r\n";
                }
                $output .= str_pad('', (($columnwidth + 2) * count($headers)) + 1, "-");
                if ($table == 'turnitintooltwo_users') {
                    $output .= str_pad('', $columnwidth + 2, "-");
                }
                $output .= "\r\n\r\n";
            } else {
                $output .= get_string('notavailableyet', 'turnitintooltwo')."\r\n";
            }

        }

        if ($cmd == 'viewreport') {
            $output .= "</pre>";
        } else if ($cmd == 'savereport') {
            echo $output;
            exit;
        }
        break;

    case "apilog":
    case "activitylog":
    case "perflog":

        $logsdir = $CFG->tempdir . "/turnitintooltwo/logs/";
        $savefile = $cmd.'_'.$filedate.'.txt';

        if (!is_null($filedate)) {
            header("Content-type: plain/text; charset=UTF-8");
            send_file( $logsdir.$savefile, $savefile, false );
        } else {

            $label = 'apilog';
            $tabs[] = new tabobject( $label, $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd='.$label,
                                        ucfirst( $label ), ucfirst( $label ), false );
            $label = 'activitylog';
            $tabs[] = new tabobject( $label, $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd='.$label,
                                        ucfirst( $label ), ucfirst( $label ), false );
            $label = 'perflog';
            $tabs[] = new tabobject( $label, $CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd='.$label,
                                        ucfirst( $label ), ucfirst( $label ), false );
            $inactive = array($cmd);
            $selected = $cmd;
            $output .= "";
            // Get tabs output.
            ob_start();
            print_tabs(array($tabs), $selected, $inactive);
            $output .= ob_get_contents();
            ob_end_clean();

            if (file_exists($logsdir) && $readdir = opendir($logsdir)) {
                $i = 0;
                while ( false !== ($entry = readdir($readdir))) {
                    if (substr_count($entry, $cmd) > 0) {
                        $i++;
                        $split = preg_split("/_/", $entry);
                        $date = array_pop($split);
                        $date = str_replace('.txt', '', $date);
                        $output .= $OUTPUT->box(html_writer::link($CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?'.
                                                                    'cmd='.$cmd.'&filedate='.$date,
                                                                    ucfirst($cmd).' ('.
                                                                        userdate(strtotime($date), '%d/%m/%Y').')'), '');
                    }
                }
                if ($i == 0) {
                    $output .= get_string("nologsfound");
                }
            } else {
                $output .= get_string("nologsfound");
            }
        }
        break;

    case "unlinkusers":
        $jsrequired = true;

        $userids = (isset($_REQUEST['userids'])) ? $_REQUEST["userids"] : array();
        $userids = clean_param_array($userids, PARAM_INT);

        // Relink users if form has been submitted.
        if ((!is_null($relink) || !is_null($unlink)) && isset($userids) && count($userids) > 0) {
            foreach ($userids as $tiiid) {
                $tuser = $DB->get_record('turnitintooltwo_users', array('id' => $tiiid));

                if ($muser = $DB->get_record('user', array('id' => $tuser->userid))) {
                    // Get the email address if the user has been deleted.
                    if (empty($muser->email) || strpos($muser->email, '@') === false) {
                        $split = explode('.', $muser->username);
                        array_pop($split);
                        $muser->email = join('.', $split);
                    }

                    // Unlink user from Turnitin.
                    $user = new turnitintooltwo_user(
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
                        $user = new turnitintooltwo_user($muser->id);
                    }

                } else {
                    $DB->delete_records('turnitintooltwo_users', array('id' => $tiiid));
                }
            }
            redirect(new moodle_url('/mod/turnitintooltwo/settings_extras.php', array('cmd' => 'unlinkusers')));
            exit;
        }

        $output .= html_writer::tag('h2', get_string('unlinkrelinkusers', 'turnitintooltwo'));

        $table = new html_table();
        $table->id = "unlinkUserTable";
        $rows = array();

        // Do the table headers.
        $cells = array();
        $cells[0] = new html_table_cell(html_writer::checkbox('selectallcb', 1, false));
        $cells[0]->attributes['class'] = 'centered_cell centered_cb_cell';
        $cells[0]->attributes['width'] = "100px";
        $cells['turnitinid'] = new html_table_cell(get_string('turnitinid', 'turnitintooltwo'));
        $cells['lastname'] = new html_table_cell(get_string('lastname'));
        $cells['firstname'] = new html_table_cell(get_string('firstname'));
        $string = "&nbsp;";
        if (!empty($config->enablepseudo)) {
            $string = get_string('pseudoemailaddress', 'turnitintooltwo');
        }
        $cells['pseudoemail'] = new html_table_cell($string);

        $table->head = $cells;

        // Include table within form.
        $elements[] = array('html', html_writer::table($table));
        $customdata["elements"] = $elements;
        $customdata["hide_submit"] = true;

        $multisubmitbuttons = array(
                                array('unlink', get_string('unlinkusers', 'turnitintooltwo')),
                                array('relink', get_string('relinkusers', 'turnitintooltwo')));
        $customdata["multi_submit_buttons"] = $multisubmitbuttons;

        $optionsform = new turnitintooltwo_form($CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=unlinkusers',
                                                    $customdata);

        $output .= $optionsform->display();
        break;

    case "files":
        if (!empty($filetodelete) && !empty($filehashtodelete)) {
            $DB->delete_records_select('files', ' id = ? AND pathnamehash = ? ', array($filetodelete, $filehashtodelete));
        }

        $jsrequired = true;

        $output .= html_writer::tag('h2', get_string('filebrowser', 'turnitintooltwo'));

        $table = new html_table();
        $table->id = "filesTable";
        $rows = array();

        // Do the table headers.
        $cells = array();
        $cells[0] = new html_table_cell("&nbsp;");
        $cells[1] = new html_table_cell("&nbsp;");
        $cells[2] = new html_table_cell("&nbsp;");
        $cells[3] = new html_table_cell(get_string('filename', 'turnitintooltwo'));
        $cells[4] = new html_table_cell("&nbsp;");
        $cells[5] = new html_table_cell(get_string('user', 'turnitintooltwo'));
        $cells[6] = new html_table_cell("&nbsp;");
        $cells[7] = new html_table_cell(get_string('created', 'turnitintooltwo'));
        $cells[8] = new html_table_cell("&nbsp;");

        $table->head = $cells;
        $output .= $OUTPUT->box(html_writer::table($table), '');

        break;

    case "courses":
        $jsrequired = true;

        $output .= html_writer::tag('h2', get_string('restorationheader', 'turnitintooltwo'));
        $output .= html_writer::tag('p', get_string('coursebrowserdesc', 'turnitintooltwo'));

        $coursesearchform = html_writer::label(get_string('coursetitle', 'turnitintooltwo').': ', 'search_course_title');
        $coursesearchform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'search_course_title',
                                                                        'name' => 'search_course_title'));

        $coursesearchform .= html_writer::label(get_string('integration', 'turnitintooltwo').': ', 'search_course_integration');
        $coursesearchform .= html_writer::select(turnitintooltwo_get_integration_ids(), 'search_course_integration', '', array('' => 'choosedots'),
                                                                array('id' => 'search_course_integration'));

        $coursesearchform .= html_writer::label(get_string('ced', 'turnitintooltwo').': ', 'search_course_end_date');
        $coursesearchform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'search_course_end_date',
                                                                        'name' => 'search_course_end_date'));

        $coursesearchform .= html_writer::tag('button', get_string('searchcourses', 'turnitintooltwo'),
                                                array("id" => "search_courses_button"));

        $output .= $OUTPUT->box($coursesearchform, 'generalbox', 'course_search_options');

        $displaylist = core_course_category::make_categories_list('');

        $categoryselectlabel = html_writer::label(get_string('selectcoursecategory', 'turnitintooltwo'),
                                                    'create_course_category');
        $categoryselect = html_writer::select($displaylist, 'create_course_category', '', array(),
                                                    array('class' => 'create_course_category'));

        $createassigncheckbox = html_writer::checkbox('create_assign', 1, false,
                                                get_string('createmoodleassignments', 'turnitintooltwo'),
                                                array("class" => "create_assignment_checkbox"));
        $createassign = html_writer::tag('div', $createassigncheckbox, array("class" => "create_assign_checkbox_container"));

        $createbutton = html_writer::tag('button', get_string('createmoodlecourses', 'turnitintooltwo'),
                                            array("id" => "create_classes_button"));
        $output .= $OUTPUT->box($categoryselectlabel." ".$categoryselect.$createassign.$createbutton,
                                    'create_checkboxes');

        $table = new html_table();
        $table->id = "mod_turnitintooltwo_course_browser_table";
        $rows = array();

        // Make up json array for drop down in table.
        $integrationidsjson = array();
        foreach (turnitintooltwo_get_integration_ids() as $k => $v) {
            $integrationidsjson[] = array('value' => $k, 'label' => $v);
        }
        $output .= html_writer::script('var integration_ids = '.json_encode($integrationidsjson));

        // Do the table headers.
        $cells = array();
        $cells[0] = new html_table_cell(html_writer::checkbox('selectallcb', 1, false));
        $cells[0]->attributes['class'] = 'centered_cell';
        $cells[1] = new html_table_cell(get_string('coursetitle', 'turnitintooltwo'));
        $cells[2] = new html_table_cell(get_string('integration', 'turnitintooltwo'));
        $cells[3] = new html_table_cell(get_string('ced', 'turnitintooltwo'));
        $cells[4] = new html_table_cell(get_string('turnitinid', 'turnitintooltwo'));
        $cells[5] = new html_table_cell(get_string('moodlelinked', 'turnitintooltwo'));
        $cells[6] = new html_table_cell('&nbsp;');

        $table->head = $cells;
        $output .= $OUTPUT->box(html_writer::table($table), '');

        $output .= turnitintooltwo_show_edit_course_end_date_form();

        break;

    case "multiple_class_recreation":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $PAGE->set_pagelayout('embedded');

        $assignments = optional_param('assignments', 0, PARAM_INT);
        $category = optional_param('category', 0, PARAM_INT);
        $classids = '';
        foreach ($_REQUEST as $k => $v) {
            if (strstr($k, "class_id") !== false) {
                $classids .= (int)$v.', ';
            }
        }
        $classids = substr($classids, 0, -2);

        $output = html_writer::tag('div', get_string('recreatemulticlasses', 'turnitintooltwo'),
                                            array('class' => 'course_creation_bulk_msg centered_div'));
        $output .= $OUTPUT->box($category, '', 'course_category');
        $output .= $OUTPUT->box($assignments, '', 'create_assignments');
        $output .= $OUTPUT->box($classids, '', 'class_ids');

        $output .= html_writer::tag('div', $OUTPUT->pix_icon('loader',
                                        get_string('recreatemulticlasses', 'turnitintooltwo'), 'mod_turnitintooltwo'),
                                        array('id' => 'course_creation_status', 'class' => 'centered_div'));
        break;

    case "class_recreation":
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey', 'error');
        }

        $PAGE->set_pagelayout('embedded');

        $tiicourseid = optional_param('id', 0, PARAM_INT);

        $output = "";
        $turnitincourse = $DB->get_records_sql("
                            SELECT tc.turnitin_cid
                            FROM {turnitintooltwo_courses} tc
                            RIGHT JOIN {course} c ON c.id = tc.courseid
                            WHERE tc.turnitin_cid = ? ", array($tiicourseid)
                        );

        if (empty($turnitincourse)) {
            $output .= turnitintooltwo_show_browser_new_course_form();
            $output .= turnitintooltwo_show_browser_link_course_form();
        }
        $output .= turnitintooltwo_init_browser_assignment_table($tiicourseid);
        break;

    case "v1migration":

        include_once("classes/v1migration/v1migration.php");

        $html = "";
        $msg = optional_param('msg', "", PARAM_ALPHA);
        $type = optional_param('type', "", PARAM_ALPHA);
        $migration_activation = optional_param('activation', '', PARAM_ALPHA);

        $migration_message = '';
        if ($migration_activation == 'success') {
            $close = html_writer::tag('button', '&times;', array('class' => 'close', 'data-dismiss' => 'alert'));
            $migration_message = html_writer::tag(
                'div',
                $close.get_string('migrationactivationsuccess', 'turnitintooltwo'),
                array('class' => 'alert alert-success', 'role' => 'alert')
            );
        }
        $html .= $migration_message;
        // Save Migration Tool enabled status.
        $alert = "";
        if ( isset($_REQUEST['enablemigrationtool']) ) {
            $saved = v1migration::togglemigrationstatus( (int)$_REQUEST['enablemigrationtool'] );
            $type = ($saved) ? 'success' : 'error';

            $urlparams = array('cmd' => 'v1migration', 'msg' => 'setting', 'type' => $type);
            redirect(new moodle_url('/mod/turnitintooltwo/settings_extras.php', $urlparams));
            exit;
        }

        // Show successful delete message if applicable.
        if ($msg == 'setting') {
            $string = ($type == "success") ? 'enablemigrationtoolsuccess' : 'enablemigrationtoolfail';

            $close = html_writer::tag('button', '&times;', array('class' => 'close', 'data-dismiss' => 'alert'));
            $alert = html_writer::tag('div', $close.get_string($string, 'turnitintooltwo'),
                            array('class' => 'alert alert-'.$type, 'role' => 'alert'));
        }

        // If v1 and v2 accounts are different then disable form elements.
        $enabled = v1migration::check_account_ids();

        // Output the form to enable the v1 migration.
        $html .= v1migration::output_settings_form($enabled);

        $html .= html_writer::tag('hr', '');
        $html .= html_writer::tag('h2', get_string('migration_status', 'turnitintooltwo'), array('class' => 'migrationheader'));

        $html .= html_writer::tag('p', get_string('migrationtoolv1list', 'turnitintooltwo', '2018031201'));

        $jsrequired = true;

        $assignmentids = (isset($_REQUEST['assignmentids'])) ? $_REQUEST["assignmentids"] : array();
        $assignmentids = clean_param_array($assignmentids, PARAM_INT);

        // Delete assignments if the form has been submitted.
        if (isset($assignmentids) && count($assignmentids) > 0) {
            foreach ($assignmentids as $assignmentid) {
                v1migration::delete_migrated_assignment($assignmentid);
            }

            $urlparams = array('cmd' => 'v1migration', 'msg' => 'delete', 'type' => 'success');
            redirect(new moodle_url('/mod/turnitintooltwo/settings_extras.php', $urlparams));
            exit;
        }

        // Show successful delete message if applicable.
        if ($msg == 'delete') {
            $close = html_writer::tag('button', '&times;', array('class' => 'close', 'data-dismiss' => 'alert'));
            $alert = html_writer::tag('div', $close.get_string("v1assignmentsdeleted", 'turnitintooltwo'),
                        array('class' => 'alert alert-success', 'role' => 'alert'));
        }

        $table = new html_table();
        $table->id = "migrationTable";
        $rows = array();

        // Do the table headers.
        $cells = array();
        $checkbox = html_writer::checkbox('selectallcb', 1, false, '', array('title' => get_string('migrationselectall', 'turnitintooltwo')));
        $cells[0] = new html_table_cell($checkbox);
        $cells[0]->attributes['class'] = 'centered_cell centered_cb_cell';
        $cells['assignmentid'] = new html_table_cell(get_string('assignmentid', 'turnitintooltwo'));
        $cells['title'] = new html_table_cell(get_string('migrationassignmenttitle', 'turnitintooltwo'));
        $cells['migrationstatus'] = new html_table_cell(get_string('hasmigrated', 'turnitintooltwo'));

        // Set the header widths. Title can take up the remainder.
        $cells[0]->attributes['width'] = "100px";
        $cells['assignmentid']->attributes['width'] = "150px";
        $cells['migrationstatus']->attributes['width'] = "100px";

        $table->head = $cells;

        $elements2[] = array('html', html_writer::table($table));
        $customdata2["elements"] = $elements2;
        $customdata2["show_cancel"] = false;
        $customdata2["disable_form_change_checker"] = true;
        $customdata2["submit_label"] = get_string('delete_selected', 'turnitintooltwo');

        $optionsform = new turnitintooltwo_form($CFG->wwwroot.'/mod/turnitintooltwo/settings_extras.php?cmd=v1migration', $customdata2);

        $html .= html_writer::tag('div', $optionsform->display(), array('id' => 'migration-delete-selected'));

        $output .= $alert . $html;

        break;
}

// Build page.
echo $OUTPUT->header();

echo html_writer::start_tag('div', array('class' => 'mod_turnitintooltwo'));
echo html_writer::tag("div", $viewcontext, array("id" => "view_context"));
if ($cmd != 'class_recreation' && $cmd != 'multiple_class_recreation') {
    echo $OUTPUT->heading(get_string('pluginname', 'turnitintooltwo'), 2, 'main');
    echo $OUTPUT->box($turnitintooltwoview->draw_settings_menu($cmd), '');
    // Show a warning if javascript is not enabled while a tutor is logged in.
    echo html_writer::tag('noscript', get_string('noscript', 'turnitintooltwo'), array("class" => "warning"));
}

$class = ($jsrequired) ? " js_required" : "";
if ($cmd == 'viewreport') {
    echo $OUTPUT->box($output, 'generalbox scrollbox'.$class);
} else if ($cmd == 'class_recreation') {
    echo $OUTPUT->box($output, 'generalbox class_recreation');
} else if ($cmd == 'multiple_class_recreation') {
    echo $output;
} else {
    echo $OUTPUT->box($output, 'generalbox'.$class);
}

echo html_writer::end_tag("div");
echo $OUTPUT->footer();
