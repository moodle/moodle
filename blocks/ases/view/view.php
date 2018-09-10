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
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_generalreports
 * @copyright  2011 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    
global $PAGE, $OUTPUT, $DB;
require_once("../../../config.php");
require_once("/code_index_reports.php");

// Input params
$courseid = required_param('courseid', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);

// Require course login
$course = $DB->get_record("course", array("id" => $courseid), '*', MUST_EXIST);
require_course_login($course);

$context = context_course::instance($courseid);
//require_capability('block/generalreports:use', $context);

// Optional params from request or default values
$action = optional_param('action', 'all', PARAM_ALPHANUM);
$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', false, PARAM_BOOL);

// Current url
$page_url = new moodle_url('/blocks/generalreports/view.php');
$page_url->params(array(
    'courseid' => $courseid,
    'instanceid' => $instanceid,
'action' => $action,
'id' => $id,
));

// Page format
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_pagetype('course-view-' . $course->format);
 $PAGE->navbar->add(get_string('pluginname', 'block_generalreports'), new moodle_url('/blocks/generalreports/view/view.php', array('courseid' => $courseid, 'instanceid' => $instanceid)));
$PAGE->set_url($page_url);
$PAGE->set_title(get_string('pagetitle', 'block_generalreports', $course->shortname));
$PAGE->set_heading($course->fullname);

// Displaying the page

$index = new code_index_reports();

echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->box($index->index());
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
