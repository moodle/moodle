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

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->dirroot.'/course/lib.php');

$id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
$a = optional_param('a', '', PARAM_INT);         // scorm ID
$organization = optional_param('organization', '', PARAM_INT); // organization ID
$action = optional_param('action', '', PARAM_ALPHA);
$preventskip = optional_param('preventskip', '', PARAM_INT); // Prevent Skip view, set by javascript redirects.

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('scorm', $id, 0, true)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else if (!empty($a)) {
    if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id, true)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

$url = new moodle_url('/mod/scorm/view.php', array('id'=>$cm->id));
if ($organization !== '') {
    $url->param('organization', $organization);
}
$PAGE->set_url($url);
$forcejs = get_config('scorm', 'forcejavascript');
if (!empty($forcejs)) {
    $PAGE->add_body_class('forcejavascript');
}

require_login($course, false, $cm);

$context = context_course::instance($course->id);
$contextmodule = context_module::instance($cm->id);

$launch = false; // Does this automatically trigger a launch based on skipview.
if (!empty($scorm->popup)) {
    $orgidentifier = '';
    $scoid = 0;
    if (empty($preventskip) && $scorm->skipview >= SCORM_SKIPVIEW_FIRST &&
        has_capability('mod/scorm:skipview', $contextmodule) &&
        !has_capability('mod/scorm:viewreport', $contextmodule)) { // Don't skip users with the capability to view reports.

        // do we launch immediately and redirect the parent back ?
        if ($scorm->skipview == SCORM_SKIPVIEW_ALWAYS || !scorm_has_tracks($scorm->id, $USER->id)) {
            $orgidentifier = '';
            if ($sco = scorm_get_sco($scorm->launch, SCO_ONLY)) {
                if (($sco->organization == '') && ($sco->launch == '')) {
                    $orgidentifier = $sco->identifier;
                } else {
                    $orgidentifier = $sco->organization;
                }
                $scoid = $sco->id;
            }
            $launch = true;
        }
    }
    // Redirect back to the section with one section per page ?

    $courseformat = course_get_format($course)->get_course();
    $sectionid = '';
    if (isset($courseformat->coursedisplay) && $courseformat->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
        $sectionid = $cm->sectionnum;
    }
    if ($courseformat->format == 'singleactivity') {
        $courseurl = $url->out(false, array('preventskip' => '1'));
    } else {
        $courseurl = course_get_url($course, $sectionid)->out(false);
    }

    $PAGE->requires->data_for_js('scormplayerdata', Array('launch' => $launch,
                                                           'currentorg' => $orgidentifier,
                                                           'sco' => $scoid,
                                                           'scorm' => $scorm->id,
                                                           'courseurl' => $courseurl,
                                                           'cwidth' => $scorm->width,
                                                           'cheight' => $scorm->height,
                                                           'popupoptions' => $scorm->options), true);
    $PAGE->requires->string_for_js('popupsblocked', 'scorm');
    $PAGE->requires->string_for_js('popuplaunched', 'scorm');
    $PAGE->requires->js('/mod/scorm/view.js', true);
}

if (isset($SESSION->scorm)) {
    unset($SESSION->scorm);
}

$strscorms = get_string("modulenameplural", "scorm");
$strscorm  = get_string("modulename", "scorm");

$shortname = format_string($course->shortname, true, array('context' => $context));
$pagetitle = strip_tags($shortname.': '.format_string($scorm->name));

// Trigger module viewed event.
$event = \mod_scorm\event\course_module_viewed::create(array(
    'objectid' => $scorm->id,
    'context' => $contextmodule,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('scorm', $scorm);
$event->add_record_snapshot('course_modules', $cm);
$event->trigger();

if (empty($preventskip) && empty($launch) && (has_capability('mod/scorm:skipview', $contextmodule))) {
    scorm_simple_play($scorm, $USER, $contextmodule, $cm->id);
}

//
// Print the page header
//
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($scorm->name));

if (!empty($action) && confirm_sesskey() && has_capability('mod/scorm:deleteownresponses', $contextmodule)) {
    if ($action == 'delete') {
        $confirmurl = new moodle_url($PAGE->url, array('action'=>'deleteconfirm'));
        echo $OUTPUT->confirm(get_string('deleteuserattemptcheck', 'scorm'), $confirmurl, $PAGE->url);
        echo $OUTPUT->footer();
        exit;
    } else if ($action == 'deleteconfirm') {
        //delete this users attempts.
        $DB->delete_records('scorm_scoes_track', array('userid' => $USER->id, 'scormid' => $scorm->id));
        scorm_update_grades($scorm, $USER->id, true);
        echo $OUTPUT->notification(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
    }
}

$currenttab = 'info';
require($CFG->dirroot . '/mod/scorm/tabs.php');

// Print the main part of the page
$attemptstatus = '';
if (empty($launch) && ($scorm->displayattemptstatus == SCORM_DISPLAY_ATTEMPTSTATUS_ALL ||
         $scorm->displayattemptstatus == SCORM_DISPLAY_ATTEMPTSTATUS_ENTRY)) {
    $attemptstatus = scorm_get_attempt_status($USER, $scorm, $cm);
}
echo $OUTPUT->box(format_module_intro('scorm', $scorm, $cm->id).$attemptstatus, 'generalbox boxaligncenter boxwidthwide', 'intro');

$scormopen = true;
$timenow = time();
if (!empty($scorm->timeopen) && $scorm->timeopen > $timenow) {
    echo $OUTPUT->box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "generalbox boxaligncenter");
    $scormopen = false;
}
if (!empty($scorm->timeclose) && $timenow > $scorm->timeclose) {
    echo $OUTPUT->box(get_string("expired", "scorm", userdate($scorm->timeclose)), "generalbox boxaligncenter");
    $scormopen = false;
}
if ($scormopen && empty($launch)) {
    scorm_view_display($USER, $scorm, 'view.php?id='.$cm->id, $cm);
}
if (!empty($forcejs)) {
    echo $OUTPUT->box(get_string("forcejavascriptmessage", "scorm"), "generalbox boxaligncenter forcejavascriptmessage");
}

if (!empty($scorm->popup)) {
    $PAGE->requires->js_init_call('M.mod_scormform.init');
}

echo $OUTPUT->footer();
