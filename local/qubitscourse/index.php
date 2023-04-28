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
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/local/qubitscourse/locallib.php');
require_once($CFG->dirroot . '/local/qubitscourse/renderer.php');
require_once($CFG->dirroot . '/cohort/externallib.php');
require_login();
$context = context_system::instance();
$qbitcourserenderer = $PAGE->get_renderer('local_qubitscourse');
$siteid = optional_param('siteid', 0, PARAM_INT);    // Site id.
$returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.

if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
    // If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
    // Sesskey must be specified, and would be set by the form anyway.
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
    if ($returnto !== 0) {
        switch ($returnto) {
            case 'sitelisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
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
    $pageparams = array('siteid' =>$siteid);
    if($qubitssite) {
        $title = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $heading = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $pagedesc = get_string("viewsitecourses", "local_qubitscourse", $qubitssite->name);
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
}else{
	throw new \moodle_exception('invalidqubitssiteid');
}

if ($returnto !== 0) {
    $pageparams['returnto'] = $returnto;
    if ($returnto === 'url' && $returnurl) {
        $pageparams['returnurl'] = $returnurl;
    }
}

// Course filters
$filters["search"] = optional_param('search', '', PARAM_RAW);
$filters["page"] = optional_param('page', 0, PARAM_INT);
$filters["perpage"] = optional_param('perpage', 10, PARAM_INT);
$filters["sortcolumn"] = optional_param('sortcolumn', "", PARAM_TEXT);
$filters["sortdir"] = optional_param('sortdir', "asc", PARAM_TEXT);

$acohortmembers = core_cohort_external::get_cohort_members(array($qubitssite->cohortid));
$cohortmembers = reset($acohortmembers);
$cohortusers = $cohortmembers["userids"];

$PAGE->set_url('/local/qubitscourse/index.php', $pageparams);
$PAGE->set_context($context);
// TODO: Have to site based course list
$PAGE->set_title($title);
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($heading);
//$PAGE->requires->js_call_amd('local_qubitscourse/modal_login');
$PAGE->requires->js_call_amd('local_qubitscourse/modal_enrolusers');
$content = $qbitcourserenderer->tenant_courses($qubitssite_courses, $filters);
echo $OUTPUT->header();
echo $OUTPUT->heading($pagedesc);

echo $content;

echo $OUTPUT->footer();