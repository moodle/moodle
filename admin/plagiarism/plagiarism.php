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
 * plagiarism.php - allows the admin to configure plagiarism stuff
 *
 * @package   administration
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');

    require_login();
    admin_externalpage_setup('plagiarism');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    require_once('plagiarism_form.php');
    $mform = new plagiarism_setup_form();

    if ($mform->is_cancelled()) {
        redirect('');
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('plagiarism', 'plagiarism'));

    if (($data = $mform->get_data()) && confirm_sesskey()) {
        if (!isset($data->turnitin_use)) {
            $data->turnitin_use = 0;
        }
        if (!isset($data->turnitin_enablegrademark)) {
            $data->turnitin_enablegrademark = 0;
        }
        if (!isset($data->turnitin_senduseremail)) {
            $data->turnitin_senduseremail = 0;
        }
        foreach ($data as $field=>$value) {
            if (strpos($field, 'turnitin')===0) {
                if ($tiiconfigfield = $DB->get_record('config_plugins', array('name'=>$field, 'plugin'=>'plagiarism'))) {
                    $tiiconfigfield->value = $value;
                    if (! $DB->update_record('config_plugins', $tiiconfigfield)) {
                        error("errorupdating");
                    }
                } else {
                    $tiiconfigfield = new stdClass();
                    $tiiconfigfield->value = $value;
                    $tiiconfigfield->plugin = 'plagiarism';
                    $tiiconfigfield->name = $field;
                    if (! $DB->insert_record('config_plugins', $tiiconfigfield)) {
                        error("errorinserting");
                    }
                }
            }
        }
        //now call TII settings to set up teacher account as set on this page.
            if ($plagiarismsettings = plagiarism_get_settings()) { //get tii settings.
                $tii = array();
                //set globals.
                $tii['username'] = $plagiarismsettings['turnitin_userid'];
                $tii['uem']      = $plagiarismsettings['turnitin_email'];
                $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
                $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
                $tii['uid']      = $plagiarismsettings['turnitin_userid'];
                $tii['utp']      = '2'; //2 = this user is an instructor
                $tii['cid']      = $plagiarismsettings['turnitin_courseprefix']; //course ID
                $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix']; //Course title.  -this uses Course->id and shortname to ensure uniqueness.
                //$tii['diagnostic'] = '1'; //debug only
                $tii['fcmd'] = '2'; //when set to 2 the TII API should return XML
                $tii['fid'] = '1'; //set command. - create user and login to Turnitin (fid=1)
                $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
                if (!empty($tiixml->rcode[0]) && $tiixml->rcode[0] == '11') {
                    notify(get_string('savedconfigsuccess', 'plagiarism'), 'notifysuccess');
                } else {
                    //disable turnitin as this config isn't correct.
                    $rec =  $DB->get_record('config_plugins', array('name'=>'turnitin_use', 'plugin'=>'plagiarism'));
                    $rec->value = 0;
                    $DB->update_record('config_plugins', $rec);
                    notify(get_string('savedconfigfailure', 'plagiarism'));
                }
            }
    }
    $plagiarismsettings = plagiarism_get_settings();
    $mform->set_data($plagiarismsettings);

    $currenttab='plagiarism';
    require_once('plagiarism_tabs.php');

    echo $OUTPUT->heading(get_string('tiiheading', 'plagiarism'), 3);
    if ($plagiarismsettings) {
        //Now show link to ADMIN tii interface - NOTE: this logs in the ADMIN user, should be hidden from normal teachers.
        $tii['uid']      = $plagiarismsettings['turnitin_userid'];
        $tii['username'] = $plagiarismsettings['turnitin_userid'];
        $tii['uem']      = $plagiarismsettings['turnitin_email'];
        $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
        $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
        $tii['utp']      = '2'; //2 = this user is an instructor
        $tii['utp'] = '3';
        $tii['fcmd'] = '1'; //when set to 2 this returns XML
        $tii['fid'] = '12'; //set commands - Administrator login/statistics.
        echo '<div align="center">';
        echo '<a href="'.turnitin_get_url($tii).'" target="_blank">'.get_string("adminlogin","plagiarism").'</a><br/>';
        $tii['utp'] = '2';
        $tii['fid'] = '1'; //set commands - Administrator login/statistics.
        echo '<a href="'.turnitin_get_url($tii).'" target="_blank">'.get_string("teacherlogin","plagiarism").'</a>';
        echo '</div>';
    }

    echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
