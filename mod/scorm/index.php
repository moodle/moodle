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

$id = required_param('id', PARAM_INT);   // Course id.

$PAGE->set_url('/mod/scorm/index.php', array('id' => $id));

if (!empty($id)) {
    if (!$course = $DB->get_record('course', array('id' => $id))) {
        print_error('invalidcourseid');
    }
} else {
    print_error('missingparameter');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');

// Trigger instances list viewed event.
$event = \mod_scorm\event\course_module_instance_list_viewed::create(array('context' => context_course::instance($course->id)));
$event->add_record_snapshot('course', $course);
$event->trigger();

$strscorm = get_string("modulename", "scorm");
$strscorms = get_string("modulenameplural", "scorm");
$strname = get_string("name");
$strsummary = get_string("summary");
$strreport = get_string("report", 'scorm');
$strlastmodified = get_string("lastmodified");

$PAGE->set_title($strscorms);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strscorms);
echo $OUTPUT->header();
echo $OUTPUT->heading($strscorms);

$usesections = course_format_uses_sections($course->format);

if ($usesections) {
    $sortorder = "cw.section ASC";
} else {
    $sortorder = "m.timemodified DESC";
}

if (! $scorms = get_all_instances_in_course("scorm", $course)) {
    notice(get_string('thereareno', 'moodle', $strscorms), "../../course/view.php?id=$course->id");
    exit;
}

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strsummary, $strreport);
    $table->align = array ("center", "left", "left", "left");
} else {
    $table->head  = array ($strlastmodified, $strname, $strsummary, $strreport);
    $table->align = array ("left", "left", "left", "left");
}

foreach ($scorms as $scorm) {
    $context = context_module::instance($scorm->coursemodule);
    $tt = "";
    if ($usesections) {
        if ($scorm->section) {
            $tt = get_section_name($course, $scorm->section);
        }
    } else {
        $tt = userdate($scorm->timemodified);
    }
    $report = '&nbsp;';
    $reportshow = '&nbsp;';
    if (has_capability('mod/scorm:viewreport', $context)) {
        $trackedusers = scorm_get_count_users($scorm->id, $scorm->groupingid);
        if ($trackedusers > 0) {
            $reportshow = html_writer::link('report.php?id='.$scorm->coursemodule,
                                                get_string('viewallreports', 'scorm', $trackedusers));
        } else {
            $reportshow = get_string('noreports', 'scorm');
        }
    } else if (has_capability('mod/scorm:viewscores', $context)) {
        require_once('locallib.php');
        $report = scorm_grade_user($scorm, $USER->id);
        $reportshow = get_string('score', 'scorm').": ".$report;
    }
    $options = (object)array('noclean' => true);
    if (!$scorm->visible) {
        // Show dimmed if the mod is hidden.
        $table->data[] = array ($tt, html_writer::link('view.php?id='.$scorm->coursemodule,
                                                        format_string($scorm->name),
                                                        array('class' => 'dimmed')),
                                format_module_intro('scorm', $scorm, $scorm->coursemodule), $reportshow);
    } else {
        // Show normal if the mod is visible.
        $table->data[] = array ($tt, html_writer::link('view.php?id='.$scorm->coursemodule, format_string($scorm->name)),
                                format_module_intro('scorm', $scorm, $scorm->coursemodule), $reportshow);
    }
}

echo html_writer::empty_tag('br');

echo html_writer::table($table);

echo $OUTPUT->footer();