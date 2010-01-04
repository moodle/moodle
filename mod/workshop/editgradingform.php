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
 * Edit grading form in for a particular instance of workshop
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // workshop instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('workshop', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        error('Course is misconfigured');
    }

    if (! $workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $workshop = $DB->get_record('workshop', array('id' => $a))) {
        error('Course module is incorrect');
    }
    if (! $course = $DB->get_record('course', array('id' => $workshop->course))) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('workshop', $workshop->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "workshop", "editgradingform", "editgradingform.php?id=$cm->id", "$workshop->id");

// where should the user be sent in case of error of canceling
$returnurl = "{$CFG->wwwroot}/mod/workshop/view.php?id={$cm->id}";
$selfurl   = "{$CFG->wwwroot}/mod/workshop/editgradingform.php?id={$cm->id}";

// todo
$dimensions = $DB->get_records('workshop_forms_accumulative', array('workshopid' => $workshop->id), 'sort');

// load the form to edit the grading strategy dimensions
$strategyform = dirname(__FILE__) . '/grading/' . $workshop->strategy . '/gradingform.php';
if (file_exists($strategyform)) {
    require_once($strategyform);
} else {
    print_error('errloadingstrategyform', 'workshop', $returnurl);
}
$classname = 'workshop_edit_' . $workshop->strategy . '_strategy_form';
$mform = new $classname($selfurl, true, count($dimensions));

// initialize form data
$formdata = new stdClass;
$formdata->workshopid   = $workshop->id;
$formdata->strategy     = $workshop->strategy;
$formdata->dimensions   = $dimensions;
$mform->set_data($formdata);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} elseif ($data = $mform->get_data()) {
    print_object($data); die();
}

// build the navigation and the header
$navlinks = array();
$navlinks[] = array('name' => get_string('modulenameplural', 'workshop'), 
                    'link' => "index.php?id=$course->id", 
                    'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name), 
                    'link' => "view.php?id=$cm->id",
                    'type' => 'activityinstance');
$navlinks[] = array('name' => get_string('editinggradingform', 'workshop'), 
                    'link' => '',
                    'type' => 'title');
$navigation = build_navigation($navlinks);

// OUTPUT STARTS HERE

print_header_simple(format_string($workshop->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, get_string('modulename', 'workshop')), navmenu($course, $cm));

print_heading(get_string('strategy' . $workshop->strategy, 'workshop'));

$mform->display();

/// Finish the page
print_footer($course);
