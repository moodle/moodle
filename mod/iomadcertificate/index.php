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
 * @package   mod_iomadcertificate
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_certificate by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('locallib.php');

$id = required_param('id', PARAM_INT);           // Course Module ID

// Ensure that the course specified is valid
if (!$course = $DB->get_record('course', array('id'=> $id))) {
    print_error('Course ID is incorrect');
}

// Requires a login
require_login($course);

// Declare variables
$currentsection = "";
$printsection = "";
$timenow = time();

// Strings used multiple times
$striomadcertificates = get_string('modulenameplural', 'iomadcertificate');
$strissued  = get_string('issued', 'iomadcertificate');
$strname  = get_string("name");
$strsectionname = get_string('sectionname', 'format_'.$course->format);

// Print the header
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/iomadcertificate/index.php', array('id'=>$course->id));
$PAGE->navbar->add($striomadcertificates);
$PAGE->set_title($striomadcertificates);
$PAGE->set_heading($course->fullname);

// Add the page view to the Moodle log
$event = \mod_iomadcertificate\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->add_record_snapshot('course', $course);
$event->trigger();

// Get the iomadcertificates, if there are none display a notice
if (!$iomadcertificates = get_all_instances_in_course('iomadcertificate', $course)) {
    echo $OUTPUT->header();
    notice(get_string('noiomadcertificates', 'iomadcertificate'), "$CFG->wwwroot/course/view.php?id=$course->id");
    echo $OUTPUT->footer();
    exit();
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();

if ($usesections) {
    $table->head  = array ($strsectionname, $strname, $strissued);
} else {
    $table->head  = array ($strname, $strissued);
}

foreach ($iomadcertificates as $iomadcertificate) {
    if (!$iomadcertificate->visible) {
        // Show dimmed if the mod is hidden
        $link = html_writer::tag('a', $iomadcertificate->name, array('class' => 'dimmed',
            'href' => $CFG->wwwroot . '/mod/iomadcertificate/view.php?id=' . $iomadcertificate->coursemodule));
    } else {
        // Show normal if the mod is visible
        $link = html_writer::tag('a', $iomadcertificate->name, array('class' => 'dimmed',
            'href' => $CFG->wwwroot . '/mod/iomadcertificate/view.php?id=' . $iomadcertificate->coursemodule));
    }

    $strsection = '';
    if ($iomadcertificate->section != $currentsection) {
        if ($iomadcertificate->section) {
            $strsection = get_section_name($course, $iomadcertificate->section);
        }
        if ($currentsection !== '') {
            $table->data[] = 'hr';
        }
        $currentsection = $iomadcertificate->section;
    }

    // Get the latest iomadcertificate issue
    if ($certrecord = $DB->get_record('iomadcertificate_issues', array('userid' => $USER->id, 'iomadcertificateid' => $iomadcertificate->id))) {
        $issued = userdate($certrecord->timecreated);
    } else {
        $issued = get_string('notreceived', 'iomadcertificate');
    }

    if ($usesections) {
        $table->data[] = array ($strsection, $link, $issued);
    } else {
        $table->data[] = array ($link, $issued);
    }
}

echo $OUTPUT->header();
echo '<br />';
echo html_writer::table($table);
echo $OUTPUT->footer();
