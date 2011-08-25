<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the script used to clone Moodle admin setting page.
 * It is used to create a new form used to pre-configure basiclti
 * activities
 *
 * @package blti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/blti/edit_form.php');
require_once($CFG->dirroot.'/mod/blti/locallib.php');

$section      = 'modsettingblti';
$return       = optional_param('return', '', PARAM_ALPHA);
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);
$action       = optional_param('action', null, PARAM_TEXT);
$id           = optional_param('id', null, PARAM_INT);
$useexisting  = optional_param('useexisting', null, PARAM_INT);
$definenew    = optional_param('definenew', null, PARAM_INT);

/// no guest autologin
require_login(0, false);
$url = new moodle_url('/mod/blti/typesettings.php');
$PAGE->set_url($url);

admin_externalpage_setup('managemodules'); // Hacky solution for printing the admin page

/// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

$statusmsg = '';
$errormsg  = '';
$focus = '';

if ($data = data_submitted() and confirm_sesskey() and isset($data->submitbutton)) {
    if (isset($id)) {
        $type = new StdClass();
        $type->id = $id;
        $type->name = $data->lti_typename;
        $type->rawname = preg_replace('/[^a-zA-Z]/', '', $type->name);
        if ($DB->update_record('blti_types', $type)) {
            unset ($data->lti_typename);
            //@TODO: update work
            foreach ($data as $key => $value) {
                if (substr($key, 0, 4)=='lti_' && !is_null($value)) {
                    $record = new StdClass();
                    $record->typeid = $id;
                    $record->name = substr($key, 4);
                    $record->value = $value;
                    if (blti_update_config($record)) {
                        $statusmsg = get_string('changessaved');
                    } else {
                        $errormsg = get_string('errorwithsettings', 'admin');
                    }
                }
            }

            // Update toolurl for all existing instances - it is the only common parameter
            // between configurations and instances
            $instances = $DB->get_records('blti', array('typeid' => $id));
            foreach ($instances as $instance) {
                if ($instance->toolurl != $data->lti_toolurl) {
                    $instance->toolurl = $data->lti_toolurl;
                    $DB->update_record('blti', $instance);
                }
            }
        }
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingblti");
        die;
    } else {
        $type = new StdClass();
        $type->name = $data->lti_typename;
        $type->rawname = preg_replace('/[^a-zA-Z]/', '', $type->name);
        if ($id = $DB->insert_record('blti_types', $type)) {
            if (!empty($data->lti_fix)) {
                $instance = $DB->get_record('blti', array('id' => $data->lti_fix));
                $instance->typeid = $id;
                $DB->update_record('blti', $instance);
            }
            unset ($data->lti_fix);

            unset ($data->lti_typename);
            foreach ($data as $key => $value) {
                if (substr($key, 0, 4)=='lti_' && !is_null($value)) {
                    $record = new StdClass();
                    $record->typeid = $id;
                    $record->name = substr($key, 4);
                    $record->value = $value;
                    if (blti_add_config($record)) {
                        $statusmsg = get_string('changessaved');
                    } else {
                        $errormsg = get_string('errorwithsettings', 'admin');
                    }
                }
            }
        } else {
            $errormsg = get_string('errorwithsettings', 'admin');
        }
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingblti");
        die;
    }
    if (empty($adminroot->errors)) {
        switch ($return) {
            case 'site':  redirect("$CFG->wwwroot/");
            case 'admin': redirect("$CFG->wwwroot/$CFG->admin/");
        }
    } else {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
        $focus = $firsterror->id;
    }
    $adminroot =& admin_get_root(true); //reload tree
    $page      =& $adminroot->locate($section);
}

if ($action == 'delete') {
    blti_delete_type($id);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingblti");
    die;
}

if (($action == 'fix') && isset($useexisting)) {
    $instance = $DB->get_record('blti', array('id' => $id));
    $instance->typeid = $useexisting;
    $DB->update_record('blti', $instance);
    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=modsettingblti");
    die;
}

/// print header stuff ------------------------------------------------------------
$PAGE->set_focuscontrol($focus);
if (empty($SITE->fullname)) {
    $PAGE->set_title($settingspage->visiblename);
    $PAGE->set_heading($settingspage->visiblename);

    $PAGE->navbar->add('Basic LTI Administration', $CFG->wwwroot.'/admin/settings.php?section=modsettingblti');

    echo $OUTPUT->header();

    echo $OUTPUT->box(get_string('configintrosite', 'admin'));

    if ($errormsg !== '') {
        echo $OUTPUT->notification($errormsg);

    } else if ($statusmsg !== '') {
        echo $OUTPUT->notification($statusmsg, 'notifysuccess');
    }

    // ---------------------------------------------------------------------------------------------------------------

    echo '<form action="typesettings.php" method="post" id="'.$id.'" >';
    echo '<div class="settingsform clearfix">';
    echo html_writer::input_hidden_params($PAGE->url);
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="hidden" name="return" value="'.$return.'" />';

    echo $settingspage->output_html();

    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges', 'admin').'" /></div>';

    echo '</div>';
    echo '</form>';

} else {
    if ($PAGE->user_allowed_editing()) {
        $url = clone($PAGE->url);
        if ($PAGE->user_is_editing()) {
            $caption = get_string('blockseditoff');
            $url->param('adminedit', 'off');
        } else {
            $caption = get_string('blocksediton');
            $url->param('adminedit', 'on');
        }
        $buttons = $OUTPUT->single_button($url, $caption, 'get');
    }

    $PAGE->set_title("$SITE->shortname: " . get_string('toolsetup', 'blti'));

    $PAGE->navbar->add('Basic LTI Administration', $CFG->wwwroot.'/admin/settings.php?section=modsettingblti');

    echo $OUTPUT->header();



    if ($errormsg !== '') {
        echo $OUTPUT->notification($errormsg);

    } else if ($statusmsg !== '') {
        echo $OUTPUT->notification($statusmsg, 'notifysuccess');
    }

    // ---------------------------------------------------------------------------------------------------------------
    echo $OUTPUT->heading(get_string('toolsetup', 'blti'));
    echo $OUTPUT->box_start('generalbox');
    if ($action == 'add') {
        $form = new mod_blti_edit_types_form();
        $form->display();
    } else if ($action == 'update') {
        $form = new mod_blti_edit_types_form('typessettings.php?id='.$id);
        $type = blti_get_type_type_config($id);
        $form->set_data($type);
        $form->display();
    } else if ($action == 'fix') {
        if (!isset($definenew) && !isset($useexisting)) {
            blti_fix_misconfigured_choice($id);
        } else if (isset($definenew)) {
            $form = new mod_blti_edit_types_form();
            $type = blti_get_type_config_from_instance($id);
            $form->set_data($type);
            $form->display();
        }
    }

    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
