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
 * Local plugin "QubitsCourse"
 *
 * @package   local_qubitscourse
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/local/qubitscourse/locallib.php');
require($CFG->dirroot."/local/qubitscourse/classes/qubits_assigncourses_form.php");
global $DB, $PAGE, $OUTPUT;
require_login();
$context = context_system::instance();
$siteid = optional_param('siteid', 0, PARAM_INT);    // Site id.
$returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.
$streditsitesettings = get_string("editsitesettings", "local_qubitssite");
$sitecourseurl =  new moodle_url("/local/qubitscourse/index.php", array("siteid" => $siteid));

if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
    // If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
    // Sesskey must be specified, and would be set by the form anyway.
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = $sitecourseurl;
    if ($returnto !== 0) {
        switch ($returnto) {
            case 'sitelisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
                break;
            case 'courselisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitscourse/index.php', array("siteid" => $siteid));
                break;
        }
    }
}

$PAGE->set_pagelayout('admin');
$pageparams = array();
$qubitssite = null;
$qubitssite_courses = new stdClass;
if ($siteid) {
    $qubitssite = $DB->get_record("local_qubits_sites", array('id' => $siteid));
    $pageparams = array('siteid' => $siteid);
    if($qubitssite) {
        $title = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $heading = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $pagedesc = $streditsitesettings;
        $qubitssite_courses->qubitssitename = $qubitssite->name;
        $qubitssite_courses->site_id = $siteid;
        $qubitssite_courses->id = 0;
        $qubitsdbcourses = $DB->get_record("local_qubits_course", array('site_id' => $siteid));
        if($qubitsdbcourses){
            $qubitssite_courses->id = $qubitsdbcourses->id;
            $qubitssite_courses->course_id = explode(",", $qubitsdbcourses->course_id);
        }
    } else {
        throw new \moodle_exception('invalidqubitssiteid');
    }
} else {
    throw new \moodle_exception('invalidqubitssiteid');
}

if ($returnto !== 0) {
    $pageparams['returnto'] = $returnto;
    if ($returnto === 'url' && $returnurl) {
        $pageparams['returnurl'] = $returnurl;
    }
}

$PAGE->set_url('/local/qubitscourse/assigncourses.php', $pageparams);
$PAGE->set_context($context);
$currenturl = new moodle_url('/local/qubitscourse/assigncourses.php?siteid=1', $pageparams);
// First create the form.
$args = array(
    'qubitssite_courses' => $qubitssite_courses,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'context' => $context
);

$editform = new qubits_assigncourses_form($currenturl->__toString(), $args);
if ($editform->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($sitecourseurl);
} else if ($data = $editform->get_data()) {
    $assignid = $data->id;
    $course_ids = $data->course_id;
    $courseids = (empty($course_ids)) ? "" : implode(",", $course_ids);
    $data->course_id = $courseids;
    if(empty($assignid)){
        local_qubitscourse_assign_courses($data);
    }else{
        local_qubitscourse_update_courses($data);
    }
    //echo "<pre>"; print_r($data); echo "</pre>"; exit;
    // Process data if submitted.
    redirect($returnurl);
}

$PAGE->set_title($title);
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($heading);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagedesc);

$editform->display();

echo $OUTPUT->footer();
