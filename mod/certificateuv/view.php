<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Handles viewing a certificate
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("$CFG->dirroot/mod/certificateuv/locallib.php");
require_once("$CFG->dirroot/mod/certificateuv/deprecatedlib.php");
require_once("$CFG->libdir/pdflib.php");

$id = required_param('id', PARAM_INT);    // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA);
$edit = optional_param('edit', -1, PARAM_BOOL);

if (!$cm = get_coursemodule_from_id('certificateuv', $id)) {
    print_error('Course Module ID was incorrect');
}
if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    print_error('course is misconfigured');
}
if (!$certificate = $DB->get_record('certificateuv', array('id'=> $cm->instance))) {
    print_error('course module is incorrect');
}

$certificate->nameteacher = "Teacher testing";

print_r($certificate);

require_login($course, false, $cm);
$context = context_module::instance($cm->id);

require_capability('mod/certificateuv:view', $context);

$event = \mod_certificateuv\event\course_module_viewed::create(array(
    'objectid' => $certificate->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('certificateuv', $certificate);
$event->trigger();

$completion=new completion_info($course);
$completion->set_module_viewed($cm);

// Initialize $PAGE, compute blocks
$PAGE->set_url('/mod/certificateuv/view.php', array('id' => $cm->id));
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title(format_string($certificate->name));
$PAGE->set_heading(format_string($course->fullname));

if (($edit != -1) and $PAGE->user_allowed_editing()) {
     $USER->editing = $edit;
}

// Add block editing button
if ($PAGE->user_allowed_editing()) {
    $editvalue = $PAGE->user_is_editing() ? 'off' : 'on';
    $strsubmit = $PAGE->user_is_editing() ? get_string('blockseditoff') : get_string('blocksediton');
    $url = new moodle_url($CFG->wwwroot . '/mod/certificateuv/view.php', array('id' => $cm->id, 'edit' => $editvalue));
    $PAGE->set_button($OUTPUT->single_button($url, $strsubmit));
}

// Check if the user can view the certificate
if ($certificate->requiredtime && !has_capability('mod/certificateuv:manage', $context)) {
    if (certificateuv_get_course_time($course->id) < ($certificate->requiredtime * 60)) {
        $a = new stdClass;
        $a->requiredtime = $certificate->requiredtime;
        notice(get_string('requiredtimenotmet', 'certificateuv', $a), "$CFG->wwwroot/course/view.php?id=$course->id");
        die;
    }
}

// Create new certificate record, or return existing record
$certrecord = certificateuv_get_issue($course, $USER, $certificate, $cm);

make_cache_directory('tcpdf');

// Load the specific certificate type.
require("$CFG->dirroot/mod/certificateuv/type/$certificate->certificatetype/certificate.php");

if (empty($action)) { // Not displaying PDF
    echo $OUTPUT->header();

    $viewurl = new moodle_url('/mod/certificateuv/view.php', array('id' => $cm->id));
    groups_print_activity_menu($cm, $viewurl);
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

    if (has_capability('mod/certificateuv:manage', $context)) {
        $numusers = count(certificateuv_get_issues($certificate->id, 'ci.timecreated ASC', $groupmode, $cm));
        $url = html_writer::tag('a', get_string('viewcertificateviews', 'certificateuv', $numusers),
            array('href' => $CFG->wwwroot . '/mod/certificateuv/report.php?id=' . $cm->id));
        echo html_writer::tag('div', $url, array('class' => 'reportlink'));
    }

    if (!empty($certificate->intro)) {
        echo $OUTPUT->box(format_module_intro('certificateuv', $certificate, $cm->id), 'generalbox', 'intro');
    }

    if ($certificate->delivery == 0)    {
        $str = get_string('openwindow', 'certificateuv');
    } elseif ($certificate->delivery == 1)    {
        $str = get_string('opendownload', 'certificateuv');
    } elseif ($certificate->delivery == 2)    {
        $str = get_string('openemail', 'certificateuv');
    }
    
    $linkname = get_string('getcertificate', 'certificateuv');

    $link = new moodle_url('/mod/certificateuv/view.php?id='.$cm->id.'&action=get');
    $button = new single_button($link, $linkname);
    if ($certificate->delivery != 1) {
        $button->add_action(new popup_action('click', $link, 'view' . $cm->id, array('height' => 600, 'width' => 800)));
    }

    if(certificateuv_course_permission($cm->course)){

            $disponibilidad_certificado = certificateuv_get_permission_user($USER->id, $certificate->id);

            if ($disponibilidad_certificado){
            	if ($attempts = certificateuv_get_attempts($certificate->id)) {
                echo certificateuv_print_attempts($course, $certificate, $attempts);
            	}

        		echo html_writer::tag('p', $str, array('style' => 'text-align:center'));
            	echo html_writer::tag('div', $OUTPUT->render($button), array('style' => 'text-align:center'));	
            }
            else {
            	echo html_writer::tag('h1', "Lo sentimos, usted no puede imprimir el certificado", array('style' => 'text-align:center','class' =>  'panel panel-warning'));
            }
            
           

            //Averiguamos permisos como profesor
            $context = context_module::instance($cm->id);

            $roles = get_user_roles($context, $USER->id, true);

            foreach ($roles as $role) { 
                
                //role profesor
                if($role->roleid == 3){
                    //añadir button de asignar permisos
                    $link = new moodle_url('/mod/certificateuv/user.php?id='.$cm->id);
                    $button = new single_button($link, "Asignar Permisos");
                    echo html_writer::tag('div', $OUTPUT->render($button), array('style' => 'text-align:center'));
                    break;        
                }
            }
    }
    else{
        echo html_writer::tag('h1', "Este curso no posee autorización por parte de la Dintev para exportar certificados", array('style' => 'text-align:center','class' =>  'panel panel-warning'));
    }
    echo $OUTPUT->footer($course);
    exit;
} else { // Output to pdf

    // No debugging here, sorry.
    // $CFG->debugdisplay = 0;
    // @ini_set('display_errors', '0');
    // @ini_set('log_errors', '1');

    $filename = certificateuv_get_certificate_filename($certificate, $cm, $course) . '.pdf';

    // PDF contents are now in $file_contents as a string.
    $filecontents = $pdf->Output('', 'S');

    if ($certificate->savecert == 1) {
        certificateuv_save_pdf($filecontents, $certrecord->id, $filename, $context->id);
    }

    if ($certificate->delivery == 0) {
        // Open in browser.
        send_file($filecontents, $filename, 0, 0, true, false, 'application/pdf');
    } elseif ($certificate->delivery == 1) {
        // Force download.
        send_file($filecontents, $filename, 0, 0, true, true, 'application/pdf');
    } elseif ($certificate->delivery == 2) {
        certificateuv_email_student($course, $certificate, $certrecord, $context, $filecontents, $filename);
        // Open in browser after sending email.
        // send_file($filecontents, $filename, 0, 0, true, false, 'application/pdf');
    }
}
