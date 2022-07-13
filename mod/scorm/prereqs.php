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

// This page is called via AJAX to repopulte the TOC when LMSFinish() is called.

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

$id = optional_param('id', '', PARAM_INT);                  // Course Module ID, or
$a = optional_param('a', '', PARAM_INT);                    // scorm ID
$scoid = required_param('scoid', PARAM_INT);                // sco ID
$attempt = required_param('attempt', PARAM_INT);            // attempt number
$mode = optional_param('mode', 'normal', PARAM_ALPHA);      // navigation mode
$currentorg = optional_param('currentorg', '', PARAM_RAW);  // selected organization.

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('scorm', $id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        throw new \moodle_exception('coursemisconf');
    }
    if (! $scorm = $DB->get_record("scorm", array("id" => $cm->instance))) {
        throw new \moodle_exception('invalidcoursemodule');
    }
} else if (!empty($a)) {
    if (! $scorm = $DB->get_record("scorm", array("id" => $a))) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $scorm->course))) {
        throw new \moodle_exception('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
} else {
    throw new \moodle_exception('missingparameter');
}

// PARAM_RAW is used for $currentorg, validate it against records stored in the table.
if (!empty($currentorg)) {
    if (!$DB->record_exists('scorm_scoes', array('scorm' => $scorm->id, 'identifier' => $currentorg))) {
        $currentorg = '';
    }
}

$PAGE->set_url('/mod/scorm/prereqs.php', array('scoid' => $scoid, 'attempt' => $attempt, 'id' => $cm->id));

require_login($course, false, $cm);

$scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe.
if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
    $scorm->version = 'scorm_12';
}
require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');


if (confirm_sesskey() && (!empty($scoid))) {
    $result = true;
    $request = null;
    if (has_capability('mod/scorm:savetrack', context_module::instance($cm->id))) {
        $result = scorm_get_toc($USER, $scorm, $cm->id, TOCJSLINK, $currentorg, $scoid, $mode, $attempt, true, false);
        echo $result->toc;
    }
}
